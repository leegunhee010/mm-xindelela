<?php

namespace App\Controllers\Admin;

use App\Libraries\GrowthStats;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;

class Dashboard extends Base
{
    public function index(): string
    {
        $um = new UserModel();
        $om = new OrderModel();
        $today = date('Y-m-d');
        return $this->page('admin/dashboard', [
            'title' => '대시보드',
            'kpi'   => [
                '인증 검수 중'  => $um->where('role', 'buyer')->where('status', 'pending')->countAllResults(),
                '인증 완료 회원'       => $um->where('role', 'buyer')->where('status', 'approved')->countAllResults(),
                '입금 대기' => $om->where('status', 'requested')->countAllResults(),
                '입금 확인 필요'    => $om->where('status', 'proof')->countAllResults(),
                '판매중 상품'       => (new ProductModel())->where('status', 'active')->countAllResults(),
                '오늘 가입'         => $um->where('role', 'buyer')->like('created_at', $today, 'after')->countAllResults(),
            ],
            'growth'        => GrowthStats::build((string) $this->request->getGet('range')),
            'pendingBuyers' => $um->where('role', 'buyer')->where('status', 'pending')->orderBy('id', 'ASC')->findAll(8),
            'recentOrders'  => $om->orderBy('id', 'DESC')->findAll(8),
            'buyersMap'     => $this->buyersMap($om->orderBy('id', 'DESC')->findAll(8)),
        ]);
    }

    private function buyersMap(array $orders): array
    {
        $ids = array_values(array_unique(array_column($orders, 'user_id')));
        $m   = [];
        if ($ids) {
            foreach ((new UserModel())->whereIn('id', $ids)->findAll() as $u) {
                $m[$u['id']] = $u;
            }
        }
        return $m;
    }
}
