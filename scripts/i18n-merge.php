<?php
// 번역 JSON({한국어: 미얀마어}) 들을 미얀마어 사전 app/Language/my/ui.php 에 합친다.
//   php scripts/i18n-merge.php a.json b.json ...
// HTML 태그·%s·{자리표시}가 원문과 다르면 경고만 하고 넣지 않는다.
$root = dirname(__DIR__);
$file = $root . '/app/Language/my/ui.php';
$dict = is_file($file) ? (require $file) : [];
$sig  = static function (string $s): array {
    preg_match_all('/<[^>]*>|%[sd]|\{[^}]+\}/u', $s, $m);
    sort($m[0]);
    return $m[0];
};
$add = $bad = 0;
foreach (array_slice($argv, 1) as $j) {
    $tr = json_decode((string) file_get_contents($j), true);
    if (! is_array($tr)) {
        fwrite(STDERR, "JSON 오류: $j\n");
        continue;
    }
    foreach ($tr as $ko => $my) {
        $ko = trim((string) $ko);
        $my = trim((string) $my);
        if ($ko === '' || $my === '') {
            continue;
        }
        if ($sig($ko) !== $sig($my)) {
            $bad++;
            fwrite(STDERR, "태그 불일치: $ko  =>  $my\n");
            continue;
        }
        $dict[$ko] = $my;
        $add++;
    }
}
ksort($dict);
file_put_contents($file, "<?php\n// 구매자 화면 미얀마어 사전 (한국어 원문 => 미얀마어). scripts/i18n-extract.php 로 빠진 문구 확인.\nreturn " . var_export($dict, true) . ";\n");
echo "added $add, rejected $bad, total " . count($dict) . "\n";
