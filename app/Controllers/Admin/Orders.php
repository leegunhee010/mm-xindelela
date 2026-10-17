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
        // 할 일 대시보드
        $today   = date('Y-m-d');
        $stale   = date('Y-m-d H:i:s', strtotime('-3 days'));
        $paidSt  = \App\Libraries\GrowthStats::PAID;
        $queue   = (new OrderModel())->where('status', 'proof')->orderBy('proof_at', 'ASC')->findAll(6);
        foreach ($queue as &$qq) {
            $qq['buyer'] = $um->find($qq['user_id']);
        }
        $etaOver = 0;
        foreach ((new OrderModel())->select('ship_info')->where('status', 'shipped')->findAll() as $x) {
            $e = mm_ship_info($x)['eta'];
            if ($e && $e < $today) {
                $etaOver++;
            }
        }
        $sum = fn ($from) => (int) ((new OrderModel())->selectSum('total')->whereIn('status', $paidSt)->where('paid_at >=', $from)->first()['total'] ?? 0);
        $dash = [
            'proof'      => $counts['proof'],
            'proofOld'   => (new OrderModel())->where('status', 'proof')->where('proof_at <', date('Y-m-d H:i:s', strtotime('-1 day')))->countAllResults(),
            'toShip'     => $counts['paid'] + $counts['preparing'],
            'requested'  => $counts['requested'],
            'reqStale'   => (new OrderModel())->where('status', 'requested')->where('created_at <', $stale)->countAllResults(),
            'shipped'    => $counts['shipped'],
            'etaOver'    => $etaOver,
            'todayN'     => (new OrderModel())->where('created_at >=', $today . ' 00:00:00')->where('status !=', 'cancelled')->countAllResults(),
            'todayPaid'  => $sum($today . ' 00:00:00'),
            'monthPaid'  => $sum(date('Y-m-01 00:00:00')),
        ];
        return $this->page('admin/orders', ['title' => '주문 관리', 'rows' => $rows, 'pager' => $om->pager, 'st' => $st, 'q' => $q, 'counts' => $counts, 'dash' => $dash, 'queue' => $queue, 'stale' => $stale]);
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
        if (in_array($st, ['paid', 'preparing', 'shipped', 'delivered'], true) && ! $o['paid_at']) {
            $data['paid_at'] = $now;
        }
        if ($st === 'shipped') {
            $data['shipped_at'] = $o['shipped_at'] ?: $now;
            if ($this->request->getPost('ship_method') !== null) {
                $data = array_merge($data, $this->shipData());
            }
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
        if (in_array($st, ['paid', 'preparing', 'shipped', 'delivered'], true) && ! $o['reward_granted']) {
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

    /** 배송 중·완료 주문의 출고 정보만 고치기 */
    public function ship(int $id)
    {
        $om = new OrderModel();
        if (! $om->find($id)) {
            return redirect()->back();
        }
        $om->update($id, $this->shipData());
        return redirect()->back()->with('success', '배송 정보를 저장했습니다.');
    }

    private function shipData(): array
    {
        $g = fn ($k) => trim((string) $this->request->getPost($k));
        $i = [
            'method'       => array_key_exists($g('ship_method'), mm_ship_methods()) ? $g('ship_method') : '',
            'carrier'      => mb_substr($g('ship_carrier'), 0, 100),
            'tracking_no'  => mb_substr($g('ship_tracking_no'), 0, 100),
            'tracking_url' => preg_match('#^https?://#i', $g('ship_tracking_url')) ? mb_substr($g('ship_tracking_url'), 0, 300) : '',
            'sent_on'      => preg_match('/^\d{4}-\d{2}-\d{2}$/', $g('ship_sent_on')) ? $g('ship_sent_on') : '',
            'eta'          => preg_match('/^\d{4}-\d{2}-\d{2}$/', $g('ship_eta')) ? $g('ship_eta') : '',
            'note'         => mb_substr($g('ship_note'), 0, 500),
        ];
        $sum = array_filter([mm_ship_methods()[$i['method']] ?? '', $i['carrier'], $i['tracking_no'] ? '송장 ' . $i['tracking_no'] : '', $i['eta'] ? '도착 예정 ' . $i['eta'] : '']);
        return ['ship_info' => json_encode($i, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'tracking' => implode(' · ', $sum)];
    }
}
