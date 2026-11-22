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
function load($f) { $i = @imagecreatefromstring(file_get_contents($f)); if (!$i) throw new Exception("bad $f"); return $i; }
function flat($im, $w, $h) { $o = imagecreatetruecolor($w, $h); imagefill($o, 0, 0, imagecolorallocate($o, 255, 255, 255)); return $o; }
$out = [];
foreach ($map as $n => $m) {
  $dir = $dst . "kr$n/"; @mkdir($dir, 0775, true); array_map('unlink', glob($dir . '*.jpg'));
  $mains = [];
  foreach ($m['main'] as $k => $f) {
    $im = load($src . $f); $w = imagesx($im); $h = imagesy($im); $nw = min($w, 1000); $nh = (int) round($h * $nw / $w);
    $o = flat($im, $nw, $nh); imagecopyresampled($o, $im, 0, 0, 0, 0, $nw, $nh, $w, $h);
    $name = sprintf('m%d.jpg', $k + 1); imagejpeg($o, $dir . $name, 90); $mains[] = "assets/img/products/kr$n/$name";
  }
  $blocks = []; $c = 0;
  foreach ($m['det'] as $f) {
    $im = load($src . $f); $w = imagesx($im); $h = imagesy($im); $nw = min($w, 1000); $s = $nw / $w; $th = (int) round($h * $s);
    for ($y = 0; $y < $th; $y += 2000) {
      $ch = min(2000, $th - $y); $o = flat($im, $nw, $ch);
      imagecopyresampled($o, $im, 0, 0, 0, (int) round($y / $s), $nw, $ch, $w, (int) round($ch / $s));
      $name = sprintf('d%02d.jpg', ++$c); imagejpeg($o, $dir . $name, 88);
      $blocks[] = ['type' => 'img', 'src' => "assets/img/products/kr$n/$name", 'seq' => true, 'w' => $nw, 'h' => $ch];
    }
  }
  $out[$n] = ['images' => $mains, 'blocks' => $blocks];
  echo "kr$n: main " . count($mains) . ", slices $c, " . round(array_sum(array_map('filesize', glob($dir . '*.jpg'))) / 1048576, 1) . "MB\n";
}
file_put_contents(__DIR__ . '/../app/Database/Seeds/kr-details.json', json_encode($out, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
