<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * 실제 한국 사입 품목 (Documents\1106_미얀마_한국사입품목.xlsx, 2026-11-06 정리).
 * 아직 입고 전이라 status='coming'(입고 예정): 가격은 입고 후 공개, 인증 회원은 줄서기.
 * 판매 도매가는 미정이라 price=0. 입고되면 관리자에서 가격 넣고 '판매중'으로 바꾸면 줄 선 회원에게 알림이 간다.
 * 상품 코드(KR-xxx)가 이미 있으면 건너뛴다(여러 번 돌려도 안전).
 */
class KrProductSeeder extends Seeder
{
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

        // [코드, 공급사, 상품명, 영문명, 규격, 입수(박스당), 소비기한, HS코드, 바코드, 사진]
        $items = [
            ['KR-001', 'pc', '간편조리 평창시래기', 'Dried Easy Cook Pyeongchang Siraegi', '80g', 20, '24개월', '0712.90-2020', '8809634580047', 'kr1'],
            ['KR-002', 'pc', '평창곤드레나물 (5분ok)', 'Dried 5min.ok Pyeongchang Gondeure-Namul', '80g', 20, '24개월', '0712.90-2097', '8809634580016', 'kr2'],
            ['KR-003', 'pc', '평창취나물', 'Dried Pyeongchang Chwi-Namul', '80g', 20, '24개월', '0712.90-2096', '8809634580061', 'kr3'],
            ['KR-004', 'pc', '들기름', 'Perilla oil', '170ml', 24, '18개월', '1515.90-1000', '8809397331634', 'kr4'],
            ['KR-005', 'pc', '봉평메밀국수 (메밀 30%)', 'Bongpyeong Buckwheat Noodles', '700g', 20, '24개월', '1902.19-1000', '8809023820587', 'kr5'],
            ['KR-006', 'pc', '평창곤드레국수', 'Pyeongchang Gondre Noodles', '300g', 24, '24개월', '1902.19-1000', '8809634580436', 'kr6'],
            ['KR-007', 'ht', '그린티믹스특선14 스틱', 'Green Tea Mix Stick', '120g', null, null, null, null, 'kr7'],
            ['KR-008', 'ht', '호지차믹스20 스틱', 'Hojicha Mix Stick', '120g', null, null, null, null, 'kr8'],
        ];

        // 판매페이지(평창팜 pcfarm.kr, 봉평메밀팜 memil.kr, 한국제다 hktmall.co.kr)에서 받은 대표사진·상세 이미지.
        // scripts/slice-kr-details.php 가 만든 목록.
        $det = json_decode((string) file_get_contents(__DIR__ . '/kr-details.json'), true) ?: [];

        foreach ($items as $i => [$code, $sk, $name, $en, $spec, $per, $shelf, $hs, $barcode, $img]) {
            $d      = $det[$i + 1] ?? ['images' => [], 'blocks' => []];
            $images = $d['images'];
            $brand  = $sups[$sk]['brand'];
            $lines  = [];
            foreach (['영문명' => $en, '규격' => $spec, '박스 입수' => $per ? $per . '개' : null, '소비기한' => $shelf, '원산지' => '한국', 'HS 코드' => $hs, '바코드' => $barcode, '보관' => '실온'] as $k => $v) {
                if ($v !== null && $v !== '') {
                    $lines[] = $k . ': ' . $v;
                }
            }
            $detail = json_encode(array_merge([['type' => 'p', 'text' => implode("
", $lines)]], $d['blocks']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $media  = ['thumb' => $images[0] ?? null, 'images' => json_encode($images), 'detail' => $detail];

            $row = $db->table('products')->where('code', $code)->get()->getRowArray();
            if ($row) {
                // 이미 있으면 사진·상세만 갱신 (가격·상태 등 관리자가 바꾼 값은 건드리지 않음)
                $db->table('products')->where('id', $row['id'])->update($media + ['updated_at' => $now]);
                continue;
            }
            $rows  = ['영문명' => $en, '규격' => $spec, '박스 입수' => $per ? $per . '개' : null, '소비기한' => $shelf, '원산지' => '한국', 'HS 코드' => $hs, '바코드' => $barcode, '보관' => '실온'];
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
                'price' => 0, 'retail_price' => null, 'moq' => 1, 'tiers' => json_encode([]),
                'unit' => '박스', 'stock' => 0, 'origin' => '한국', 'brand' => $brand,
                'status' => 'coming', 'is_best' => 0, 'views' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
