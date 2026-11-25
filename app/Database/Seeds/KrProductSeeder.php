<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * 실제 판매 상품 (2026-11-22 사용자 지시).
 *  - 한국 사입 품목 8개: Documents\1106_미얀마_한국사입품목.xlsx (평창팜 6·한국제다 2)
 *  - 요뽀끼 컵떡볶이 4종: 요뽀끼 공식 스토어 (스마트스토어 yopokki 5811376855 와 같은 상품, SSG 공식 스토어 페이지에서 사진·상세)
 *
 * 단위 = 개, 최소 1개부터. 개당 가격(Ks) = 사입가(원) × 배수 × 3.74
 *   1개~ : × 4.3  /  5개 이상 : × 4  /  1박스(입수) 이상 : × 4
 * 요뽀끼는 사입가를 모르므로 판매가 기준 2배수 → 사입가 = 판매가 ÷ 2 로 두고 같은 표를 쓴다.
 * 판매페이지 사진·상세는 scripts/slice-kr-details.php 가 만든 kr-details.json.
 * 상품 코드가 이미 있으면 사진·상세·가격을 갱신한다(여러 번 돌려도 안전).
 */
class KrProductSeeder extends Seeder
{
    private const KRW_TO_MMK = 3.74;  // 1원 = 3.74 Ks
    private const X_ONE      = 4.3;   // 1개 구매
    private const X_FIVE     = 4;     // 5개 이상
    private const X_BOX      = 4;     // 1박스 이상
    /** 홈 베스트에 올릴 상품 코드 */
    private const BEST = ['YO-001', 'YO-002', 'YO-003', 'YO-004', 'GW-001', 'BR-003', 'MJ-001', 'SP-002', 'KR-004', 'JY-002'];

    private static function ks(float $won, float $x): int
    {
        return (int) (round($won * $x * self::KRW_TO_MMK / 100) * 100);
    }

    public function run()
    {
        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        // 카테고리: 식품·건강식품 (없으면 만든다)
        $cat = $db->table('categories')->where('name', '식품·건강식품')->get()->getRowArray();
        if ($cat) {
            $catId = (int) $cat['id'];
        } else {
            $db->table('categories')->insert(['name' => '식품·건강식품', 'slug' => 'food', 'sort' => 2, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now]);
            $catId = (int) $db->insertID();
        }

        // 공급사 (관리자가 관리, user_id=0)
        $sups = [
            'pc' => ['company_name' => '평창팜', 'brand' => '평창팜', 'contact_name' => '김정수', 'phone' => '033-332-9779', 'address' => '강원도 평창군 평창읍 기교길 89', 'website' => 'http://www.pcfarm.kr',
                'intro' => '강원도 평창의 나물·들기름·메밀국수 가공 업체. 전 품목 실온 보관 식품.'],
            'ht' => ['company_name' => '한국제다', 'brand' => '한국제다', 'contact_name' => '유웅태 차장(해외영업부)', 'phone' => '062-222-3973', 'address' => '광주광역시 동구 백서로 106-1', 'website' => 'http://www.hankooktea.co.kr',
                'intro' => '광주의 녹차·전통차 전문 제조사. 녹차·호지차 믹스 스틱.'],
            'ch' => ['company_name' => '초록원', 'brand' => '초록원', 'phone' => '053-852-3000', 'website' => 'https://cholocwon.co.kr',
                'intro' => '대구의 전통차·과일청 제조사. 율무차·대추차 등 분말·액상 차.'],
            'wh' => ['company_name' => '웰빙헬스팜', 'brand' => '고운발', 'website' => 'https://3wbmall.com',
                'intro' => '발 보습 크림 고운발 제조사 (주)웰빙헬스팜.'],
            'mj' => ['company_name' => '엠제이케어', 'brand' => 'MJCARE', 'contact_name' => null, 'website' => null,
                'intro' => '에센스 마스크팩 브랜드 엠제이케어(MJCARE). 제조 미진화장품.'],
            'br' => ['company_name' => '뷰티레시피', 'brand' => '뷰티레시피', 'website' => 'http://www.brecipe.com',
                'intro' => '스킨케어 브랜드 뷰티레시피(Beauty Recipe). 리틀머메이드·스노우화이트·네버우드 라인.'],
            'sp' => ['company_name' => '사평', 'brand' => '사평', 'intro' => '즉석 카레 브랜드 사평.'],
            'jy' => ['company_name' => '자연생각', 'brand' => '자연생각', 'intro' => '분말 전통차 브랜드 자연생각. 율무차·유자차·단호박고구마차.'],
            'hy' => ['company_name' => '한양제과', 'brand' => '한양제과', 'intro' => '사탕·캬라멜 제조사 한양제과.'],
            'yo' => ['company_name' => '요뽀끼 (영풍)', 'brand' => '요뽀끼', 'website' => 'https://smartstore.naver.com/yopokki',
                'intro' => '컵떡볶이 브랜드 요뽀끼(YOPOKKI). 전자레인지 조리 컵떡볶이.'],
        ];
        $supIds = [];
        foreach ($sups as $k => $s) {
            $row = $db->table('suppliers')->where('company_name', $s['company_name'])->get()->getRowArray();
            if ($row) {
                $supIds[$k] = (int) $row['id'];
                continue;
            }
            $db->table('suppliers')->insert($s + ['user_id' => 0, 'status' => 'approved', 'created_at' => $now, 'updated_at' => $now]);
            $supIds[$k] = (int) $db->insertID();
        }

        $gwCost = 12000 / 2;     // 웰빙헬스팜 공식몰(3wbmall.com/product/-/30) 판매가 12,000원 ÷ 2
        $mjCost = 28950 / 80 / 2; // 엠제이케어 에센스 마스크팩 80매(8종×10매) 28,950원 → 1매 362원 ÷ 2
        $chCost = 10800 / 2;     // 초록원 공식몰 판매가 10,800원 ÷ 2
        $yoCost = 19900 / 6 / 2; // 요뽀끼 공식 스토어 6입 세트 19,900원 → 1컵 판매가 3,317원 ÷ 2

        // [코드, 사입가(원, 1개당), 재고(개), 공급사, 상품명, 영문명, 규격, 박스 입수(개), 소비기한, HS코드, 바코드, 사진 키, 메모]
        $items = [
            ['KR-001', 5800, 20, 'pc', '간편조리 평창시래기', 'Dried Easy Cook Pyeongchang Siraegi', '80g', 20, '24개월', '0712.90-2020', '8809634580047', 1, null],
            ['KR-002', 7000, 20, 'pc', '평창곤드레나물 (5분ok)', 'Dried 5min.ok Pyeongchang Gondeure-Namul', '80g', 20, '24개월', '0712.90-2097', '8809634580016', 2, null],
            ['KR-003', 7000, 20, 'pc', '평창취나물', 'Dried Pyeongchang Chwi-Namul', '80g', 20, '24개월', '0712.90-2096', '8809634580061', 3, null],
            ['KR-004', 13500, 24, 'pc', '들기름', 'Perilla oil', '170ml', 24, '18개월', '1515.90-1000', '8809397331634', 4, null],
            ['KR-005', 4200, 40, 'pc', '봉평메밀국수 (메밀 30%)', 'Bongpyeong Buckwheat Noodles', '700g', 20, '24개월', '1902.19-1000', '8809023820587', 5, null],
            ['KR-006', 6600, 24, 'pc', '평창곤드레국수', 'Pyeongchang Gondre Noodles', '300g', 24, '24개월', '1902.19-1000', '8809634580436', 6, null],
            ['KR-007', 4400, 2, 'ht', '그린티믹스특선14 스틱', 'Green Tea Mix Stick', '120g', 20, null, null, null, 7, '1개 = 120g 스틱 1통'],
            ['KR-008', 3850, 1, 'ht', '호지차믹스20 스틱', 'Hojicha Mix Stick', '120g', 20, null, null, null, 8, '1개 = 120g 스틱 1통'],
            ['YO-001', $yoCost, 0, 'yo', '요뽀끼 매콤달콤 컵떡볶이', 'YOPOKKI Sweet & Spicy Topokki Cup', '120g', 30, '제조일로부터 14개월', null, null, 'yo1', '1개 = 컵 1개 (떡 100g + 소스 20g)'],
            ['YO-002', $yoCost, 0, 'yo', '요뽀끼 치즈 컵떡볶이', 'YOPOKKI Cheese Topokki Cup', '120g', 30, '제조일로부터 14개월', null, null, 'yo2', '1개 = 컵 1개 (떡 100g + 소스 20g)'],
            ['YO-003', $yoCost, 0, 'yo', '요뽀끼 로제 컵떡볶이', 'YOPOKKI Rose Topokki Cup', '120g', 30, '제조일로부터 14개월', null, null, 'yo3', '1개 = 컵 1개 (떡 100g + 소스 20g)'],
            ['YO-004', $yoCost, 0, 'yo', '요뽀끼 매콤 까르보나라 컵떡볶이', 'YOPOKKI Spicy Carbonara Topokki Cup', '120g', 30, '제조일로부터 14개월', null, null, 'yo4', '1개 = 컵 1개 (떡 100g + 소스 20g)'],
            ['GW-001', $gwCost, 0, 'wh', '명품 고운발 풋크림', 'Premium Gounbal Foot Cream', '110g', null, null, null, null, 'gw1', null, ['cat' => '화장품·스킨케어']],
            ['MJ-001', $mjCost, 0, 'mj', '엠제이케어 에센스 마스크팩', 'MJCARE Essence Mask Pack', '8종', null, '제조일로부터 1년 이내 발송 / 개봉 후 12개월', null, null, 'mj1', '콜라겐·알부틴·큐텐·백색(백장미)·로얄제리·진주·알로에·석류 8종 중 선택, 1종 10매 단위', ['cat' => '화장품·스킨케어', 'unit' => '매', 'moq' => 10, 'five' => 50]],
            // 뷰티레시피 5종: 쿠팡 판매가(1개, 2026-11-22) ÷ 2 를 사입가로
            ['BR-001', 8900 / 2, 0, 'br', '뷰티레시피 인어공주 프로바이오틱스 여성청결제', 'Little Mermaid Probiotics Feminine Wash', '300ml', null, null, null, null, 'br1', null, ['cat' => '화장품·스킨케어']],
            ['BR-002', 10620 / 2, 0, 'br', '뷰티레시피 스노우화이트 멜라 스탑 화이트닝 크림', 'Snow White Mela Stop Whitening Cream', '80ml', null, null, null, null, 'br2', null, ['cat' => '화장품·스킨케어']],
            ['BR-003', 9900 / 2, 0, 'br', '뷰티레시피 리틀머메이드 디스 이즈 프린세스 클렌징워터', 'Little Mermaid This is Princess Cleansing Water', '500ml', null, null, null, '8809132285963', 'br3', null, ['cat' => '화장품·스킨케어']],
            ['BR-004', 11900 / 2, 0, 'br', '뷰티레시피 리틀머메이드 디스 이즈 프린세스 클렌징오일', 'Little Mermaid This is Princess Cleansing Oil', '300ml', null, null, null, null, 'br4', null, ['cat' => '화장품·스킨케어']],
            ['BR-005', 10900 / 2, 0, 'br', '뷰티레시피 네버우드 펩타이드 앤 시카 리페어 크림', 'NeverWood Peptide & Cica Repair Cream', '50ml', null, null, null, null, 'br5', null, ['cat' => '화장품·스킨케어']],
            // 쿠팡 판매가(1개, 2026-11-22) ÷ 2 를 사입가로
            ['SP-001', 9150 / 2, 0, 'sp', '사평 말차카레 2p', 'Sapyeong Matcha Curry 2p', '320g', null, null, null, null, 'cu1', '1개 = 160g × 2봉'],
            ['SP-002', 14520 / 2, 0, 'sp', '사평 스트레스 킬러 마라카레 2p', 'Sapyeong Stress Killer Mala Curry 2p', '320g', null, null, null, null, 'cu2', '1개 = 160g × 2봉'],
            ['JY-001', 8050 / 2, 0, 'jy', '자연생각 단호박고구마차', 'Sweet Pumpkin & Sweet Potato Tea 50T', '50T (18g × 50)', null, null, null, null, 'jy1', '1개 = 50스틱 1봉'],
            ['JY-002', 4320 / 2, 0, 'jy', '자연생각 유자차', 'Citron Tea 50T', '50T (18g × 50)', null, null, null, null, 'jy2', '1개 = 50스틱 1봉'],
            ['JY-003', 9060 / 2, 0, 'jy', '자연생각 검은콩 율무차', 'Black Bean Job’s Tears Tea 50T', '50T (18g × 50)', null, null, null, null, 'jy3', '1개 = 50스틱 1봉'],
            ['HY-001', 10480 / 2, 0, 'hy', '한양제과 누룽지맛사탕', 'Hanyang Nurungji Candy', '1kg', null, null, null, null, 'hy1', null],
            ['HY-002', 10500 / 2, 0, 'hy', '한양제과 땅콩캬라멜', 'Hanyang Peanut Caramel', '900g', null, null, null, null, 'hy2', null],
            ['CH-001', $chCost, 0, 'ch', '고려 호두아몬드율무차', 'Walnut Almond Job’s Tears Tea', '50입', null, null, null, null, 'ch1', '1개 = 50스틱 1통'],
        ];

        $det = json_decode((string) file_get_contents(__DIR__ . '/kr-details.json'), true) ?: [];

        $catIdOf = function (string $name) use ($db, $now, $catId): int {
            $row = $db->table('categories')->where('name', $name)->get()->getRowArray();
            return $row ? (int) $row['id'] : $catId;
        };

        foreach ($items as $it) {
            [$code, $cost, $stock, $sk, $name, $en, $spec, $per, $shelf, $hs, $barcode, $img, $memo] = $it;
            $opt  = ($it[13] ?? []) + ['cat' => '식품·건강식품', 'unit' => '개', 'moq' => 1, 'five' => 5];
            $one  = self::ks($cost, self::X_ONE);
            $five = self::ks($cost, self::X_FIVE);
            $box  = self::ks($cost, self::X_BOX);
            $tiers = [['min_qty' => $opt['five'], 'price' => $five]];
            if ($per && $per > $opt['five']) {
                $tiers[] = ['min_qty' => $per, 'price' => $box];
            }

            $d      = $det[$img] ?? ['images' => [], 'blocks' => []];
            $brand  = $sups[$sk]['brand'];
            $lines  = [];
            foreach (['영문명' => $en, '규격' => $spec, '구성' => $memo, '박스 입수' => $per ? $per . '개' : null, '소비기한' => $shelf, '원산지' => '한국', 'HS 코드' => $hs, '바코드' => $barcode, '보관' => '실온'] as $k => $v) {
                if ($v !== null && $v !== '') {
                    $lines[] = $k . ': ' . $v;
                }
            }
            $data = [
                'thumb'   => $d['images'][0] ?? null,
                'images'  => json_encode($d['images']),
                'detail'  => json_encode(array_merge([['type' => 'p', 'text' => implode("\n", $lines)]], $d['blocks']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'price'   => $one,
                'tiers'   => json_encode($tiers),
                'moq'     => $opt['moq'],
                'unit'    => $opt['unit'],
                'category_id' => $catIdOf($opt['cat']),
                'stock'   => $stock,
                'status'  => 'active',
                'summary' => $brand . ' 정품. ' . $opt['moq'] . $opt['unit'] . '부터 주문, ' . $opt['five'] . $opt['unit'] . ($per ? '·1박스(' . $per . '개)' : '') . ' 이상 도매가.',
                'description' => '<p>' . esc(implode(' / ', $lines)) . '</p>',
                'updated_at'  => $now,
            ];

            $row = $db->table('products')->where('code', $code)->get()->getRowArray();
            if ($row) {
                $db->table('products')->where('id', $row['id'])->update($data);
                continue;
            }
            $db->table('products')->insert($data + [
                'supplier_id' => $supIds[$sk], 'category_id' => $catId, 'code' => $code,
                'name' => $name . ' ' . $spec, 'retail_price' => null,
                'origin' => '한국', 'brand' => $brand, 'is_best' => 0, 'views' => 0,
                'created_at' => $now,
            ]);
        }

        // 데모 상품·데모 공급사 삭제 (2026-11-25 사용자 지시 "데모 다 지워"). 데모 주문 중 데모 상품이 든 것도 같이 지움
        $demoIds = array_map('intval', array_column($db->table('products')->select('id')->like('code', 'DEMO-', 'after')->get()->getResultArray(), 'id'));
        if ($demoIds) {
            foreach (['carts', 'wishes', 'inquiries', 'queues'] as $tb) {
                if ($db->tableExists($tb)) {
                    $db->table($tb)->whereIn('product_id', $demoIds)->delete();
                }
            }
            $orderIds = array_unique(array_map('intval', array_column($db->table('order_items')->select('order_id')->whereIn('product_id', $demoIds)->get()->getResultArray(), 'order_id')));
            if ($orderIds) {
                $db->table('order_items')->whereIn('order_id', $orderIds)->delete();
                $db->table('orders')->whereIn('id', $orderIds)->delete();
            }
            $db->table('products')->whereIn('id', $demoIds)->delete();
        }
        foreach ($db->table('suppliers')->like('company_name', '(데모)')->get()->getResultArray() as $ds) {
            if (! $db->table('products')->where('supplier_id', $ds['id'])->countAllResults()) {
                $db->table('suppliers')->where('id', $ds['id'])->delete();
            }
        }

        // 베스트 상품 (홈 '가장 많이 주문한 베스트' 칸): 처음 한 번만 지정. 이후엔 관리자 상품 화면의 '베스트' 체크가 기준
        if (! $db->table('products')->where('is_best', 1)->countAllResults()) {
            $db->table('products')->whereIn('code', self::BEST)->update(['is_best' => 1]);
        }

        // 미얀마어 배너·공지·카테고리 칸이 비어 있으면 사전 번역으로 채움 (관리자에서 보이도록)
        $dict = require APPPATH . 'Language/my/ui.php';
        $fill = static function (string $table, array $fields) use ($db, $dict) {
            foreach ($db->table($table)->get()->getResultArray() as $r) {
                $upd = [];
                foreach ($fields as $f) {
                    if (trim((string) ($r[$f . '_my'] ?? '')) === '' && ($v = $dict[trim((string) $r[$f])] ?? '') !== '') {
                        $upd[$f . '_my'] = $v;
                    }
                }
                if ($upd) {
                    $db->table($table)->where('id', $r['id'])->update($upd);
                }
            }
        };
        $fill('banners', ['title', 'subtitle']);
        $fill('notices', ['title', 'body']);
        $fill('categories', ['name']);

        // 미얀마어 상품명·공급사명 (names-my.php). 데모 상품 포함, 매번 반영
        $my = require __DIR__ . '/names-my.php';
        foreach ($my['products'] as $code => $nm) {
            $db->table('products')->where('code', $code)->update(['name_my' => $nm]);
        }
        foreach ($my['suppliers'] as $ko => $nm) {
            $db->table('suppliers')->where('company_name', $ko)->update(['name_my' => $nm]);
        }
    }
}
