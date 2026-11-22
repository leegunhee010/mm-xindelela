<?php
// 화면 문구 모으기: t('...') 원문 + DB 문구(카테고리·배너) → 미얀마어 사전(app/Language/my/ui.php)에 없는 것 출력
//   php scripts/i18n-extract.php           빠진 문구 개수와 목록
//   php scripts/i18n-extract.php --json F  빠진 문구를 JSON 배열로 F 에 저장
$root = dirname(__DIR__);
$keys = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/app'));
foreach ($it as $f) {
    if (substr($f, -4) !== '.php' || str_contains($f, 'Language')) continue;
    $toks = token_get_all(file_get_contents($f));
    foreach ($toks as $i => $t) {
        if (is_array($t) && $t[0] === T_STRING && $t[1] === 't' && ($toks[$i + 1] ?? null) === '(' && is_array($toks[$i + 2] ?? null) && $toks[$i + 2][0] === T_CONSTANT_ENCAPSED_STRING) {
            $s = $toks[$i + 2][1];
            $s = $s[0] === "'" ? stripslashes(substr($s, 1, -1)) : stripcslashes(substr($s, 1, -1));
            $keys[trim($s)] = true;
        }
    }
}
$db = $root . '/writable/mm.db';
if (is_file($db)) {
    $d = new SQLite3($db);
    foreach (["select name from categories", "select title from banners", "select subtitle from banners", "select svalue from settings where skey in ('site_name','verify_sla_text')"] as $q) {
        $r = $d->query($q);
        while ($x = $r->fetchArray(SQLITE3_NUM)) if ($x[0]) $keys[trim($x[0])] = true;
    }
}
$dict = require $root . '/app/Language/my/ui.php';
$missing = array_values(array_filter(array_keys($keys), fn ($k) => $k !== '' && preg_match('/[\x{AC00}-\x{D7A3}]/u', $k) && empty($dict[$k])));
if (($argv[1] ?? '') === '--json') {
    file_put_contents($argv[2], json_encode($missing, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}
echo 'total ' . count($keys) . ', missing ' . count($missing) . "\n";
