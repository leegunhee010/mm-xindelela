<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** 구매자 로그인 필수 (승인 여부는 컨트롤러에서 판단). */
class BuyerAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $u = mm_user();
        if ($u === null || $u['role'] !== 'buyer') {
            session()->set('redirect_after', current_url());
            return redirect()->to('/login')->with('error', '로그인이 필요합니다.');
        }
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
