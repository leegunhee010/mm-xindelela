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

if (! function_exists('mm_verify_states')) {
    /** 구매자 비즈니스 인증 상태. 관리자·구매자 화면이 모두 이 이름을 쓴다. */
    function mm_verify_states(): array
    {
        return ['pending' => '인증 검수 중', 'approved' => '인증 완료', 'rejected' => '보완 요청', 'suspended' => '이용 정지'];
    }
}

if (! function_exists('mm_verify_status_label')) {
    function mm_verify_status_label(string $status): string
    {
        return mm_verify_states()[$status] ?? $status;
    }
}

if (! function_exists('mm_product_status_label')) {
    function mm_product_status_label(string $status): string
    {
        return [
            'draft'    => '임시저장',
            'pending'  => '검수 대기',
            'active'   => '판매중',
            'coming'   => '입고 예정',
            'hidden'   => '숨김',
            'rejected' => '반려',
        ][$status] ?? $status;
    }
}

if (! function_exists('mm_order_flow')) {
    /** 주문 진행 단계 (순서대로). 관리자·구매자 화면이 모두 이 이름을 쓴다. */
    function mm_order_flow(): array
    {
        return [
            'requested' => '입금 대기',
            'proof'     => '입금 확인 요청',
            'paid'      => '결제 완료',
            'preparing' => '출고 준비',
            'shipped'   => '배송중',
            'delivered' => '배송 완료',
        ];
    }
}

if (! function_exists('mm_order_status_label')) {
    function mm_order_status_label(string $status): string
    {
        return (mm_order_flow() + ['quoted' => '입금 대기', 'cancelled' => '취소'])[$status] ?? $status;
    }
}

if (! function_exists('mm_order_pay_label')) {
    /** 결제(입금) 상태만 따로: 미입금 / 확인 요청 / 결제 완료 / 취소 */
    function mm_order_pay_label(string $status): string
    {
        return match ($status) {
            'requested', 'quoted' => '미입금',
            'proof'               => '확인 요청',
            'cancelled'           => '취소',
            default               => '결제 완료',
        };
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

if (! function_exists('mm_maps_key')) {
    /** 구글맵 키: 관리자 설정값, 비어 있으면 서버 환경변수 GOOGLE_MAPS_KEY(.env 포함) */
    function mm_maps_key(): string
    {
        $k = trim((string) mm_setting('google_maps_key', ''));
        if ($k === '') {
            $k = trim((string) (env('GOOGLE_MAPS_KEY') ?: getenv('GOOGLE_MAPS_KEY') ?: ''));
        }
        return $k;
    }
}

if (! function_exists('mm_ago')) {
    /** 지난 시간 짧게: 방금 / 12분 전 / 3시간 전 / 2일 전 / 10.07 */
    function mm_ago(?string $dt): string
    {
        if (! $dt) {
            return '-';
        }
        $s = time() - strtotime($dt);
        if ($s < 60) {
            return '방금';
        }
        if ($s < 3600) {
            return intdiv($s, 60) . '분 전';
        }
        if ($s < 86400) {
            return intdiv($s, 3600) . '시간 전';
        }
        if ($s < 86400 * 7) {
            return intdiv($s, 86400) . '일 전';
        }
        return date('m.d', strtotime($dt));
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
     * 비즈니스 인증 규정. 가입·재제출·관리자 검수·이용안내가 모두 이 정의를 쓴다.
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
                if ($src !== '' && (preg_match('#^(uploads|assets/img/products)/[A-Za-z0-9/_.-]+$#', $src) || preg_match('#^https?://#i', $src))) {
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

if (! function_exists('mm_wish_guest_ids')) {
    /** 비회원 관심상품: 쿠키 mm_wish 의 상품 번호 목록(최근 것이 앞). */
    function mm_wish_guest_ids(): array
    {
        $raw = (string) service('request')->getCookie('mm_wish');
        return array_values(array_unique(array_filter(array_map('intval', explode(',', $raw)))));
    }
}

if (! function_exists('mm_wish_ids')) {
    /** 관심상품 번호 목록. 구매자는 DB, 그 외(비회원)는 쿠키. */
    function mm_wish_ids(): array
    {
        static $ids = null;
        if ($ids !== null) {
            return $ids;
        }
        $u = mm_user();
        if ($u && $u['role'] === 'buyer') {
            $rows = (new \App\Models\WishModel())->select('product_id')->where('user_id', $u['id'])->orderBy('id', 'DESC')->findAll();
            return $ids = array_map('intval', array_column($rows, 'product_id'));
        }
        return $ids = mm_wish_guest_ids();
    }
}

if (! function_exists('mm_wish_merge_guest')) {
    /** 로그인·가입 직후: 비회원 때 담은 관심상품을 계정으로 옮긴다. 쿠키 삭제는 호출한 쪽 응답에서. */
    function mm_wish_merge_guest(int $userId): void
    {
        $wm = new \App\Models\WishModel();
        foreach (array_reverse(mm_wish_guest_ids()) as $pid) {
            if (! $wm->where('user_id', $userId)->where('product_id', $pid)->countAllResults()) {
                $wm->insert(['user_id' => $userId, 'product_id' => $pid]);
            }
        }
    }
}

if (! function_exists('mm_repeat_ids')) {
    /**
     * '재구매 많음' 상품 번호. 같은 상품을 서로 다른 주문으로 2번 이상 산 회원이
     * repeat_badge_min(기본 3)명 이상인 상품. 취소 주문 제외.
     */
    function mm_repeat_ids(): array
    {
        static $ids = null;
        if ($ids !== null) {
            return $ids;
        }
        $min  = max(1, (int) mm_setting('repeat_badge_min', 3));
        $rows = db_connect()->query(
            'SELECT product_id, COUNT(*) AS n FROM (
                SELECT oi.product_id, o.user_id FROM order_items oi JOIN orders o ON o.id = oi.order_id
                WHERE o.status <> ? GROUP BY oi.product_id, o.user_id HAVING COUNT(DISTINCT o.id) >= 2
             ) t GROUP BY product_id HAVING COUNT(*) >= ?',
            ['cancelled', $min]
        )->getResultArray();
        return $ids = array_map('intval', array_column($rows, 'product_id'));
    }
}

if (! function_exists('mm_unread_count')) {
    /** 읽지 않은 사이트 알림 수. */
    function mm_unread_count(): int
    {
        static $n = null;
        if ($n === null) {
            $u = mm_user();
            $n = ($u && $u['role'] === 'buyer') ? (new \App\Models\NotificationModel())->where('user_id', $u['id'])->where('read_at', null)->countAllResults() : 0;
        }
        return $n;
    }
}

if (! function_exists('mm_save_setting')) {
    /** 설정 한 줄 저장(없으면 추가). */
    function mm_save_setting(string $key, string $value): void
    {
        $sm  = new SettingModel();
        $row = $sm->where('skey', $key)->first();
        $row ? $sm->update($row['id'], ['svalue' => $value]) : $sm->insert(['skey' => $key, 'svalue' => $value]);
    }
}

if (! function_exists('mm_welcome_defaults')) {
    /** 가입 랜딩(/welcome) 편집 항목 기본값. {가입포인트} 같은 자리표시는 설정값으로 바뀐다. */
    function mm_welcome_defaults(): array
    {
        return [
            'tag'    => '무료 회원가입',
            'title1' => '한국 브랜드 도매몰에서',
            'title2' => '지금 바로 주문해 보세요',
            'sub'    => '미얀마 소매상과 온라인 셀러를 위한 회원제 도매몰입니다',
            'band1'  => '한국 브랜드 상품을 미얀마에서 도매가로,',
            'band2'  => 'XINDELELA 도매몰에서 시작하세요',
            'pics'   => array_map(static fn ($k) => 'assets/img/demo/' . $k . '.jpg', ['p1', 'p3', 'p5', 'p8', 'p2', 'p10', 'p6', 'p9']),
            'faq'    => [
                ['q' => '누구나 회원이 될 수 있나요?', 'a' => '오프라인 매장, 온라인 판매, 판매 네트워크 중 하나라도 운영하고 있다면 가입할 수 있습니다. 개인 사용 목적의 구매는 승인되지 않습니다.'],
                ['q' => '인증은 얼마나 걸리나요?', 'a' => '담당자가 제출 자료를 직접 확인하며 {인증기간}합니다. 자료가 부족하면 이유를 알려 드리고, 마이페이지에서 자료를 추가해 다시 제출할 수 있습니다.'],
                ['q' => '가입 축하 포인트는 언제 받나요?', 'a' => '비즈니스 인증이 승인되는 순간 {가입포인트}가 적립됩니다. 1P는 1 {통화}이며, 주문할 때 상품 금액의 {사용한도}까지 쓸 수 있습니다.'],
                ['q' => '몇 개부터 주문할 수 있나요?', 'a' => '상품마다 최소주문수량이 정해져 있고, 그 수량부터 1개씩 늘릴 수 있습니다. 수량이 많을수록 낮은 단가가 자동으로 적용됩니다.'],
                ['q' => '결제는 어떻게 하나요?', 'a' => '주문을 신청하면 결제 금액과 KBZPay 계좌가 안내됩니다. 송금한 뒤 주문 상세에서 송금자 이름을 적고 입금 확인을 요청하면 됩니다.'],
                ['q' => '가입비가 있나요?', 'a' => '가입과 인증은 무료입니다.'],
            ],
        ];
    }
}

if (! function_exists('mm_tokens')) {
    /** 관리자 편집 문구 속 자리표시를 지금 설정값으로 바꾼다. */
    function mm_tokens(string $s): string
    {
        return strtr($s, [
            '{가입포인트}' => number_format((int) mm_setting('points_signup_bonus', 0)) . 'P',
            '{인증기간}'   => (string) mm_setting('verify_sla_text', ''),
            '{사용한도}'   => (int) mm_setting('points_use_max_pct', 30) . '%',
            '{통화}'       => (string) mm_setting('currency_symbol', 'Ks'),
        ]);
    }
}

if (! function_exists('mm_welcome')) {
    /** 가입 랜딩 설정: 기본값 + 관리자 저장값(settings.welcome_json). $raw=true 면 자리표시를 그대로 둔다(편집 화면용). */
    function mm_welcome(bool $raw = false): array
    {
        $d = mm_welcome_defaults();
        $s = json_decode((string) mm_setting('welcome_json', ''), true);
        if (is_array($s)) {
            foreach ($d as $k => $v) {
                if (isset($s[$k]) && (is_array($v) ? (is_array($s[$k]) && $s[$k]) : trim((string) $s[$k]) !== '')) {
                    $d[$k] = $s[$k];
                }
            }
        }
        if (! $raw) {
            foreach (['tag', 'title1', 'title2', 'sub', 'band1', 'band2'] as $k) {
                $d[$k] = mm_tokens($d[$k]);
            }
            $d['faq'] = array_map(static fn ($f) => ['q' => mm_tokens($f['q']), 'a' => mm_tokens($f['a'])], $d['faq']);
        }
        return $d;
    }
}
