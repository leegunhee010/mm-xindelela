<!DOCTYPE html>
<html lang="<?= mm_lang() === 'my' ? 'my' : 'ko' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<?php
$seoSite  = mm_setting('seo_title') ?: t(mm_setting('site_name', 'XINDELELA'));
$seoPath  = uri_string();
$seoTitle = $seoPath === '' ? $seoSite : $title . ' | ' . $seoSite;
$seoDesc  = $metaDesc ?? mm_setting('seo_description', '');
$seoImg   = $ogImage ?? (mm_setting('seo_og_image') ? base_url(mm_setting('seo_og_image')) : null);
$seoFav   = mm_setting('seo_favicon');
$seoIndex = mm_setting('seo_indexable', '0') === '1' && ! preg_match('#^(mypage|cart|checkout|login|register|logout|admin)#', $seoPath);
?>
<title><?= esc($seoTitle) ?></title>
<meta name="robots" content="<?= $seoIndex ? 'index,follow' : 'noindex,nofollow' ?>">
<?php if ($seoDesc): ?><meta name="description" content="<?= esc($seoDesc) ?>"><?php endif ?>
<?php if ($kw = mm_setting('seo_keywords')): ?><meta name="keywords" content="<?= esc($kw) ?>"><?php endif ?>
<link rel="canonical" href="<?= esc(current_url()) ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= esc($seoSite) ?>">
<meta property="og:title" content="<?= esc($seoTitle) ?>">
<?php if ($seoDesc): ?><meta property="og:description" content="<?= esc($seoDesc) ?>"><?php endif ?>
<meta property="og:url" content="<?= esc(current_url()) ?>">
<?php if ($seoImg): ?><meta property="og:image" content="<?= esc($seoImg) ?>"><meta name="twitter:card" content="summary_large_image"><meta name="twitter:image" content="<?= esc($seoImg) ?>"><?php endif ?>
<?php if ($seoFav): ?><link rel="icon" href="<?= base_url($seoFav) ?>"><link rel="apple-touch-icon" href="<?= base_url($seoFav) ?>"><?php endif ?>
<?php /* 아이스크림몰(i-screammall.co.kr) 미러에서 가져온 CSS 번들 (순서 그대로) */
foreach (['4d3a10b18ca278ac', '6aa6463b93b71a20', '382ef9402adc32b5', '02a257d789961a18', '08b906017950856f', 'e8e7c6b189408a7a', 'dd5d352a082118c1', '2adaf18aa8b18675', 'ecf867fdbdbe578d', '169b376d8b0459bd', '1ff38b619b0f57a0', '9bab8700f9b1c889', 'e0b4ecca42b0e9a0', 'f403fbff81a08bb1', 'b44dd7a96b17af8f', 'dbb0d29717545456', 'c7c17e71f387eea1', '6ced9ccd3ec5b59c', '13a7aa8b5e9c64f9', 'd842bb5bd531ea6d', 'a32bd69c5b27056c', 'ef46db3751d8e999'] as $css): ?>
<link rel="stylesheet" href="<?= base_url('assets/is/css/' . $css . '.css') ?>">
<?php endforeach ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.1.14/swiper-bundle.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/is-fill.css?v=11') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/mm.css?v=33') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/mm-mobile.css?v=12') ?>">
<?php if (mm_lang() === 'my'): ?><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Myanmar:wght@400;500;700&display=swap"><?php endif ?>
<link rel="stylesheet" href="<?= base_url('assets/css/mm-lang.css?v=2') ?>">
</head>
<body>
<?php
$cartCount = 0;
if ($user && $user['role'] === 'buyer') {
    $cartCount = (new \App\Models\CartModel())->where('user_id', $user['id'])->countAllResults();
}
$wishCount = count(mm_wish_ids());
$isBuyerU  = $user && $user['role'] === 'buyer';
$unread    = mm_unread_count();
$cats = (new \App\Models\CategoryModel())->where('is_active', 1)->orderBy('sort')->findAll();
$sf   = (string) service('request')->getGet('sf');
$cur  = uri_string();
?>
<!-- 모바일 헤더 (1024px 이하에서만 보임) -->
<div class="m-hd">
  <div class="m-hd-row">
    <a class="m-logo mm-logo" href="<?= site_url('/') ?>">XINDELELA <em>mall</em></a>
    <div class="m-hd-ic">
      <?php if ($isBuyerU): ?><a class="m-ic m-ic-bell" href="<?= site_url('notifications') ?>" aria-label="<?= t('알림') ?>"><?php if ($unread): ?><i><?= $unread ?></i><?php endif ?></a><?php endif ?>
      <a class="m-ic m-ic-wish" href="<?= site_url('wishlist') ?>" aria-label="<?= t('관심상품') ?>"><i data-wish-count<?= $wishCount ? '' : ' hidden' ?>><?= $wishCount ?></i></a>
      <a class="m-ic m-ic-cart" href="<?= site_url('cart') ?>" aria-label="<?= t('장바구니') ?>"><?php if ($cartCount): ?><i><?= $cartCount ?></i><?php endif ?></a>
      <button type="button" class="m-ic m-ic-menu" id="mMenuOpen" aria-label="<?= t('메뉴') ?>"></button>
    </div>
  </div>
  <form class="m-search" action="<?= site_url('products') ?>" method="get"><input type="search" name="q" value="<?= esc($q ?? '') ?>" placeholder="<?= t('상품명·브랜드 검색') ?>"><button type="submit" aria-label="<?= t('검색') ?>"></button></form>
  <nav class="m-cats">
    <a href="<?= site_url('products?sort=pop') ?>" class="<?= str_contains($cur, 'sort=pop') ? 'on' : '' ?>"><?= t('베스트') ?></a>
    <a href="<?= site_url('products?sort=new') ?>"><?= t('신상품') ?></a>
    <?php foreach ($cats as $c): ?><a href="<?= site_url('category/' . $c['id']) ?>" class="<?= $cur === 'category/' . $c['id'] ? 'on' : '' ?>"><?= esc(t($c['name'])) ?></a><?php endforeach ?>
  </nav>
  <div class="m-lang"><?= view('_lang_toggle') ?></div>
</div>
<div class="m-drawer" id="mDrawer" hidden>
  <div class="m-drawer-in">
    <div class="m-drawer-h"><b><?= t('메뉴') ?></b><button type="button" id="mMenuClose" aria-label="<?= t('닫기') ?>">✕</button></div>
    <div class="m-drawer-me">
      <?php if ($user && $user['role'] === 'buyer'): ?>
        <b><?= esc($user['company_name'] ?: $user['name']) ?></b><span class="st st-<?= esc($user['status']) ?>"><?= mm_verify_status_label($user['status']) ?></span>
        <p><?= t('포인트') ?> <b><?= number_format((int) $user['points_balance']) ?>P</b></p>
      <?php else: ?>
        <p><?= t('도매가는 비즈니스 인증 회원에게 열립니다.') ?></p>
        <div class="m-drawer-btns"><a class="is-main" href="<?= site_url('register') ?>"><?= t('회원가입') ?></a><a href="<?= site_url('login') ?>"><?= t('로그인') ?></a></div>
        <a class="m-drawer-bonus" href="<?= site_url('welcome') ?>"><?= t('처음 오셨나요? 가입 혜택 보기 ›') ?></a>
      <?php endif ?>
    </div>
    <p class="m-drawer-t"><?= t('카테고리') ?></p>
    <div class="m-drawer-grid"><?php foreach ($cats as $c): ?><a href="<?= site_url('category/' . $c['id']) ?>"><?= esc(t($c['name'])) ?></a><?php endforeach ?></div>
    <p class="m-drawer-t"><?= t('쇼핑') ?></p>
    <ul class="m-drawer-ul">
      <?php if ($isBuyerU): ?><li><a href="<?= site_url('notifications') ?>"><?= t('알림') ?><?= $unread ? ' <b class="m-drawer-n">' . $unread . '</b>' : '' ?></a></li><?php endif ?>
      <li><a href="<?= site_url('wishlist') ?>"><?= t('관심상품') ?></a></li>
      <li><a href="<?= site_url('mypage/orders') ?>"><?= t('주문 내역') ?></a></li>
      <li><a href="<?= site_url('mypage') ?>"><?= t('마이페이지') ?></a></li>
      <li><a href="<?= site_url('mypage/verify') ?>"><?= t('비즈니스 인증') ?></a></li>
      <li><a href="<?= site_url('notices') ?>"><?= t('공지사항') ?></a></li>
      <li><a href="<?= site_url('guide') ?>"><?= t('이용안내') ?></a></li>
      <?php if ($user): ?><li><a href="<?= site_url($user['role'] === 'admin' ? 'admin/logout' : 'logout') ?>"><?= t('로그아웃') ?></a></li><?php endif ?>
    </ul>
    <?php $csV0 = (string) mm_setting('cs_viber', ''); ?>
    <a class="m-drawer-cs" href="viber://chat?number=<?= rawurlencode(preg_replace('/[^0-9+]/', '', $csV0)) ?>"><?= t('고객센터 Viber') ?> <b><?= esc($csV0) ?></b></a>
  </div>
</div>
<header class="pc-hd relative w-full max-w-full bg-white z-[600]"><div class="pc:mx-auto pc:min-h-[208px] pc:min-w-large">
  <!-- 탑바 (검정 40px) -->
  <div class="flex h-[40px] w-full justify-between bg-black mo:hidden pc:block"><div class="relative flex items-center pc:gap-[32px] m-auto flex h-[40px] w-large justify-between gap-[24px]">
    <ul class="header-top-menu">
      <li class="menu-item active"><a target="_self" class="menu-item-link" href="<?= site_url('/') ?>"><?= t('XINDELELA 미얀마 도매몰') ?></a></li>
      <li class="menu-item"><a class="menu-item-link" href="<?= site_url('guide') ?>"><?= t('이용안내') ?></a></li>
      <li class="menu-item"><a class="menu-item-link" href="<?= site_url('notices') ?>"><?= t('공지사항') ?></a></li>
    </ul>
    <ul class="account-menu flex items-center gap-[16px] text-[12px]">
      <?php if ($user && $user['role'] === 'buyer'): ?>
        <li><a class="acount-menu-link" href="<?= site_url('mypage') ?>"><b class="text-white"><?= esc($user['company_name'] ?: $user['name']) ?></b> <?= t('님 ·') ?> <?= mm_verify_status_label($user['status']) ?></a></li>
        <li><a class="acount-menu-link" href="<?= site_url('mypage/points') ?>"><?= t('포인트') ?> <b class="text-white"><?= number_format((int) $user['points_balance']) ?></b>P</a></li>
        <li><a class="acount-menu-link" href="<?= site_url('logout') ?>"><?= t('로그아웃') ?></a></li>
      <?php elseif ($user && $user['role'] === 'admin'): ?>
        <li><a class="acount-menu-link" href="<?= site_url('admin') ?>"><b class="text-white"><?= t('관리자') ?></b></a></li>
        <li><a class="acount-menu-link" href="<?= site_url('admin/logout') ?>"><?= t('로그아웃') ?></a></li>
      <?php else: ?>
        <?php if (($signupBonus = (int) mm_setting('points_signup_bonus', 0)) > 0): ?><li><a class="acount-menu-link font-bold" style="color:#ff8a3d" href="<?= site_url('welcome') ?>"><?= t('가입하면') ?> <?= number_format($signupBonus) ?>P</a></li><?php endif ?>
        <li><a class="acount-menu-link" href="<?= site_url('register') ?>"><?= t('회원가입') ?></a></li>
        <li><a class="acount-menu-link" href="<?= site_url('login') ?>"><?= t('로그인') ?></a></li>
      <?php endif ?>
      <li><a class="acount-menu-link" href="<?= site_url('notices') ?>"><?= t('고객센터') ?></a></li>
    </ul>
  </div></div>

  <!-- 로고·검색·유틸 -->
  <div class="w-full mo:hidden pc:block"><div class="header-wraper w-full pc:mx-auto pc:w-large">
    <div class="relative flex items-center pc:gap-[32px] pc:h-[52px] pc:mt-[28px] pc:mb-[18px]">
      <h1 class="shrink-0"><a href="<?= site_url('/') ?>" class="flex items-center gap-[10px]">
        <span class="flex flex-col"><span class="mm-logo mm-logo-hd">XINDELELA <em>mall</em></span><span class="mt-[3px] text-[12px] text-gray3"><?= t('미얀마 K-브랜드 도매') ?></span></span>
      </a></h1>
      <form class="search-area rounded-[30px] bg-gray9 pc:w-[384px] pc:min-h-12 pc:py-1 pc:pl-4 pc:h-auto pc:z-10" action="<?= site_url('products') ?>" method="get">
        <div class="search-bar relative flex h-full w-full min-w-0 items-center">
          <select name="sf" class="bg-inherit text-body3 text-gray3 pr-[6px] border-r border-gray6 mr-[6px] outline-none"><option value="name"><?= t('상품명') ?></option><option value="brand" <?= $sf === 'brand' ? 'selected' : '' ?>><?= t('브랜드') ?></option></select>
          <input placeholder="<?= t('한국 정품 K-브랜드를 도매가로 ✨') ?>" class="search-inp min-w-0 flex-1 bg-inherit py-[10px] pl-[10px] pr-[40px] placeholder:text-body3 placeholder:text-gray5 pc:h-full" type="text" name="q" value="<?= esc($q ?? '') ?>">
          <div class="absolute right-[12px] top-1/2 flex -translate-y-1/2 items-center gap-[8px]"><button type="submit" class="btn-search shrink-0">Search</button></div>
        </div>
      </form>
      <div class="util-menu flex mo:gap-[23px] pc:gap-[30px] ml-auto">
        <div class="relative group"><a class="car mo:hidden" href="<?= site_url('mypage/orders') ?>" title="<?= t('주문내역') ?>"><span class="blind"><?= t('주문/배송 페이지로 이동') ?></span></a><div class="absolute left-1/2 -translate-x-1/2 top-9 hidden group-hover:flex items-center justify-center whitespace-nowrap rounded-[6px] bg-gray-700 px-[10px] py-[2px] text-[12px] text-white"><?= t('주문내역') ?></div></div>
        <div class="group relative inline-block mo:hidden"><a class="mypage mo:hidden" href="<?= site_url('mypage') ?>" title="<?= t('마이페이지') ?>"><span class="blind"><?= t('마이페이지로 이동') ?></span></a><div class="absolute left-1/2 -translate-x-1/2 top-9 hidden group-hover:flex items-center justify-center whitespace-nowrap rounded-[6px] bg-gray-700 px-[10px] py-[2px] text-[12px] text-white"><?= t('마이페이지') ?></div></div>
        <?php if ($isBuyerU): ?><div class="relative group"><a class="u-bell relative block size-[32px]" href="<?= site_url('notifications') ?>" title="<?= t('알림') ?>"><span class="blind"><?= t('알림') ?></span><?php if ($unread): ?><span class="absolute -right-[6px] -top-[6px] flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-primary px-[4px] text-[11px] font-bold text-white"><?= $unread ?></span><?php endif ?></a><div class="absolute left-1/2 -translate-x-1/2 top-9 hidden group-hover:flex items-center justify-center whitespace-nowrap rounded-[6px] bg-gray-700 px-[10px] py-[2px] text-[12px] text-white"><?= t('알림') ?></div></div><?php endif ?>
        <div class="relative group"><a class="u-wish relative block size-[32px]" href="<?= site_url('wishlist') ?>" title="<?= t('관심상품') ?>"><span class="blind"><?= t('관심상품') ?></span><span data-wish-count class="absolute -right-[6px] -top-[6px] flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-primary px-[4px] text-[11px] font-bold text-white"<?= $wishCount ? '' : ' hidden' ?>><?= $wishCount ?></span></a><div class="absolute left-1/2 -translate-x-1/2 top-9 hidden group-hover:flex items-center justify-center whitespace-nowrap rounded-[6px] bg-gray-700 px-[10px] py-[2px] text-[12px] text-white"><?= t('관심상품') ?></div></div>
        <div class="relative group"><a class="cart" href="<?= site_url('cart') ?>" title="<?= t('장바구니') ?>"><span class="blind"><?= t('장바구니') ?></span><?php if ($cartCount): ?><span class="absolute -right-[6px] -top-[6px] flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-primary px-[4px] text-[11px] font-bold text-white"><?= $cartCount ?></span><?php endif ?></a><div class="absolute left-1/2 -translate-x-1/2 top-9 hidden group-hover:flex items-center justify-center whitespace-nowrap rounded-[6px] bg-gray-700 px-[10px] py-[2px] text-[12px] text-white"><?= t('장바구니') ?></div></div>
      </div>
    </div>

    <!-- GNB -->
    <div class="gnb-wrap left-0 m-auto w-full bg-white"><div class="w-[1280px] m-auto"><div class="flex items-center pc:gap-[32px] h-[55px]">
      <div class="relative group/cat flex h-[55px] items-center">
        <button type="button" class="btn-category flex items-center justify-center" aria-label="<?= t('전체 카테고리') ?>"><span class="blind"><?= t('전체 카테고리') ?></span></button>
        <div class="absolute left-0 top-[52px] z-[700] hidden w-[240px] overflow-hidden rounded-[12px] border border-gray6 bg-white py-[8px] shadow-[0_8px_24px_rgba(0,0,0,.08)] group-hover/cat:block">
          <?php foreach ($cats as $c): ?><a class="block px-[20px] py-[10px] text-[15px] font-medium hover:bg-[#fff3eb] hover:text-primary" href="<?= site_url('category/' . $c['id']) ?>"><?= esc(t($c['name'])) ?></a><?php endforeach ?>
        </div>
      </div>
      <div class="gnb flex w-full items-center justify-between">
        <div class="flex items-center gap-[32px]">
          <a class="link-item pc:inline-flex pc:items-center text-[18px] font-medium <?= str_contains($cur, 'sort=pop') ? 'text-primary' : '' ?>" href="<?= site_url('products?sort=pop') ?>"><span class="relative whitespace-nowrap"><?= t('베스트') ?></span></a>
          <a class="link-item pc:inline-flex pc:items-center text-[18px] font-medium" href="<?= site_url('products?sort=new') ?>"><span class="relative whitespace-nowrap"><?= t('신상품') ?><span class="absolute -right-[12px] -top-[6px] flex size-[14px] items-center justify-center rounded-full bg-primary text-[9px] font-bold text-white">N</span></span></a>
          <?php foreach (array_slice($cats, 0, 4) as $c): ?><a class="link-item pc:inline-flex pc:items-center text-[18px] font-medium <?= $cur === 'category/' . $c['id'] ? 'text-primary' : '' ?>" href="<?= site_url('category/' . $c['id']) ?>"><span class="relative whitespace-nowrap"><?= esc(t($c['name'])) ?></span></a><?php endforeach ?>
        </div>
        <div class="flex items-center gap-[32px]">
          <?= view('_lang_toggle') ?>
          <a class="link-item text-[18px] font-medium" href="<?= site_url('notices') ?>"><?= t('공지사항') ?></a>
          <a class="link-item text-[18px] font-medium" href="<?= site_url('guide') ?>"><?= t('이용안내') ?></a>
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

<footer class="site-ft w-full border-t border-solid border-gray6"><div class="m-auto mo:w-full pc:w-large"><div class="footer-conts-wrap flex justify-between pt-[50px] pc:pb-[40px]">
  <div class="footer-conts-item">
    <a href="<?= site_url('/') ?>" class="mm-logo mm-logo-ft inline-block mb-[28px]">XINDELELA <em>mall</em></a>
    <ul class="footer-item-wrap type1 text-body3 text-gray3">
      <li class="footer-item mb-[20px]"><a href="<?= site_url('guide') ?>"><?= t('이용안내') ?></a></li>
      <li class="footer-item mb-[20px]"><a href="<?= site_url('notices') ?>"><?= t('공지사항') ?></a></li>
      <li class="footer-item mb-[20px] font-bold text-black"><a href="<?= site_url('welcome') ?>"><?= t('회원가입 안내 · 가입 혜택') ?></a></li>
      <li class="footer-item mb-[20px]"><a href="<?= site_url('mypage/inquiries') ?>"><?= t('고객센터') ?></a></li>
    </ul>
    <ul class="footer-item-wrap type2 mb-0 text-body3 text-gray3">
      <li class="footer-item company font-bold">XINDELELA Corporation</li>
      <li class="footer-item"><?= t('미얀마 K-브랜드 B2B 도매 플랫폼') ?></li>
      <li class="footer-item">mm.xindelela.com</li>
    </ul>
    <ul class="footer-item-wrap type2 mt-[4px] text-body3 text-gray3">
      <li class="footer-item"><?= t('이메일:') ?> <?= esc(mm_setting('cs_email', '')) ?></li>
      <li class="footer-item">Viber: <?= esc(mm_setting('cs_viber', '')) ?></li>
    </ul>
    <p class="ft-note text-label1 font-regular text-[#999] mt-[24px] pc:w-[848px]"><?= t('본 사이트는 비즈니스 인증을 받은 회원에게만 도매가를 공개하는 회원제 도매몰입니다. 결제는 사이트 밖에서 KBZPay 선불로 진행되며, 입금이 확인되면 바로 출고합니다.') ?></p>
  </div>
  <div class="footer-conts-item flex flex-col">
    <p class="text-gray3"><?= t('고객센터 (Viber)') ?></p>
    <?php $csV = (string) mm_setting('cs_viber', ''); $csN = preg_replace('/[^0-9+]/', '', $csV); ?>
    <p class="text-[30px] font-bold leading-[1.2] mt-[4px] mb-[10px]"><a href="viber://chat?number=<?= rawurlencode($csN) ?>" title="<?= t('Viber로 대화하기') ?>"><?= esc($csV) ?></a></p>
    <p class="mt-[4px] text-body3 font-regular"><?= t('평일 09:00 ~ 18:00') ?></p>
    <p class="mt-[4px] text-body3 text-gray3"><?= t('주말·공휴일 휴무') ?></p>
    <a href="<?= site_url('mypage/inquiries') ?>" class="mt-[16px] inline-flex w-fit items-center rounded-[99px] border border-primary px-[16px] py-[8px] text-body3 font-bold text-primary"><?= t('1:1 문의하기') ?></a>
  </div>
</div></div></footer>
<nav class="m-tab">
  <a href="<?= site_url('/') ?>" class="t-home <?= $cur === '' ? 'on' : '' ?>"><i></i><?= t('홈') ?></a>
  <button type="button" class="t-cat" data-drawer><i></i><?= t('카테고리') ?></button>
  <a href="<?= site_url('cart') ?>" class="t-cart <?= $cur === 'cart' ? 'on' : '' ?>"><i><?php if ($cartCount): ?><em><?= $cartCount ?></em><?php endif ?></i><?= t('장바구니') ?></a>
  <a href="<?= site_url('mypage/orders') ?>" class="t-order <?= str_starts_with($cur, 'mypage/orders') ? 'on' : '' ?>"><i></i><?= t('주문내역') ?></a>
  <a href="<?= site_url('mypage') ?>" class="t-my <?= $cur === 'mypage' ? 'on' : '' ?>"><i></i><?= t('마이') ?></a>
</nav>
<script>
(function () {
  var d = document.getElementById('mDrawer');
  function open() { d.hidden = false; document.documentElement.classList.add('m-lock'); }
  function close() { d.hidden = true; document.documentElement.classList.remove('m-lock'); }
  document.getElementById('mMenuOpen').onclick = open;
  document.getElementById('mMenuClose').onclick = close;
  document.querySelectorAll('[data-drawer]').forEach(function (b) { b.onclick = open; });
  d.addEventListener('click', function (e) { if (e.target === d) close(); });
})();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.1.14/swiper-bundle.min.js"></script>
<script src="<?= base_url('assets/js/mm.js?v=13') ?>"></script>
</body>
</html>
