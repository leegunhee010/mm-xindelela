<?php
// 한 번 쓰는 변환 도구: 구매자 화면 파일의 한국어 문구를 t('...') 로 감싼다.
//   php scripts/i18n-wrap.php <파일...>
// - HTML 부분: 한글이 들어간 글자 덩어리 → t('...') 출력 태그로 감쌈 (HTML 주석 안은 건너뜀)
// - PHP 부분: 한글이 든 '...' 문자열 → t('...')  (배열 키, case, 이미 t( 안인 것은 건너뜀)
// - 큰따옴표+변수 문자열은 손으로 고치도록 목록만 출력

$H = '/[\x{AC00}-\x{D7A3}]/u';

function q(string $s): string { return "'" . str_replace(['\\', "'"], ['\\\\', "\\'"], $s) . "'"; }

function wrapHtml(string $html, string $H, int &$n): string
{
    // 주석·<style> 은 그대로, 나머지 덩어리만 처리
    $parts = preg_split('/(<!--.*?-->|<style\b.*?<\/style>)/su', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    foreach ($parts as $i => $p) {
        if ($i % 2 === 1) {
            continue;
        }
        $parts[$i] = preg_replace_callback('/[^<>"\'`\n{}]*[\x{AC00}-\x{D7A3}][^<>"\'`\n{}]*/u', function ($m) use (&$n) {
            $s = $m[0];
            if (! preg_match('/^(\s*)(.*?)(\s*)$/us', $s, $mm) || $mm[2] === '') {
                return $s;
            }
            $n++;
            return $mm[1] . '<?= t(' . q($mm[2]) . ') ?>' . $mm[3];
        }, $p);
    }
    return implode('', $parts);
}

foreach (array_slice($argv, 1) as $file) {
    $src  = file_get_contents($file);
    $toks = token_get_all($src);
    $out  = '';
    $n    = 0;
    $dq   = [];
    $sig  = static function (array $toks, int $i, int $dir) {
        for ($j = $i + $dir; $j >= 0 && $j < count($toks); $j += $dir) {
            $t = $toks[$j];
            if (is_array($t) && in_array($t[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }
            return $t;
        }
        return null;
    };
    foreach ($toks as $i => $t) {
        if (! is_array($t)) {
            $out .= $t;
            continue;
        }
        [$id, $text, $line] = $t;
        if ($id === T_INLINE_HTML && preg_match($H, $text)) {
            $out .= wrapHtml($text, $H, $n);
            continue;
        }
        if ($id === T_CONSTANT_ENCAPSED_STRING && preg_match($H, $text) && $text[0] === "'") {
            $next = $sig($toks, $i, 1);
            $prev = $sig($toks, $i, -1);
            $prev2 = null;
            // 이미 t( 안이면 건너뜀
            for ($j = $i - 1; $j >= 0; $j--) {
                if (is_array($toks[$j]) && $toks[$j][0] === T_WHITESPACE) continue;
                if ($toks[$j] === '(') { $prev2 = $sig($toks, $j, -1); }
                break;
            }
            $isKey  = is_array($next) && $next[0] === T_DOUBLE_ARROW;
            $isCase = is_array($prev) && $prev[0] === T_CASE;
            $isIdx  = $prev === '[' && $next === ']';
            $inT    = is_array($prev2) && $prev2[0] === T_STRING && in_array($prev2[1], ['t', 'log_message', 'lang'], true);
            if (! $isKey && ! $isCase && ! $isIdx && ! $inT) {
                $out .= 't(' . $text . ')';
                $n++;
                continue;
            }
        }
        if (($id === T_ENCAPSED_AND_WHITESPACE) && preg_match($H, $text)) {
            $dq[] = $line;
        }
        $out .= $text;
    }
    file_put_contents($file, $out);
    echo $file . ': ' . $n . ($dq ? '  ※ 큰따옴표 문자열 확인 줄: ' . implode(',', array_unique($dq)) : '') . "\n";
}
