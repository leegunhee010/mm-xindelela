<?php

namespace App\Controllers;

/** 언어 전환: /lang/my, /lang/ko → 쿠키 저장 후 보던 화면으로 */
class Lang extends BaseController
{
    public function set(string $lang)
    {
        $lang = in_array($lang, ['ko', 'my'], true) ? $lang : 'my';
        $back = (string) $this->request->getServer('HTTP_REFERER');
        if ($back === '' || ! str_starts_with($back, base_url())) {
            $back = site_url('/');
        }
        return redirect()->to($back)->setCookie('mm_lang', $lang, 60 * 60 * 24 * 365, '', '/', '', false, false, 'Lax');
    }
}
