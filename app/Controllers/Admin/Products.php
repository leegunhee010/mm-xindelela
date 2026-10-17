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
        $pm  = new ProductModel();
        $g   = fn ($k) => trim((string) $this->request->getGet($k));
        $st  = $g('status') ?: 'all';
        $f   = ['q' => $g('q'), 'cat' => (int) $g('cat'), 'sup' => (int) $g('sup'), 'best' => $g('best') === '1', 'stock' => $g('stock'), 'sort' => $g('sort') ?: 'new', 'per' => in_array((int) $g('per'), [30, 50, 100], true) ? (int) $g('per') : 30];

        // 상태 탭 숫자는 다른 필터를 반영해서 센다
        $apply = function ($b) use ($f) {
            if ($f['q'] !== '') {
                $b->groupStart()->like('name', $f['q'])->orLike('brand', $f['q'])->orLike('code', $f['q'])->groupEnd();
            }
            if ($f['cat']) {
                $b->where('category_id', $f['cat']);
            }
            if ($f['sup']) {
                $b->where('supplier_id', $f['sup']);
            }
            if ($f['best']) {
                $b->where('is_best', 1);
            }
            if ($f['stock'] === 'low') {
                $b->where('stock <', 'moq * 3', false);
            } elseif ($f['stock'] === 'out') {
                $b->where('stock <', 'moq', false);
            }
            return $b;
        };
        $counts = [];
        foreach (['all', 'active', 'hidden', 'draft'] as $k) {
            $b = $apply(new ProductModel());
            if ($k !== 'all') {
                $b->where('status', $k);
            }
            $counts[$k] = $b->countAllResults();
        }
        $b = $apply($pm);
        if ($st !== 'all') {
            $b->where('status', $st);
        }
        $order = ['new' => ['id', 'DESC'], 'name' => ['name', 'ASC'], 'price' => ['price', 'ASC'], 'stock' => ['stock', 'ASC'], 'views' => ['views', 'DESC'], 'orders' => ['order_count', 'DESC']][$f['sort']] ?? ['id', 'DESC'];
        $rows = $b->orderBy($order[0], $order[1])->paginate($f['per'], 'p');
        $sm = new SupplierModel();
        $cm = new CategoryModel();
        $sups = $cats = [];
        foreach ($rows as &$r) {
            $r['supplier'] = $sups[$r['supplier_id']] ??= $sm->find($r['supplier_id']);
            $r['cat']      = $r['category_id'] ? ($cats[$r['category_id']] ??= $cm->find($r['category_id'])) : null;
        }
        return $this->page('admin/products', [
            'title'      => '상품 관리', 'rows' => $rows, 'pager' => $pm->pager, 'st' => $st, 'f' => $f, 'q' => $f['q'], 'counts' => $counts,
            'categories' => (new CategoryModel())->orderBy('sort')->findAll(),
            'suppliers'  => (new SupplierModel())->orderBy('company_name')->findAll(),
        ]);
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
        // 상세 이미지: 남길 것(순서 번호대로 정렬) + 새로 올린 것은 뒤에
        $keepD = (array) $this->request->getPost('keep_detail');
        $ordD  = (array) $this->request->getPost('detail_order');
        $detail = [];
        foreach ($keepD as $i => $path) {
            if ($path !== '') {
                $detail[] = ['p' => $path, 'o' => (int) ($ordD[$i] ?? 999)];
            }
        }
        usort($detail, fn ($x, $y) => $x['o'] <=> $y['o']);
        $detail = array_column($detail, 'p');
        try {
            foreach (Uploader::saveMany($this->request->getFileMultiple('detail_files') ?: [], 'products/detail', Uploader::IMG, 10) as $f) {
                $detail[] = $f['path'];
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
            'detail_images' => json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
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
