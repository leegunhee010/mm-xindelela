<?php

namespace App\Controllers\Admin;

use App\Libraries\Uploader;
use App\Models\SettingModel;

/**
 * SEO 설정: 검색 결과 제목·설명, 공유 이미지(og:image), 파비콘, 검색엔진 노출 여부.
 * 값은 settings 테이블(seo_*)에 저장하고 layouts/main 의 <head> 가 읽는다.
 */
class Seo extends Base
{
    private const KEYS = ['seo_title', 'seo_description', 'seo_keywords', 'seo_og_image', 'seo_favicon', 'seo_indexable'];

    public function index(): string
    {
        $vals = [];
        foreach ((new SettingModel())->whereIn('skey', self::KEYS)->findAll() as $r) {
            $vals[$r['skey']] = $r['svalue'];
        }
        return $this->page('admin/seo', ['title' => 'SEO 설정', 'v' => $vals]);
    }

    public function save()
    {
        $set = [
            'seo_title'       => mb_substr(trim((string) $this->request->getPost('seo_title')), 0, 120),
            'seo_description' => mb_substr(trim((string) $this->request->getPost('seo_description')), 0, 300),
            'seo_keywords'    => mb_substr(trim((string) $this->request->getPost('seo_keywords')), 0, 300),
            'seo_indexable'   => $this->request->getPost('seo_indexable') ? '1' : '0',
        ];
        try {
            if ($og = Uploader::save($this->request->getFile('og_image'), 'seo', Uploader::IMG, 5)) {
                $set['seo_og_image'] = $og;
            }
            $ico = ['image/png', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/svg+xml', 'image/webp'];
            if ($fv = Uploader::save($this->request->getFile('favicon'), 'seo', $ico, 1)) {
                $set['seo_favicon'] = $fv;
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        if ($this->request->getPost('del_og')) {
            $set['seo_og_image'] = '';
        }
        if ($this->request->getPost('del_favicon')) {
            $set['seo_favicon'] = '';
        }
        $sm = new SettingModel();
        foreach ($set as $k => $v) {
            $row = $sm->where('skey', $k)->first();
            $row ? $sm->update($row['id'], ['svalue' => $v]) : $sm->insert(['skey' => $k, 'svalue' => $v]);
        }
        return redirect()->to('/admin/seo')->with('success', 'SEO 설정을 저장했습니다.');
    }
}
