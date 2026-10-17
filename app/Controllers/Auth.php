<?php

namespace App\Controllers;

use App\Libraries\Uploader;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (mm_user()) {
            return redirect()->to('/');
        }
        return $this->render('auth/login', ['title' => '로그인']);
    }

    public function attemptLogin()
    {
        $um   = new UserModel();
        $user = $um->where('email', strtolower(trim((string) $this->request->getPost('email'))))->where('role', 'buyer')->first();
        if (! $user || ! password_verify((string) $this->request->getPost('password'), $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', '이메일 또는 비밀번호가 올바르지 않습니다.');
        }
        if ($user['status'] === 'suspended') {
            return redirect()->back()->with('error', '정지된 계정입니다. 고객센터로 문의해 주세요.');
        }
        session()->regenerate();
        session()->set('user_id', (int) $user['id']);
        $um->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        $to = session()->get('redirect_after') ?: '/';
        session()->remove('redirect_after');
        return redirect()->to($to);
    }

    public function register(): string
    {
        return $this->render('auth/register', [
            'title' => '회원가입',
            'ref'   => (string) $this->request->getGet('ref'),
        ]);
    }

    public function attemptRegister()
    {
        $rules = [
            'email'         => 'required|valid_email|max_length[190]',
            'password'      => 'required|min_length[6]',
            'password2'     => 'required|matches[password]',
            'name'          => 'required|max_length[100]',
            'phone'         => 'required|max_length[50]',
            'company_name'  => 'required|max_length[190]',
            'city'          => 'required|max_length[100]',
        ];
        if (! $this->validate($rules, [
            'email' => ['required' => '이메일을 입력해 주세요.', 'valid_email' => '이메일 형식이 올바르지 않습니다.'],
            'password' => ['required' => '비밀번호를 입력해 주세요.', 'min_length' => '비밀번호는 6자 이상입니다.'],
            'password2' => ['matches' => '비밀번호 확인이 일치하지 않습니다.'],
            'name' => ['required' => '담당자 이름을 입력해 주세요.'],
            'phone' => ['required' => '연락처를 입력해 주세요.'],
            'company_name' => ['required' => '상호(가게·페이지 이름)를 입력해 주세요.'],
            'city' => ['required' => '지역(도시)을 입력해 주세요.'],
        ])) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }
        $um    = new UserModel();
        $email = strtolower(trim((string) $this->request->getPost('email')));
        if ($um->where('email', $email)->countAllResults()) {
            return redirect()->back()->withInput()->with('error', '이미 가입된 이메일입니다.');
        }
        $fb    = trim((string) $this->request->getPost('fb_page'));
        $files = $this->request->getFileMultiple('docs') ?: [];
        try {
            $docs = Uploader::saveMany($files, 'docs', Uploader::DOC, 5);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        $vt = array_values(array_intersect(array_keys(mm_verify_types()), (array) $this->request->getPost('verify_types')));
        if (! $vt) {
            return redirect()->back()->withInput()->with('error', '오프라인 매장 · 온라인 판매 · 판매 네트워크 중 해당하는 것을 하나 이상 골라 주세요.');
        }
        if ($fb === '' && ! $docs) {
            return redirect()->back()->withInput()->with('error', '판매 채널 링크나 사진·캡처를 하나 이상 올려 주세요.');
        }
        // 추천 코드
        $referredBy = null;
        $ref        = strtoupper(trim((string) $this->request->getPost('ref')));
        if ($ref !== '') {
            $r = $um->where('referral_code', $ref)->where('role', 'buyer')->first();
            if (! $r) {
                return redirect()->back()->withInput()->with('error', '추천 코드가 올바르지 않습니다.');
            }
            $referredBy = (int) $r['id'];
        }
        do {
            $code = mm_referral_code();
        } while ($um->where('referral_code', $code)->countAllResults());

        $id = $um->insert([
            'role'          => 'buyer',
            'email'         => $email,
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'name'          => trim((string) $this->request->getPost('name')),
            'phone'         => trim((string) $this->request->getPost('phone')),
            'viber'         => trim((string) $this->request->getPost('viber')),
            'status'        => 'pending',
            'company_name'  => trim((string) $this->request->getPost('company_name')),
            'business_type' => count($vt) === 1 && $vt[0] !== 'network' ? $vt[0] : 'both',
            'verify_types'  => implode(',', $vt),
            'fb_page'       => $fb,
            'city'          => trim((string) $this->request->getPost('city')),
            'address'       => trim((string) $this->request->getPost('address')),
            'doc_files'     => json_encode($docs, JSON_UNESCAPED_UNICODE),
            'referral_code' => $code,
            'referred_by'   => $referredBy,
        ]);
        session()->regenerate();
        session()->set('user_id', (int) $id);
        return redirect()->to('/mypage/verify')->with('success', '가입이 완료되었습니다. 제출하신 자료를 확인한 뒤 ' . mm_setting('verify_sla_text', '영업일 기준 1일 이내') . ' 승인해 드립니다.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
