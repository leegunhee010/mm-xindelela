<?php

/**
 * mm.xindelela.com 공용 헬퍼 (Autoload: 'mm').
 */

use App\Models\SettingModel;

if (! function_exists('mm_setting')) {
    /** 설정값 조회 (요청 단위 캐시). */
    function mm_setting(string $key, $default = null)
    {
        static $cache = null;
        if ($cache === null) {
            $cache = [];
            try {
                foreach ((new SettingModel())->findAll() as $row) {
                    $cache[$row['skey']] = $row['svalue'];
                }
            } catch (\Throwable $e) {
                // 마이그레이션 전
            }
        }
        return array_key_exists($key, $cache) && $cache[$key] !== null && $cache[$key] !== '' ? $cache[$key] : $default;
    }
}

if (! function_exists('mm_user')) {
    /** 로그인 회원 배열 (없으면 null). 세션엔 id만 두고 매 요청 DB에서 읽는다(승인 상태가 바뀌면 즉시 반영). */
    function mm_user(): ?array
    {
        static $user = false;
        if ($user !== false) {
            return $user;
        }
        $id = session()->get('user_id');
        if (! $id) {
            return $user = null;
        }
        $user = (new \App\Models\UserModel())->find($id) ?: null;
        return $user;
    }
}

if (! function_exists('mm_is_approved')) {
    /** 도매가 열람·주문 가능한 승인 구매자인가. */
    function mm_is_approved(): bool
    {
        $u = mm_user();
        return $u !== null && $u['role'] === 'buyer' && $u['status'] === 'approved';
    }
}

if (! function_exists('mm_price')) {
    /** 금액 표기. 통화는 설정(currency_symbol, currency_position). */
    function mm_price($n, bool $withUnit = true): string
    {
        $s = number_format((float) $n);
        if (! $withUnit) {
            return $s;
        }
        $sym = mm_setting('currency_symbol', 'Ks');
        return mm_setting('currency_position', 'after') === 'before' ? $sym . ' ' . $s : $s . ' ' . $sym;
    }
}

if (! function_exists('mm_unit_price')) {
    /** 수량별 단가 적용. tiers = [{min_qty, price}] (min_qty 오름차순 아님을 가정하고 정렬). */
    function mm_unit_price(array $product, int $qty): int
    {
        $price = (int) $product['price'];
        $tiers = is_array($product['tiers'] ?? null) ? $product['tiers'] : (json_decode($product['tiers'] ?? '[]', true) ?: []);
        usort($tiers, static fn ($a, $b) => (int) $a['min_qty'] <=> (int) $b['min_qty']);
        foreach ($tiers as $t) {
            if ($qty >= (int) $t['min_qty'] && (int) $t['price'] > 0) {
                $price = (int) $t['price'];
            }
        }
        return $price;
    }
}

if (! function_exists('mm_status_label')) {
    function mm_status_label(string $status): string
    {
        return [
            'pending'   => '검수 대기',
            'approved'  => '승인',
            'rejected'  => '반려',
            'suspended' => '정지',
        ][$status] ?? $status;
    }
}

if (! function_exists('mm_product_status_label')) {
    function mm_product_status_label(string $status): string
    {
        return [
            'draft'    => '임시저장',
            'pending'  => '검수 대기',
            'active'   => '판매중',
            'hidden'   => '숨김',
            'rejected' => '반려',
        ][$status] ?? $status;
    }
}

if (! function_exists('mm_order_status_label')) {
    function mm_order_status_label(string $status): string
    {
        return [
            'requested' => '입금 대기',
            'quoted'    => '입금 대기',
            'proof'     => '입금 확인중',
            'paid'      => '입금 확인',
            'preparing' => '발주·준비중',
            'shipped'   => '배송중',
            'delivered' => '배송 완료',
            'cancelled' => '취소',
        ][$status] ?? $status;
    }
}

if (! function_exists('mm_payment_label')) {
    function mm_payment_label(string $m): string
    {
        return ['kbzpay' => 'KBZPay 선불', 'cod' => '착불(COD)'][$m] ?? $m;
    }
}

if (! function_exists('mm_point_type_label')) {
    function mm_point_type_label(string $t): string
    {
        return [
            'signup_bonus'         => '가입 축하',
            'referral_signup'      => '추천 회원 승인',
            'referral_first_order' => '추천 회원 첫 주문',
            'order_use'            => '주문 사용',
            'order_refund'         => '주문 취소 환원',
            'order_reward'         => '구매 적립',
            'admin_adjust'         => '관리자 조정',
        ][$t] ?? $t;
    }
}

if (! function_exists('mm_thumb')) {
    /** 상품 썸네일 URL (없으면 자리표시). */
    function mm_thumb(?string $path): string
    {
        if ($path) {
            return str_starts_with($path, 'http') ? $path : base_url($path);
        }
        return base_url('assets/img/noimg.svg');
    }
}

if (! function_exists('mm_json')) {
    function mm_json($v, array $default = []): array
    {
        if (is_array($v)) {
            return $v;
        }
        $d = json_decode((string) $v, true);
        return is_array($d) ? $d : $default;
    }
}

if (! function_exists('mm_dt')) {
    function mm_dt(?string $dt, string $fmt = 'Y.m.d H:i'): string
    {
        return $dt ? date($fmt, strtotime($dt)) : '-';
    }
}

if (! function_exists('mm_flash')) {
    /** 플래시 메시지 HTML. */
    function mm_flash(): string
    {
        $s   = session();
        $out = '';
        foreach (['success' => 'ok', 'error' => 'err', 'info' => 'info'] as $k => $cls) {
            if ($m = $s->getFlashdata($k)) {
                $out .= '<div class="flash flash-' . $cls . '">' . esc($m) . '</div>';
            }
        }
        return $out;
    }
}

if (! function_exists('mm_referral_code')) {
    function mm_referral_code(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $c     = '';
        for ($i = 0; $i < 8; $i++) {
            $c .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $c;
    }
}

if (! function_exists('mm_verify_types')) {
    /**
     * 사업자 인증 규정. 가입·재제출·관리자 검수·이용안내가 모두 이 정의를 쓴다.
     * 셋 중 하나만 증빙하면 된다. 사진·캡처도 인정.
     */
    function mm_verify_types(): array
    {
        return [
            'offline' => [
                'label'  => '오프라인 매장',
                'desc'   => '가게·매장을 운영하고 있어요',
                'docs'   => ['간판이 보이는 매장 외관 사진', '진열대나 매장 안 사진', '영업허가증 (있으면)'],
                'check'  => ['사진 속 매장이 실제로 영업 중인가', '상호(간판)가 가입 정보와 맞는가'],
            ],
            'online' => [
                'label'  => '온라인 판매',
                'desc'   => '페이스북·틱톡·인스타 등에서 팔고 있어요',
                'docs'   => ['판매 페이지 링크', '판매 글 캡처', '고객 주문·배송 대화 캡처'],
                'check'  => ['판매 페이지(또는 캡처)에 최근 판매 글이 있는가', '페이지 이름이 가입 상호와 맞는가'],
            ],
            'network' => [
                'label'  => '판매 네트워크',
                'desc'   => '거래처·고객 그룹에 물건을 대고 있어요',
                'docs'   => ['Viber·Telegram 고객/거래처 그룹 캡처', '거래처 주문·납품 대화 캡처', '거래 장부·영수증 사진'],
                'check'  => ['여러 고객·거래처에 반복해서 판매·공급하는가', '개인 사용 목적 구매가 아닌가'],
            ],
        ];
    }
}

if (! function_exists('mm_verify_labels')) {
    /** 저장된 'offline,online' → '오프라인 매장 · 온라인 판매' */
    function mm_verify_labels(?string $csv): string
    {
        $t = mm_verify_types();
        $out = [];
        foreach (array_filter(explode(',', (string) $csv)) as $k) {
            if (isset($t[$k])) {
                $out[] = $t[$k]['label'];
            }
        }
        return $out ? implode(' · ', $out) : '-';
    }
}

if (! function_exists('mm_verify_reject_reasons')) {
    /** 관리자 반려 사유 빠른 입력 */
    function mm_verify_reject_reasons(): array
    {
        return [
            '판매 활동을 확인할 수 있는 자료가 없습니다. 매장 사진, 판매 글 캡처, 거래처 대화 캡처 중 하나를 올려 주세요.',
            '사진·캡처가 흐려서 내용을 확인할 수 없습니다. 선명한 자료로 다시 올려 주세요.',
            '제출하신 링크(페이지)에 들어갈 수 없거나 판매 글이 없습니다. 판매 글이 보이는 캡처를 함께 올려 주세요.',
            '자료의 상호와 가입하신 상호가 다릅니다. 같은 가게·페이지인지 확인할 수 있는 자료를 올려 주세요.',
        ];
    }
}

if (! function_exists('mm_mask_name')) {
    /** 순위표용 이름 가리기: 'Yangon Beauty Shop' → 'Yan*** Shop', '양곤상회' → '양**' */
    function mm_mask_name(string $s): string
    {
        $s = trim($s);
        $len = mb_strlen($s);
        if ($len <= 2) {
            return mb_substr($s, 0, 1) . '*';
        }
        $words = preg_split('/\s+/u', $s);
        if (count($words) >= 2) {
            return mb_substr($words[0], 0, 3) . '*** ' . end($words);
        }
        $keep = max(1, (int) floor($len / 3));
        return mb_substr($s, 0, $keep) . str_repeat('*', min(4, $len - $keep));
    }
}

if (! function_exists('mm_ship_methods')) {
    function mm_ship_methods(): array
    {
        return ['courier' => '택배', 'cargo' => '화물·트럭', 'direct' => '직접 배송', 'pickup' => '방문 수령', 'air' => '항공 특송'];
    }
}

if (! function_exists('mm_ship_info')) {
    /** orders.ship_info(JSON) → 배열. 예전 주문은 tracking 문자열만 있음. */
    function mm_ship_info(array $o): array
    {
        $i = json_decode((string) ($o['ship_info'] ?? ''), true) ?: [];
        return array_merge(['method' => '', 'carrier' => '', 'tracking_no' => '', 'tracking_url' => '', 'sent_on' => '', 'eta' => '', 'note' => ''], $i);
    }
}

if (! function_exists('mm_clean_blocks')) {
    /** 관리자에서 넘어온 상세 블록 JSON 을 검증·정리 */
    function mm_clean_blocks(string $json): array
    {
        $out = [];
        foreach ((json_decode($json, true) ?: []) as $b) {
            $t = $b['type'] ?? '';
            if ($t === 'p') {
                $txt = trim((string) ($b['text'] ?? ''));
                if ($txt !== '') {
                    $out[] = ['type' => 'p', 'text' => mb_substr($txt, 0, 5000)];
                }
            } elseif ($t === 'img') {
                $src = trim((string) ($b['src'] ?? ''));
                if ($src !== '' && (preg_match('#^uploads/[A-Za-z0-9/_.-]+$#', $src) || preg_match('#^https?://#i', $src))) {
                    $blk = ['type' => 'img', 'src' => $src];
                    if (! empty($b['seq'])) {
                        $blk['seq'] = true;
                        $blk['w'] = (int) ($b['w'] ?? 0);
                        $blk['h'] = (int) ($b['h'] ?? 0);
                    }
                    $out[] = $blk;
                }
            } elseif ($t === 'video') {
                $src = trim((string) ($b['src'] ?? ''));
                if (preg_match('#^https?://#i', $src)) {
                    $out[] = ['type' => 'video', 'src' => mb_substr($src, 0, 300)];
                }
            }
        }
        return $out;
    }
}

if (! function_exists('mm_detail_blocks')) {
    /** 상품의 상세 블록. 예전 상품(HTML 설명·상세 이미지)은 블록으로 바꿔 돌려준다. */
    function mm_detail_blocks(array $p): array
    {
        $b = json_decode((string) ($p['detail'] ?? ''), true);
        if (is_array($b) && $b) {
            return $b;
        }
        $out = [];
        if (! empty($p['description'])) {
            $out[] = ['type' => 'html', 'html' => $p['description']];
        }
        foreach (mm_json($p['detail_images'] ?? null) as $src) {
            $out[] = ['type' => 'img', 'src' => $src];
        }
        return $out;
    }
}

if (! function_exists('mm_video_embed')) {
    /** 유튜브·비메오 주소 → 임베드 주소 */
    function mm_video_embed(string $url): ?string
    {
        if (preg_match('#(?:youtu\.be/|youtube\.com/(?:watch\?v=|shorts/|embed/))([A-Za-z0-9_-]{6,})#', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        if (preg_match('#vimeo\.com/(\d+)#', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }
        return null;
    }
}
