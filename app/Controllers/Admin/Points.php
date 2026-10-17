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

    /** 추천 현황: 요약 지표 · 추천인 순위 · 추천 가입 회원 (한 화면에서 관리) */
    public function referrals(): string
    {
        $um   = new UserModel();
        $all  = $um->where('role', 'buyer')->where('referred_by IS NOT NULL')->orderBy('id', 'DESC')->findAll();
        $ids  = array_column($all, 'id');
        $refIds = array_values(array_unique(array_column($all, 'referred_by')));

        // 소개 회원별 입금 확인 매출
        $sales = [];
        if ($ids) {
            foreach ((new OrderModel())->select('user_id, total, status')->whereIn('user_id', $ids)->whereIn('status', \App\Libraries\GrowthStats::PAID)->findAll() as $o) {
                $sales[$o['user_id']] = ($sales[$o['user_id']] ?? 0) + (int) $o['total'];
            }
        }
        // 추천 포인트 지급 내역 (ref_id = 소개 회원)
        $paid = [];   // [소개회원id][type] = amount
        $paidTo = []; // [추천인id] = 합계
        foreach ((new PointLogModel())->whereIn('type', ['referral_signup', 'referral_first_order'])->findAll() as $l) {
            $paid[$l['ref_id']][$l['type']] = (int) $l['amount'];
            $paidTo[$l['user_id']] = ($paidTo[$l['user_id']] ?? 0) + (int) $l['amount'];
        }
        $refs = [];
        if ($refIds) {
            foreach ($um->whereIn('id', $refIds)->findAll() as $r) {
                $refs[$r['id']] = $r;
            }
        }
        // 추천인 순위
        $rank = [];
        foreach ($all as $m) {
            $k = $m['referred_by'];
            $rank[$k] ??= ['user' => $refs[$k] ?? null, 'n' => 0, 'approved' => 0, 'ordered' => 0, 'sales' => 0, 'points' => $paidTo[$k] ?? 0];
            $rank[$k]['n']++;
            $rank[$k]['approved'] += $m['status'] === 'approved' ? 1 : 0;
            $rank[$k]['ordered']  += $m['first_order_at'] ? 1 : 0;
            $rank[$k]['sales']    += $sales[$m['id']] ?? 0;
        }
        usort($rank, fn ($x, $y) => [$y['n'], $y['sales']] <=> [$x['n'], $x['sales']]);

        $n = count($all);
        $approved = count(array_filter($all, fn ($m) => $m['status'] === 'approved'));
        $ordered  = count(array_filter($all, fn ($m) => ! empty($m['first_order_at'])));
        $month    = date('Y-m');
        $kpi = [
            'total'     => $n,
            'month'     => count(array_filter($all, fn ($m) => str_starts_with((string) $m['created_at'], $month))),
            'approveRt' => $n ? round($approved / $n * 100) : 0,
            'orderRt'   => $n ? round($ordered / $n * 100) : 0,
            'sales'     => array_sum($sales),
            'points'    => array_sum($paidTo),
        ];

        // 목록 필터
        $f = (string) ($this->request->getGet('f') ?? 'all');
        $list = array_values(array_filter($all, function ($m) use ($f) {
            return match ($f) {
                'pending'  => $m['status'] === 'pending',
                'approved' => $m['status'] === 'approved' && empty($m['first_order_at']),
                'ordered'  => ! empty($m['first_order_at']),
                default    => true,
            };
        }));
        $counts = [
            'all'      => $n,
            'pending'  => count(array_filter($all, fn ($m) => $m['status'] === 'pending')),
            'approved' => count(array_filter($all, fn ($m) => $m['status'] === 'approved' && empty($m['first_order_at']))),
            'ordered'  => $ordered,
        ];
        return $this->page('admin/referrals', [
            'title' => '추천 현황', 'kpi' => $kpi, 'rank' => $rank, 'list' => $list, 'refs' => $refs,
            'sales' => $sales, 'paid' => $paid, 'f' => $f, 'counts' => $counts,
            'rules' => ['signup' => (int) mm_setting('points_referral_signup', 0), 'first' => (int) mm_setting('points_referral_first_order', 0)],
        ]);
    }
}
