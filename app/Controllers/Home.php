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
            'banners'    => (new BannerModel())->where('is_active', 1)->orderBy('sort')->findAll(),
            'categories' => (new CategoryModel())->where('is_active', 1)->orderBy('sort')->findAll(),
            'newest'     => $pm->where('status', 'active')->orderBy('id', 'DESC')->findAll(10),
            'best'       => $pm->where('status', 'active')->where('is_best', 1)->orderBy('order_count', 'DESC')->orderBy('views', 'DESC')->findAll(5),
            'notices'    => (new NoticeModel())->orderBy('is_pinned', 'DESC')->orderBy('id', 'DESC')->findAll(5),
        ]);
    }

    public function guide(): string
    {
        return $this->render('guide', ['title' => '이용 안내']);
    }
}
