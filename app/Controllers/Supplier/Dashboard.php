<?php

namespace App\Controllers\Supplier;

use App\Libraries\Uploader;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\SupplierModel;

class Dashboard extends Base
{
    public function index(): string
    {
        $s   = $this->sup();
        $pm  = new ProductModel();
        $oim = new OrderItemModel();
        $om  = new OrderModel();
        $orderIds = array_column($oim->select('order_id')->where('supplier_id', $s['id'])->groupBy('order_id')->findAll(), 'order_id');
        $pendingShip = $orderIds ? $om->whereIn('id', $orderIds)->whereIn('status', ['paid', 'preparing'])->countAllResults() : 0;
        return $this->page('supplier/dashboard', [
            'title' => '공급사센터',
            'kpi'   => [
                'active'  => $pm->where('supplier_id', $s['id'])->where('status', 'active')->countAllResults(),
                'pending' => $pm->where('supplier_id', $s['id'])->where('status', 'pending')->countAllResults(),
                'toship'  => $pendingShip,
                'orders'  => count($orderIds),
            ],
            'recent' => $orderIds ? $om->whereIn('id', $orderIds)->orderBy('id', 'DESC')->findAll(8) : [],
        ]);
    }

    public function profile(): string
    {
        return $this->page('supplier/profile', ['title' => '공급사 정보']);
    }

    public function updateProfile()
    {
        $s = $this->sup();
        $data = [
            'company_name' => trim((string) $this->request->getPost('company_name')),
            'brand'        => trim((string) $this->request->getPost('brand')),
            'intro'        => trim((string) $this->request->getPost('intro')),
            'contact_name' => trim((string) $this->request->getPost('contact_name')),
            'phone'        => trim((string) $this->request->getPost('phone')),
            'address'      => trim((string) $this->request->getPost('address')),
            'biz_no'       => trim((string) $this->request->getPost('biz_no')),
        ];
        try {
            if ($logo = Uploader::save($this->request->getFile('logo'), 'suppliers', Uploader::IMG, 3)) {
                $data['logo'] = $logo;
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        (new SupplierModel())->update($s['id'], $data);
        return redirect()->to('/supplier/profile')->with('success', '저장했습니다.');
    }
}
