<?php

/**
 * 스키마 적용 스크립트 (CodeIgniter를 거치지 않는 순수 PHP).
 *
 *   php scripts/apply-sql.php docs/admin-accounts.sql
 *
 * 이 서버에서는 php spark migrate 를 쓸 수 없다:
 *   - /usr/bin/php8.2 는 버전은 맞지만 mysqli 확장이 없다
 *   - /usr/bin/php (8.1) 은 mysqli가 있지만 CI4 4.7이 8.2 미만을 거부한다
 *   - 사이트가 도는 PHP는 1Panel 런타임 컨테이너 안이고 docker 권한이 없다
 *
 * CI4를 부팅하지 않으면 8.2 제약이 사라지므로, mysqli만 있으면 되는 이 스크립트로
 * .sql 파일을 그대로 적용한다. 배포마다 실행되므로 SQL은 반드시 여러 번 실행해도
 * 안전해야 한다 (CREATE TABLE IF NOT EXISTS, 중복 INSERT 방지 등).
 *
 * PHP 8.1에서도 동작해야 한다 — 8.2 전용 문법을 쓰지 말 것.
 */

$target = $argv[1] ?? '';
if ($target === '' || ! file_exists($target)) {
    fwrite(STDERR, "사용법: php scripts/apply-sql.php <파일.sql | 디렉터리>\n");
    exit(1);
}

// 디렉터리를 주면 그 안의 .sql 을 이름순으로 모두 적용한다 (001-, 002- ...)
if (is_dir($target)) {
    $files = glob(rtrim($target, '/\\') . '/*.sql') ?: [];
    sort($files, SORT_STRING);
} else {
    $files = [$target];
}

if ($files === []) {
    echo "   적용할 .sql 파일이 없습니다: {$target}\n";
    exit(0);
}

$root = dirname(__DIR__);
$envPath = $root . '/.env';
if (! is_readable($envPath)) {
    fwrite(STDERR, ".env 를 읽을 수 없습니다: {$envPath}\n");
    exit(1);
}

$env = parse_ini_file($envPath, false, INI_SCANNER_RAW);
if ($env === false) {
    fwrite(STDERR, ".env 파싱에 실패했습니다.\n");
    exit(1);
}

/** .env 값은 따옴표로 감싸여 있을 수 있다. */
$conf = static function (string $key, string $default = '') use ($env): string {
    $v = $env['database.default.' . $key] ?? $default;
    return trim((string) $v, " \t\"'");
};

$host = $conf('hostname', 'localhost');
$user = $conf('username');
$pass = $conf('password');
$name = $conf('database');
$port = (int) ($conf('port', '3306') ?: 3306);

if ($user === '' || $name === '') {
    fwrite(STDERR, ".env 에 DB 사용자 또는 데이터베이스 이름이 없습니다.\n");
    exit(1);
}

mysqli_report(MYSQLI_REPORT_OFF); // 예외 대신 반환값으로 직접 처리한다

/**
 * .env의 호스트는 1Panel MySQL 컨테이너 이름(예: 1Panel-mysql-xxxx)이라
 * 컨테이너 네트워크 안에서만 해석된다. 이 스크립트는 호스트에서 도므로
 * 이름이 안 풀리면 호스트에 공개된 포트로도 시도한다.
 */
$candidates = [[$host, $port]];
if (! filter_var($host, FILTER_VALIDATE_IP) && strtolower($host) !== 'localhost') {
    $candidates[] = ['127.0.0.1', $port];
    if ($port !== 3306) {
        $candidates[] = ['127.0.0.1', 3306];
    }
}

$db     = null;
$tried  = [];
foreach ($candidates as [$h, $p]) {
    $c = mysqli_init();
    if ($c === false) {
        continue;
    }
    $c->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    if (@$c->real_connect($h, $user, $pass, $name, $p)) {
        $db = $c;
        echo "   DB 연결: {$name}@{$h}:{$p}\n";
        break;
    }
    $tried[] = "{$h}:{$p} — " . ($c->connect_error ?: '연결 실패');
}

if ($db === null) {
    fwrite(STDERR, "DB에 연결하지 못했습니다. 시도한 경로:\n");
    foreach ($tried as $t) {
        fwrite(STDERR, "  - {$t}\n");
    }
    fwrite(STDERR, ".env 의 database.default.hostname 이 컨테이너 이름이면, 호스트에서는\n");
    fwrite(STDERR, "1Panel의 MySQL 공개 포트로만 닿을 수 있습니다.\n");
    exit(1);
}

$db->set_charset('utf8mb4');

$totalSteps = 0;

foreach ($files as $file) {
    $sql = file_get_contents($file);
    if ($sql === false || trim($sql) === '') {
        fwrite(STDERR, "SQL 파일이 비어 있습니다: {$file}\n");
        exit(1);
    }

    echo '   실행: ' . basename($file) . "\n";

    if (! $db->multi_query($sql)) {
        fwrite(STDERR, "SQL 실행 실패 ({$file}): {$db->error}\n");
        exit(1);
    }

    // multi_query는 첫 오류에서 멈춘다. 결과를 끝까지 넘기며 오류를 확인한다.
    $stepped = 0;
    do {
        $stepped++;
        if ($result = $db->store_result()) {
            // 확인용 SELECT의 결과는 배포 로그에 남긴다
            while ($row = $result->fetch_assoc()) {
                $parts = [];
                foreach ($row as $k => $v) {
                    $parts[] = $k . '=' . $v;
                }
                echo '   확인: ' . implode('  ', $parts) . "\n";
            }
            $result->free();
        }
        if ($db->errno) {
            fwrite(STDERR, "SQL 오류 ({$file}, 구문 {$stepped}): {$db->error}\n");
            exit(1);
        }
    } while ($db->more_results() && $db->next_result());

    if ($db->errno) {
        fwrite(STDERR, "SQL 오류 ({$file}): {$db->error}\n");
        exit(1);
    }

    $totalSteps += $stepped;
}

echo '   스키마 적용 완료 (' . count($files) . '개 파일, 구문 ' . $totalSteps . "개)\n";
$db->close();
exit(0);
