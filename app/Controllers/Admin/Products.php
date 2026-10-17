<?php

namespace App\Controllers\Admin;

use App\Libraries\Uploader;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\SupplierModel;

class Products extends Base
{
    public function index(): string
    {
        $pm = new ProductModel();
        $st = (string) ($this->request->getGet('status') ?? 'all');
        $q  = trim((string) $this->request->getGet('q'));
        $b  = $pm;
        if ($st !== '' && $st !== 'all') {
            $b = $b->where('status', $st);
        }
        if ($q !== '') {
            $b = $b->like('name', $q);
        }
        $rows = $b->orderBy('id', 'DESC')->paginate(30, 'p');
        $sm = new SupplierModel();
        $cm = new CategoryModel();
        $sups = $cats = [];
        foreach ($rows as &$r) {
            $r['supplier'] = $sups[$r['supplier_id']] ??= $sm->find($r['supplier_id']);
            $r['cat']      = $r['category_id'] ? ($cats[$r['category_id']] ??= $cm->find($r['category_id'])) : null;
        }
        return $this->page('admin/products', ['title' => '상품 관리', 'rows' => $rows, 'pager' => $pm->pager, 'st' => $st, 'q' => $q,
            'counts' => ['all' => $pm->countAllResults(), 'active' => $pm->where('status', 'active')->countAllResults(), 'hidden' => $pm->where('status', 'hidden')->countAllResults(), 'draft' => $pm->where('status', 'draft')->countAllResults()]]);
    }

    public function show(int $id): string
    {
        $p = (new ProductModel())->find($id);
        if (! $p) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $this->page('admin/product', ['title' => '상품 검수', 'p' => $p, 'images' => mm_json($p['images']), 'tiers' => mm_json($p['tiers']), 'supplier' => (new SupplierModel())->find($p['supplier_id']), 'cat' => $p['category_id'] ? (new CategoryModel())->find($p['category_id']) : null]);
    }

    public function status(int $id)
    {
        $st = (string) $this->request->getPost('status');
        if (! in_array($st, ['active', 'hidden', 'draft', 'rejected', 'pending'], true)) {
            return redirect()->back();
        }
        $data = ['status' => $st];
        if ($st === 'rejected') {
            $data['reject_reason'] = trim((string) $this->request->getPost('reason')) ?: '검수 기준 미달';
        }
        (new ProductModel())->update($id, $data);
        return redirect()->back()->with('success', '상품 상태: ' . mm_product_status_label($st));
    }

    public function best(int $id)
    {
        $p = (new ProductModel())->find($id);
        if ($p) {
            (new ProductModel())->update($id, ['is_best' => $p['is_best'] ? 0 : 1]);
        }
        return redirect()->back();
    }

    public function form(?int $id = null): string
    {
        $p = $id ? (new ProductModel())->find($id) : null;
        if ($id && ! $p) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $this->page('admin/product_form', [
            'title'      => $p ? '상품 수정' : '상품 등록',
            'p'          => $p,
            'tiers'      => $p ? mm_json($p['tiers']) : [],
            'images'     => $p ? mm_json($p['images']) : [],
            'categories' => (new CategoryModel())->where('is_active', 1)->orderBy('sort')->findAll(),
            'suppliers'  => (new SupplierModel())->orderBy('company_name')->findAll(),
        ]);
    }

    public function save()
    {
        $pm = new ProductModel();
        $id = (int) $this->request->getPost('id');
        $p  = $id ? $pm->find($id) : null;
        if ($id && ! $p) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        if (! $this->validate(['name' => 'required|max_length[255]', 'price' => 'required|integer', 'moq' => 'required|integer', 'supplier_id' => 'required|integer'])) {
            return redirect()->back()->withInput()->with('error', '공급사·상품명·도매가·최소수량은 필수입니다.');
        }
        $tiers = [];
        foreach ((array) $this->request->getPost('tier_qty') as $i => $q) {
            $q  = (int) $q;
            $pr = (int) (((array) $this->request->getPost('tier_price'))[$i] ?? 0);
            if ($q > 0 && $pr > 0) {
                $tiers[] = ['min_qty' => $q, 'price' => $pr];
            }
        }
        usort($tiers, static fn ($a, $b) => $a['min_qty'] <=> $b['min_qty']);
        $images = array_values(array_filter((array) $this->request->getPost('keep_images')));
        try {
            foreach (Uploader::saveMany($this->request->getFileMultiple('images') ?: [], 'products', Uploader::IMG, 5) as $f) {
                $images[] = $f['path'];
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        $status = (string) $this->request->getPost('status');
        $data = [
            'supplier_id'   => (int) $this->request->getPost('supplier_id'),
            'category_id'   => (int) $this->request->getPost('category_id') ?: null,
            'code'          => trim((string) $this->request->getPost('code')),
            'name'          => trim((string) $this->request->getPost('name')),
            'summary'       => trim((string) $this->request->getPost('summary')),
            'description'   => (string) $this->request->getPost('description'),
            'thumb'         => $images[0] ?? null,
            'images'        => json_encode($images, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'price'         => (int) $this->request->getPost('price'),
            'retail_price'  => (int) $this->request->getPost('retail_price') ?: null,
            'tiers'         => json_encode($tiers),
            'moq'           => max(1, (int) $this->request->getPost('moq')),
            'unit'          => trim((string) $this->request->getPost('unit')) ?: '개',
            'stock'         => (int) $this->request->getPost('stock'),
            'origin'        => trim((string) $this->request->getPost('origin')) ?: '한국',
            'brand'         => trim((string) $this->request->getPost('brand')),
            'status'        => in_array($status, ['active', 'hidden', 'draft'], true) ? $status : 'active',
            'reject_reason' => null,
        ];
        if ($p) {
            $pm->update($id, $data);
        } else {
            $id = $pm->insert($data);
        }
        return redirect()->to('/admin/products/' . $id . '/edit')->with('success', '저장했습니다.');
    }

    public function delete(int $id)
    {
        (new ProductModel())->delete($id);
        return redirect()->to('/admin/products')->with('success', '삭제했습니다.');
    }
}
