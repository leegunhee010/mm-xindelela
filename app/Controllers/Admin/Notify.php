<?php

namespace App\Controllers\Admin;

use App\Models\NotificationModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use App\Models\WishModel;

/** 관리자가 회원에게 사이트 안 알림 보내기 (헤더 종 아이콘으로 받음) + 보낸 기록. */
class Notify extends Base
{
    public function index(): string
    {
        $pid = (int) $this->request->getGet('product');
        $db  = db_connect();
        $sent = $db->table('notifications')
            ->select("title, body, link, MIN(created_at) AS at, COUNT(*) AS n, SUM(CASE WHEN read_at IS NULL THEN 0 ELSE 1 END) AS r")
            ->where('type', 'admin')->groupBy('title, body, link')->orderBy('at', 'DESC')->limit(30)->get()->getResultArray();
        return $this->page('admin/notify', [
            'title'   => '알림 보내기',
            'product' => $pid ? (new ProductModel())->find($pid) : null,
            'counts'  => $this->counts($pid),
            'sent'    => $sent,
        ]);
    }

    public function send()
    {
        $title = trim((string) $this->request->getPost('title'));
        $body  = trim((string) $this->request->getPost('body'));
        $link  = trim((string) $this->request->getPost('link'));
        $to    = (string) $this->request->getPost('to');
        if ($title === '') {
            return redirect()->back()->withInput()->with('error', '알림 제목을 적어 주세요.');
        }
        // 사이트 안 주소만 (예: products/12, welcome). 전체 주소를 넣으면 도메인 뒤만 쓴다.
        $link = ltrim((string) (parse_url($link, PHP_URL_PATH) ?? ''), '/') . (($q = parse_url($link, PHP_URL_QUERY)) ? '?' . $q : '');
        $ids  = $this->targets($to, (int) $this->request->getPost('product'), trim((string) $this->request->getPost('email')));
        if (! $ids) {
            return redirect()->back()->withInput()->with('error', '받을 회원이 없습니다.');
        }
        $nm  = new NotificationModel();
        $now = date('Y-m-d H:i:s');
        $nm->insertBatch(array_map(static fn ($id) => ['user_id' => $id, 'type' => 'admin', 'title' => mb_substr($title, 0, 200), 'body' => $body !== '' ? mb_substr($body, 0, 500) : null, 'link' => $link !== '' ? $link : null, 'created_at' => $now], $ids));
        return redirect()->to('/admin/notify')->with('success', count($ids) . '명에게 알림을 보냈습니다.');
    }

    /** 받는 회원 id 목록. 정지 회원 제외. */
    private function targets(string $to, int $pid, string $email): array
    {
        $um = (new UserModel())->select('id')->where('role', 'buyer')->where('status !=', 'suspended');
        switch ($to) {
            case 'approved':
                $um->where('status', 'approved');
                break;
            case 'pending':
                $um->whereIn('status', ['pending', 'rejected']);
                break;
            case 'wish':
                $ids = array_column((new WishModel())->select('user_id')->where('product_id', $pid)->findAll(), 'user_id');
                if (! $ids) {
                    return [];
                }
                $um->whereIn('id', $ids);
                break;
            case 'one':
                if ($email === '') {
                    return [];
                }
                $um->groupStart()->where('email', strtolower($email))->orWhere('referral_code', strtoupper($email))->groupEnd();
                break;
            case 'all':
                break;
            default:
                return [];
        }
        return array_map('intval', array_column($um->findAll(), 'id'));
    }

    private function counts(int $pid): array
    {
        $c = static fn () => (new UserModel())->where('role', 'buyer')->where('status !=', 'suspended');
        return [
            'all'      => $c()->countAllResults(),
            'approved' => $c()->where('status', 'approved')->countAllResults(),
            'pending'  => $c()->whereIn('status', ['pending', 'rejected'])->countAllResults(),
            'wish'     => $pid ? (new WishModel())->where('product_id', $pid)->countAllResults() : 0,
        ];
    }
}
