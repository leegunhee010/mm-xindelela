<?php

namespace App\Controllers\Admin;

use App\Libraries\Uploader;
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
            $r['user']  = $r['user_id'] ? $um->find($r['user_id']) : null;
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
        return $this->page('admin/supplier', ['title' => '공급사 상세', 's' => $s, 'u' => $s['user_id'] ? (new UserModel())->find($s['user_id']) : null, 'products' => (new ProductModel())->where('supplier_id', $id)->orderBy('id', 'DESC')->findAll(30)]);
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
            if ($st !== 'approved') {
                (new ProductModel())->where('supplier_id', $id)->where('status', 'active')->set(['status' => 'hidden'])->update();
            }
        }
        return redirect()->back()->with('success', '공급사 상태를 변경했습니다.');
    }

    public function form(?int $id = null): string
    {
        $s = $id ? (new SupplierModel())->find($id) : null;
        if ($id && ! $s) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $this->page('admin/supplier_form', ['title' => $s ? '공급사 수정' : '공급사 추가', 's' => $s, 'docs' => $s ? mm_json($s['doc_files'] ?? null) : []]);
    }

    /** 공급사 정보 저장 (추가·수정). 공급사 로그인은 없고 관리자가 직접 관리한다. */
    public function save(?int $id = null)
    {
        $sm = new SupplierModel();
        $id = $id ?: (int) $this->request->getPost('id');
        $s  = $id ? $sm->find($id) : null;
        // 상세 화면의 수수료율만 저장하는 짧은 폼
        if ($s && $this->request->getPost('company_name') === null) {
            $sm->update($id, ['commission_rate' => (float) $this->request->getPost('commission_rate')]);
            return redirect()->back()->with('success', '저장했습니다.');
        }
        if (! $this->validate(['company_name' => 'required|max_length[190]'])) {
            return redirect()->back()->withInput()->with('error', '회사명을 입력해 주세요.');
        }
        try {
            $docs = Uploader::saveMany($this->request->getFileMultiple('docs') ?: [], 'supplier-docs', Uploader::DOC, 10);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        $keep = array_values(array_filter(mm_json($s['doc_files'] ?? null), fn ($d) => in_array($d['path'], (array) $this->request->getPost('keep_docs'), true)));
        $data = [
            'company_name'    => trim((string) $this->request->getPost('company_name')),
            'brand'           => trim((string) $this->request->getPost('brand')),
            'intro'           => trim((string) $this->request->getPost('intro')),
            'contact_name'    => trim((string) $this->request->getPost('contact_name')),
            'phone'           => trim((string) $this->request->getPost('phone')),
            'address'         => trim((string) $this->request->getPost('address')),
            'biz_no'          => trim((string) $this->request->getPost('biz_no')),
            'website'         => trim((string) $this->request->getPost('website')),
            'doc_files'       => json_encode(array_merge($keep, $docs), JSON_UNESCAPED_UNICODE),
            'commission_rate' => (float) $this->request->getPost('commission_rate'),
        ];
        if ($s) {
            $sm->update($id, $data);
        } else {
            $data['user_id'] = 0;
            $data['status']  = 'approved';
            $id = $sm->insert($data);
        }
        return redirect()->to('/admin/suppliers/' . $id)->with('success', '공급사 정보를 저장했습니다.');
    }
}
