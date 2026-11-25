<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;

/** 로그인한 관리자 본인의 비밀번호 변경 */
class Account extends Base
{
    public function password(): string
    {
        return $this->page('admin/password', ['title' => '내 비밀번호 변경', 'weak' => (bool) session()->get('admin_pw_weak')]);
    }

    public function savePassword()
    {
        $u   = mm_user();
        $cur = (string) $this->request->getPost('current');
        $new = (string) $this->request->getPost('password');
        $re  = (string) $this->request->getPost('password2');
        if (! password_verify($cur, $u['password_hash'])) {
            return redirect()->back()->with('error', '현재 비밀번호가 맞지 않습니다.');
        }
        if (mb_strlen($new) < 10) {
            return redirect()->back()->with('error', '새 비밀번호는 10자 이상으로 정해 주세요.');
        }
        if ($new !== $re) {
            return redirect()->back()->with('error', '새 비밀번호 두 칸이 서로 다릅니다.');
        }
        if ($new === $cur || in_array($new, ['admin1234', 'staff1234'], true)) {
            return redirect()->back()->with('error', '기존·초기 비밀번호와 다른 비밀번호로 정해 주세요.');
        }
        (new UserModel())->update($u['id'], ['password_hash' => password_hash($new, PASSWORD_DEFAULT)]);
        session()->remove('admin_pw_weak');
        return redirect()->to('/admin')->with('success', '비밀번호를 바꿨습니다. 다음 로그인부터 새 비밀번호를 쓰세요.');
    }
}
