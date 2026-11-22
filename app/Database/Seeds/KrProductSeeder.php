<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * 실제 한국 사입 품목 (Documents\1106_미얀마_한국사입품목.xlsx, 2026-11-06 정리).
 * 판매중(active), 단위=박스, 가격=박스당 (사입가 × 입수 × 4 × 3.74). 재고=들여오는 박스 수.
 * 상품 코드(KR-xxx)가 이미 있으면 건너뛴다(여러 번 돌려도 안전).
 */
class KrProductSeeder extends Seeder
{
    private const MARKUP     = 4;     // 사입가의 4배
    private const KRW_TO_MMK = 3.74;  // 1원 = 3.74 Ks
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

        // [코드, 사입가(원, 개당·한국제다는 박스당), 들여오는 박스 수, 공급사, 상품명, 영문명, 규격, 입수(박스당), 소비기한, HS코드, 바코드, 사진]
        $items = [
            ['KR-001', 5800, 1, 'pc', '간편조리 평창시래기', 'Dried Easy Cook Pyeongchang Siraegi', '80g', 20, '24개월', '0712.90-2020', '8809634580047', 'kr1'],
            ['KR-002', 7000, 1, 'pc', '평창곤드레나물 (5분ok)', 'Dried 5min.ok Pyeongchang Gondeure-Namul', '80g', 20, '24개월', '0712.90-2097', '8809634580016', 'kr2'],
            ['KR-003', 7000, 1, 'pc', '평창취나물', 'Dried Pyeongchang Chwi-Namul', '80g', 20, '24개월', '0712.90-2096', '8809634580061', 'kr3'],
            ['KR-004', 13500, 1, 'pc', '들기름', 'Perilla oil', '170ml', 24, '18개월', '1515.90-1000', '8809397331634', 'kr4'],
            ['KR-005', 4200, 2, 'pc', '봉평메밀국수 (메밀 30%)', 'Bongpyeong Buckwheat Noodles', '700g', 20, '24개월', '1902.19-1000', '8809023820587', 'kr5'],
            ['KR-006', 6600, 1, 'pc', '평창곤드레국수', 'Pyeongchang Gondre Noodles', '300g', 24, '24개월', '1902.19-1000', '8809634580436', 'kr6'],
            ['KR-007', 4400, 2, 'ht', '그린티믹스특선14 스틱', 'Green Tea Mix Stick', '120g', null, null, null, null, 'kr7'],
            ['KR-008', 3850, 1, 'ht', '호지차믹스20 스틱', 'Hojicha Mix Stick', '120g', null, null, null, null, 'kr8'],
        ];

        // 판매페이지(평창팜 pcfarm.kr, 봉평메밀팜 memil.kr, 한국제다 hktmall.co.kr)에서 받은 대표사진·상세 이미지.
        // scripts/slice-kr-details.php 가 만든 목록.
        $det = json_decode((string) file_get_contents(__DIR__ . '/kr-details.json'), true) ?: [];

        foreach ($items as $i => [$code, $cost, $boxes, $sk, $name, $en, $spec, $per, $shelf, $hs, $barcode, $img]) {
            // 판매가(박스당) = 사입가 × 박스 입수 × 4배 × 환율 3.74 (1원 = 3.74 Ks, 2026-11 사용자 지정). 100 Ks 반올림.
            $perBox = (int) (round($cost * ($per ?: 1) * self::MARKUP * self::KRW_TO_MMK / 100) * 100);
            $perPc  = $per ? (int) (round($cost * self::MARKUP * self::KRW_TO_MMK / 100) * 100) : null;
            $d      = $det[$i + 1] ?? ['images' => [], 'blocks' => []];
            $images = $d['images'];
            $brand  = $sups[$sk]['brand'];
            $lines  = [];
            foreach (['영문명' => $en, '규격' => $spec, '박스 입수' => $per ? $per . '개' . ($perPc ? ' (개당 약 ' . number_format($perPc) . ' Ks)' : '') : null, '소비기한' => $shelf, '원산지' => '한국', 'HS 코드' => $hs, '바코드' => $barcode, '보관' => '실온'] as $k => $v) {
                if ($v !== null && $v !== '') {
                    $lines[] = $k . ': ' . $v;
                }
            }
            $detail = json_encode(array_merge([['type' => 'p', 'text' => implode("
", $lines)]], $d['blocks']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $media  = ['thumb' => $images[0] ?? null, 'images' => json_encode($images), 'detail' => $detail];

            $row = $db->table('products')->where('code', $code)->get()->getRowArray();
            $sale = ['price' => $perBox, 'stock' => $boxes, 'status' => 'active'];
            if ($row) {
                // 이미 있으면 사진·상세 갱신. 가격은 아직 0(입고 예정)일 때만 넣는다 (관리자가 정한 가격은 유지).
                $upd = $media + ['updated_at' => $now];
                if ((int) $row['price'] === 0) {
                    $upd += $sale;
                }
                $db->table('products')->where('id', $row['id'])->update($upd);
                continue;
            }
            $rows  = ['영문명' => $en, '규격' => $spec, '박스 입수' => $per ? $per . '개' . ($perPc ? ' (개당 약 ' . number_format($perPc) . ' Ks)' : '') : null, '소비기한' => $shelf, '원산지' => '한국', 'HS 코드' => $hs, '바코드' => $barcode, '보관' => '실온'];
            $li    = '';
            foreach ($rows as $k => $v) {
                if ($v !== null && $v !== '') {
                    $li .= '<li>' . esc($k) . ': ' . esc($v) . '</li>';
                }
            }
            $db->table('products')->insert([
                'supplier_id' => $supIds[$sk], 'category_id' => $catId, 'code' => $code,
                'name' => $name . ' ' . $spec, 'summary' => $brand . ' 정품. 박스 단위 도매 공급.',
                'description' => '<ul>' . $li . '</ul>',
                'thumb' => $media['thumb'], 'images' => $media['images'], 'detail' => $media['detail'],
                'price' => $sale['price'], 'retail_price' => null, 'moq' => 1, 'tiers' => json_encode([]),
                'unit' => '박스', 'stock' => $sale['stock'], 'origin' => '한국', 'brand' => $brand,
                'status' => $sale['status'], 'is_best' => 0, 'views' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
