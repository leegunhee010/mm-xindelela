<?php

namespace App\Controllers\Supplier;

use App\Controllers\BaseController;
use App\Models\SupplierModel;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        $u = mm_user();
        if ($u && $u['role'] === 'supplier') {
            return redirect()->to('/supplier');
        }
        return $this->render('supplier/login', ['title' => '공급사 로그인']);
    }

    public function attemptLogin()
    {
        $um   = new UserModel();
        $user = $um->where('email', strtolower(trim((string) $this->request->getPost('email'))))->where('role', 'supplier')->first();
        if (! $user || ! password_verify((string) $this->request->getPost('password'), $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', '이메일 또는 비밀번호가 올바르지 않습니다.');
        }
        session()->regenerate();
        session()->set('user_id', (int) $user['id']);
        $um->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        return redirect()->to('/supplier');
    }

    public function register(): string
    {
        return $this->render('supplier/register', ['title' => '공급사 입점 신청']);
    }

    public function attemptRegister()
    {
        if (! $this->validate([
            'email'        => 'required|valid_email',
            'password'     => 'required|min_length[6]',
            'password2'    => 'required|matches[password]',
            'company_name' => 'required|max_length[190]',
            'contact_name' => 'required|max_length[100]',
            'phone'        => 'required|max_length[50]',
        ])) {
            return redirect()->back()->withInput()->with('error', '필수 항목을 확인해 주세요. (비밀번호 6자 이상, 확인 일치)');
        }
        $um    = new UserModel();
        $email = strtolower(trim((string) $this->request->getPost('email')));
        if ($um->where('email', $email)->countAllResults()) {
            return redirect()->back()->withInput()->with('error', '이미 가입된 이메일입니다.');
        }
        $company = trim((string) $this->request->getPost('company_name'));
        $uid = $um->insert([
            'role'          => 'supplier',
            'email'         => $email,
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'name'          => trim((string) $this->request->getPost('contact_name')),
            'phone'         => trim((string) $this->request->getPost('phone')),
            'status'        => 'pending',
            'company_name'  => $company,
        ]);
        (new SupplierModel())->insert([
            'user_id'      => $uid,
            'company_name' => $company,
            'brand'        => trim((string) $this->request->getPost('brand')),
            'intro'        => trim((string) $this->request->getPost('intro')),
            'contact_name' => trim((string) $this->request->getPost('contact_name')),
            'phone'        => trim((string) $this->request->getPost('phone')),
            'address'      => trim((string) $this->request->getPost('address')),
            'biz_no'       => trim((string) $this->request->getPost('biz_no')),
            'status'       => 'pending',
        ]);
        session()->regenerate();
        session()->set('user_id', (int) $uid);
        return redirect()->to('/supplier/pending')->with('success', '입점 신청이 접수되었습니다. 담당자 확인 후 승인해 드립니다.');
    }

    public function pending()
    {
        $u = mm_user();
        if ($u && $u['role'] === 'supplier' && $u['status'] === 'approved') {
            return redirect()->to('/supplier');
        }
        return $this->render('supplier/pending', ['title' => '입점 심사 중']);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/supplier/login');
    }
}
