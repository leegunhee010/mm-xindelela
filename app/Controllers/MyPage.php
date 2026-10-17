<?php

namespace App\Controllers;

use App\Libraries\PointService;
use App\Libraries\Uploader;
use App\Models\InquiryModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\PointLogModel;
use App\Models\ProductModel;
use App\Models\UserModel;

class MyPage extends BaseController
{
    public function index(): string
    {
        $u  = mm_user();
        $om = new OrderModel();
        $counts = [];
        foreach (['requested', 'proof', 'paid', 'preparing', 'shipped', 'delivered'] as $s) {
            $counts[$s] = $om->where('user_id', $u['id'])->where('status', $s)->countAllResults();
        }
        return $this->render('mypage/index', [
            'title'   => '마이페이지',
            'counts'  => $counts,
            'recent'  => $om->where('user_id', $u['id'])->orderBy('id', 'DESC')->findAll(5),
            'refCount' => (new UserModel())->where('referred_by', $u['id'])->countAllResults(),
        ]);
    }

    public function orders(): string
    {
        $u  = mm_user();
        $om = new OrderModel();
        $orders = $om->where('user_id', $u['id'])->orderBy('id', 'DESC')->paginate(20, 'p');
        $oim = new OrderItemModel();
        foreach ($orders as &$o) {
            $o['items'] = $oim->where('order_id', $o['id'])->findAll();
        }
        return $this->render('mypage/orders', ['title' => '주문 내역', 'orders' => $orders, 'pager' => $om->pager]);
    }

    public function order(int $id): string
    {
        $u = mm_user();
        $o = (new OrderModel())->where('user_id', $u['id'])->find($id);
        if (! $o) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $this->render('mypage/order', [
            'title' => '주문 상세',
            'o'     => $o,
            'items' => (new OrderItemModel())->where('order_id', $id)->findAll(),
            'mapsKey' => trim((string) mm_setting('google_maps_key', '')),
        ]);
    }

    /** 입금 확인 요청: 송금자 이름만 받고 관리자가 KBZPay 내역에서 확인한다. */
    public function proof(int $id)
    {
        $u  = mm_user();
        $om = new OrderModel();
        $o  = $om->where('user_id', $u['id'])->find($id);
        if (! $o || ! in_array($o['status'], ['requested', 'quoted', 'proof'], true)) {
            return redirect()->back()->with('error', '입금 확인을 요청할 수 있는 상태가 아닙니다.');
        }
        $name = trim((string) $this->request->getPost('payment_note'));
        if ($name === '') {
            return redirect()->back()->with('error', '송금한 사람 이름을 적어 주세요.');
        }
        $om->update($id, [
            'payment_note' => mb_substr($name, 0, 100),
            'proof_at'     => date('Y-m-d H:i:s'),
            'status'       => 'proof',
        ]);
        return redirect()->to('/mypage/orders/' . $id)->with('success', '입금 확인을 요청했습니다. 확인되면 바로 발주합니다.');
    }

    public function cancel(int $id)
    {
        $u  = mm_user();
        $om = new OrderModel();
        $o  = $om->where('user_id', $u['id'])->find($id);
        if (! $o || ! in_array($o['status'], ['requested', 'quoted', 'proof'], true)) {
            return redirect()->back()->with('error', '취소할 수 있는 상태가 아닙니다.');
        }
        $om->update($id, ['status' => 'cancelled', 'cancelled_at' => date('Y-m-d H:i:s')]);
        (new PointService())->onOrderCancelled($o);
        return redirect()->to('/mypage/orders/' . $id)->with('success', '주문 신청을 취소했습니다.');
    }

    /** 배송지 수정: 입금 확인 전(입금 대기·입금 확인 요청)까지만. */
    public function address(int $id)
    {
        $u  = mm_user();
        $om = new OrderModel();
        $o  = $om->where('user_id', $u['id'])->find($id);
        if (! $o || ! in_array($o['status'], ['requested', 'quoted', 'proof'], true)) {
            return redirect()->back()->with('error', '입금 확인 후에는 배송지를 바꿀 수 없습니다. 고객센터로 문의해 주세요.');
        }
        if (! $this->validate([
            'ship_name'    => 'required|max_length[100]',
            'ship_phone'   => 'required|max_length[50]',
            'ship_city'    => 'required|max_length[100]',
            'ship_address' => 'required|max_length[255]',
        ])) {
            return redirect()->back()->withInput()->with('error', '받는 사람·연락처·도시·상세 주소를 모두 입력해 주세요.');
        }
        $lat = $this->request->getPost('ship_lat');
        $lng = $this->request->getPost('ship_lng');
        $om->update($id, [
            'ship_name'    => trim((string) $this->request->getPost('ship_name')),
            'ship_phone'   => trim((string) $this->request->getPost('ship_phone')),
            'ship_city'    => trim((string) $this->request->getPost('ship_city')),
            'ship_address' => trim((string) $this->request->getPost('ship_address')),
            'ship_lat'     => is_numeric($lat) ? (float) $lat : null,
            'ship_lng'     => is_numeric($lng) ? (float) $lng : null,
        ]);
        return redirect()->to('/mypage/orders/' . $id)->with('success', '배송지를 수정했습니다.');
    }

    public function points(): string
    {
        $u  = mm_user();
        $lm = new PointLogModel();
        return $this->render('mypage/points', [
            'title' => '포인트',
            'logs'  => $lm->where('user_id', $u['id'])->orderBy('id', 'DESC')->paginate(30, 'p'),
            'pager' => $lm->pager,
        ]);
    }

    public function referral(): string
    {
        $u   = mm_user();
        $um  = new UserModel();
        $per = $this->request->getGet('p') === 'all' ? 'all' : 'month';

        // 순위 = 인증을 통과한 소개 회원 수 (이번 달: 이번 달에 가입한 소개 회원 기준)
        $b = $um->select('referred_by, created_at')->where('role', 'buyer')->where('status', 'approved')->where('referred_by IS NOT NULL');
        if ($per === 'month') {
            $b->where('created_at >=', date('Y-m-01 00:00:00'));
        }
        $cnt = [];
        foreach ($b->findAll() as $r) {
            $cnt[$r['referred_by']] = ($cnt[$r['referred_by']] ?? 0) + 1;
        }
        arsort($cnt);
        $board = [];
        $rank  = 0;
        $prev  = null;
        $i     = 0;
        $names = $cnt ? array_column($um->select('id, company_name, name')->whereIn('id', array_keys($cnt))->findAll(), null, 'id') : [];
        foreach ($cnt as $id => $n) {
            $i++;
            if ($n !== $prev) {
                $rank = $i; // 동점은 같은 순위
                $prev = $n;
            }
            $nm = $names[$id]['company_name'] ?? ($names[$id]['name'] ?? '');
            $board[] = ['id' => (int) $id, 'rank' => $rank, 'n' => $n, 'name' => (int) $id === (int) $u['id'] ? $nm : mm_mask_name($nm)];
        }
        $me = null;
        foreach ($board as $row) {
            if ($row['id'] === (int) $u['id']) {
                $me = $row;
                break;
            }
        }
        $referred = $um->where('referred_by', $u['id'])->orderBy('id', 'DESC')->findAll();
        $paid = [];
        $earned = 0;
        foreach ((new PointLogModel())->where('user_id', $u['id'])->whereIn('type', ['referral_signup', 'referral_first_order'])->findAll() as $l) {
            $paid[$l['ref_id']][$l['type']] = (int) $l['amount'];
            $earned += (int) $l['amount'];
        }
        return $this->render('mypage/referral', [
            'title'    => '친구 소개',
            'referred' => $referred,
            'link'     => base_url('register?ref=' . $u['referral_code']),
            'per'      => $per,
            'board'    => array_slice($board, 0, 10),
            'boardN'   => count($board),
            'me'       => $me,
            'paid'     => $paid,
            'earned'   => $earned,
            'approvedN' => count(array_filter($referred, fn ($r) => $r['status'] === 'approved')),
        ]);
    }

    public function profile(): string
    {
        return $this->render('mypage/profile', ['title' => '회원 정보']);
    }

    public function updateProfile()
    {
        $u = mm_user();
        (new UserModel())->update($u['id'], [
            'name'    => trim((string) $this->request->getPost('name')),
            'phone'   => trim((string) $this->request->getPost('phone')),
            'viber'   => trim((string) $this->request->getPost('viber')),
            'city'    => trim((string) $this->request->getPost('city')),
            'address' => trim((string) $this->request->getPost('address')),
        ]);
        return redirect()->to('/mypage/profile')->with('success', '회원 정보를 수정했습니다.');
    }

    public function updatePassword()
    {
        $u = mm_user();
        if (! password_verify((string) $this->request->getPost('current'), $u['password_hash'])) {
            return redirect()->back()->with('error', '현재 비밀번호가 올바르지 않습니다.');
        }
        $new = (string) $this->request->getPost('password');
        if (strlen($new) < 6 || $new !== $this->request->getPost('password2')) {
            return redirect()->back()->with('error', '새 비밀번호는 6자 이상, 확인과 일치해야 합니다.');
        }
        (new UserModel())->update($u['id'], ['password_hash' => password_hash($new, PASSWORD_DEFAULT)]);
        return redirect()->to('/mypage/profile')->with('success', '비밀번호를 변경했습니다.');
    }

    /** 인증 상태·자료 재제출 화면. */
    public function verify(): string
    {
        return $this->render('mypage/verify', ['title' => '사업자 인증']);
    }

    public function resubmit()
    {
        $u = mm_user();
        if ($u['status'] === 'approved') {
            return redirect()->to('/mypage');
        }
        try {
            $docs = Uploader::saveMany($this->request->getFileMultiple('docs') ?: [], 'docs', Uploader::DOC, 5);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        $fb = trim((string) $this->request->getPost('fb_page'));
        $vt = array_values(array_intersect(array_keys(mm_verify_types()), (array) $this->request->getPost('verify_types')));
        if (! $vt) {
            return redirect()->back()->with('error', '오프라인 매장 · 온라인 판매 · 판매 네트워크 중 해당하는 것을 하나 이상 골라 주세요.');
        }
        $existing = mm_json($u['doc_files']);
        if ($fb === '' && ! $docs && ! $existing) {
            return redirect()->back()->with('error', '판매 채널 링크나 사진·캡처를 하나 이상 올려 주세요.');
        }
        (new UserModel())->update($u['id'], [
            'verify_types'  => implode(',', $vt),
            'fb_page'       => $fb,
            'doc_files'     => json_encode(array_merge($existing, $docs), JSON_UNESCAPED_UNICODE),
            'status'        => 'pending',
            'reject_reason' => null,
        ]);
        return redirect()->to('/mypage/verify')->with('success', '자료를 다시 제출했습니다. 담당자가 확인한 뒤 승인해 드립니다.');
    }

    public function inquiry()
    {
        $u    = mm_user();
        $body = trim((string) $this->request->getPost('body'));
        if ($body === '') {
            return redirect()->back()->with('error', '문의 내용을 입력해 주세요.');
        }
        $pid = (int) $this->request->getPost('product_id') ?: null;
        if ($pid && ! (new ProductModel())->find($pid)) {
            $pid = null;
        }
        (new InquiryModel())->insert(['user_id' => $u['id'], 'product_id' => $pid, 'body' => $body]);
        return redirect()->back()->with('success', '문의를 접수했습니다. 마이페이지에서 답변을 확인하실 수 있습니다.');
    }

    public function inquiries(): string
    {
        $u   = mm_user();
        $im  = new InquiryModel();
        $pm  = new ProductModel();
        $rows = $im->where('user_id', $u['id'])->orderBy('id', 'DESC')->findAll();
        foreach ($rows as &$r) {
            $r['product'] = $r['product_id'] ? $pm->find($r['product_id']) : null;
        }
        return $this->render('mypage/inquiries', ['title' => '문의 내역', 'rows' => $rows]);
    }
}
