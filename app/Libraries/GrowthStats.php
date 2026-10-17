<?php

namespace App\Libraries;

use App\Models\OrderModel;
use App\Models\UserModel;

/**
 * 대시보드 성장 지표: 신규 가입(구매자) · 주문 수 · 주문 금액.
 * 기간 단위(일·주·월)로 묶고, 같은 길이의 직전 기간과 비교해 성장률을 낸다.
 * DB 함수 차이(MySQL/SQLite)를 피하려고 묶음은 PHP에서 한다.
 */
class GrowthStats
{
    /** 입금 확인 이후 상태 = 실제 매출로 본다. */
    public const PAID = ['paid', 'preparing', 'shipped', 'delivered'];

    public const RANGES = [
        'd' => ['label' => '최근 30일', 'unit' => '일', 'n' => 30],
        'w' => ['label' => '최근 12주', 'unit' => '주', 'n' => 12],
        'm' => ['label' => '최근 12개월', 'unit' => '월', 'n' => 12],
    ];

    public static function build(string $range): array
    {
        $range = isset(self::RANGES[$range]) ? $range : 'd';
        $n     = self::RANGES[$range]['n'];

        // 이번 기간 n칸 + 직전 기간 n칸
        $keys = [];
        $cur  = self::bucketStart(new \DateTimeImmutable('today'), $range);
        for ($i = 2 * $n - 1; $i >= 0; $i--) {
            $keys[] = self::shift($cur, $range, -$i);
        }
        $from = $keys[0]->format('Y-m-d 00:00:00');
        $idx  = [];
        foreach ($keys as $i => $k) {
            $idx[$k->format('Y-m-d')] = $i;
        }
        $signups = $orders = $amount = array_fill(0, 2 * $n, 0);

        $users = (new UserModel())->select('created_at')->where('role', 'buyer')->where('created_at >=', $from)->findAll();
        foreach ($users as $u) {
            $i = self::slot($u['created_at'], $range, $idx);
            if ($i !== null) {
                $signups[$i]++;
            }
        }
        $rows = (new OrderModel())->select('created_at, status, total')->where('created_at >=', $from)->where('status !=', 'cancelled')->findAll();
        foreach ($rows as $o) {
            $i = self::slot($o['created_at'], $range, $idx);
            if ($i === null) {
                continue;
            }
            $orders[$i]++;
            if (in_array($o['status'], self::PAID, true)) {
                $amount[$i] += (int) $o['total'];
            }
        }

        $labels = [];
        foreach (array_slice($keys, $n) as $k) {
            $labels[] = $range === 'm' ? $k->format('Y.m') : ($range === 'w' ? $k->format('m.d') . '주' : $k->format('m.d'));
        }
        $metric = static function (array $series) use ($n): array {
            $now  = array_sum(array_slice($series, $n));
            $prev = array_sum(array_slice($series, 0, $n));
            return [
                'series' => array_slice($series, $n),
                'total'  => $now,
                'prev'   => $prev,
                'growth' => $prev > 0 ? round(($now - $prev) / $prev * 100, 1) : null,
            ];
        };

        return [
            'range'   => $range,
            'ranges'  => self::RANGES,
            'labels'  => $labels,
            'signups' => $metric($signups),
            'orders'  => $metric($orders),
            'amount'  => $metric($amount),
        ];
    }

    private static function bucketStart(\DateTimeImmutable $d, string $range): \DateTimeImmutable
    {
        if ($range === 'm') {
            return $d->modify('first day of this month')->setTime(0, 0);
        }
        if ($range === 'w') {
            return $d->modify('monday this week')->setTime(0, 0);
        }
        return $d->setTime(0, 0);
    }

    private static function shift(\DateTimeImmutable $d, string $range, int $by): \DateTimeImmutable
    {
        $u = ['d' => 'day', 'w' => 'week', 'm' => 'month'][$range];
        return $d->modify(($by >= 0 ? '+' : '') . $by . ' ' . $u);
    }

    private static function slot(?string $dt, string $range, array $idx): ?int
    {
        if (! $dt) {
            return null;
        }
        $k = self::bucketStart(new \DateTimeImmutable(substr($dt, 0, 10)), $range)->format('Y-m-d');
        return $idx[$k] ?? null;
    }
}
