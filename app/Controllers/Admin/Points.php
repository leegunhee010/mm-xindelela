<?php

namespace App\Controllers\Admin;

use App\Models\OrderModel;
use App\Models\PointLogModel;
use App\Models\UserModel;

class Points extends Base
{
    public function index(): string
    {
        $lm   = new PointLogModel();
        $type = (string) ($this->request->getGet('type') ?? '');
        $b    = $lm;
        if ($type !== '') {
            $b = $b->where('type', $type);
        }
        $rows = $b->orderBy('id', 'DESC')->paginate(50, 'p');
        $um   = new UserModel();
        foreach ($rows as &$r) {
            $r['user'] = $um->find($r['user_id']);
        }
        $db   = db_connect();
        $sum  = $db->table('point_logs')->selectSum('amount')->where('amount >', 0)->get()->getRowArray()['amount'] ?? 0;
        $used = $db->table('point_logs')->selectSum('amount')->where('amount <', 0)->get()->getRowArray()['amount'] ?? 0;
        $bal  = $db->table('users')->selectSum('points_balance')->where('role', 'buyer')->get()->getRowArray()['points_balance'] ?? 0;
        return $this->page('admin/points', ['title' => '포인트 원장', 'rows' => $rows, 'pager' => $lm->pager, 'type' => $type, 'sum' => (int) $sum, 'used' => (int) $used, 'bal' => (int) $bal]);
    }

}
