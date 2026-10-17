<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=1200">
<title><?= esc($title) ?> | 관리자</title>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="<?= base_url('assets/css/mm.css?v=7') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css?v=5') ?>">
</head>
<body class="ad">
<?php $cur = uri_string(); $nav = function (string $path, string $label, int $badge = 0) use ($cur) {
    $on = $cur === $path || ($path !== 'admin' && str_starts_with($cur, $path . '/')) || ($path !== 'admin' && $cur === $path);
    echo '<a href="' . site_url($path) . '" class="' . ($on ? 'on' : '') . '">' . $label . ($badge ? '<span class="bd">' . $badge . '</span>' : '') . '</a>';
}; ?>
<aside class="sb">
  <div class="brand">XINDELELA<small>mm.xindelela.com 관리자</small></div>
  <div class="grp">운영</div>
  <?php $nav('admin', '대시보드'); $nav('admin/buyers', '회원 관리', $badges['buyers']); $nav('admin/orders', '주문·입금 관리', $badges['orders']); $nav('admin/inquiries', '문의', $badges['inquiries']); ?>
  <div class="grp">상품</div>
  <?php $nav('admin/products', '상품 검수', $badges['products']); $nav('admin/suppliers', '공급사', $badges['suppliers']); $nav('admin/categories', '카테고리'); ?>
  <div class="grp">포인트</div>
  <?php $nav('admin/points', '포인트 원장'); $nav('admin/referrals', '추천 현황'); ?>
  <div class="grp">콘텐츠·설정</div>
  <?php $nav('admin/notices', '공지사항'); $nav('admin/banners', '메인 배너'); $nav('admin/settings', '설정'); ?>
  <div class="ft"><a href="<?= site_url('/') ?>" target="_blank">사이트 보기</a><a href="<?= site_url('admin/logout') ?>">로그아웃</a></div>
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
