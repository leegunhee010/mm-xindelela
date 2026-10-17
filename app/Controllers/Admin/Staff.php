<?php

namespace App\Controllers\Admin;

use App\Models\AdminLogModel;
use App\Models\UserModel;

/** 담당자(관리자) 계정 관리. 대표 관리자(owner)만 쓸 수 있다. */
class Staff extends Base
{
    private function ownerOnly()
    {
        return (mm_user()['admin_level'] ?? '') === 'owner' ? null : redirect()->to('/admin')->with('error', '담당자 계정은 대표 관리자만 관리할 수 있습니다.');
    }

    public function index()
    {
        if ($r = $this->ownerOnly()) {
            return $r;
        }
        $rows = (new UserModel())->where('role', 'admin')->orderBy('id')->findAll();
        $lm   = new AdminLogModel();
        foreach ($rows as &$r) {
            $r['last']  = $lm->where('admin_id', $r['id'])->orderBy('id', 'DESC')->first();
            $r['month'] = $lm->where('admin_id', $r['id'])->where('created_at >=', date('Y-m-01 00:00:00'))->countAllResults();
        }
        $edit = (int) $this->request->getGet('edit');
        return $this->page('admin/staff', ['title' => '담당자 계정', 'rows' => $rows, 'edit' => $edit ? (new UserModel())->where('role', 'admin')->find($edit) : null]);
    }

    public function save()
    {
        if ($r = $this->ownerOnly()) {
            return $r;
        }
        $um    = new UserModel();
        $id    = (int) $this->request->getPost('id');
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $name  = trim((string) $this->request->getPost('name'));
        $pw    = (string) $this->request->getPost('password');
        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', '이름과 이메일을 확인해 주세요.');
        }
        $dup = $um->where('email', $email)->where('id !=', $id ?: 0)->first();
        if ($dup) {
            return redirect()->back()->withInput()->with('error', '이미 쓰고 있는 이메일입니다.');
        }
        if (! $id && strlen($pw) < 8) {
            return redirect()->back()->withInput()->with('error', '새 담당자 비밀번호는 8자 이상으로 정해 주세요.');
        }
        if ($pw !== '' && strlen($pw) < 8) {
            return redirect()->back()->withInput()->with('error', '비밀번호는 8자 이상이어야 합니다.');
        }
        $level  = $this->request->getPost('admin_level') === 'owner' ? 'owner' : 'staff';
        $status = $this->request->getPost('status') === 'suspended' ? 'suspended' : 'approved';
        if ($id === (int) mm_user()['id'] && ($level !== 'owner' || $status !== 'approved')) {
            return redirect()->back()->with('error', '내 계정의 권한을 낮추거나 정지할 수는 없습니다.');
        }
        $data = ['name' => $name, 'email' => $email, 'phone' => trim((string) $this->request->getPost('phone')), 'admin_level' => $level, 'status' => $status];
        if ($pw !== '') {
            $data['password_hash'] = password_hash($pw, PASSWORD_DEFAULT);
        }
        if ($id) {
            $um->where('role', 'admin')->where('id', $id)->set($data)->update();
        } else {
            $data['role'] = 'admin';
            $um->insert($data);
        }
        return redirect()->to('/admin/staff')->with('success', '담당자 계정을 저장했습니다.');
    }
}
