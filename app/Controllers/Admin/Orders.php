<?php

namespace App\Controllers\Admin;

use App\Libraries\PointService;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\SupplierModel;
use App\Models\UserModel;

class Orders extends Base
{
    public function index(): string
    {
        $om = new OrderModel();
        $st = (string) ($this->request->getGet('status') ?? '');
        $q  = trim((string) $this->request->getGet('q'));
        $b  = $om;
        if ($st !== '') {
            $b = $b->where('status', $st);
        }
        if ($q !== '') {
            $b = $b->like('order_no', $q);
        }
        $rows = $b->orderBy('id', 'DESC')->paginate(30, 'p');
        $um   = new UserModel();
        $oim  = new OrderItemModel();
        foreach ($rows as &$r) {
            $r['buyer'] = $um->find($r['user_id']);
            $r['icnt']  = $oim->where('order_id', $r['id'])->countAllResults();
        }
        $counts = [];
        foreach (['requested', 'proof', 'paid', 'preparing', 'shipped', 'delivered', 'cancelled'] as $s) {
            $counts[$s] = $om->where('status', $s)->countAllResults();
        }
        return $this->page('admin/orders', ['title' => '주문 관리', 'rows' => $rows, 'pager' => $om->pager, 'st' => $st, 'q' => $q, 'counts' => $counts]);
    }

    public function show(int $id): string
    {
        $o = (new OrderModel())->find($id);
        if (! $o) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $items = (new OrderItemModel())->where('order_id', $id)->findAll();
        $sm    = new SupplierModel();
        foreach ($items as &$it) {
            $it['supplier'] = $sm->find($it['supplier_id']);
        }
        return $this->page('admin/order', ['title' => '주문 상세', 'o' => $o, 'items' => $items, 'buyer' => (new UserModel())->find($o['user_id'])]);
    }

    public function status(int $id)
    {
        $om = new OrderModel();
        $o  = $om->find($id);
        $st = (string) $this->request->getPost('status');
        if (! $o || ! in_array($st, ['requested', 'proof', 'paid', 'preparing', 'shipped', 'delivered', 'cancelled'], true)) {
            return redirect()->back();
        }
        $data = ['status' => $st];
        $now  = date('Y-m-d H:i:s');
        if ($st === 'paid' && ! $o['paid_at']) {
            $data['paid_at'] = $now;
        }
        if ($st === 'shipped') {
            $data['shipped_at'] = $now;
            $data['tracking']   = trim((string) $this->request->getPost('tracking')) ?: $o['tracking'];
            (new OrderItemModel())->where('order_id', $id)->set('item_status', 'shipped')->update();
        }
        if ($st === 'delivered') {
            $data['delivered_at'] = $now;
        }
        if ($st === 'cancelled') {
            $data['cancelled_at'] = $now;
        }
        $om->update($id, $data);
        $o2 = $om->find($id);
        // 입금 확인 → 포인트(추천 첫주문·적립) + 상품 주문수
        if ($st === 'paid' && ! $o['reward_granted']) {
            (new PointService())->onOrderPaid($o2);
            $om->update($id, ['reward_granted' => 1]);
            $pm = new ProductModel();
            foreach ((new OrderItemModel())->where('order_id', $id)->findAll() as $it) {
                $pm->where('id', $it['product_id'])->set('order_count', 'order_count + 1', false)->update();
            }
        }
        if ($st === 'cancelled' && $o['status'] !== 'cancelled') {
            (new PointService())->onOrderCancelled($o2);
        }
        return redirect()->back()->with('success', '주문 상태: ' . mm_order_status_label($st));
    }

    public function memo(int $id)
    {
        (new OrderModel())->update($id, ['admin_memo' => trim((string) $this->request->getPost('admin_memo'))]);
        return redirect()->back()->with('success', '메모를 저장했습니다.');
    }
}
