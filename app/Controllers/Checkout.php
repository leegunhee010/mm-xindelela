<?php

namespace App\Controllers;

use App\Libraries\PointService;
use App\Models\CartModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;

/**
 * 주문 "신청". 사이트 결제 없음.
 * 신청(requested·입금 대기) → KBZPay 송금·캡처 업로드(proof) → 입금 확인(paid) → 발주/배송.
 */
class Checkout extends BaseController
{
    public function index()
    {
        if (! mm_is_approved()) {
            return redirect()->to('/cart');
        }
        $u = mm_user();
        [$items, $subtotal] = Cart::items((int) $u['id']);
        if (! $items) {
            return redirect()->to('/cart')->with('error', '장바구니가 비어 있습니다.');
        }
        return $this->render('checkout/index', [
            'title'     => '주문 신청',
            'items'     => $items,
            'subtotal'  => $subtotal,
            'maxPoints' => $this->maxPoints($u, $subtotal),
            'codEnabled' => mm_setting('cod_enabled', '0') === '1',
            'mapsKey'    => trim((string) mm_setting('google_maps_key', '')),
        ]);
    }

    private function maxPoints(array $u, int $subtotal): int
    {
        $pct = (int) mm_setting('points_use_max_pct', 30);
        $cap = (int) floor($subtotal * $pct / 100);
        return max(0, min((int) $u['points_balance'], $cap));
    }

    public function submit()
    {
        if (! mm_is_approved()) {
            return redirect()->to('/cart');
        }
        $u = mm_user();
        [$items, $subtotal] = Cart::items((int) $u['id']);
        if (! $items) {
            return redirect()->to('/cart')->with('error', '장바구니가 비어 있습니다.');
        }
        if (! $this->validate([
            'ship_name'    => 'required|max_length[100]',
            'ship_phone'   => 'required|max_length[50]',
            'ship_city'    => 'required|max_length[100]',
            'ship_address' => 'required|max_length[255]',
        ])) {
            return redirect()->back()->withInput()->with('error', '배송지 정보를 모두 입력해 주세요.');
        }
        $method = $this->request->getPost('payment_method') === 'cod' && mm_setting('cod_enabled', '0') === '1' ? 'cod' : 'kbzpay';

        $points = (int) $this->request->getPost('points');
        $minUse = (int) mm_setting('points_min_use', 0);
        $points = max(0, min($points, $this->maxPoints($u, $subtotal)));
        if ($points > 0 && $points < $minUse) {
            $points = 0;
        }

        $om = new OrderModel();
        $orderNo = date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        $orderId = $om->insert([
            'order_no'       => $orderNo,
            'user_id'        => $u['id'],
            'status'         => 'requested',
            'payment_method' => $method,
            'subtotal'       => $subtotal,
            'shipping_fee'   => 0,
            'points_used'    => $points,
            'total'          => $subtotal - $points,
            'ship_name'      => trim((string) $this->request->getPost('ship_name')),
            'ship_phone'     => trim((string) $this->request->getPost('ship_phone')),
            'ship_city'      => trim((string) $this->request->getPost('ship_city')),
            'ship_address'   => trim((string) $this->request->getPost('ship_address')),
            'ship_lat'       => is_numeric($this->request->getPost('ship_lat')) ? (float) $this->request->getPost('ship_lat') : null,
            'ship_lng'       => is_numeric($this->request->getPost('ship_lng')) ? (float) $this->request->getPost('ship_lng') : null,
            'memo'           => trim((string) $this->request->getPost('memo')),
        ]);
        $oim = new OrderItemModel();
        foreach ($items as $it) {
            $oim->insert([
                'order_id'     => $orderId,
                'product_id'   => $it['p']['id'],
                'supplier_id'  => $it['p']['supplier_id'],
                'product_name' => $it['p']['name'],
                'thumb'        => $it['p']['thumb'],
                'unit_price'   => $it['unit'],
                'qty'          => $it['qty'],
                'line_total'   => $it['line'],
            ]);
        }
        if ($points > 0) {
            (new PointService())->add((int) $u['id'], -$points, 'order_use', 'order', (int) $orderId, '주문 사용 ' . $orderNo);
        }
        (new CartModel())->where('user_id', $u['id'])->delete();
        return redirect()->to('/checkout/done/' . $orderId);
    }

    public function done(int $id): string
    {
        $u = mm_user();
        $o = (new OrderModel())->where('user_id', $u['id'])->find($id);
        if (! $o) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $this->render('checkout/done', ['title' => '주문 신청 완료', 'o' => $o]);
    }
}
