<?php

namespace App\Controllers\Admin;

use App\Models\ProductModel;
use App\Models\UserModel;
use App\Models\WishModel;

/** 관심상품 현황: 상품별로 몇 명이 담았는지(수요 참고), 누가 담았는지. 비회원 쿠키 관심상품은 집계되지 않는다. */
class Wishes extends Base
{
    public function index(): string
    {
        $rows = (new WishModel())->select('product_id, COUNT(*) AS n, MAX(created_at) AS last')->groupBy('product_id')->orderBy('n', 'DESC')->orderBy('last', 'DESC')->findAll(200);
        $pm   = new ProductModel();
        foreach ($rows as &$r) {
            $r['p'] = $pm->find($r['product_id']);
        }
        unset($r);
        $rows = array_values(array_filter($rows, static fn ($r) => $r['p'] !== null));
        $wm   = new WishModel();
        return $this->page('admin/wishes', [
            'title' => '관심상품 현황',
            'rows'  => $rows,
            'kpi'   => [
                'members'  => (int) ($wm->select('COUNT(DISTINCT user_id) AS c')->first()['c'] ?? 0),
                'total'    => (new WishModel())->countAllResults(),
                'week'     => (new WishModel())->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))->countAllResults(),
                'products' => count($rows),
            ],
        ]);
    }

    public function show(int $productId): string
    {
        $p = (new ProductModel())->find($productId);
        if (! $p) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $rows = (new WishModel())->where('product_id', $productId)->orderBy('created_at', 'DESC')->findAll();
        $um   = new UserModel();
        foreach ($rows as &$r) {
            $r['user'] = $um->find($r['user_id']);
        }
        unset($r);
        return $this->page('admin/wish', ['title' => '관심상품 · 담은 회원', 'p' => $p, 'rows' => $rows]);
    }
}
