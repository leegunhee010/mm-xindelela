<?php
// 로컬 AI 스톡(rinda-mirror/s3pool, 실브랜드 없음)에서 상품 자리 정사각 크롭 → public/assets/img/demo/pN.jpg
// 사용: php scripts/crop-stock.php   (map: 상품키 => [원본, x비율, y비율, 폭비율])
$src = 'C:/Users/이건희/rinda-mirror/s3pool/';
$out = __DIR__ . '/../public/assets/img/demo/';
$map = [
  'p1' => ['51', .50, .55, .55], 'p2' => ['22', .50, .60, .45], 'p3' => ['08', .35, .55, .55], 'p4' => ['50', .28, .76, .38],
  'p5' => ['53', .28, .55, .45], 'p6' => ['43', .33, .80, .34], 'p7' => ['30', .72, .80, .40], 'p8' => ['31', .33, .80, .34],
  'p9' => ['36', .55, .62, .50], 'p10' => ['39', .50, .76, .42], 'p11' => ['30', .35, .78, .34], 'p12' => ['33', .72, .76, .40],
  'banner_1' => ['08', .5, .5, 1.0, 'wide'], 'banner_2' => ['51', .5, .5, 1.0, 'wide'], 'banner_3' => ['54', .5, .5, 1.0, 'wide'],
];
foreach ($map as $k => $m) {
  [$f, $cx, $cy, $wr] = $m; $wide = ($m[4] ?? '') === 'wide';
  $im = imagecreatefromjpeg($src . $f . '.jpg'); $W = imagesx($im); $H = imagesy($im);
  if ($wide) { $tw = 1260; $th = 832; $cw = $W; $ch = (int) round($W * 832 / 1260); if ($ch > $H) { $ch = $H; $cw = (int) round($H * 1260 / 832); } }
  else { $tw = $th = 600; $cw = $ch = (int) round(min($W, $H) * $wr); }
  $x = (int) max(0, min($W - $cw, $cx * $W - $cw / 2)); $y = (int) max(0, min($H - $ch, $cy * $H - $ch / 2));
  $dst = imagecreatetruecolor($tw, $th); imagecopyresampled($dst, $im, 0, 0, $x, $y, $tw, $th, $cw, $ch);
  imagejpeg($dst, $out . $k . '.jpg', 86); echo "$k <- $f.jpg ($x,$y,$cw)\n";
}
