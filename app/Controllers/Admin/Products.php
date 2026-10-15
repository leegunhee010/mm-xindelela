<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\SupplierModel;

class Products extends Base
{
    public function index(): string
    {
        $pm = new ProductModel();
        $st = (string) ($this->request->getGet('status') ?? 'pending');
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
            'counts' => ['pending' => $pm->where('status', 'pending')->countAllResults(), 'active' => $pm->where('status', 'active')->countAllResults(), 'hidden' => $pm->where('status', 'hidden')->countAllResults(), 'rejected' => $pm->where('status', 'rejected')->countAllResults()]]);
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
        if (! in_array($st, ['active', 'hidden', 'rejected', 'pending'], true)) {
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
}
