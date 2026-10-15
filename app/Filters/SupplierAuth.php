<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SupplierAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $u = mm_user();
        if ($u === null || $u['role'] !== 'supplier') {
            return redirect()->to('/supplier/login')->with('error', '공급사 로그인이 필요합니다.');
        }
        if ($u['status'] !== 'approved') {
            return redirect()->to('/supplier/pending');
        }
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
