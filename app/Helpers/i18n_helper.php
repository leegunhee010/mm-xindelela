<?php

/**
 * 구매자 화면 다국어 (미얀마어 기본, 한국어 전환).
 *  - 화면 문구는 한국어 원문을 키로 쓴다: t('장바구니') → 미얀마어 사전(app/Language/my/ui.php)에서 찾고, 없으면 원문 그대로.
 *  - 언어는 쿠키 mm_lang (my|ko). 없으면 미얀마어. /lang/ko, /lang/my 로 바꾼다.
 *  - 관리자 화면(/admin)은 항상 한국어.
 *  - 새 문구를 넣으면 반드시 t() 로 감싸고 ui.php 에 미얀마어를 채운다 (scripts/i18n-extract.php 로 빠진 것 확인).
 */

if (! function_exists('mm_lang')) {
    function mm_lang(): string
    {
        static $lang = null;
        if ($lang !== null) {
            return $lang;
        }
        if (is_cli()) {
            return $lang = 'ko';
        }
        $path = trim((string) service('request')->getUri()->getPath(), '/');
        if ($path === 'admin' || str_starts_with($path, 'admin/')) {
            return $lang = 'ko';
        }
        $c = $_COOKIE['mm_lang'] ?? '';
        return $lang = in_array($c, ['ko', 'my'], true) ? $c : 'my';
    }
}

if (! function_exists('t')) {
    /** 한국어 원문 → 현재 언어 문구. %s 자리표시는 sprintf 로 채운다. */
    function t(string $ko, ...$args): string
    {
        static $dict = null;
        $s = $ko;
        if (mm_lang() === 'my') {
            if ($dict === null) {
                $f    = APPPATH . 'Language/my/ui.php';
                $dict = is_file($f) ? (require $f) : [];
            }
            $key = trim($ko);
            if (isset($dict[$key]) && $dict[$key] !== '') {
                // 앞뒤 공백은 원문 것을 유지
                $s = (preg_match('/^\s+/u', $ko, $m) ? $m[0] : '') . $dict[$key] . (preg_match('/\s+$/u', $ko, $m) ? $m[0] : '');
            }
        }
        return $args ? vsprintf($s, $args) : $s;
    }
}

if (! function_exists('mm_pname')) {
    /** 상품명: 미얀마어 화면이면 name_my(있을 때), 아니면 한국어 name */
    function mm_pname(?array $p): string
    {
        if (! $p) {
            return '';
        }
        return (mm_lang() === 'my' && ! empty($p['name_my'])) ? $p['name_my'] : (string) ($p['name'] ?? '');
    }
}

if (! function_exists('mm_sname')) {
    /** 공급사명: 미얀마어 화면이면 name_my(있을 때), 아니면 company_name */
    function mm_sname(?array $s): string
    {
        if (! $s) {
            return '';
        }
        return (mm_lang() === 'my' && ! empty($s['name_my'])) ? $s['name_my'] : (string) ($s['company_name'] ?? '');
    }
}

if (! function_exists('mm_tr_lines')) {
    /** 상세 맨 위 규격 글(줄마다 "라벨: 값")을 줄 단위로 번역. 사전에 없는 값(영문명·숫자 등)은 그대로 */
    function mm_tr_lines(string $text): string
    {
        if (mm_lang() !== 'my') {
            return $text;
        }
        $out = [];
        foreach (preg_split('/\r?\n/', $text) as $line) {
            if (str_contains($line, ': ')) {
                [$k, $v] = explode(': ', $line, 2);
                $out[]   = t($k) . ': ' . t($v);
            } else {
                $out[] = t($line);
            }
        }
        return implode("\n", $out);
    }
}
