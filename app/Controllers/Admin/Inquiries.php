<?php

namespace App\Controllers\Admin;

use App\Models\InquiryModel;
use App\Models\ProductModel;
use App\Models\UserModel;

/** 문의함: 왼쪽 목록 + 오른쪽 답변 패널. 기본은 답변 대기(오래된 순) */
class Inquiries extends Base
{
    public function index(): string
    {
        $st = (string) $this->request->getGet('st');
        $st = in_array($st, ['wait', 'done', 'all'], true) ? $st : 'wait';
        $q  = trim((string) $this->request->getGet('q'));

        $im = new InquiryModel();
        $counts = [
            'wait' => (new InquiryModel())->where('answer', null)->countAllResults(),
            'done' => (new InquiryModel())->where('answer IS NOT NULL')->countAllResults(),
            'all'  => (new InquiryModel())->countAllResults(),
        ];

        $im->select('inquiries.*')->join('users u', 'u.id = inquiries.user_id', 'left');
        if ($st === 'wait') {
            $im->where('inquiries.answer', null)->orderBy('inquiries.id', 'ASC');
        } else {
            if ($st === 'done') {
                $im->where('inquiries.answer IS NOT NULL');
            }
            $im->orderBy('inquiries.id', 'DESC');
        }
        if ($q !== '') {
            $im->groupStart()->like('u.company_name', $q)->orLike('inquiries.body', $q)->orLike('u.phone', $q)->groupEnd();
        }
        $rows = $im->paginate(20, 'p');

        $um = new UserModel();
        $pm = new ProductModel();
        foreach ($rows as &$r) {
            $r['user']    = $um->find($r['user_id']);
            $r['product'] = $r['product_id'] ? $pm->find($r['product_id']) : null;
        }
        unset($r);

        $selId = (int) $this->request->getGet('id');
        $sel   = null;
        foreach ($rows as $r) {
            if ($r['id'] === $selId || (string) $r['id'] === (string) $selId) {
                $sel = $r;
            }
        }
        if (! $sel && $selId) {
            $sel = (new InquiryModel())->find($selId);
            if ($sel) {
                $sel['user']    = $um->find($sel['user_id']);
                $sel['product'] = $sel['product_id'] ? $pm->find($sel['product_id']) : null;
            }
        }
        $sel ??= $rows[0] ?? null;

        $others = [];
        if ($sel) {
            $others = (new InquiryModel())->where('user_id', $sel['user_id'])->where('id !=', $sel['id'])->orderBy('id', 'DESC')->findAll(5);
        }

        return $this->page('admin/inquiries', [
            'title' => '문의 관리', 'rows' => $rows, 'pager' => $im->pager, 'st' => $st, 'q' => $q,
            'counts' => $counts, 'sel' => $sel, 'others' => $others,
        ]);
    }

    public function answer(int $id)
    {
        $a = trim((string) $this->request->getPost('answer'));
        if ($a === '') {
            return redirect()->back()->with('error', '답변을 입력해 주세요.');
        }
        (new InquiryModel())->update($id, ['answer' => $a, 'answered_at' => date('Y-m-d H:i:s')]);

        // '저장하고 다음' → 남은 답변 대기 중 가장 오래된 문의로
        if ($this->request->getPost('next')) {
            $next = (new InquiryModel())->where('answer', null)->orderBy('id', 'ASC')->first();
            if ($next) {
                return redirect()->to(site_url('admin/inquiries?st=wait&id=' . $next['id']))->with('success', '답변을 저장했습니다. 다음 문의입니다.');
            }
            return redirect()->to(site_url('admin/inquiries?st=done&id=' . $id))->with('success', '답변을 저장했습니다. 대기 중인 문의가 없습니다.');
        }
        return redirect()->to(site_url('admin/inquiries?st=' . ($this->request->getPost('st') ?: 'done') . '&id=' . $id))->with('success', '답변을 저장했습니다.');
    }
}
