<?php
// 판매페이지에서 받은 원본(public/assets/img/products/detail) → 상품별 폴더에 대표사진·상세 조각으로 정리.
// 긴 상세 이미지는 폭 최대 1000, 높이 2000px 단위로 잘라 JPEG(.88)로 저장. 결과는 kr-details.json.
$src = __DIR__ . '/../public/assets/img/products/detail/';
$dst = __DIR__ . '/../public/assets/img/products/';
$map = [
  1 => ['main' => ['kr1_main.jpg'], 'det' => ['kr1_d1.jpg', 'kr1_d2.jpg']],
  2 => ['main' => ['kr2_main.jpg'], 'det' => ['kr2_d1.png', 'kr2_d2.jpg']],
  3 => ['main' => ['kr3_main.jpg'], 'det' => ['kr3_d1.jpg', 'kr3_d2.jpg']],
  4 => ['main' => ['kr4_main.jpg', 'kr4_main2.jpg'], 'det' => ['kr4_d1.jpg', 'kr4_d2.jpg', 'kr4_d3.jpg', 'kr4_d4.jpg']],
  5 => ['main' => ['kr5_main.jpg'], 'det' => ['kr5_d1.jpg']],
  6 => ['main' => ['kr6_main.png', 'kr6_main2.jpg', 'kr6_main3.jpg'], 'det' => ['kr6_d1.jpg', 'kr6_d2.jpg', 'kr6_d3.jpg', 'kr6_d4.jpg', 'kr6_d5.jpg', 'kr6_d6.png', 'kr6_d7.jpg']],
  7 => ['main' => ['kr7_main.jpg', 'kr7_main2.jpg'], 'det' => ['ht_1.jpg', 'ht_25.jpg', 'ht_3.jpg', 'ht_4.jpg']],
  8 => ['main' => ['kr8_main.jpg', 'kr7_main2.jpg'], 'det' => ['ht_1.jpg', 'ht_24.jpg', 'ht_3.jpg', 'ht_4.jpg']],
];
// 요뽀끼 4종 (SSG 요뽀끼 공식 스토어 컵떡볶이 6입 세트: 대표사진 6장 + 상세 16장)
foreach (['yo1', 'yo2', 'yo3', 'yo4'] as $k) {
  $map[$k] = ['main' => array_map(fn ($i) => "{$k}_main$i.jpg", range(1, 6)), 'det' => array_map('basename', glob($src . "{$k}_d*"))];
}
// 초록원 호두아몬드율무차 50입 (cholocwon.co.kr product_no=32). 마지막 상세(고객센터·국내 배송 안내)는 뺌
$map['ch1'] = ['main' => ['ch1_main1.jpg', 'ch1_main2.png'], 'det' => ['ch1_d01.jpg', 'ch1_d02.jpg', 'ch1_d03.jpg']];
// 웰빙헬스팜 명품 고운발 (3wbmall.com/product/-/30), 엠제이케어 에센스 마스크팩 80매 8종 (SSG 1000591271148)
$map['gw1'] = ['main' => ['gw_main1.jpg', 'gw_main2.png'], 'det' => array_map('basename', glob($src . 'gw_d*'))];
$map['mj1'] = ['main' => array_map(fn ($i) => "mj_main$i.jpg", range(1, 9)), 'det' => array_map('basename', glob($src . 'mj_d*'))];
// 뷰티레시피 5종 (쿠팡 상품 페이지, 크롬으로 열어 이미지 주소 수집)
foreach (['br1', 'br2', 'br3', 'br4', 'br5', 'cu1', 'cu2', 'jy1', 'jy2', 'jy3', 'hy1', 'hy2'] as $k) {
  $map[$k] = ['main' => array_map('basename', glob($src . "{$k}_main*")), 'det' => array_map('basename', glob($src . "{$k}_d*"))];
  natsort($map[$k]['main']); $map[$k]['main'] = array_values($map[$k]['main']);
}
function load($f) { $i = @imagecreatefromstring(file_get_contents($f)); if (!$i) throw new Exception("bad $f"); return $i; }
function flat($im, $w, $h) { $o = imagecreatetruecolor($w, $h); imagefill($o, 0, 0, imagecolorallocate($o, 255, 255, 255)); return $o; }
$out = [];
foreach ($map as $n => $m) {
  $dn = is_int($n) ? "kr$n" : $n; $dir = $dst . "$dn/"; @mkdir($dir, 0775, true); array_map('unlink', glob($dir . '*.jpg'));
  $mains = [];
  foreach ($m['main'] as $k => $f) {
    $im = load($src . $f); $w = imagesx($im); $h = imagesy($im); $nw = min($w, 1000); $nh = (int) round($h * $nw / $w);
    $o = flat($im, $nw, $nh); imagecopyresampled($o, $im, 0, 0, 0, 0, $nw, $nh, $w, $h);
    $name = sprintf('m%d.jpg', $k + 1); imagejpeg($o, $dir . $name, 90); $mains[] = "assets/img/products/$dn/$name";
  }
  $blocks = []; $c = 0;
  foreach ($m['det'] as $f) {
    $im = load($src . $f); $w = imagesx($im); $h = imagesy($im); $nw = min($w, 1000); $s = $nw / $w; $th = (int) round($h * $s);
    for ($y = 0; $y < $th; $y += 2000) {
      $ch = min(2000, $th - $y); $o = flat($im, $nw, $ch);
      imagecopyresampled($o, $im, 0, 0, 0, (int) round($y / $s), $nw, $ch, $w, (int) round($ch / $s));
      $name = sprintf('d%02d.jpg', ++$c); imagejpeg($o, $dir . $name, 88);
      $blocks[] = ['type' => 'img', 'src' => "assets/img/products/$dn/$name", 'seq' => true, 'w' => $nw, 'h' => $ch];
    }
  }
  $out[$n] = ['images' => $mains, 'blocks' => $blocks];
  echo "$dn: main " . count($mains) . ", slices $c, " . round(array_sum(array_map('filesize', glob($dir . '*.jpg'))) / 1048576, 1) . "MB\n";
}
file_put_contents(__DIR__ . '/../app/Database/Seeds/kr-details.json', json_encode($out, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
