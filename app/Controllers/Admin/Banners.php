<?php

namespace App\Controllers\Admin;

use App\Libraries\Uploader;
use App\Models\BannerModel;

class Banners extends Base
{
    public function index(): string
    {
        return $this->page('admin/banners', ['title' => '메인 배너', 'rows' => (new BannerModel())->orderBy('sort')->findAll()]);
    }

    public function save()
    {
        $bm   = new BannerModel();
        $id   = (int) $this->request->getPost('id');
        $data = [
            'title'     => trim((string) $this->request->getPost('title')),
            'subtitle'  => trim((string) $this->request->getPost('subtitle')),
            'link'      => trim((string) $this->request->getPost('link')),
            'bg'        => trim((string) $this->request->getPost('bg')) ?: '#eef5e3',
            'sort'      => (int) $this->request->getPost('sort'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];
        try {
            if ($img = Uploader::save($this->request->getFile('image'), 'banners', Uploader::IMG, 5)) {
                $data['image'] = $img;
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        $id ? $bm->update($id, $data) : $bm->insert($data);
        return redirect()->to('/admin/banners')->with('success', '저장했습니다.');
    }

    public function delete(int $id)
    {
        (new BannerModel())->delete($id);
        return redirect()->to('/admin/banners');
    }
}
