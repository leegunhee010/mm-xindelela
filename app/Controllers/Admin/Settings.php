<?php

namespace App\Controllers\Admin;

use App\Models\SettingModel;

class Settings extends Base
{
    /** 설정 항목 정의: key => [라벨, 설명, 타입] */
    public const FIELDS = [
        '사이트' => [
            'site_name'       => ['사이트 이름', '', 'text'],
            'topbar_text'     => ['상단 띠 문구', '헤더 맨 위 한 줄', 'text'],
            'verify_sla_text' => ['인증 소요 안내 문구', '예: 영업일 기준 1일 이내 검수', 'text'],
            'cs_viber'        => ['고객센터 Viber', '', 'text'],
            'cs_phone'        => ['고객센터 전화', '', 'text'],
            'cs_email'        => ['고객센터 이메일', '', 'text'],
        ],
        '통화·결제' => [
            'currency_symbol'   => ['통화 기호', 'Ks, $, ₩ 등', 'text'],
            'currency_position' => ['기호 위치', 'after(뒤) 또는 before(앞)', 'text'],
            'kbzpay_account'    => ['KBZPay 입금 계좌 안내', '주문 완료·주문 상세에 표시', 'text'],
            'cod_enabled'       => ['착불(COD) 허용', '1=허용, 0=준비중', 'text'],
        ],
        '포인트' => [
            'points_signup_bonus'         => ['가입 승인 보너스', '승인 시 본인에게', 'number'],
            'points_referral_signup'      => ['추천: 회원 승인 시', '추천인에게', 'number'],
            'points_referral_first_order' => ['추천: 첫 주문 입금 시', '추천인에게', 'number'],
            'points_reward_rate'          => ['구매 적립률(%)', '입금 확인 시 상품금액 × %', 'number'],
            'points_use_max_pct'          => ['주문당 최대 사용률(%)', '상품 금액 기준', 'number'],
            'points_min_use'              => ['최소 사용 포인트', '', 'number'],
        ],
    ];

    public function index(): string
    {
        $vals = [];
        foreach ((new SettingModel())->findAll() as $r) {
            $vals[$r['skey']] = $r['svalue'];
        }
        return $this->page('admin/settings', ['title' => '설정', 'fields' => self::FIELDS, 'vals' => $vals]);
    }

    public function save()
    {
        $sm = new SettingModel();
        foreach (self::FIELDS as $group) {
            foreach ($group as $k => $def) {
                $v   = (string) $this->request->getPost($k);
                $row = $sm->where('skey', $k)->first();
                $row ? $sm->update($row['id'], ['svalue' => $v]) : $sm->insert(['skey' => $k, 'svalue' => $v]);
            }
        }
        return redirect()->to('/admin/settings')->with('success', '설정을 저장했습니다.');
    }
}
