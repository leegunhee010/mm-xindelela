<?php

namespace App\Controllers\Admin;

use App\Models\ProductModel;
use App\Models\SupplierModel;
use App\Models\UserModel;

class Suppliers extends Base
{
    public function index(): string
    {
        $sm = new SupplierModel();
        $st = (string) ($this->request->getGet('status') ?? '');
        $b  = $sm;
        if ($st !== '') {
            $b = $sm->where('status', $st);
        }
        $rows = $b->orderBy('id', 'DESC')->paginate(30, 'p');
        $um   = new UserModel();
        $pm   = new ProductModel();
        foreach ($rows as &$r) {
            $r['user']  = $um->find($r['user_id']);
            $r['pcnt']  = $pm->where('supplier_id', $r['id'])->countAllResults();
        }
        return $this->page('admin/suppliers', ['title' => '공급사 관리', 'rows' => $rows, 'pager' => $sm->pager, 'st' => $st]);
    }

    public function show(int $id): string
    {
        $s = (new SupplierModel())->find($id);
        if (! $s) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $this->page('admin/supplier', ['title' => '공급사 상세', 's' => $s, 'u' => (new UserModel())->find($s['user_id']), 'products' => (new ProductModel())->where('supplier_id', $id)->orderBy('id', 'DESC')->findAll(30)]);
    }

    public function status(int $id)
    {
        $st = (string) $this->request->getPost('status');
        if (! in_array($st, ['pending', 'approved', 'rejected', 'suspended'], true)) {
            return redirect()->back();
        }
        $s = (new SupplierModel())->find($id);
        if ($s) {
            (new SupplierModel())->update($id, ['status' => $st]);
            (new UserModel())->update($s['user_id'], ['status' => $st, 'approved_at' => $st === 'approved' ? date('Y-m-d H:i:s') : null]);
            if ($st !== 'approved') {
                (new ProductModel())->where('supplier_id', $id)->where('status', 'active')->set(['status' => 'hidden'])->update();
            }
        }
        return redirect()->back()->with('success', '공급사 상태를 변경했습니다.');
    }

    public function save(int $id)
    {
        (new SupplierModel())->update($id, ['commission_rate' => (float) $this->request->getPost('commission_rate')]);
        return redirect()->back()->with('success', '저장했습니다.');
    }
}
