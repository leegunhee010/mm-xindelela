<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;

abstract class Base extends BaseController
{
    protected function page(string $view, array $data = []): string
    {
        $data['badges'] = [
            'buyers'    => (new UserModel())->where('role', 'buyer')->where('status', 'pending')->countAllResults(),
            'suppliers' => (new UserModel())->where('role', 'supplier')->where('status', 'pending')->countAllResults(),
            'products'  => (new ProductModel())->where('status', 'pending')->countAllResults(),
            'orders'    => (new OrderModel())->whereIn('status', ['requested', 'proof'])->countAllResults(),
            'inquiries' => (new \App\Models\InquiryModel())->where('answer', null)->countAllResults(),
        ];
        return $this->render($view, $data, 'layouts/admin');
    }
}
