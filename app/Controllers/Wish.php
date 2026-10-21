<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\SupplierModel;
use App\Models\WishModel;

/**
 * 관심상품. 비회원도 담을 수 있고(쿠키 mm_wish, 90일), 로그인·가입하면 계정으로 옮겨진다.
 * 가격을 못 보는 사람에게 "담아 두고 인증받으면 바로 주문"으로 가입을 유도하는 장치.
 */
class Wish extends BaseController
{
    private const COOKIE_DAYS = 90;
    private const GUEST_MAX   = 100;

    public function index(): string
    {
        $ids   = mm_wish_ids();
        $items = [];
        if ($ids) {
            $pm  = new ProductModel();
            $sm  = new SupplierModel();
            $map = [];
            foreach ($pm->whereIn('id', $ids)->where('status', 'active')->findAll() as $p) {
                $map[(int) $p['id']] = $p;
            }
            $sups = [];
            foreach ($ids as $id) {
                if (isset($map[$id])) {
                    $p       = $map[$id];
                    $items[] = ['p' => $p, 'supplier' => $sups[$p['supplier_id']] ??= $sm->find($p['supplier_id'])];
                }
            }
        }
        return $this->render('wish/index', ['title' => '관심상품', 'items' => $items, 'approved' => mm_is_approved()]);
    }

    public function toggle()
    {
        $pid = (int) $this->request->getPost('product_id');
        $p   = $pid ? (new ProductModel())->find($pid) : null;
        if (! $p || $p['status'] !== 'active') {
            return $this->reply(false, '판매중인 상품이 아닙니다.');
        }
        $u = mm_user();
        if ($u && $u['role'] === 'buyer') {
            $wm  = new WishModel();
            $row = $wm->where('user_id', $u['id'])->where('product_id', $pid)->first();
            if ($row) {
                $wm->delete($row['id']);
            } else {
                $wm->insert(['user_id' => $u['id'], 'product_id' => $pid]);
            }
            $count = $wm->where('user_id', $u['id'])->countAllResults();
            return $this->reply(! $row, null, $count);
        }
        $ids = mm_wish_guest_ids();
        $on  = ! in_array($pid, $ids, true);
        $ids = $on ? array_slice(array_merge([$pid], $ids), 0, self::GUEST_MAX) : array_values(array_diff($ids, [$pid]));
        $this->response->setCookie('mm_wish', implode(',', $ids), self::COOKIE_DAYS * 86400);
        return $this->reply($on, null, count($ids));
    }

    /** 선택한 관심상품을 최소주문수량으로 장바구니에 담는다(이미 담긴 상품은 그대로). */
    public function toCart()
    {
        if (! mm_is_approved()) {
            return redirect()->back()->with('error', '비즈니스 인증이 완료된 회원만 담을 수 있습니다.');
        }
        $u    = mm_user();
        $ids  = array_intersect(array_map('intval', (array) $this->request->getPost('ids')), mm_wish_ids());
        $pm   = new ProductModel();
        $cm   = new CartModel();
        $done = 0;
        foreach ($ids as $pid) {
            $p = $pm->find($pid);
            if (! $p || $p['status'] !== 'active' || $cm->where('user_id', $u['id'])->where('product_id', $pid)->countAllResults()) {
                continue;
            }
            $cm->insert(['user_id' => $u['id'], 'product_id' => $pid, 'qty' => max(1, (int) $p['moq'])]);
            $done++;
        }
        if (! $ids) {
            return redirect()->back()->with('error', '담을 상품을 선택해 주세요.');
        }
        return redirect()->to('/cart')->with('success', $done ? $done . '개 상품을 최소주문수량으로 담았습니다. 수량은 여기서 바꿀 수 있습니다.' : '선택한 상품은 이미 장바구니에 있습니다.');
    }

    private function reply(bool $on, ?string $error = null, int $count = 0)
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($error ? ['ok' => false, 'error' => $error] : ['ok' => true, 'on' => $on, 'count' => $count, 'guest' => ! (mm_user() && mm_user()['role'] === 'buyer')]);
        }
        if ($error) {
            return redirect()->back()->with('error', $error);
        }
        return redirect()->back()->withCookies()->with('success', $on ? '관심상품에 담았습니다.' : '관심상품에서 뺐습니다.');
    }
}
