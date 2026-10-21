<?php

namespace App\Controllers\Admin;

use App\Libraries\QueueService;
use App\Models\ProductModel;
use App\Models\QueueModel;
use App\Models\SupplierModel;
use App\Models\UserModel;

/** 줄서기 관리: 입고 예정 상품별 줄 선 회원 수·희망 수량(발주 판단용), 입고 완료/입고 취소. */
class Queues extends Base
{
    public function index(): string
    {
        $pm     = new ProductModel();
        $coming = $pm->where('status', 'coming')->orderBy('id', 'DESC')->findAll();
        // 최근에 줄이 닫힌 상품(입고됨·입고 취소)
        $closedIds = array_column((new QueueModel())->select('product_id, MAX(updated_at) AS t')->whereIn('status', ['arrived', 'failed'])->groupBy('product_id')->orderBy('t', 'DESC')->findAll(20), 'product_id');
        $closed    = $closedIds ? (new ProductModel())->whereIn('id', $closedIds)->findAll() : [];
        $qs        = new QueueService();
        $sm        = new SupplierModel();
        $sups      = [];
        foreach ([&$coming, &$closed] as &$list) {
            foreach ($list as &$p) {
                $p['supplier'] = $sups[$p['supplier_id']] ??= $sm->find($p['supplier_id']);
            }
            unset($p);
        }
        unset($list);
        return $this->page('admin/queues', [
            'title'  => '줄서기',
            'coming' => $coming,
            'closed' => $closed,
            'stats'  => $qs->stats(array_column($coming, 'id')),
            'done'   => $this->closedStats($closedIds),
        ]);
    }

    public function show(int $id): string
    {
        $p = (new ProductModel())->find($id);
        if (! $p) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $rows = (new QueueModel())->where('product_id', $id)->orderBy('created_at', 'ASC')->findAll();
        $um   = new UserModel();
        foreach ($rows as &$r) {
            $r['user'] = $um->find($r['user_id']);
        }
        unset($r);
        $waiting = array_values(array_filter($rows, static fn ($r) => $r['status'] === 'waiting'));
        return $this->page('admin/queue', [
            'title'   => '줄서기 상세',
            'p'       => $p,
            'rows'    => $rows,
            'n'       => count($waiting),
            'qty'     => array_sum(array_column($waiting, 'qty')),
        ]);
    }

    public function arrive(int $id)
    {
        $n = (new QueueService())->arrive($id);
        return redirect()->to('/admin/queues/' . $id)->with('success', '입고 완료로 바꾸고 판매를 시작했습니다. 줄 선 회원 ' . $n . '명에게 알림을 보냈습니다.');
    }

    public function fail(int $id)
    {
        $n = (new QueueService())->fail($id, trim((string) $this->request->getPost('reason')));
        return redirect()->to('/admin/queues/' . $id)->with('success', '입고를 취소하고 상품을 숨겼습니다. 줄 선 회원 ' . $n . '명에게 알림을 보냈습니다.');
    }

    /** 닫힌 줄: 상품별 [입고 알림 받은 수, 입고 취소 알림 수, 희망 수량 합] */
    private function closedStats(array $ids): array
    {
        $out = [];
        if (! $ids) {
            return $out;
        }
        foreach ((new QueueModel())->select('product_id, status, COUNT(*) AS n, SUM(qty) AS qty')->whereIn('product_id', $ids)->whereIn('status', ['arrived', 'failed'])->groupBy('product_id, status')->findAll() as $r) {
            $out[(int) $r['product_id']][$r['status']] = ['n' => (int) $r['n'], 'qty' => (int) $r['qty']];
        }
        return $out;
    }
}
