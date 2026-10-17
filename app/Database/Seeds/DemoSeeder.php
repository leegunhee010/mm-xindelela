<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * 초기 데이터: 관리자·설정·카테고리·데모 공급사/상품·공지·배너.
 * 재실행 안전(이미 있으면 건너뜀).
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        // ── 설정 ──
        $settings = [
            'site_name'                   => 'XINDELELA 미얀마 도매몰',
            'currency_symbol'             => 'Ks',
            'currency_position'           => 'after',
            'points_signup_bonus'         => '0',
            'points_referral_signup'      => '5000',
            'points_referral_first_order' => '10000',
            'points_reward_rate'          => '0',
            'points_use_max_pct'          => '30',
            'points_min_use'              => '1000',
            'kbzpay_account'              => 'KBZPay 09891257296 (PARK CHANG MIN)',
            'cod_enabled'                 => '0',
            'verify_sla_text'             => '영업일 기준 1일 이내 검수',
            'cs_phone'                    => '09-XXX-XXX-XXX',
            'cs_viber'                    => '09-XXX-XXX-XXX',
            'cs_email'                    => 'mm@xindelela.com',
            'topbar_text'                 => '신규 가입 사업자 인증은 영업일 기준 1일 이내 완료됩니다.',
        ];
        foreach ($settings as $k => $v) {
            if (! $db->table('settings')->where('skey', $k)->countAllResults()) {
                $db->table('settings')->insert(['skey' => $k, 'svalue' => $v, 'updated_at' => $now]);
            }
        }

        // ── 관리자 ──
        if (! $db->table('users')->where('email', 'admin@xindelela.com')->countAllResults()) {
            $db->table('users')->insert([
                'role' => 'admin', 'email' => 'admin@xindelela.com',
                'password_hash' => password_hash('admin1234', PASSWORD_DEFAULT),
                'name' => '관리자', 'status' => 'approved', 'approved_at' => $now,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // ── 카테고리 ──
        $cats = ['화장품·스킨케어', '식품·음료', '건강식품', '생활용품', '주방용품', '패션·잡화', '유아동', '전자·소형가전'];
        $catIds = [];
        foreach ($cats as $i => $name) {
            $row = $db->table('categories')->where('name', $name)->get()->getRowArray();
            if (! $row) {
                $db->table('categories')->insert(['name' => $name, 'slug' => 'c' . ($i + 1), 'sort' => $i + 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now]);
                $catIds[] = (int) $db->insertID();
            } else {
                $catIds[] = (int) $row['id'];
            }
        }

        // ── 데모 공급사 ──
        $supUserId = null;
        $row = $db->table('users')->where('email', 'supplier@demo.kr')->get()->getRowArray();
        if (! $row) {
            $db->table('users')->insert([
                'role' => 'supplier', 'email' => 'supplier@demo.kr',
                'password_hash' => password_hash('supplier1234', PASSWORD_DEFAULT),
                'name' => '김공급', 'phone' => '010-0000-0000', 'status' => 'approved', 'approved_at' => $now,
                'company_name' => '케이뷰티 코리아(데모)', 'created_at' => $now, 'updated_at' => $now,
            ]);
            $supUserId = (int) $db->insertID();
            $db->table('suppliers')->insert([
                'user_id' => $supUserId, 'company_name' => '케이뷰티 코리아(데모)', 'brand' => 'K-BEAUTY',
                'intro' => '한국 화장품·생활용품 수출 전문. 미얀마 도매 공급 데모 계정입니다.',
                'contact_name' => '김공급', 'phone' => '010-0000-0000', 'address' => '서울시 광진구',
                'status' => 'approved', 'created_at' => $now, 'updated_at' => $now,
            ]);
        } else {
            $supUserId = (int) $row['id'];
        }
        $sup = $db->table('suppliers')->where('user_id', $supUserId)->get()->getRowArray();
        $supplierId = (int) $sup['id'];

        // ── 데모 구매자 (승인/대기 각 1) ──
        foreach ([
            ['buyer@demo.mm', '승인구매자', 'Yangon Beauty Shop', 'approved'],
            ['pending@demo.mm', '대기구매자', 'Mandalay Mart', 'pending'],
        ] as [$email, $name, $company, $status]) {
            if (! $db->table('users')->where('email', $email)->countAllResults()) {
                $db->table('users')->insert([
                    'role' => 'buyer', 'email' => $email, 'password_hash' => password_hash('buyer1234', PASSWORD_DEFAULT),
                    'name' => $name, 'phone' => '09-123456789', 'viber' => '09-123456789', 'status' => $status,
                    'company_name' => $company, 'business_type' => 'both', 'fb_page' => 'https://facebook.com/' . strtolower(str_replace(' ', '', $company)),
                    'city' => 'Yangon', 'address' => 'No.1, Pyay Rd', 'referral_code' => strtoupper(substr(md5($email), 0, 8)),
                    'approved_at' => $status === 'approved' ? $now : null, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        // ── 데모 상품 ──
        if (! $db->table('products')->countAllResults()) {
            $demo = [
                ['수분 진정 토너 300ml', 0, 18000, 32000, 12, [[36, 17000], [72, 16000]], 'K-BEAUTY'],
                ['비타민C 세럼 50ml', 0, 25000, 45000, 12, [[36, 23500]], 'K-BEAUTY'],
                ['마스크팩 10매입 (수분)', 0, 9000, 15000, 24, [[48, 8500], [120, 8000]], 'K-BEAUTY'],
                ['선크림 SPF50+ 50ml', 0, 14000, 25000, 12, [[36, 13000]], 'K-BEAUTY'],
                ['김 스낵 5g×12봉', 1, 7000, 12000, 20, [[60, 6500]], '바다향'],
                ['불닭맛 컵라면 12입', 1, 16000, 26000, 6, [[24, 15000]], '코리아푸드'],
                ['홍삼 스틱 10포', 2, 30000, 52000, 10, [[30, 28000]], '진생원'],
                ['콜라겐 젤리 30포', 2, 22000, 38000, 10, [[30, 20500]], '뷰티랩'],
                ['주방 세제 1L (레몬)', 3, 5000, 9000, 24, [[48, 4700]], '클린홈'],
                ['항균 칫솔 4입', 3, 4000, 7500, 30, [[90, 3700]], '클린홈'],
                ['스테인리스 보온병 500ml', 4, 12000, 22000, 12, [[36, 11000]], '키친프로'],
                ['양말 5족 세트', 5, 6000, 11000, 20, [[60, 5500]], '삭스랩'],
            ];
            $colors = ['#E8F5D8', '#FFE9D6', '#FFF3C4', '#DCEBFF', '#F1E4FF', '#FFE0E6', '#E0F7F4', '#ECECEC'];
            $dir    = FCPATH . 'assets/img/demo';
            if (! is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            foreach ($demo as $i => [$name, $ci, $price, $retail, $moq, $tiers, $brand]) {
                $file = 'assets/img/demo/p' . ($i + 1) . '.svg';
                $jpg  = 'assets/img/demo/p' . ($i + 1) . '.jpg'; // 임시 스톡 사진이 있으면 우선 사용
                if (is_file(FCPATH . $jpg)) {
                    $file = $jpg;
                }
                $svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600">'
                      . '<rect width="600" height="600" fill="' . $colors[$ci % 8] . '"/>'
                      . '<rect x="150" y="120" width="300" height="360" rx="24" fill="#fff" opacity=".85"/>'
                      . '<text x="300" y="320" font-family="Malgun Gothic,sans-serif" font-size="28" fill="#1b1b1b" text-anchor="middle">' . htmlspecialchars($name) . '</text>'
                      . '<text x="300" y="360" font-family="Malgun Gothic,sans-serif" font-size="18" fill="#646464" text-anchor="middle">' . htmlspecialchars($brand) . ' · 샘플 이미지</text></svg>';
                if (! is_file(FCPATH . $file)) {
                    file_put_contents(FCPATH . $file, $svg);
                }
                $db->table('products')->insert([
                    'supplier_id' => $supplierId, 'category_id' => $catIds[$ci], 'code' => 'DEMO-' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                    'name' => $name, 'summary' => $brand . ' 정품. 박스 단위 도매 공급.',
                    'description' => '<p>' . esc($name) . ' 상세 설명입니다. 공급사가 상품 등록 시 상세 내용(HTML)을 입력합니다.</p><ul><li>제조국: 한국</li><li>유통기한: 제조일로부터 24개월</li><li>박스 입수: ' . $moq . '개</li></ul>',
                    'thumb' => $file, 'images' => json_encode([$file]),
                    'price' => $price, 'retail_price' => $retail, 'moq' => $moq,
                    'tiers' => json_encode(array_map(static fn ($t) => ['min_qty' => $t[0], 'price' => $t[1]], $tiers)),
                    'unit' => '개', 'stock' => 500 + $i * 37, 'origin' => '한국', 'brand' => $brand,
                    'status' => 'active', 'is_best' => $i % 3 === 0 ? 1 : 0, 'views' => 120 - $i * 7,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        // ── 공지 ──
        if (! $db->table('notices')->countAllResults()) {
            $db->table('notices')->insertBatch([
                ['title' => '사업자 인증 안내: 가입 후 영업일 기준 1일 이내 승인', 'body' => "<p>오프라인 매장, 온라인 판매, 판매 네트워크 중 하나만 증빙하면 됩니다. 매장 사진, 판매 글 캡처, 거래처 대화 캡처 등 사진·캡처도 인정하며, 담당자가 직접 확인한 뒤 승인합니다.</p><p>승인 전에는 상품은 보실 수 있지만 도매가와 주문 신청은 제한됩니다.</p>", 'is_pinned' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['title' => '결제 안내: 주문 신청 후 KBZPay 선불', 'body' => "<p>주문 신청 → 담당자가 배송비 포함 최종 금액 안내 → KBZPay 송금 → 입금 캡처 업로드 → 입금 확인 후 발주·배송.</p>", 'is_pinned' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['title' => '친구 소개 포인트 안내', 'body' => "<p>마이페이지의 추천 코드를 공유하세요. 소개한 회원이 승인되면 포인트가 적립되고, 그 회원이 첫 주문을 하면 추가 적립됩니다.</p>", 'is_pinned' => 0, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        // ── 배너 ──
        if (! $db->table('banners')->countAllResults()) {
            $db->table('banners')->insertBatch([
                ['title' => "한국 정품 K-브랜드\n박스 단위 도매가로", 'subtitle' => '미얀마 소매상·온라인 셀러 전용 폐쇄몰', 'image' => 'assets/img/demo/banner_1.jpg', 'bg' => '#E8F5D8', 'link' => '/products', 'sort' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['title' => "친구를 소개하면\n포인트 적립", 'subtitle' => '추천 회원 승인 + 첫 주문 시 2회 적립', 'image' => 'assets/img/demo/banner_2.jpg', 'bg' => '#FFF3C4', 'link' => '/mypage/referral', 'sort' => 2, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['title' => "가입 후 영업일 1일\n사업자 인증 완료", 'subtitle' => '매장·온라인·판매망 중 하나만 증빙하면 끝', 'image' => 'assets/img/demo/banner_3.jpg', 'bg' => '#DCEBFF', 'link' => '/register', 'sort' => 3, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }
    }
}
