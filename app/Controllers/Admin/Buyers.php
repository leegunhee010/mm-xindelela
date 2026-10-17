<?php

namespace App\Controllers\Admin;

use App\Libraries\GrowthStats;
use App\Libraries\PointService;
use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Models\PointLogModel;
use App\Models\UserModel;

class Buyers extends Base
{
    public function index(): string
    {
        $um = new UserModel();
        $st = (string) ($this->request->getGet('status') ?? 'all');
        $q  = trim((string) $this->request->getGet('q'));
        $b  = $um->where('role', 'buyer');
        if ($st !== '' && $st !== 'all') {
            $b->where('status', $st);
        }
        if ($q !== '') {
            $b->groupStart()->like('company_name', $q)->orLike('name', $q)->orLike('email', $q)->orLike('phone', $q)->groupEnd();
        }
        $rows = $b->orderBy('created_at', $st === 'pending' ? 'ASC' : 'DESC')->paginate(30, 'p');
        $stats = $this->orderStats(array_column($rows, 'id'));
        $refIds = array_values(array_unique(array_filter(array_column($rows, 'referred_by'))));
        $refs = [];
        if ($refIds) {
            foreach ((new UserModel())->whereIn('id', $refIds)->findAll() as $r) {
                $refs[$r['id']] = $r;
            }
        }
        $carts = [];
        if ($rows) {
            foreach ((new CartModel())->select('user_id')->whereIn('user_id', array_column($rows, 'id'))->findAll() as $c) {
                $carts[$c['user_id']] = ($carts[$c['user_id']] ?? 0) + 1;
            }
        }
        return $this->page('admin/buyers', ['title' => '회원 관리', 'rows' => $rows, 'stats' => $stats, 'refs' => $refs, 'carts' => $carts, 'pager' => $um->pager, 'st' => $st, 'q' => $q,
            'counts' => ['pending' => $um->where('role', 'buyer')->where('status', 'pending')->countAllResults(), 'approved' => $um->where('role', 'buyer')->where('status', 'approved')->countAllResults(), 'rejected' => $um->where('role', 'buyer')->where('status', 'rejected')->countAllResults(), 'suspended' => $um->where('role', 'buyer')->where('status', 'suspended')->countAllResults()]]);
    }

    public function show(int $id): string
    {
        $um = new UserModel();
        $u  = $um->where('role', 'buyer')->find($id);
        if (! $u) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $orders = (new OrderModel())->where('user_id', $id)->orderBy('created_at', 'DESC')->findAll();
        $cart   = [];
        $pm     = new ProductModel();
        foreach ((new CartModel())->where('user_id', $id)->orderBy('id', 'DESC')->findAll() as $c) {
            $p = $pm->find($c['product_id']);
            if ($p) {
                $unit   = mm_unit_price($p, (int) $c['qty']);
                $cart[] = ['c' => $c, 'p' => $p, 'unit' => $unit, 'line' => $unit * (int) $c['qty']];
            }
        }
        $referred = $um->where('referred_by', $id)->orderBy('id', 'DESC')->findAll();
        $tab = (string) ($this->request->getGet('tab') ?? 'info');
        return $this->page('admin/buyer', [
            'title'    => '회원 상세',
            'b'        => $u,
            'tab'      => in_array($tab, ['info', 'orders', 'cart', 'points', 'referral'], true) ? $tab : 'info',
            'docs'     => mm_json($u['doc_files']),
            'referrer' => $u['referred_by'] ? $um->find($u['referred_by']) : null,
            'referred' => $referred,
            'refStats' => $this->orderStats(array_column($referred, 'id')),
            'orders'   => $orders,
            'stat'     => $this->orderStats([$id])[$id] ?? ['n' => 0, 'paid' => 0, 'last' => null],
            'cart'     => $cart,
            'points'   => (new PointLogModel())->where('user_id', $id)->orderBy('id', 'DESC')->findAll(100),
        ]);
    }

    /** 회원별 주문 수(취소 제외) · 입금 확인 금액 · 마지막 주문일. */
    private function orderStats(array $ids): array
    {
        $out = [];
        if (! $ids) {
            return $out;
        }
        foreach ((new OrderModel())->select('user_id, status, total, created_at')->whereIn('user_id', $ids)->where('status !=', 'cancelled')->findAll() as $o) {
            $k = $o['user_id'];
            $out[$k] ??= ['n' => 0, 'paid' => 0, 'last' => null];
            $out[$k]['n']++;
            if (in_array($o['status'], GrowthStats::PAID, true)) {
                $out[$k]['paid'] += (int) $o['total'];
            }
            if ($out[$k]['last'] === null || $o['created_at'] > $out[$k]['last']) {
                $out[$k]['last'] = $o['created_at'];
            }
        }
        return $out;
    }

    public function approve(int $id)
    {
        $um = new UserModel();
        $u  = $um->where('role', 'buyer')->find($id);
        if (! $u) {
            return redirect()->back()->with('error', '회원이 없습니다.');
        }
        $um->update($id, ['status' => 'approved', 'approved_at' => date('Y-m-d H:i:s'), 'reject_reason' => null]);
        (new PointService())->onBuyerApproved($um->find($id));
        return redirect()->to('/admin/buyers/' . $id)->with('success', '승인했습니다. 이제 도매가와 주문 신청이 열립니다.');
    }

    public function reject(int $id)
    {
        $um = new UserModel();
        $reason = trim((string) $this->request->getPost('reason'));
        if ($reason === '') {
            return redirect()->back()->with('error', '반려 사유를 입력해 주세요. (회원에게 그대로 표시됩니다)');
        }
        $um->where('role', 'buyer')->where('id', $id)->set(['status' => 'rejected', 'reject_reason' => $reason])->update();
        return redirect()->to('/admin/buyers/' . $id)->with('success', '반려했습니다. 회원이 자료를 다시 제출하면 인증 검수 중으로 돌아옵니다.');
    }

    public function status(int $id)
    {
        $st = (string) $this->request->getPost('status');
        if (! in_array($st, ['pending', 'approved', 'rejected', 'suspended'], true)) {
            return redirect()->back();
        }
        $um = new UserModel();
        $u  = $um->where('role', 'buyer')->find($id);
        if (! $u) {
            return redirect()->back();
        }
        $data = ['status' => $st];
        if ($st === 'approved') {
            $data['approved_at']   = $u['approved_at'] ?: date('Y-m-d H:i:s');
            $data['reject_reason'] = null;
        }
        $um->update($id, $data);
        if ($st === 'approved') {
            (new PointService())->onBuyerApproved($um->find($id)); // 승인 버튼과 같게 (중복 지급은 막힘)
        }
        return redirect()->back()->with('success', '상태를 바꿨습니다: ' . mm_verify_status_label($st));
    }

    public function note(int $id)
    {
        (new UserModel())->where('role', 'buyer')->where('id', $id)->set(['admin_note' => trim((string) $this->request->getPost('admin_note'))])->update();
        return redirect()->back()->with('success', '메모를 저장했습니다.');
    }

    public function points(int $id)
    {
        $amt  = (int) $this->request->getPost('amount');
        $memo = trim((string) $this->request->getPost('memo'));
        if ($amt === 0) {
            return redirect()->back()->with('error', '0이 아닌 금액을 입력해 주세요.');
        }
        if (! (new PointService())->add($id, $amt, 'admin_adjust', 'admin', (int) mm_user()['id'], $memo ?: '관리자 조정')) {
            return redirect()->back()->with('error', '잔액이 부족해 차감할 수 없습니다.');
        }
        return redirect()->back()->with('success', '포인트를 조정했습니다.');
    }
}
