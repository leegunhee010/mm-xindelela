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

    /** 관리자에서 바꿀 수 있는 줄서기 문구 (키 => 설명). 나머지는 시스템 문구라 고정. */
    public const TEXT_KEYS = [
        'name'                 => '기능 이름 (메뉴·안내 곳곳)',
        'btn'                  => '줄서기 버튼',
        'badge'                => '입고 예정 표시 (카드·메뉴·배지)',
        'price_hidden'         => '가격 자리 문구',
        'count'                => '줄 선 인원 (%s = 인원 수)',
        'count_zero'           => '아직 아무도 없을 때',
        'help_title'           => '도움말 제목',
        'help_1'               => '도움말 1',
        'help_2'               => '도움말 2',
        'help_3'               => '도움말 3',
        'agree_short'          => '신청 시 확인 체크 문구',
        'joined_title'         => '줄 선 뒤 상태 문구',
        'menu'                 => '마이페이지 메뉴 이름',
        'notify_arrived_title' => '입고 알림 제목 (%s = 상품명)',
        'notify_arrived_body'  => '입고 알림 내용',
        'notify_failed_title'  => '입고 취소 알림 제목 (%s = 상품명)',
        'notify_failed_body'   => '입고 취소 알림 내용',
    ];

    public function texts(): string
    {
        $over = json_decode((string) mm_setting('q_texts', ''), true);
        return $this->page('admin/queue_texts', ['title' => '줄서기 문구', 'keys' => self::TEXT_KEYS, 'def' => mm_q_defaults(), 'over' => is_array($over) ? $over : []]);
    }

    public function saveTexts()
    {
        $out = [];
        foreach (self::TEXT_KEYS as $k => $l) {
            $v = trim((string) $this->request->getPost('t_' . $k));
            if ($v !== '' && $v !== mm_q_defaults()[$k]) {
                $out[$k] = $v;
            }
        }
        mm_save_setting('q_texts', $out ? json_encode($out, JSON_UNESCAPED_UNICODE) : '');
        return redirect()->to('/admin/queues/texts')->with('success', '줄서기 문구를 저장했습니다. 비워 둔 칸은 기본 문구로 나옵니다.');
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
