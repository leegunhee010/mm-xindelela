<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=1200">
<title><?= esc($title) ?> | 공급사센터</title>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="<?= base_url('assets/css/mm.css?v=7') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css?v=2') ?>">
</head>
<body class="ad">
<?php $cur = uri_string(); $nav = function (string $path, string $label) use ($cur) {
    $on = $cur === $path || ($path !== 'supplier' && str_starts_with($cur, $path));
    echo '<a href="' . site_url($path) . '" class="' . ($on ? 'on' : '') . '">' . $label . '</a>';
}; ?>
<aside class="sb" style="background:#1f3a0f">
  <div class="brand">공급사센터<small><?= esc($supplier['company_name']) ?></small></div>
  <div class="grp">메뉴</div>
  <?php $nav('supplier', '대시보드'); $nav('supplier/products', '상품 관리'); $nav('supplier/orders', '주문·출고'); $nav('supplier/profile', '공급사 정보'); ?>
  <div class="ft"><a href="<?= site_url('/') ?>" target="_blank">사이트 보기</a><a href="<?= site_url('supplier/logout') ?>">로그아웃</a></div>
</aside>
<div class="bodyw">
  <div class="tb"><h1><?= esc($title) ?></h1><div class="u"><?= esc($user['name']) ?> (<?= esc($user['email']) ?>)</div></div>
  <div class="ct">
    <?= mm_flash() ?>
    <?= $content ?>
  </div>
</div>
<script src="<?= base_url('assets/js/mm.js?v=2') ?>"></script>
</body>
</html>
