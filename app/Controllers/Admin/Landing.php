<?php

namespace App\Controllers\Admin;

use App\Libraries\Uploader;

/** 가입 랜딩(/welcome) 편집: 첫 화면 문구·사진·마무리 문구·FAQ. settings.welcome_json 에 저장. */
class Landing extends Base
{
    private const MAX_PICS = 12;

    /** 편집할 언어 (ko|my). 미얀마어는 문구·FAQ만 따로 저장하고 사진은 한국어와 같이 씀 */
    private function lang(): string
    {
        return ($this->request->getGetPost('lang') === 'my') ? 'my' : 'ko';
    }

    public function index(): string
    {
        $lang = $this->lang();
        $GLOBALS['MM_LANG_FORCE'] = $lang;
        $w = mm_welcome(true);
        $d = mm_welcome_defaults();
        unset($GLOBALS['MM_LANG_FORCE']);
        return $this->page('admin/landing', ['title' => '가입 랜딩', 'w' => $w, 'd' => $d, 'lang' => $lang]);
    }

    public function save()
    {
        $req  = $this->request;
        $lang = $this->lang();
        $GLOBALS['MM_LANG_FORCE'] = $lang;
        $w = mm_welcome(true);
        unset($GLOBALS['MM_LANG_FORCE']);
        foreach (['tag', 'title1', 'title2', 'sub', 'band1', 'band2'] as $k) {
            $w[$k] = trim((string) $req->getPost($k));
        }

        // 사진: 기존 순서·삭제 + 새 업로드, 또는 기본 사진으로 되돌리기 (미얀마어 편집에서는 사진을 건드리지 않음)
        if ($lang === 'my') {
            unset($w['pics']);
        } elseif ($req->getPost('pics_reset')) {
            $w['pics'] = [];
        } else {
            $keep  = [];
            $order = (array) $req->getPost('pic_order');
            foreach ((array) $req->getPost('pic') as $i => $path) {
                if (! in_array($path, (array) $req->getPost('pic_del'), true)) {
                    $keep[] = ['p' => (string) $path, 'o' => (int) ($order[$i] ?? $i)];
                }
            }
            usort($keep, static fn ($a, $b) => $a['o'] <=> $b['o']);
            $pics = array_column($keep, 'p');
            try {
                foreach (Uploader::saveMany((array) ($req->getFileMultiple('new_pics') ?? []), 'landing', Uploader::IMG, 5) as $u) {
                    $pics[] = $u['path'];
                }
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
            $w['pics'] = array_slice($pics, 0, self::MAX_PICS);
        }

        $faq = [];
        $qs  = (array) $req->getPost('faq_q');
        $as  = (array) $req->getPost('faq_a');
        foreach ($qs as $i => $q) {
            $q = trim((string) $q);
            $a = trim((string) ($as[$i] ?? ''));
            if ($q !== '' && $a !== '') {
                $faq[] = ['q' => $q, 'a' => $a];
            }
        }
        $w['faq'] = $faq;

        mm_save_setting($lang === 'my' ? 'welcome_json_my' : 'welcome_json', json_encode($w, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return redirect()->to('/admin/landing' . ($lang === 'my' ? '?lang=my' : ''))->with('success', '가입 랜딩을 저장했습니다. 비워 둔 칸은 기본 문구로 나옵니다.');
    }
}
