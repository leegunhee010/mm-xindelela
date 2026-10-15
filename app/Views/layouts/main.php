<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=980">
<title><?= esc($title) ?> | <?= esc(mm_setting('site_name', 'XINDELELA')) ?></title>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="<?= base_url('assets/css/mm.css?v=2') ?>">
</head>
<body>
<?php
$cartCount = 0;
if ($user && $user['role'] === 'buyer') {
    $cartCount = (new \App\Models\CartModel())->where('user_id', $user['id'])->countAllResults();
}
$cats = (new \App\Models\CategoryModel())->where('is_active', 1)->orderBy('sort')->findAll();
?>
<div class="topbar"><div class="wrap">
  <div class="l"><span>●</span> <?= esc(mm_setting('topbar_text', '')) ?></div>
  <div class="r">
    <?php if ($user && $user['role'] === 'buyer'): ?>
      <a href="<?= site_url('mypage') ?>"><b><?= esc($user['company_name'] ?: $user['name']) ?></b> 님 <span class="st st-<?= esc($user['status']) ?>"><?= mm_status_label($user['status']) ?></span></a>
      <a href="<?= site_url('mypage/orders') ?>">주문내역</a>
      <a href="<?= site_url('mypage/points') ?>">포인트 <b><?= number_format((int) $user['points_balance']) ?></b>P</a>
      <a href="<?= site_url('cart') ?>">장바구니<?php if ($cartCount): ?><span class="badge"><?= $cartCount ?></span><?php endif ?></a>
      <a href="<?= site_url('logout') ?>">로그아웃</a>
    <?php elseif ($user && $user['role'] === 'supplier'): ?>
      <a href="<?= site_url('supplier') ?>"><b>공급사센터</b> (<?= esc($user['company_name']) ?>)</a>
      <a href="<?= site_url('supplier/logout') ?>">로그아웃</a>
    <?php elseif ($user && $user['role'] === 'admin'): ?>
      <a href="<?= site_url('admin') ?>"><b>관리자</b></a>
      <a href="<?= site_url('admin/logout') ?>">로그아웃</a>
    <?php else: ?>
      <a href="<?= site_url('login') ?>">로그인</a>
      <a href="<?= site_url('register') ?>"><b>회원가입</b></a>
      <a href="<?= site_url('mypage') ?>">마이페이지</a>
      <a href="<?= site_url('cart') ?>">장바구니</a>
    <?php endif ?>
    <a href="<?= site_url('supplier/login') ?>">공급사센터</a>
    <a href="<?= site_url('notices') ?>">고객센터</a>
  </div>
</div></div>

<div class="hsearch"><div class="wrap">
  <a class="logo" href="<?= site_url('/') ?>">
    <span class="mark">X</span>
    <span><span class="t1">XINDELELA</span><span class="t2">미얀마 K-브랜드 도매몰</span></span>
  </a>
  <form class="sbox" action="<?= site_url('products') ?>" method="get">
    <?php $sf = (string) service('request')->getGet('sf'); ?><select name="sf"><option value="name">상품명</option><option value="brand" <?= $sf === 'brand' ? 'selected' : '' ?>>브랜드</option></select>
    <input type="text" name="q" value="<?= esc($q ?? '') ?>" placeholder="찾는 상품을 입력하세요">
    <button type="submit" aria-label="검색"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg></button>
  </form>
  <div class="hright">고객센터 (Viber)<b><?= esc(mm_setting('cs_viber', '')) ?></b></div>
</div></div>

<div class="gnb"><div class="wrap">
  <div class="cat"><span class="ham"></span>카테고리
    <div class="drop">
      <?php foreach ($cats as $c): ?><a href="<?= site_url('category/' . $c['id']) ?>"><?= esc($c['name']) ?></a><?php endforeach ?>
    </div>
  </div>
  <nav class="menu">
    <a href="<?= site_url('products') ?>">전체상품</a>
    <a href="<?= site_url('products?sort=new') ?>">신상품</a>
    <a href="<?= site_url('products?sort=pop') ?>">베스트</a>
    <a href="<?= site_url('notices') ?>">공지사항</a>
    <a href="<?= site_url('guide') ?>">이용안내</a>
    <a href="<?= site_url('mypage/referral') ?>" class="hot">친구소개 포인트</a>
    <a href="<?= site_url('supplier/register') ?>">공급사 입점</a>
  </nav>
</div></div>

<?= mm_flash() ?>
<main><div class="wrap">
<?= $content ?>
</div></main>

<footer>
  <div class="wrap">
    <div class="fm"><a href="<?= site_url('guide') ?>">이용안내</a><a href="<?= site_url('notices') ?>">공지사항</a><a href="<?= site_url('supplier/register') ?>">공급사 입점문의</a><a href="<?= site_url('register') ?>">회원가입</a></div>
    <div class="fi">
      <b>XINDELELA Corporation</b> · 미얀마 K-브랜드 B2B 도매 플랫폼 (mm.xindelela.com)<br>
      고객센터 Viber <?= esc(mm_setting('cs_viber', '')) ?> · 이메일 <?= esc(mm_setting('cs_email', '')) ?> · 운영시간 월~금 09:00~18:00 (미얀마 시간)<br>
      본 사이트는 사업자 인증을 받은 회원에게만 도매가를 공개하는 회원제 도매몰입니다. 결제는 사이트 밖에서 KBZPay 선불로 진행됩니다.
    </div>
  </div>
</footer>
<script src="<?= base_url('assets/js/mm.js?v=2') ?>"></script>
</body>
</html>
