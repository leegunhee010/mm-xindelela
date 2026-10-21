<?php

namespace App\Libraries;

use App\Models\NotificationModel;
use App\Models\ProductModel;
use App\Models\QueueModel;

/**
 * 줄서기. 입고 예정(status=coming) 상품에 인증 회원이 줄을 선다.
 * 주문·예약·결제가 아니고 책임도 없다. 순번 우선권 없이, 입고되면 모두에게 동시에 알린다.
 * 상태: waiting(줄 서는 중) → arrived(입고 알림 받음) | failed(입고 취소) | cancelled(본인 취소)
 */
class QueueService
{
    private QueueModel $queues;

    public function __construct()
    {
        $this->queues = new QueueModel();
    }

    /** 줄 서기(이미 섰으면 수량만 바꿈, 취소했던 줄도 다시 섬). */
    public function join(int $userId, array $product, int $qty): void
    {
        $qty = max((int) $product['moq'], $qty);
        $row = $this->queues->where('user_id', $userId)->where('product_id', $product['id'])->first();
        if ($row) {
            $this->queues->update($row['id'], ['qty' => $qty, 'status' => 'waiting']);
        } else {
            $this->queues->insert(['user_id' => $userId, 'product_id' => $product['id'], 'qty' => $qty, 'status' => 'waiting']);
        }
    }

    public function cancel(int $userId, int $productId): void
    {
        $this->queues->where('user_id', $userId)->where('product_id', $productId)->where('status', 'waiting')->set(['status' => 'cancelled'])->update();
    }

    /** 입고 완료: 판매 시작 + 줄 선 회원 모두에게 동시 알림. 알린 인원 수를 돌려준다. */
    public function arrive(int $productId): int
    {
        $p = (new ProductModel())->find($productId);
        if (! $p) {
            return 0;
        }
        if ($p['status'] !== 'active') {
            (new ProductModel())->update($productId, ['status' => 'active']);
        }
        return $this->close($productId, 'arrived', 'queue_arrived',
            mm_q('notify_arrived_title', $p['name']),
            mm_q('notify_arrived_body'));
    }

    /** 입고 취소(통관 불가 등): 상품 숨김 + 줄 선 회원 모두에게 알림. */
    public function fail(int $productId, string $reason = ''): int
    {
        $p = (new ProductModel())->find($productId);
        if (! $p) {
            return 0;
        }
        (new ProductModel())->update($productId, ['status' => 'hidden']);
        return $this->close($productId, 'failed', 'queue_failed',
            mm_q('notify_failed_title', $p['name']),
            trim(mm_q('notify_failed_body') . ($reason !== '' ? ' (' . $reason . ')' : '')));
    }

    private function close(int $productId, string $status, string $type, string $title, string $body): int
    {
        $rows = $this->queues->where('product_id', $productId)->where('status', 'waiting')->findAll();
        $nm   = new NotificationModel();
        foreach ($rows as $r) {
            $this->queues->update($r['id'], ['status' => $status]);
            $nm->insert(['user_id' => $r['user_id'], 'type' => $type, 'title' => $title, 'body' => $body, 'link' => 'products/' . $productId]);
        }
        return count($rows);
    }

    /** 상품별 [줄 선 회원 수, 희망 수량 합계] (waiting 기준). */
    public function stats(array $productIds): array
    {
        $out = [];
        if (! $productIds) {
            return $out;
        }
        foreach ($this->queues->select('product_id, COUNT(*) AS n, SUM(qty) AS qty')->whereIn('product_id', $productIds)->where('status', 'waiting')->groupBy('product_id')->findAll() as $r) {
            $out[(int) $r['product_id']] = ['n' => (int) $r['n'], 'qty' => (int) $r['qty']];
        }
        return $out;
    }
}
