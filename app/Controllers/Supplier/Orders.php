<?php

namespace App\Controllers\Supplier;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\UserModel;

/** 공급사는 입금 확인된(paid 이후) 주문 중 자기 상품 라인만 본다. */
class Orders extends Base
{
    private const VISIBLE = ['paid', 'preparing', 'shipped', 'delivered'];

    public function index(): string
    {
        $s   = $this->sup();
        $oim = new OrderItemModel();
        $om  = new OrderModel();
        $ids = array_column($oim->select('order_id')->where('supplier_id', $s['id'])->groupBy('order_id')->findAll(), 'order_id');
        $st  = (string) $this->request->getGet('status');
        $orders = [];
        if ($ids) {
            $b = $om->whereIn('id', $ids)->whereIn('status', self::VISIBLE);
            if ($st !== '') {
                $b->where('status', $st);
            }
            $orders = $b->orderBy('id', 'DESC')->paginate(30, 'p');
            foreach ($orders as &$o) {
                $o['items'] = $oim->where('order_id', $o['id'])->where('supplier_id', $s['id'])->findAll();
            }
        }
        return $this->page('supplier/orders', ['title' => '주문 관리', 'orders' => $orders, 'pager' => $om->pager, 'st' => $st]);
    }

    public function show(int $id): string
    {
        $s   = $this->sup();
        $oim = new OrderItemModel();
        $items = $oim->where('order_id', $id)->where('supplier_id', $s['id'])->findAll();
        $o   = (new OrderModel())->find($id);
        if (! $items || ! $o || ! in_array($o['status'], self::VISIBLE, true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $this->page('supplier/order', ['title' => '주문 상세', 'o' => $o, 'items' => $items, 'buyer' => (new UserModel())->find($o['user_id'])]);
    }

    /** 내 라인 출고 처리. 전 라인 출고되면 주문 상태 shipped. */
    public function ship(int $id)
    {
        $s   = $this->sup();
        $oim = new OrderItemModel();
        $om  = new OrderModel();
        $oim->where('order_id', $id)->where('supplier_id', $s['id'])->set('item_status', 'shipped')->update();
        $left = $oim->where('order_id', $id)->where('item_status !=', 'shipped')->countAllResults();
        $o    = $om->find($id);
        if ($o && $left === 0 && in_array($o['status'], ['paid', 'preparing'], true)) {
            $om->update($id, ['status' => 'shipped', 'shipped_at' => date('Y-m-d H:i:s'), 'tracking' => trim((string) $this->request->getPost('tracking')) ?: $o['tracking']]);
        } elseif ($o && $o['status'] === 'paid') {
            $om->update($id, ['status' => 'preparing']);
        }
        return redirect()->back()->with('success', '출고 처리했습니다.');
    }
}
