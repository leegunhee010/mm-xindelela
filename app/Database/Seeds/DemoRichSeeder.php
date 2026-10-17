<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * 테스트용 가짜 데이터 (DemoSeeder 다음에 실행).
 * 회원 36명(인증 단계별)·추천 관계·주문 약 140건(단계별)·장바구니·문의·포인트·담당자 2명·작업 기록·공급사/상품 추가.
 * 이름·상호는 모두 지어낸 것이며 이메일은 @demo.mm. 한 번만 실행된다(settings.demo_rich_seeded).
 * 실행: php spark db:seed DemoRichSeeder
 */
class DemoRichSeeder extends Seeder
{
    private int $t0;

    public function run(): void
    {
        helper('mm');
        $db = $this->db;
        if ($db->table('settings')->where('skey', 'demo_rich_seeded')->countAllResults()) {
            echo "이미 들어가 있습니다.\n";
            return;
        }
        mt_srand(20261017);
        $this->t0 = time();
        $at = fn (int $daysAgo, int $h = 0) => date('Y-m-d H:i:s', $this->t0 - $daysAgo * 86400 - $h * 3600 - mt_rand(0, 3000));

        // ── 담당자 2명 ──
        $staffPw = getenv('STAFF_PASSWORD') ?: (getenv('ADMIN_PASSWORD') ?: 'staff1234');
        $owner   = $db->table('users')->where('email', 'admin@xindelela.com')->get()->getRowArray();
        $staff   = [['id' => (int) $owner['id'], 'name' => $owner['name']]];
        foreach ([['staff1@demo.mm', 'Ma Thida (데모)'], ['staff2@demo.mm', 'Ko Min (데모)']] as [$em, $nm]) {
            $db->table('users')->insert(['role' => 'admin', 'admin_level' => 'staff', 'email' => $em, 'password_hash' => password_hash($staffPw, PASSWORD_DEFAULT),
                'name' => $nm, 'status' => 'approved', 'created_at' => $at(110), 'updated_at' => $at(110)]);
            $staff[] = ['id' => (int) $db->insertID(), 'name' => $nm];
        }
        $pickStaff = fn () => $staff[mt_rand(0, count($staff) - 1)];
        $logs = [];
        $log  = function (string $when, string $action, string $type, ?int $id, string $sum) use (&$logs, $pickStaff) {
            $s = $pickStaff();
            $logs[] = ['admin_id' => $s['id'], 'admin_name' => $s['name'], 'action' => $action, 'target_type' => $type, 'target_id' => $id, 'summary' => $sum, 'ip' => '127.0.0.1', 'created_at' => $when];
        };

        // ── 공급사·상품 추가 ──
        $cats = array_column($db->table('categories')->get()->getResultArray(), 'id');
        $catMap = [0, 1, 1, 2, 2, 2, 2, 2];
        $supIds = [(int) $db->table('suppliers')->get()->getRowArray()['id']];
        foreach ([['한빛식품 (데모)', '한빛', '라면·김·간식류 수출 전문 (데모 공급사)'], ['청솔생활 (데모)', '청솔', '주방·생활용품 제조 (데모 공급사)'], ['모아뷰티 (데모)', '모아', '기초 화장품 OEM (데모 공급사)']] as [$cn, $br, $intro]) {
            $db->table('suppliers')->insert(['user_id' => 0, 'company_name' => $cn, 'brand' => $br, 'intro' => $intro, 'contact_name' => '담당자', 'phone' => '010-0000-0000',
                'commission_rate' => 10, 'status' => 'approved', 'created_at' => $at(100), 'updated_at' => $at(100)]);
            $supIds[] = (int) $db->insertID();
            $log($at(100), 'supplier_create', 'supplier', (int) $db->insertID(), '공급사 ' . $cn . ' 정보 저장');
        }
        $more = [
            ['쌀과자 20봉 (데모)', 1, 8000, 14000, 20, 1, 'p5'], ['떡볶이 소스 500g (데모)', 1, 6500, 11000, 24, 1, 'p6'], ['보리차 티백 50입 (데모)', 1, 9000, 15000, 12, 1, 'p8'],
            ['수세미 10입 (데모)', 3, 3000, 6000, 40, 2, 'p9'], ['밀폐용기 4종 (데모)', 4, 15000, 26000, 6, 2, 'p11'], ['실리콘 주걱 (데모)', 4, 4500, 8000, 24, 2, 'p11'],
            ['핸드크림 50ml (데모)', 0, 5000, 9000, 24, 3, 'p1'], ['클렌징폼 150ml (데모)', 0, 8000, 14000, 24, 3, 'p2'], ['립밤 3입 (데모)', 0, 6000, 10000, 30, 3, 'p4'],
            ['아기 물티슈 80매 (데모)', 6, 3500, 6000, 36, 2, 'p10'], ['보조배터리 10000mAh (데모)', 7, 28000, 45000, 6, 0, 'p12'], ['면장갑 10켤레 (데모)', 5, 5500, 9500, 20, 2, 'p12'],
        ];
        foreach ($more as $i => [$name, $ci, $price, $retail, $moq, $si, $img]) {
            $st = $i === 10 ? 'hidden' : ($i === 11 ? 'draft' : 'active');
            $db->table('products')->insert([
                'supplier_id' => $supIds[$si], 'category_id' => $cats[$catMap[$ci]] ?? null, 'code' => 'DEMO-' . str_pad((string) (13 + $i), 3, '0', STR_PAD_LEFT),
                'name' => $name, 'summary' => '테스트용 데모 상품', 'thumb' => 'assets/img/demo/' . $img . '.jpg', 'images' => json_encode(['assets/img/demo/' . $img . '.jpg']),
                'price' => $price, 'retail_price' => $retail, 'moq' => $moq, 'tiers' => json_encode([['min_qty' => $moq * 3, 'price' => (int) round($price * 0.93 / 100) * 100]]),
                'unit' => '개', 'stock' => [12, 30, 900, 450, 8, 220, 640, 300, 75, 500, 40, 100][$i], 'origin' => '한국', 'brand' => explode(' ', $name)[0] === '보조배터리' ? '파워온' : '데모브랜드',
                'status' => $st, 'is_best' => $i % 4 === 0 ? 1 : 0, 'views' => mt_rand(10, 400), 'created_at' => $at(95 - $i * 3), 'updated_at' => $at(20),
            ]);
            $log($at(95 - $i * 3), 'product_create', 'product', (int) $db->insertID(), '상품 등록: ' . $name);
        }
        $products = $db->table('products')->where('status', 'active')->get()->getResultArray();

        // ── 구매자 36명 ──
        $first = ['Aung', 'Kyaw', 'Thida', 'Nilar', 'Zaw', 'Hnin', 'Myo', 'Su', 'Ei', 'Win', 'Thandar', 'Khin', 'Htet', 'Phyo', 'Yadanar', 'Naing'];
        $shops = ['Golden Lotus', 'Shwe Pyi', 'Bright Star', 'Happy Basket', 'Green Leaf', 'Royal Pearl', 'Sunrise', 'Moonlight', 'Lucky Seven', 'Blue Ocean', 'Silver Moon', 'Pyae Sone',
                  'New Life', 'Thu Kha', 'Ruby', 'Jasmine', 'Rainbow', 'Mingalar', 'Aye Mya', 'Daily Fresh', 'Smile', 'Ever Green', 'Shine', 'Lotus Garden', 'Ocean Breeze', 'Myat Noe',
                  'Family', 'Star Light', 'Diamond', 'Cherry', 'Lemon Tree', 'Orchid', 'Phoenix', 'Unity', 'Treasure', 'Harmony'];
        $kinds = ['Cosmetics', 'Mart', 'Store', 'Shop', 'Online', 'Trading'];
        $cities = ['Yangon', 'Yangon', 'Yangon', 'Mandalay', 'Mandalay', 'Naypyidaw', 'Bago', 'Mawlamyine', 'Taunggyi', 'Pathein'];
        $vt = ['offline', 'online', 'network', 'offline,online', 'online,network'];
        $rejects = ['판매 활동을 확인할 수 있는 자료가 없습니다. 매장 사진, 판매 글 캡처, 거래처 대화 캡처 중 하나를 올려 주세요.', '사진·캡처가 흐려서 내용을 확인할 수 없습니다. 선명한 자료로 다시 올려 주세요.'];
        $buyers = [];
        foreach ($shops as $i => $sh) {
            $status = $i < 24 ? 'approved' : ($i < 30 ? 'pending' : ($i < 34 ? 'rejected' : 'suspended'));
            $created = $status === 'pending' ? mt_rand(0, 3) : mt_rand(8, 118);
            $refBy = null;
            if ($i > 3 && mt_rand(1, 100) <= 45) {
                $cand = array_values(array_filter($buyers, fn ($b) => $b['status'] === 'approved' && $b['days'] > $created));
                if ($cand) {
                    $refBy = $cand[min(count($cand) - 1, (int) floor(pow(mt_rand(0, 1000) / 1000, 2) * count($cand)))]['id'];
                }
            }
            $name = $first[$i % count($first)] . ' ' . $first[($i * 7 + 3) % count($first)];
            $email = 'shop' . ($i + 1) . '@demo.mm';
            $db->table('users')->insert([
                'role' => 'buyer', 'email' => $email, 'password_hash' => password_hash('buyer1234', PASSWORD_DEFAULT), 'name' => $name,
                'phone' => '09-' . mt_rand(400, 999) . '-' . mt_rand(100, 999) . '-' . mt_rand(100, 999), 'status' => $status,
                'company_name' => $sh . ' ' . $kinds[$i % count($kinds)] . ' (Demo)', 'business_type' => 'both', 'verify_types' => $vt[$i % count($vt)],
                'fb_page' => $i % 3 === 0 ? '' : 'https://example.com/demo-shop-' . ($i + 1), 'city' => $cities[$i % count($cities)], 'address' => 'No.' . mt_rand(1, 200) . ', Demo St',
                'doc_files' => '[]', 'reject_reason' => $status === 'rejected' ? $rejects[$i % 2] : null, 'referral_code' => strtoupper(substr(md5($email . 'r'), 0, 8)),
                'referred_by' => $refBy, 'points_balance' => 0, 'approved_at' => in_array($status, ['approved', 'suspended'], true) ? $at($created - 1) : null,
                'last_login_at' => $at(mt_rand(0, 10)), 'created_at' => $at($created), 'updated_at' => $at($created),
            ]);
            $id = (int) $db->insertID();
            $buyers[] = ['id' => $id, 'status' => $status, 'days' => $created, 'ref' => $refBy, 'name' => $sh];
            if ($status === 'approved') {
                $log($at($created - 1), 'buyer_approve', 'buyer', $id, '회원 ' . $sh . ' 비즈니스 인증 완료(승인)');
            } elseif ($status === 'rejected') {
                $log($at(max(0, $created - 1)), 'buyer_reject', 'buyer', $id, '회원 ' . $sh . ' 반려: ' . $rejects[$i % 2]);
            }
        }

        // ── 주문 ──
        $events = []; // 포인트 이벤트 [time, user, amount, type, ref_type, ref_id, memo]
        foreach ($buyers as $b) {
            if ($b['ref'] && $b['status'] === 'approved') {
                $events[] = [$at($b['days'] - 1), $b['ref'], 5000, 'referral_signup', 'user', $b['id'], '추천 회원 승인: ' . $b['name']];
            }
        }
        $approved = array_values(array_filter($buyers, fn ($b) => $b['status'] === 'approved'));
        $orderIds = [];
        $firstPaid = [];
        $names = ['Mg Mg', 'Ko Aung', 'Ma Su', 'Daw Hla', 'U Tun', 'Ma Ei', 'Ko Zaw', 'Ma Nilar'];
        for ($n = 0; $n < 140; $n++) {
            $days = (int) floor(pow(mt_rand(0, 1000) / 1000, 0.65) * 110); // 최근일수록 많게
            $days = 110 - $days;
            $b = $approved[mt_rand(0, count($approved) - 1)];
            if ($b['days'] < $days) {
                $days = mt_rand(0, max(0, $b['days'] - 1));
            }
            $created = $at($days, mt_rand(0, 10));
            // 상태: 오래된 주문은 대부분 완료
            $r = mt_rand(1, 100);
            if ($days > 14) {
                $st = $r <= 90 ? 'delivered' : 'cancelled';
            } elseif ($days > 7) {
                $st = $r <= 85 ? 'delivered' : ($r <= 93 ? 'cancelled' : 'shipped');
            } else {
                $st = $r <= 30 ? 'requested' : ($r <= 55 ? 'proof' : ($r <= 75 ? 'paid' : ($r <= 92 ? 'shipped' : 'cancelled')));
            }
            $items = [];
            $sub = 0;
            $pick = array_rand($products, mt_rand(1, 3));
            foreach ((array) $pick as $pi) {
                $p = $products[$pi];
                $qty = (int) $p['moq'] * mt_rand(1, 4) + (mt_rand(0, 1) ? mt_rand(0, (int) $p['moq']) : 0);
                $unit = mm_unit_price($p, $qty);
                $items[] = ['p' => $p, 'qty' => $qty, 'unit' => $unit];
                $sub += $unit * $qty;
            }
            $pts = mt_rand(1, 100) <= 12 ? 1000 * mt_rand(1, 5) : 0;
            $no = date('ymd', strtotime($created)) . '-' . strtoupper(substr(md5((string) $n . $created), 0, 5));
            $h = fn ($d) => date('Y-m-d H:i:s', strtotime($created) + $d * 3600);
            $paidLike = in_array($st, ['paid', 'shipped', 'delivered'], true);
            $eta = date('Y-m-d', strtotime($created) + 30 * 3600 + 4 * 86400);
            $db->table('orders')->insert([
                'order_no' => $no, 'user_id' => $b['id'], 'status' => $st, 'payment_method' => 'kbzpay', 'subtotal' => $sub, 'shipping_fee' => 0, 'points_used' => $pts, 'total' => $sub - $pts,
                'ship_name' => $names[$n % count($names)], 'ship_phone' => '09-' . mt_rand(400, 999) . '-' . mt_rand(100, 999) . '-' . mt_rand(100, 999),
                'ship_city' => $cities[$n % count($cities)], 'ship_address' => 'No.' . mt_rand(1, 300) . ', Demo Rd',
                'payment_note' => in_array($st, ['proof', 'paid', 'shipped', 'delivered'], true) ? $names[mt_rand(0, count($names) - 1)] : null,
                'proof_at' => $st !== 'requested' && $st !== 'cancelled' ? $h(3) : null, 'paid_at' => $paidLike ? $h(6) : null,
                'shipped_at' => in_array($st, ['shipped', 'delivered'], true) ? $h(30) : null, 'delivered_at' => $st === 'delivered' ? $h(100) : null,
                'cancelled_at' => $st === 'cancelled' ? $h(20) : null, 'reward_granted' => $paidLike ? 1 : 0,
                'tracking' => in_array($st, ['shipped', 'delivered'], true) ? '택배 · Royal Express · 송장 RX' . mt_rand(100000, 999999) . ' · 도착 예정 ' . $eta : null,
                'ship_info' => in_array($st, ['shipped', 'delivered'], true) ? json_encode(['method' => 'courier', 'carrier' => 'Royal Express', 'tracking_no' => 'RX' . mt_rand(100000, 999999), 'tracking_url' => '', 'sent_on' => date('Y-m-d', strtotime($h(30))), 'eta' => $eta, 'note' => ''], JSON_UNESCAPED_UNICODE) : null,
                'created_at' => $created, 'updated_at' => $created,
            ]);
            $oid = (int) $db->insertID();
            $orderIds[] = $oid;
            foreach ($items as $it) {
                $db->table('order_items')->insert(['order_id' => $oid, 'product_id' => $it['p']['id'], 'supplier_id' => $it['p']['supplier_id'], 'product_name' => $it['p']['name'],
                    'thumb' => $it['p']['thumb'], 'unit_price' => $it['unit'], 'qty' => $it['qty'], 'line_total' => $it['unit'] * $it['qty'],
                    'item_status' => in_array($st, ['shipped', 'delivered'], true) ? 'shipped' : 'pending', 'created_at' => $created, 'updated_at' => $created]);
                if ($paidLike) {
                    $db->table('products')->where('id', $it['p']['id'])->set('order_count', 'order_count + 1', false)->update();
                }
            }
            if ($pts) {
                $events[] = [$created, $b['id'], -$pts, 'order_use', 'order', $oid, '주문 사용 ' . $no];
                if ($st === 'cancelled') {
                    $events[] = [$h(20), $b['id'], $pts, 'order_refund', 'order', $oid, '주문 취소 환원 ' . $no];
                }
            }
            if ($paidLike) {
                $log($h(6), 'order_paid', 'order', $oid, "주문 {$no} 입금 확인");
                if (! isset($firstPaid[$b['id']]) || $firstPaid[$b['id']] > $h(6)) {
                    $firstPaid[$b['id']] = $h(6);
                }
            }
            if (in_array($st, ['shipped', 'delivered'], true)) {
                $log($h(30), 'order_shipped', 'order', $oid, "주문 {$no} 출고(배송중)");
            }
            if ($st === 'delivered') {
                $log($h(100), 'order_delivered', 'order', $oid, "주문 {$no} 배송 완료");
            }
            if ($st === 'cancelled') {
                $log($h(20), 'order_cancelled', 'order', $oid, "주문 {$no} 주문 취소");
            }
        }
        foreach ($firstPaid as $uid => $when) {
            $db->table('users')->where('id', $uid)->update(['first_order_at' => $when]);
            $u = array_values(array_filter($buyers, fn ($b) => $b['id'] === $uid))[0] ?? null;
            if ($u && $u['ref']) {
                $events[] = [$when, $u['ref'], 10000, 'referral_first_order', 'user', $uid, '추천 회원 첫 주문: ' . $u['name']];
            }
        }
        // 포인트: 시간순으로 잔액 계산 (모자라면 사용분은 건너뜀)
        usort($events, fn ($x, $y) => strcmp($x[0], $y[0]));
        $bal = [];
        $skipped = [];
        foreach ($events as [$when, $uid, $amt, $type, $rt, $rid, $memo]) {
            $cur = $bal[$uid] ?? 0;
            if ($type === 'order_refund' && isset($skipped[$rid])) {
                continue;
            }
            if ($cur + $amt < 0) {
                // 잔액이 없던 회원의 포인트 사용은 없던 일로 (주문 금액도 맞춤)
                if ($type === 'order_use') {
                    $skipped[$rid] = true;
                    $db->table('orders')->where('id', $rid)->set('points_used', 0)->set('total', 'subtotal', false)->update();
                }
                continue;
            }
            $bal[$uid] = $cur + $amt;
            $db->table('point_logs')->insert(['user_id' => $uid, 'type' => $type, 'amount' => $amt, 'balance_after' => $bal[$uid], 'ref_type' => $rt, 'ref_id' => $rid, 'memo' => $memo, 'created_at' => $when]);
        }
        foreach ($bal as $uid => $v) {
            $db->table('users')->where('id', $uid)->update(['points_balance' => $v]);
        }

        // ── 장바구니·문의 ──
        foreach (array_slice($approved, 0, 14) as $k => $b) {
            foreach ((array) array_rand($products, mt_rand(1, 3)) as $pi) {
                $db->table('carts')->insert(['user_id' => $b['id'], 'product_id' => $products[$pi]['id'], 'qty' => (int) $products[$pi]['moq'] * mt_rand(1, 3), 'created_at' => $at(mt_rand(0, 6)), 'updated_at' => $at(0)]);
            }
        }
        $qs = ['이 상품 100박스 이상이면 단가 조정이 되나요?', '유통기한이 얼마나 남은 제품으로 오나요?', '샘플 1개만 먼저 받아볼 수 있나요?', '만달레이까지 배송은 며칠 걸리나요?', '다른 색상도 있나요?',
               '입금했는데 확인 요청을 깜빡했어요. 어떻게 하나요?', '세금계산서(영수증) 발행 되나요?', '다음 입고는 언제인가요?', '박스 포장 단위가 어떻게 되나요?', '주문 후 배송지를 바꿀 수 있나요?'];
        $as = ['300박스 이상부터 별도 단가로 안내드립니다. Viber로 연락 주세요.', '제조일로부터 6개월 이내 제품으로 출고됩니다.', '샘플은 최소 수량 주문 시 함께 넣어 드립니다.', '보통 입금 확인 후 3~5일 걸립니다.',
               '현재는 사진의 색상만 있습니다.', '주문 상세에서 송금자 이름을 적고 입금 확인 요청을 눌러 주세요.'];
        foreach ($qs as $k => $q) {
            $b = $approved[($k * 5) % count($approved)];
            $answered = $k < count($as);
            $db->table('inquiries')->insert(['user_id' => $b['id'], 'product_id' => $products[$k % count($products)]['id'], 'body' => $q, 'answer' => $answered ? $as[$k] : null,
                'answered_at' => $answered ? $at($k) : null, 'created_at' => $at($k + 1), 'updated_at' => $at($k)]);
            if ($answered) {
                $log($at($k), 'inquiry_answer', 'inquiry', (int) $db->insertID(), '문의 답변 #' . $db->insertID());
            }
        }

        // ── 작업 기록 ──
        usort($logs, fn ($x, $y) => strcmp($x['created_at'], $y['created_at']));
        foreach (array_chunk($logs, 100) as $chunk) {
            $db->table('admin_logs')->insertBatch($chunk);
        }
        $db->table('settings')->insert(['skey' => 'demo_rich_seeded', 'svalue' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        echo '가짜 데이터: 회원 ' . count($buyers) . '명, 주문 ' . count($orderIds) . '건, 작업 기록 ' . count($logs) . "건\n";
    }
}
