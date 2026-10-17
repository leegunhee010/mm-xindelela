<?php

namespace App\Controllers\Admin;

use App\Models\AdminLogModel;
use App\Models\UserModel;

/** 관리자 작업 기록: 누가 언제 무엇을 했는지. */
class Logs extends Base
{
    public const TYPES = ['order' => '주문', 'buyer' => '회원', 'product' => '상품', 'supplier' => '공급사', 'category' => '카테고리', 'notice' => '공지', 'banner' => '배너', 'inquiry' => '문의', 'settings' => '설정', 'staff' => '담당자'];

    public function index(): string
    {
        $lm = new AdminLogModel();
        $g  = fn ($k) => trim((string) $this->request->getGet($k));
        $f  = ['admin' => (int) $g('admin'), 'type' => $g('type'), 'from' => $g('from'), 'to' => $g('to'), 'q' => $g('q')];
        if ($f['admin']) {
            $lm->where('admin_id', $f['admin']);
        }
        if (isset(self::TYPES[$f['type']])) {
            $lm->where('target_type', $f['type']);
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['from'])) {
            $lm->where('created_at >=', $f['from'] . ' 00:00:00');
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['to'])) {
            $lm->where('created_at <=', $f['to'] . ' 23:59:59');
        }
        if ($f['q'] !== '') {
            $lm->like('summary', $f['q']);
        }
        $rows = $lm->orderBy('id', 'DESC')->paginate(50, 'p');
        return $this->page('admin/logs', [
            'title' => '작업 기록', 'rows' => $rows, 'pager' => $lm->pager, 'f' => $f, 'types' => self::TYPES,
            'admins' => (new UserModel())->where('role', 'admin')->orderBy('id')->findAll(),
        ]);
    }
}
