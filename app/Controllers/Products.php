<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\SupplierModel;

class Products extends BaseController
{
    private const PER_PAGE = 20;

    public function index(): string
    {
        return $this->listing(null);
    }

    public function category(int $id): string
    {
        return $this->listing($id);
    }

    private function listing(?int $catId): string
    {
        $pm   = new ProductModel();
        $q    = trim((string) $this->request->getGet('q'));
        $sort = (string) $this->request->getGet('sort') ?: 'new';
        $cat  = $catId ?: (int) $this->request->getGet('cat');

        $b = $pm->where('status', 'active');
        if ($cat) {
            $b->where('category_id', $cat);
        }
        if ($q !== '') {
            if ((string) $this->request->getGet('sf') === 'brand') {
                $b->like('brand', $q);
            } else {
                $b->groupStart()->like('name', $q)->orLike('brand', $q)->orLike('summary', $q)->groupEnd();
            }
        }
        match ($sort) {
            'low'   => $b->orderBy('price', 'ASC'),
            'high'  => $b->orderBy('price', 'DESC'),
            'pop'   => $b->orderBy('order_count', 'DESC')->orderBy('views', 'DESC'),
            default => $b->orderBy('id', 'DESC'),
        };
        $products = $b->paginate(self::PER_PAGE, 'p');
        $catRow   = $cat ? (new CategoryModel())->find($cat) : null;

        return $this->render('products/index', [
            'title'      => $catRow ? $catRow['name'] : ($q !== '' ? '"' . $q . '" 검색결과' : '전체 상품'),
            'products'   => $products,
            'pager'      => $pm->pager,
            'categories' => (new CategoryModel())->where('is_active', 1)->orderBy('sort')->findAll(),
            'cat'        => $cat,
            'catRow'     => $catRow,
            'q'          => $q,
            'sort'       => $sort,
            'total'      => $pm->pager->getTotal('p'),
            'suppliers'  => $this->supplierMap($products),
        ]);
    }

    public function show(int $id): string
    {
        $pm = new ProductModel();
        $p  = $pm->find($id);
        $u  = mm_user();
        $isOwner = $u && $u['role'] === 'supplier' && (new SupplierModel())->where('user_id', $u['id'])->where('id', $p['supplier_id'] ?? 0)->countAllResults() > 0;
        if (! $p || ($p['status'] !== 'active' && ! $isOwner && ! ($u && $u['role'] === 'admin'))) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $pm->where('id', $id)->set('views', 'views + 1', false)->update();

        $supplier = (new SupplierModel())->find($p['supplier_id']);
        $related  = $pm->where('status', 'active')->where('category_id', $p['category_id'])->where('id !=', $id)->orderBy('views', 'DESC')->findAll(5);

        return $this->render('products/show', [
            'title'    => $p['name'],
            'p'        => $p,
            'images'   => mm_json($p['images']),
            'tiers'    => mm_json($p['tiers']),
            'supplier' => $supplier,
            'category' => $p['category_id'] ? (new CategoryModel())->find($p['category_id']) : null,
            'related'  => $related,
            'approved' => mm_is_approved(),
        ]);
    }

    public function supplier(int $id): string
    {
        $s = (new SupplierModel())->find($id);
        if (! $s || $s['status'] !== 'approved') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $pm       = new ProductModel();
        $products = $pm->where('status', 'active')->where('supplier_id', $id)->orderBy('id', 'DESC')->paginate(self::PER_PAGE, 'p');
        return $this->render('products/index', [
            'title'      => $s['company_name'],
            'products'   => $products,
            'pager'      => $pm->pager,
            'categories' => (new CategoryModel())->where('is_active', 1)->orderBy('sort')->findAll(),
            'cat'        => 0,
            'catRow'     => null,
            'q'          => '',
            'sort'       => 'new',
            'total'      => $pm->pager->getTotal('p'),
            'suppliers'  => [$id => $s],
            'supplierPage' => $s,
        ]);
    }

    private function supplierMap(array $products): array
    {
        $ids = array_values(array_unique(array_map(static fn ($p) => (int) $p['supplier_id'], $products)));
        if (! $ids) {
            return [];
        }
        $map = [];
        foreach ((new SupplierModel())->whereIn('id', $ids)->findAll() as $s) {
            $map[(int) $s['id']] = $s;
        }
        return $map;
    }
}
