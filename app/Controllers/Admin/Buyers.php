<?php

namespace App\Controllers\Admin;

use App\Libraries\PointService;
use App\Models\OrderModel;
use App\Models\PointLogModel;
use App\Models\UserModel;

class Buyers extends Base
{
    public function index(): string
    {
        $um = new UserModel();
        $st = (string) ($this->request->getGet('status') ?? 'pending');
        $q  = trim((string) $this->request->getGet('q'));
        $b  = $um->where('role', 'buyer');
        if ($st !== '' && $st !== 'all') {
            $b->where('status', $st);
        }
        if ($q !== '') {
            $b->groupStart()->like('company_name', $q)->orLike('name', $q)->orLike('email', $q)->orLike('phone', $q)->groupEnd();
        }
        $rows = $b->orderBy($st === 'pending' ? 'id' : 'id', $st === 'pending' ? 'ASC' : 'DESC')->paginate(30, 'p');
        return $this->page('admin/buyers', ['title' => '구매자 관리', 'rows' => $rows, 'pager' => $um->pager, 'st' => $st, 'q' => $q,
            'counts' => ['pending' => $um->where('role', 'buyer')->where('status', 'pending')->countAllResults(), 'approved' => $um->where('role', 'buyer')->where('status', 'approved')->countAllResults(), 'rejected' => $um->where('role', 'buyer')->where('status', 'rejected')->countAllResults(), 'suspended' => $um->where('role', 'buyer')->where('status', 'suspended')->countAllResults()]]);
    }

    public function show(int $id): string
    {
        $um = new UserModel();
        $u  = $um->where('role', 'buyer')->find($id);
        if (! $u) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $this->page('admin/buyer', [
            'title'    => '구매자 상세',
            'b'        => $u,
            'docs'     => mm_json($u['doc_files']),
            'referrer' => $u['referred_by'] ? $um->find($u['referred_by']) : null,
            'referred' => $um->where('referred_by', $id)->findAll(),
            'orders'   => (new OrderModel())->where('user_id', $id)->orderBy('id', 'DESC')->findAll(10),
            'points'   => (new PointLogModel())->where('user_id', $id)->orderBy('id', 'DESC')->findAll(15),
        ]);
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
        return redirect()->to('/admin/buyers/' . $id)->with('success', '반려했습니다. 회원이 자료를 다시 제출하면 검수 대기로 돌아옵니다.');
    }

    public function status(int $id)
    {
        $st = (string) $this->request->getPost('status');
        if (! in_array($st, ['pending', 'approved', 'rejected', 'suspended'], true)) {
            return redirect()->back();
        }
        (new UserModel())->where('role', 'buyer')->where('id', $id)->set(['status' => $st])->update();
        return redirect()->back()->with('success', '상태를 변경했습니다.');
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
