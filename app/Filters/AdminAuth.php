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
        // 초기 비밀번호(admin1234 등)로 들어온 경우 비밀번호부터 바꾸게 함
        if (session()->get('admin_pw_weak') && ! in_array(trim($request->getUri()->getPath(), '/'), ['admin/password', 'admin/logout'], true)) {
            return redirect()->to('/admin/password')->with('error', '초기 비밀번호로 로그인했습니다. 먼저 새 비밀번호로 바꿔 주세요.');
        }
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
