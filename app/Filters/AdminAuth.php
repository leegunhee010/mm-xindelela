<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $u = mm_user();
        if ($u === null || $u['role'] !== 'admin' || ($u['status'] ?? '') === 'suspended') {
            return redirect()->to('/admin/login')->with('error', '관리자 로그인이 필요합니다.');
        }
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
