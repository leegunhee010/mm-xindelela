<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Categories extends Base
{
    public function index(): string
    {
        $rows = (new CategoryModel())->orderBy('sort')->orderBy('id')->findAll();
        $pm   = new ProductModel();
        foreach ($rows as &$r) {
            $r['pcnt'] = $pm->where('category_id', $r['id'])->countAllResults();
        }
        return $this->page('admin/categories', ['title' => '카테고리', 'rows' => $rows]);
    }

    public function save()
    {
        $cm   = new CategoryModel();
        $id   = (int) $this->request->getPost('id');
        $name = trim((string) $this->request->getPost('name'));
        if ($name === '') {
            return redirect()->back()->with('error', '이름을 입력해 주세요.');
        }
        $data = ['name' => $name, 'sort' => (int) $this->request->getPost('sort'), 'is_active' => $this->request->getPost('is_active') ? 1 : 0];
        if ($id) {
            $cm->update($id, $data);
        } else {
            $data['slug'] = 'c' . time();
            $cm->insert($data);
        }
        return redirect()->to('/admin/categories')->with('success', '저장했습니다.');
    }

    public function delete(int $id)
    {
        if ((new ProductModel())->where('category_id', $id)->countAllResults()) {
            return redirect()->back()->with('error', '상품이 있는 카테고리는 삭제할 수 없습니다. 비활성으로 바꿔 주세요.');
        }
        (new CategoryModel())->delete($id);
        return redirect()->to('/admin/categories')->with('success', '삭제했습니다.');
    }
}
