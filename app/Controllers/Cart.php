<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\SupplierModel;

class Cart extends BaseController
{
    public function index(): string
    {
        $u = mm_user();
        if (! mm_is_approved()) {
            return $this->render('cart/locked', ['title' => '장바구니']);
        }
        [$items, $subtotal] = $this->items((int) $u['id']);
        return $this->render('cart/index', [
            'title'    => '장바구니',
            'items'    => $items,
            'subtotal' => $subtotal,
        ]);
    }

    /** 장바구니 행 + 상품 + 현재 단가 계산. */
    public static function items(int $userId): array
    {
        $cm    = new CartModel();
        $pm    = new ProductModel();
        $sm    = new SupplierModel();
        $rows  = $cm->where('user_id', $userId)->orderBy('id', 'DESC')->findAll();
        $items = [];
        $sub   = 0;
        foreach ($rows as $r) {
            $p = $pm->find($r['product_id']);
            if (! $p || $p['status'] !== 'active') {
                $cm->delete($r['id']);
                continue;
            }
            $qty   = max((int) $p['moq'], (int) $r['qty']);
            $unit  = mm_unit_price($p, $qty);
            $line  = $unit * $qty;
            $sub  += $line;
            $items[] = ['cart' => $r, 'p' => $p, 'qty' => $qty, 'unit' => $unit, 'line' => $line, 'supplier' => $sm->find($p['supplier_id'])];
        }
        return [$items, $sub];
    }

    public function add()
    {
        if (! mm_is_approved()) {
            return redirect()->back()->with('error', '비즈니스 인증이 완료된 회원만 담을 수 있습니다.');
        }
        $u   = mm_user();
        $pid = (int) $this->request->getPost('product_id');
        $qty = (int) $this->request->getPost('qty');
        $p   = (new ProductModel())->find($pid);
        if (! $p || $p['status'] !== 'active') {
            return redirect()->back()->with('error', '판매중인 상품이 아닙니다.');
        }
        $qty = max((int) $p['moq'], $qty);
        $cm  = new CartModel();
        $row = $cm->where('user_id', $u['id'])->where('product_id', $pid)->first();
        if ($row) {
            $cm->update($row['id'], ['qty' => (int) $row['qty'] + $qty]);
        } else {
            $cm->insert(['user_id' => $u['id'], 'product_id' => $pid, 'qty' => $qty]);
        }
        if ($this->request->getPost('go') === 'checkout') {
            return redirect()->to('/checkout');
        }
        return redirect()->back()->with('success', '장바구니에 담았습니다.');
    }

    /** 지난 주문 그대로 다시 담기. 판매 중인 상품만, 지금 최소수량보다 적으면 최소수량으로. */
    public function reorder(int $orderId)
    {
        if (! mm_is_approved()) {
            return redirect()->back()->with('error', '비즈니스 인증이 완료된 회원만 담을 수 있습니다.');
        }
        $u = mm_user();
        $o = (new OrderModel())->where('user_id', $u['id'])->find($orderId);
        if (! $o) {
            return redirect()->back()->with('error', '주문을 찾을 수 없습니다.');
        }
        $pm   = new ProductModel();
        $cm   = new CartModel();
        $done = 0;
        $skip = [];
        foreach ((new OrderItemModel())->where('order_id', $orderId)->findAll() as $it) {
            $p = $pm->find($it['product_id']);
            if (! $p || $p['status'] !== 'active') {
                $skip[] = $it['product_name'];
                continue;
            }
            $qty = max((int) $p['moq'], (int) $it['qty']);
            $row = $cm->where('user_id', $u['id'])->where('product_id', $p['id'])->first();
            if ($row) {
                $cm->update($row['id'], ['qty' => max((int) $row['qty'], $qty)]);
            } else {
                $cm->insert(['user_id' => $u['id'], 'product_id' => $p['id'], 'qty' => $qty]);
            }
            $done++;
        }
        if (! $done) {
            return redirect()->back()->with('error', '이 주문의 상품은 지금 판매하지 않습니다.');
        }
        $msg = '지난 주문 상품 ' . $done . '개를 같은 수량으로 담았습니다. 단가는 지금 가격으로 다시 계산됩니다.';
        if ($skip) {
            $msg .= ' 판매가 끝난 상품은 빠졌습니다: ' . implode(', ', $skip);
        }
        return redirect()->to('/cart')->with('success', $msg);
    }

    public function update()
    {
        $u  = mm_user();
        $cm = new CartModel();
        foreach ((array) $this->request->getPost('qty') as $cartId => $qty) {
            $row = $cm->where('user_id', $u['id'])->find((int) $cartId);
            if ($row) {
                $p = (new ProductModel())->find($row['product_id']);
                $cm->update($row['id'], ['qty' => max((int) ($p['moq'] ?? 1), (int) $qty)]);
            }
        }
        return redirect()->to('/cart');
    }

    public function remove()
    {
        $u  = mm_user();
        $cm = new CartModel();
        $id = (int) $this->request->getPost('id');
        $cm->where('user_id', $u['id'])->where('id', $id)->delete();
        return redirect()->to('/cart');
    }
}
