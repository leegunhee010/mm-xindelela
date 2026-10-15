<?php

namespace App\Libraries;

use App\Models\PointLogModel;
use App\Models\UserModel;

/**
 * 포인트 원장. 잔액은 users.points_balance, 이력은 point_logs.
 * 모든 증감은 반드시 여기를 거친다.
 */
class PointService
{
    private UserModel $users;
    private PointLogModel $logs;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->logs  = new PointLogModel();
    }

    /** 증감(+/-) 기록. 잔액 부족이면 false. */
    public function add(int $userId, int $amount, string $type, ?string $refType = null, ?int $refId = null, ?string $memo = null): bool
    {
        if ($amount === 0) {
            return true;
        }
        $user = $this->users->find($userId);
        if (! $user) {
            return false;
        }
        $after = (int) $user['points_balance'] + $amount;
        if ($after < 0) {
            return false;
        }
        $this->users->update($userId, ['points_balance' => $after]);
        $this->logs->insert([
            'user_id'       => $userId,
            'type'          => $type,
            'amount'        => $amount,
            'balance_after' => $after,
            'ref_type'      => $refType,
            'ref_id'        => $refId,
            'memo'          => $memo,
        ]);
        return true;
    }

    /** 같은 사유로 이미 지급됐는지 (중복 지급 방지). */
    public function exists(int $userId, string $type, string $refType, int $refId): bool
    {
        return $this->logs->where(['user_id' => $userId, 'type' => $type, 'ref_type' => $refType, 'ref_id' => $refId])->countAllResults() > 0;
    }

    /** 구매자 승인 시: 가입 보너스 + 추천인 포인트. */
    public function onBuyerApproved(array $buyer): void
    {
        $bonus = (int) mm_setting('points_signup_bonus', 0);
        if ($bonus > 0 && ! $this->exists((int) $buyer['id'], 'signup_bonus', 'user', (int) $buyer['id'])) {
            $this->add((int) $buyer['id'], $bonus, 'signup_bonus', 'user', (int) $buyer['id'], '가입 승인 축하 포인트');
        }
        $ref = (int) ($buyer['referred_by'] ?? 0);
        $amt = (int) mm_setting('points_referral_signup', 0);
        if ($ref > 0 && $amt > 0 && ! $this->exists($ref, 'referral_signup', 'user', (int) $buyer['id'])) {
            $this->add($ref, $amt, 'referral_signup', 'user', (int) $buyer['id'], '추천 회원 승인: ' . ($buyer['company_name'] ?: $buyer['name']));
        }
    }

    /** 주문 입금 확인(paid) 시: 추천인 첫 주문 포인트 + 구매 적립. */
    public function onOrderPaid(array $order): void
    {
        $buyer = $this->users->find((int) $order['user_id']);
        if (! $buyer) {
            return;
        }
        // 첫 주문 → 추천인
        if (empty($buyer['first_order_at'])) {
            $this->users->update((int) $buyer['id'], ['first_order_at' => date('Y-m-d H:i:s')]);
            $ref = (int) ($buyer['referred_by'] ?? 0);
            $amt = (int) mm_setting('points_referral_first_order', 0);
            if ($ref > 0 && $amt > 0 && ! $this->exists($ref, 'referral_first_order', 'user', (int) $buyer['id'])) {
                $this->add($ref, $amt, 'referral_first_order', 'user', (int) $buyer['id'], '추천 회원 첫 주문: ' . ($buyer['company_name'] ?: $buyer['name']));
            }
        }
        // 구매 적립(%)
        $rate = (float) mm_setting('points_reward_rate', 0);
        if ($rate > 0 && ! $this->exists((int) $buyer['id'], 'order_reward', 'order', (int) $order['id'])) {
            $reward = (int) floor(((int) $order['subtotal'] - (int) $order['points_used']) * $rate / 100);
            if ($reward > 0) {
                $this->add((int) $buyer['id'], $reward, 'order_reward', 'order', (int) $order['id'], '구매 적립 ' . $order['order_no']);
            }
        }
    }

    /** 주문 취소 시 사용 포인트 환원. */
    public function onOrderCancelled(array $order): void
    {
        $used = (int) $order['points_used'];
        if ($used > 0 && ! $this->exists((int) $order['user_id'], 'order_refund', 'order', (int) $order['id'])) {
            $this->add((int) $order['user_id'], $used, 'order_refund', 'order', (int) $order['id'], '주문 취소 환원 ' . $order['order_no']);
        }
    }
}
