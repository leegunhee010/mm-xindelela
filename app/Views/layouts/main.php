<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=1280">
<title><?= esc($title) ?> | <?= esc(mm_setting('site_name', 'XINDELELA')) ?></title>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="<?= base_url('assets/css/mm.css?v=3') ?>">
</head>
<body>
<?php
$cartCount = 0;
if ($user && $user['role'] === 'buyer') {
    $cartCount = (new \App\Models\CartModel())->where('user_id', $user['id'])->countAllResults();
}
$cats = (new \App\Models\CategoryModel())->where('is_active', 1)->orderBy('sort')->findAll();
$sf   = (string) service('request')->getGet('sf');
$ico  = [
    'order' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/></svg>',
    'user'  => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>',
    'cart'  => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4h2l2.4 11h11L21 7H6"/><circle cx="9" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/></svg>',
];
?>
<div class="topbar"><div class="wrap">
  <div class="l"><span>XINDELELA 미얀마 도매몰</span><a href="<?= site_url('guide') ?>">이용안내</a><a href="<?= site_url('notices') ?>">공지사항</a><a href="<?= site_url('supplier/register') ?>">공급사 입점</a></div>
  <div class="r">
    <?php if ($user && $user['role'] === 'buyer'): ?>
      <a href="<?= site_url('mypage') ?>"><b><?= esc($user['company_name'] ?: $user['name']) ?></b> 님</a>
      <a href="<?= site_url('mypage/verify') ?>"><?= mm_status_label($user['status']) ?></a>
      <a href="<?= site_url('mypage/points') ?>">포인트 <b><?= number_format((int) $user['points_balance']) ?></b>P</a>
      <a href="<?= site_url('logout') ?>">로그아웃</a>
    <?php elseif ($user && $user['role'] === 'supplier'): ?>
      <a href="<?= site_url('supplier') ?>"><b>공급사센터</b> <?= esc($user['company_name']) ?></a>
      <a href="<?= site_url('supplier/logout') ?>">로그아웃</a>
    <?php elseif ($user && $user['role'] === 'admin'): ?>
      <a href="<?= site_url('admin') ?>"><b>관리자</b></a>
      <a href="<?= site_url('admin/logout') ?>">로그아웃</a>
    <?php else: ?>
      <a href="<?= site_url('register') ?>">회원가입</a>
      <a href="<?= site_url('login') ?>">로그인</a>
    <?php endif ?>
    <a href="<?= site_url('supplier/login') ?>">공급사센터</a>
    <a href="<?= site_url('notices') ?>">고객센터</a>
  </div>
</div></div>

<div class="hsearch"><div class="wrap">
  <a class="logo" href="<?= site_url('/') ?>">
    <span class="mark">X</span>
    <span><span class="t1">XINDELELA<em>mall</em></span><span class="t2">미얀마 K-브랜드 도매</span></span>
  </a>
  <form class="sbox" action="<?= site_url('products') ?>" method="get">
    <select name="sf"><option value="name">상품명</option><option value="brand" <?= $sf === 'brand' ? 'selected' : '' ?>>브랜드</option></select>
    <input type="text" name="q" value="<?= esc($q ?? '') ?>" placeholder="한국 정품 K-브랜드를 도매가로 ✨">
    <button type="submit" aria-label="검색"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg></button>
  </form>
  <div class="hright">
    <a href="<?= site_url('mypage/orders') ?>"><?= $ico['order'] ?><span class="tip">주문내역</span></a>
    <a href="<?= site_url('mypage') ?>"><?= $ico['user'] ?><span class="tip">마이페이지</span></a>
    <a href="<?= site_url('cart') ?>"><?= $ico['cart'] ?><?php if ($cartCount): ?><span class="cnt"><?= $cartCount ?></span><?php endif ?><span class="tip">장바구니</span></a>
  </div>
</div></div>

<div class="gnb"><div class="wrap">
  <div class="cat"><button type="button" aria-label="전체 카테고리"><span class="ham"></span></button>
    <div class="drop">
      <?php foreach ($cats as $c): ?><a href="<?= site_url('category/' . $c['id']) ?>"><?= esc($c['name']) ?></a><?php endforeach ?>
    </div>
  </div>
  <nav class="menu">
    <a href="<?= site_url('products?sort=pop') ?>">베스트</a>
    <a href="<?= site_url('products?sort=new') ?>" class="hot">신상품</a>
    <?php foreach (array_slice($cats, 0, 4) as $c): ?><a href="<?= site_url('category/' . $c['id']) ?>"><?= esc($c['name']) ?></a><?php endforeach ?>
  </nav>
  <nav class="menu2">
    <a href="<?= site_url('mypage/referral') ?>">친구소개</a>
    <a href="<?= site_url('notices') ?>">공지사항</a>
    <a href="<?= site_url('guide') ?>">이용안내</a>
    <a href="<?= site_url('supplier/register') ?>">공급사 입점</a>
  </nav>
</div></div>
<div class="hline"></div>

<?= mm_flash() ?>
<main><div class="wrap">
<?= $content ?>
</div></main>

<footer>
  <div class="wrap">
    <div class="fl">
      <div class="flogo">XINDELELA<em>mall</em></div>
      <div class="fm"><a href="<?= site_url('guide') ?>">이용안내</a><a href="<?= site_url('notices') ?>">공지사항</a><a href="<?= site_url('register') ?>">회원가입</a><a href="<?= site_url('supplier/register') ?>">공급사 입점 및 제조 협업 문의</a></div>
      <div class="fi">
        <p>XINDELELA Corporation · 미얀마 K-브랜드 B2B 도매 플랫폼 (mm.xindelela.com)</p>
        <p>이메일: <?= esc(mm_setting('cs_email', '')) ?> · Viber: <?= esc(mm_setting('cs_viber', '')) ?></p>
        <p class="sm">본 사이트는 사업자 인증을 받은 회원에게만 도매가를 공개하는 회원제 도매몰입니다. 결제는 사이트 밖에서 KBZPay 선불로 진행되며, 상품은 한국 공급사가 직접 등록·발송합니다.</p>
      </div>
    </div>
    <div class="fr">
      <div class="t">고객센터 (Viber)</div>
      <div class="ph"><?= esc(mm_setting('cs_viber', '')) ?></div>
      <p>평일 09:00 ~ 18:00 (미얀마 시간)</p>
      <p>주말·공휴일 휴무</p>
      <a class="btn btn-og btn-sm" href="<?= site_url('mypage/inquiries') ?>">1:1 문의하기</a>
    </div>
  </div>
</footer>
<script src="<?= base_url('assets/js/mm.js?v=3') ?>"></script>
</body>
</html>
