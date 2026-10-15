<?php

namespace App\Controllers\Supplier;

use App\Controllers\BaseController;
use App\Models\SupplierModel;

abstract class Base extends BaseController
{
    protected array $supplier;

    protected function sup(): array
    {
        if (! isset($this->supplier)) {
            $u = mm_user();
            $s = (new SupplierModel())->where('user_id', $u['id'])->first();
            if (! $s) {
                $id = (new SupplierModel())->insert(['user_id' => $u['id'], 'company_name' => $u['company_name'] ?: $u['name'], 'status' => $u['status']]);
                $s  = (new SupplierModel())->find($id);
            }
            $this->supplier = $s;
        }
        return $this->supplier;
    }

    protected function page(string $view, array $data = []): string
    {
        $data['supplier'] = $this->sup();
        return $this->render($view, $data, 'layouts/supplier');
    }
}
