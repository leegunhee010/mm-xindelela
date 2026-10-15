<?php

namespace App\Controllers\Admin;

use App\Models\InquiryModel;
use App\Models\ProductModel;
use App\Models\UserModel;

class Inquiries extends Base
{
    public function index(): string
    {
        $im   = new InquiryModel();
        $rows = $im->orderBy('answered_at', 'ASC')->orderBy('id', 'DESC')->paginate(30, 'p');
        $um   = new UserModel();
        $pm   = new ProductModel();
        foreach ($rows as &$r) {
            $r['user']    = $um->find($r['user_id']);
            $r['product'] = $r['product_id'] ? $pm->find($r['product_id']) : null;
        }
        return $this->page('admin/inquiries', ['title' => '문의 관리', 'rows' => $rows, 'pager' => $im->pager]);
    }

    public function answer(int $id)
    {
        $a = trim((string) $this->request->getPost('answer'));
        if ($a === '') {
            return redirect()->back()->with('error', '답변을 입력해 주세요.');
        }
        (new InquiryModel())->update($id, ['answer' => $a, 'answered_at' => date('Y-m-d H:i:s')]);
        return redirect()->back()->with('success', '답변을 저장했습니다.');
    }
}
