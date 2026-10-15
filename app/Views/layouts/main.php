<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=1280">
<title><?= esc($title) ?> | <?= esc(mm_setting('site_name', 'XINDELELA')) ?></title>
<meta name="robots" content="noindex,nofollow">
<?php /* 아이스크림몰(i-screammall.co.kr) 미러에서 가져온 CSS 번들 (순서 그대로) */
foreach (['4d3a10b18ca278ac', '6aa6463b93b71a20', '382ef9402adc32b5', '02a257d789961a18', '08b906017950856f', 'e8e7c6b189408a7a', 'dd5d352a082118c1', '2adaf18aa8b18675', 'ecf867fdbdbe578d', '169b376d8b0459bd', '1ff38b619b0f57a0', '9bab8700f9b1c889', 'e0b4ecca42b0e9a0', 'f403fbff81a08bb1', 'b44dd7a96b17af8f', 'dbb0d29717545456', 'c7c17e71f387eea1', '6ced9ccd3ec5b59c', '13a7aa8b5e9c64f9', 'd842bb5bd531ea6d', 'a32bd69c5b27056c', 'ef46db3751d8e999'] as $css): ?>
<link rel="stylesheet" href="<?= base_url('assets/is/css/' . $css . '.css') ?>">
<?php endforeach ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.1.14/swiper-bundle.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/is-fill.css?v=2') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/mm.css?v=4') ?>">
</head>
<body>
<?php
$cartCount = 0;
if ($user && $user['role'] === 'buyer') {
    $cartCount = (new \App\Models\CartModel())->where('user_id', $user['id'])->countAllResults();
}
$cats = (new \App\Models\CategoryModel())->where('is_active', 1)->orderBy('sort')->findAll();
$sf   = (string) service('request')->getGet('sf');
$cur  = uri_string();
?>
<header class="relative w-full max-w-full bg-white z-[600]"><div class="pc:mx-auto pc:min-h-[208px] pc:min-w-large">
  <!-- 탑바 (검정 40px) -->
  <div class="flex h-[40px] w-full justify-between bg-black mo:hidden pc:block"><div class="relative flex items-center pc:gap-[32px] m-auto flex h-[40px] w-large justify-between gap-[24px]">
    <ul class="header-top-menu">
      <li class="menu-item active"><a target="_self" class="menu-item-link" href="<?= site_url('/') ?>">XINDELELA 미얀마 도매몰</a></li>
      <li class="menu-item"><a class="menu-item-link" href="<?= site_url('guide') ?>">이용안내</a></li>
      <li class="menu-item"><a class="menu-item-link" href="<?= site_url('notices') ?>">공지사항</a></li>
      <li class="menu-item"><a class="menu-item-link" href="<?= site_url('supplier/register') ?>">공급사 입점</a></li>
      <li class="menu-item"><a class="menu-item-link" target="_blank" rel="noopener" href="https://xindelela.com">XINDELELA</a></li>
    </ul>
    <ul class="account-menu flex items-center gap-[16px] text-[12px]">
      <?php if ($user && $user['role'] === 'buyer'): ?>
        <li><a class="acount-menu-link" href="<?= site_url('mypage') ?>"><b class="text-white"><?= esc($user['company_name'] ?: $user['name']) ?></b> 님 · <?= mm_status_label($user['status']) ?></a></li>
        <li><a class="acount-menu-link" href="<?= site_url('mypage/points') ?>">포인트 <b class="text-white"><?= number_format((int) $user['points_balance']) ?></b>P</a></li>
        <li><a class="acount-menu-link" href="<?= site_url('logout') ?>">로그아웃</a></li>
      <?php elseif ($user && $user['role'] === 'supplier'): ?>
        <li><a class="acount-menu-link" href="<?= site_url('supplier') ?>"><b class="text-white">공급사센터</b> <?= esc($user['company_name']) ?></a></li>
        <li><a class="acount-menu-link" href="<?= site_url('supplier/logout') ?>">로그아웃</a></li>
      <?php elseif ($user && $user['role'] === 'admin'): ?>
        <li><a class="acount-menu-link" href="<?= site_url('admin') ?>"><b class="text-white">관리자</b></a></li>
        <li><a class="acount-menu-link" href="<?= site_url('admin/logout') ?>">로그아웃</a></li>
      <?php else: ?>
        <li><a class="acount-menu-link" href="<?= site_url('register') ?>">회원가입</a></li>
        <li><a class="acount-menu-link" href="<?= site_url('login') ?>">로그인</a></li>
      <?php endif ?>
      <li><a class="acount-menu-link" href="<?= site_url('supplier/login') ?>">공급사센터</a></li>
      <li><a class="acount-menu-link" href="<?= site_url('notices') ?>">고객센터</a></li>
    </ul>
  </div></div>

  <!-- 로고·검색·유틸 -->
  <div class="w-full mo:hidden pc:block"><div class="header-wraper w-full pc:mx-auto pc:w-large">
    <div class="relative flex items-center pc:gap-[32px] pc:h-[44px] pc:mt-[32px]">
      <h1 class="shrink-0"><a href="<?= site_url('/') ?>" class="flex items-center gap-[10px]">
        <span class="flex size-[40px] items-center justify-center rounded-[12px] bg-primary text-[22px] font-bold text-white">X</span>
        <span class="flex flex-col"><span class="text-[24px] font-bold leading-[1.1] tracking-[-0.5px]">XINDELELA<em class="not-italic text-primary">mall</em></span><span class="mt-[3px] text-[12px] text-gray3">미얀마 K-브랜드 도매</span></span>
      </a></h1>
      <form class="search-area rounded-[30px] bg-gray9 pc:w-[384px] pc:min-h-12 pc:py-1 pc:pl-4 pc:h-auto pc:z-10" action="<?= site_url('products') ?>" method="get">
        <div class="search-bar relative flex h-full w-full min-w-0 items-center">
          <select name="sf" class="bg-inherit text-body3 text-gray3 pr-[6px] border-r border-gray6 mr-[6px] outline-none"><option value="name">상품명</option><option value="brand" <?= $sf === 'brand' ? 'selected' : '' ?>>브랜드</option></select>
          <input placeholder="한국 정품 K-브랜드를 도매가로 ✨" class="search-inp min-w-0 flex-1 bg-inherit py-[10px] pl-[10px] pr-[40px] placeholder:text-body3 placeholder:text-gray5 pc:h-full" type="text" name="q" value="<?= esc($q ?? '') ?>">
          <div class="absolute right-[12px] top-1/2 flex -translate-y-1/2 items-center gap-[8px]"><button type="submit" class="btn-search shrink-0">Search</button></div>
        </div>
      </form>
      <div class="util-menu flex mo:gap-[23px] pc:gap-[30px] ml-auto">
        <div class="relative group"><a class="car mo:hidden" href="<?= site_url('mypage/orders') ?>" title="주문내역"><span class="blind">주문/배송 페이지로 이동</span></a><div class="absolute left-1/2 -translate-x-1/2 top-9 hidden group-hover:flex items-center justify-center whitespace-nowrap rounded-[6px] bg-gray-700 px-[10px] py-[2px] text-[12px] text-white">주문내역</div></div>
        <div class="group relative inline-block mo:hidden"><a class="mypage mo:hidden" href="<?= site_url('mypage') ?>" title="마이페이지"><span class="blind">마이페이지로 이동</span></a><div class="absolute left-1/2 -translate-x-1/2 top-9 hidden group-hover:flex items-center justify-center whitespace-nowrap rounded-[6px] bg-gray-700 px-[10px] py-[2px] text-[12px] text-white">마이페이지</div></div>
        <div class="relative group"><a class="cart" href="<?= site_url('cart') ?>" title="장바구니"><span class="blind">장바구니</span><?php if ($cartCount): ?><span class="absolute -right-[6px] -top-[6px] flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-primary px-[4px] text-[11px] font-bold text-white"><?= $cartCount ?></span><?php endif ?></a><div class="absolute left-1/2 -translate-x-1/2 top-9 hidden group-hover:flex items-center justify-center whitespace-nowrap rounded-[6px] bg-gray-700 px-[10px] py-[2px] text-[12px] text-white">장바구니</div></div>
      </div>
    </div>

    <!-- GNB -->
    <div class="gnb-wrap left-0 m-auto w-full bg-white"><div class="w-[1280px] m-auto"><div class="flex items-center pc:gap-[32px] h-[55px]">
      <div class="relative group/cat flex h-[55px] items-center">
        <button type="button" class="btn-category flex items-center justify-center" aria-label="전체 카테고리"><span class="blind">전체 카테고리</span></button>
        <div class="absolute left-0 top-[52px] z-[700] hidden w-[240px] overflow-hidden rounded-[12px] border border-gray6 bg-white py-[8px] shadow-[0_8px_24px_rgba(0,0,0,.08)] group-hover/cat:block">
          <?php foreach ($cats as $c): ?><a class="block px-[20px] py-[10px] text-[15px] font-medium hover:bg-[#fff3eb] hover:text-primary" href="<?= site_url('category/' . $c['id']) ?>"><?= esc($c['name']) ?></a><?php endforeach ?>
        </div>
      </div>
      <div class="gnb flex w-full items-center justify-between">
        <div class="flex items-center gap-[32px]">
          <a class="link-item pc:inline-flex pc:items-center text-[18px] font-medium <?= str_contains($cur, 'sort=pop') ? 'text-primary' : '' ?>" href="<?= site_url('products?sort=pop') ?>"><span class="relative whitespace-nowrap">베스트</span></a>
          <a class="link-item pc:inline-flex pc:items-center text-[18px] font-medium" href="<?= site_url('products?sort=new') ?>"><span class="relative whitespace-nowrap">신상품<span class="absolute -right-[12px] -top-[6px] flex size-[14px] items-center justify-center rounded-full bg-primary text-[9px] font-bold text-white">N</span></span></a>
          <?php foreach (array_slice($cats, 0, 4) as $c): ?><a class="link-item pc:inline-flex pc:items-center text-[18px] font-medium <?= $cur === 'category/' . $c['id'] ? 'text-primary' : '' ?>" href="<?= site_url('category/' . $c['id']) ?>"><span class="relative whitespace-nowrap"><?= esc($c['name']) ?></span></a><?php endforeach ?>
        </div>
        <div class="flex items-center gap-[32px]">
          <a class="link-item text-[18px] font-medium" href="<?= site_url('mypage/referral') ?>">친구소개</a>
          <a class="link-item text-[18px] font-medium" href="<?= site_url('notices') ?>">공지사항</a>
          <a class="link-item text-[18px] font-medium" href="<?= site_url('guide') ?>">이용안내</a>
          <a class="link-item text-[18px] font-medium" href="<?= site_url('supplier/register') ?>">공급사 입점</a>
        </div>
      </div>
    </div></div></div>
  </div></div>
</div></header>

<?= mm_flash() ?>
<div class="container relative w-full max-w-full flex-auto bg-white pc:pb-[120px]" id="container"><div class="contents-wrap size-full w-full px-0 pc:mx-auto pc:min-w-large bg-white"><div class="body-wrap w-full">
<?php if (! empty($fullWidth)): ?>
<?= $content ?>
<?php else: ?>
<div class="mmwrap pc:pt-[24px]"><?= $content ?></div>
<?php endif ?>
</div></div></div>

<footer class="w-full border-t border-solid border-gray6"><div class="m-auto mo:w-full pc:w-large"><div class="footer-conts-wrap flex justify-between pt-[50px] pc:pb-[40px]">
  <div class="footer-conts-item">
    <a href="<?= site_url('/') ?>" class="inline-flex items-center gap-[8px] mb-[28px]"><span class="flex size-[32px] items-center justify-center rounded-[10px] bg-primary text-[18px] font-bold text-white">X</span><span class="text-[20px] font-bold">XINDELELA<em class="not-italic text-primary">mall</em></span></a>
    <ul class="footer-item-wrap type1 text-body3 text-gray3">
      <li class="footer-item mb-[20px]"><a href="<?= site_url('guide') ?>">이용안내</a></li>
      <li class="footer-item mb-[20px]"><a href="<?= site_url('notices') ?>">공지사항</a></li>
      <li class="footer-item mb-[20px] font-bold text-black"><a href="<?= site_url('register') ?>">회원가입 · 사업자 인증</a></li>
      <li class="footer-item mb-[20px]"><a href="<?= site_url('mypage/inquiries') ?>">고객센터</a></li>
      <li class="footer-item"><a href="<?= site_url('supplier/register') ?>">입점 및 제조 협업 문의</a></li>
    </ul>
    <ul class="footer-item-wrap type2 mb-0 text-body3 text-gray3">
      <li class="footer-item company font-bold">XINDELELA Corporation</li>
      <li class="footer-item">미얀마 K-브랜드 B2B 도매 플랫폼</li>
      <li class="footer-item">mm.xindelela.com</li>
    </ul>
    <ul class="footer-item-wrap type2 mt-[4px] text-body3 text-gray3">
      <li class="footer-item">이메일: <?= esc(mm_setting('cs_email', '')) ?></li>
      <li class="footer-item">Viber: <?= esc(mm_setting('cs_viber', '')) ?></li>
    </ul>
    <p class="text-label1 font-regular text-[#999] mt-[24px] w-[848px]">본 사이트는 사업자 인증을 받은 회원에게만 도매가를 공개하는 회원제 도매몰입니다. 결제는 사이트 밖에서 KBZPay 선불로 진행되며, 상품은 한국 공급사가 직접 등록·발송합니다.</p>
  </div>
  <div class="footer-conts-item flex flex-col">
    <p class="text-gray3">고객센터 (Viber)</p>
    <p class="text-[30px] font-bold leading-[1.2] mt-[4px] mb-[10px]"><?= esc(mm_setting('cs_viber', '')) ?></p>
    <p class="mt-[4px] text-body3 font-regular">평일 09:00 ~ 18:00 (미얀마 시간)</p>
    <p class="mt-[4px] text-body3 text-gray3">주말·공휴일 휴무</p>
    <a href="<?= site_url('mypage/inquiries') ?>" class="mt-[16px] inline-flex w-fit items-center rounded-[99px] border border-primary px-[16px] py-[8px] text-body3 font-bold text-primary">1:1 문의하기</a>
  </div>
</div></div></footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.1.14/swiper-bundle.min.js"></script>
<script src="<?= base_url('assets/js/mm.js?v=4') ?>"></script>
</body>
</html>
