<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 *
 * mm.xindelela.com 라우트
 *   /                공개(상품은 열람, 도매가·주문은 승인 구매자만)
 *   /mypage/*        구매자
 *   /admin/*         관리자
 */

// ─── 공개 ───
$routes->get('/', 'Home::index');
$routes->get('products', 'Products::index');
$routes->get('products/(:num)', 'Products::show/$1');
$routes->get('category/(:num)', 'Products::category/$1');
$routes->get('suppliers/(:num)', 'Products::supplier/$1');
$routes->get('notices', 'Notices::index');
$routes->get('notices/(:num)', 'Notices::show/$1');
$routes->get('guide', 'Home::guide');
$routes->get('deploy-check', 'DeployCheck::index');

// ─── 인증(구매자) ───
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');
$routes->get('logout', 'Auth::logout');

// ─── 구매자 ───
$routes->group('', ['filter' => 'buyerauth'], static function ($routes) {
    $routes->get('cart', 'Cart::index');
    $routes->post('cart/add', 'Cart::add');
    $routes->post('cart/update', 'Cart::update');
    $routes->post('cart/remove', 'Cart::remove');
    $routes->get('checkout', 'Checkout::index');
    $routes->post('checkout', 'Checkout::submit');
    $routes->get('checkout/done/(:num)', 'Checkout::done/$1');
    $routes->post('inquiry', 'MyPage::inquiry');

    $routes->get('mypage', 'MyPage::index');
    $routes->get('mypage/orders', 'MyPage::orders');
    $routes->get('mypage/orders/(:num)', 'MyPage::order/$1');
    $routes->post('mypage/orders/(:num)/proof', 'MyPage::proof/$1');
    $routes->post('mypage/orders/(:num)/cancel', 'MyPage::cancel/$1');
    $routes->post('mypage/orders/(:num)/address', 'MyPage::address/$1');
    $routes->get('mypage/points', 'MyPage::points');
    $routes->get('mypage/referral', 'MyPage::referral');
    $routes->get('mypage/profile', 'MyPage::profile');
    $routes->post('mypage/profile', 'MyPage::updateProfile');
    $routes->post('mypage/password', 'MyPage::updatePassword');
    $routes->get('mypage/verify', 'MyPage::verify');
    $routes->post('mypage/verify', 'MyPage::resubmit');
    $routes->get('mypage/inquiries', 'MyPage::inquiries');
});

// 공급사 로그인·센터 없음(10/17) — 공급사·상품은 관리자가 직접 관리. 옛 주소는 홈으로.
$routes->addRedirect('supplier', '/');
$routes->addRedirect('supplier/(:any)', '/');

// ─── 관리자 ───
$routes->get('admin/login', 'Admin\Auth::login');
$routes->post('admin/login', 'Admin\Auth::attemptLogin');
$routes->get('admin/logout', 'Admin\Auth::logout');
$routes->group('admin', ['filter' => ['adminauth', 'adminlog'], 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('buyers', 'Buyers::index');
    $routes->get('buyers/(:num)', 'Buyers::show/$1');
    $routes->post('buyers/(:num)/approve', 'Buyers::approve/$1');
    $routes->post('buyers/(:num)/reject', 'Buyers::reject/$1');
    $routes->post('buyers/(:num)/status', 'Buyers::status/$1');
    $routes->post('buyers/(:num)/note', 'Buyers::note/$1');
    $routes->post('buyers/(:num)/points', 'Buyers::points/$1');
    $routes->get('suppliers', 'Suppliers::index');
    $routes->get('suppliers/(:num)', 'Suppliers::show/$1');
    $routes->post('suppliers/(:num)/status', 'Suppliers::status/$1');
    $routes->post('suppliers/(:num)/save', 'Suppliers::save/$1');
    $routes->get('suppliers/new', 'Suppliers::form');
    $routes->get('suppliers/(:num)/edit', 'Suppliers::form/$1');
    $routes->post('suppliers/store', 'Suppliers::save');
    $routes->get('products', 'Products::index');
    $routes->get('products/new', 'Products::form');
    $routes->get('products/(:num)/edit', 'Products::form/$1');
    $routes->post('products/save', 'Products::save');
    $routes->post('products/(:num)/delete', 'Products::delete/$1');
    $routes->get('products/(:num)', 'Products::show/$1');
    $routes->post('products/(:num)/status', 'Products::status/$1');
    $routes->post('products/(:num)/best', 'Products::best/$1');
    $routes->get('categories', 'Categories::index');
    $routes->post('categories/save', 'Categories::save');
    $routes->post('categories/(:num)/delete', 'Categories::delete/$1');
    $routes->get('orders', 'Orders::index');
    $routes->get('orders/(:num)', 'Orders::show/$1');
    $routes->post('orders/(:num)/status', 'Orders::status/$1');
    $routes->post('orders/(:num)/memo', 'Orders::memo/$1');
    $routes->post('orders/(:num)/ship', 'Orders::ship/$1');
    $routes->get('points', 'Points::index');
    $routes->get('referrals', 'Points::referrals');
    $routes->get('inquiries', 'Inquiries::index');
    $routes->post('inquiries/(:num)/answer', 'Inquiries::answer/$1');
    $routes->get('notices', 'Notices::index');
    $routes->get('notices/new', 'Notices::form');
    $routes->get('notices/(:num)/edit', 'Notices::form/$1');
    $routes->post('notices/save', 'Notices::save');
    $routes->post('notices/(:num)/delete', 'Notices::delete/$1');
    $routes->get('banners', 'Banners::index');
    $routes->post('banners/save', 'Banners::save');
    $routes->post('banners/(:num)/delete', 'Banners::delete/$1');
    $routes->get('staff', 'Staff::index');
    $routes->post('staff/save', 'Staff::save');
    $routes->get('logs', 'Logs::index');
    $routes->get('seo', 'Seo::index');
    $routes->post('seo', 'Seo::save');
    $routes->get('settings', 'Settings::index');
    $routes->post('settings', 'Settings::save');
});
