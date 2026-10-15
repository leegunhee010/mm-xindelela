<?php

namespace App\Controllers\Admin;

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

    public function referrals(): string
    {
        $um   = new UserModel();
        $rows = $um->where('role', 'buyer')->where('referred_by IS NOT NULL')->orderBy('id', 'DESC')->paginate(50, 'p');
        foreach ($rows as &$r) {
            $r['referrer'] = $um->find($r['referred_by']);
        }
        // 추천 상위
        $top = db_connect()->table('users')->select('referred_by, COUNT(*) AS cnt')->where('role', 'buyer')->where('referred_by IS NOT NULL')->groupBy('referred_by')->orderBy('cnt', 'DESC')->limit(10)->get()->getResultArray();
        foreach ($top as &$t) {
            $t['user'] = $um->find($t['referred_by']);
        }
        return $this->page('admin/referrals', ['title' => '추천 현황', 'rows' => $rows, 'pager' => $um->pager, 'top' => $top]);
    }
}
