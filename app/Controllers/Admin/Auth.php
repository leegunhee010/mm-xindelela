<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        $u = mm_user();
        if ($u && $u['role'] === 'admin') {
            return redirect()->to('/admin');
        }
        return view('admin/login', ['title' => '관리자 로그인']);
    }

    public function attemptLogin()
    {
        $um   = new UserModel();
        $user = $um->where('email', strtolower(trim((string) $this->request->getPost('email'))))->where('role', 'admin')->first();
        if (! $user || ! password_verify((string) $this->request->getPost('password'), $user['password_hash'])) {
            return redirect()->back()->with('error', '이메일 또는 비밀번호가 올바르지 않습니다.');
        }
        session()->regenerate();
        session()->set('user_id', (int) $user['id']);
        $um->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        return redirect()->to('/admin');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}
