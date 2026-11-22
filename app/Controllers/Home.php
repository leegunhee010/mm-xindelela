<?php

namespace App\Controllers;

use App\Models\BannerModel;
use App\Models\CategoryModel;
use App\Models\NoticeModel;
use App\Models\ProductModel;

class Home extends BaseController
{
    public function index(): string
    {
        $pm = new ProductModel();
        return $this->render('home', [
            'fullWidth'  => true,
            'banners'    => (new BannerModel())->where('is_active', 1)->orderBy('sort')->findAll(),
            'categories' => (new CategoryModel())->where('is_active', 1)->orderBy('sort')->findAll(),
            // 신상품 카테고리 칩은 화면 안에서 걸러 보여 주므로 넉넉히 가져온다(카테고리별 최대 10개 표시)
            'newest'     => $pm->where('status', 'active')->orderBy('id', 'DESC')->findAll(40),
            'best'       => $pm->where('status', 'active')->where('is_best', 1)->orderBy('order_count', 'DESC')->orderBy('views', 'DESC')->findAll(5),
            'notices'    => (new NoticeModel())->orderBy('is_pinned', 'DESC')->orderBy('id', 'DESC')->findAll(5),
        ]);
    }

    public function guide(): string
    {
        return $this->render('guide', ['title' => '이용 안내']);
    }

    /** 가입 랜딩. ?ref=추천코드 는 가입 버튼까지 넘긴다. */
    public function welcome(): string
    {
        $pm  = new ProductModel();
        $ref = preg_replace('/[^A-Za-z0-9]/', '', (string) $this->request->getGet('ref'));
        $off = (new ProductModel())->select('MIN(1 - price * 1.0 / retail_price) AS lo, MAX(1 - price * 1.0 / retail_price) AS hi')
            ->where('status', 'active')->where('retail_price >', 0)->first();
        return $this->render('welcome', [
            'title'      => '회원가입 안내',
            'fullWidth'  => true,
            'ref'        => $ref,
            'joinUrl'    => site_url('register' . ($ref !== '' ? '?ref=' . $ref : '')),
            'bonus'      => (int) mm_setting('points_signup_bonus', 0),
            'offLo'      => (int) round(((float) ($off['lo'] ?? 0)) * 100),
            'offHi'      => (int) round(((float) ($off['hi'] ?? 0)) * 100),
            'categories' => (new CategoryModel())->where('is_active', 1)->orderBy('sort')->findAll(),
            'products'   => $pm->where('status', 'active')->orderBy('is_best', 'DESC')->orderBy('order_count', 'DESC')->orderBy('id', 'DESC')->findAll(12),
        ]);
    }

    /** 상태 확인(키 값은 노출하지 않음) */
    public function health()
    {
        return $this->response->setJSON(['ok' => true, 'maps' => mm_maps_key() !== '' ? 'on' : 'off']);
    }
}
