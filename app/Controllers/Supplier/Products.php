<?php

namespace App\Controllers\Supplier;

use App\Libraries\Uploader;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class Products extends Base
{
    public function index(): string
    {
        $s  = $this->sup();
        $pm = new ProductModel();
        $st = (string) $this->request->getGet('status');
        $b  = $pm->where('supplier_id', $s['id']);
        if ($st !== '') {
            $b->where('status', $st);
        }
        return $this->page('supplier/products', [
            'title'    => '상품 관리',
            'products' => $b->orderBy('id', 'DESC')->paginate(30, 'p'),
            'pager'    => $pm->pager,
            'st'       => $st,
            'cats'     => $this->catMap(),
        ]);
    }

    private function catMap(): array
    {
        $m = [];
        foreach ((new CategoryModel())->orderBy('sort')->findAll() as $c) {
            $m[$c['id']] = $c['name'];
        }
        return $m;
    }

    public function form(?int $id = null): string
    {
        $s = $this->sup();
        $p = null;
        if ($id) {
            $p = (new ProductModel())->where('supplier_id', $s['id'])->find($id);
            if (! $p) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }
        return $this->page('supplier/product_form', [
            'title'      => $p ? '상품 수정' : '상품 등록',
            'p'          => $p,
            'tiers'      => $p ? mm_json($p['tiers']) : [],
            'images'     => $p ? mm_json($p['images']) : [],
            'categories' => (new CategoryModel())->where('is_active', 1)->orderBy('sort')->findAll(),
        ]);
    }

    public function save()
    {
        $s  = $this->sup();
        $pm = new ProductModel();
        $id = (int) $this->request->getPost('id');
        $p  = $id ? $pm->where('supplier_id', $s['id'])->find($id) : null;
        if ($id && ! $p) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        if (! $this->validate(['name' => 'required|max_length[255]', 'price' => 'required|integer', 'moq' => 'required|integer'])) {
            return redirect()->back()->withInput()->with('error', '상품명·도매가·최소수량은 필수입니다.');
        }
        // 수량별 단가
        $tiers = [];
        foreach ((array) $this->request->getPost('tier_qty') as $i => $q) {
            $q  = (int) $q;
            $pr = (int) (((array) $this->request->getPost('tier_price'))[$i] ?? 0);
            if ($q > 0 && $pr > 0) {
                $tiers[] = ['min_qty' => $q, 'price' => $pr];
            }
        }
        usort($tiers, static fn ($a, $b) => $a['min_qty'] <=> $b['min_qty']);

        // 이미지: 기존 유지분 + 새 업로드
        $images = array_values(array_filter((array) $this->request->getPost('keep_images')));
        try {
            foreach (Uploader::saveMany($this->request->getFileMultiple('images') ?: [], 'products', Uploader::IMG, 5) as $f) {
                $images[] = $f['path'];
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $data = [
            'supplier_id'  => $s['id'],
            'category_id'  => (int) $this->request->getPost('category_id') ?: null,
            'code'         => trim((string) $this->request->getPost('code')),
            'name'         => trim((string) $this->request->getPost('name')),
            'summary'      => trim((string) $this->request->getPost('summary')),
            'description'  => (string) $this->request->getPost('description'),
            'thumb'        => $images[0] ?? null,
            'images'       => json_encode($images, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'price'        => (int) $this->request->getPost('price'),
            'retail_price' => (int) $this->request->getPost('retail_price') ?: null,
            'tiers'        => json_encode($tiers),
            'moq'          => max(1, (int) $this->request->getPost('moq')),
            'unit'         => trim((string) $this->request->getPost('unit')) ?: '개',
            'stock'        => (int) $this->request->getPost('stock'),
            'origin'       => trim((string) $this->request->getPost('origin')) ?: '한국',
            'brand'        => trim((string) $this->request->getPost('brand')),
        ];
        $action = (string) $this->request->getPost('action'); // draft|submit|hide|show
        if ($action === 'draft') {
            $data['status'] = 'draft';
        } elseif ($action === 'hide') {
            $data['status'] = 'hidden';
        } elseif ($action === 'show' && $p && in_array($p['status'], ['hidden'], true)) {
            $data['status'] = 'active';
        } else {
            // 신규 또는 임시/반려 → 검수 요청. 이미 판매중이면 유지(관리자 재검수는 선택)
            $data['status'] = ($p && $p['status'] === 'active') ? 'active' : 'pending';
            $data['reject_reason'] = null;
        }
        if ($p) {
            $pm->update($id, $data);
        } else {
            $id = $pm->insert($data);
        }
        return redirect()->to('/supplier/products')->with('success', $data['status'] === 'pending' ? '저장했습니다. 관리자 검수 후 노출됩니다.' : '저장했습니다.');
    }

    public function delete(int $id)
    {
        $s = $this->sup();
        (new ProductModel())->where('supplier_id', $s['id'])->where('id', $id)->delete();
        return redirect()->to('/supplier/products')->with('success', '삭제했습니다.');
    }
}
