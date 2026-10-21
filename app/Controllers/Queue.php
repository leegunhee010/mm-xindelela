<?php

namespace App\Controllers;

use App\Libraries\QueueService;
use App\Models\NotificationModel;
use App\Models\ProductModel;
use App\Models\QueueModel;

/** 줄서기(구매자) + 사이트 안 알림. 인증 회원만 줄을 설 수 있다. */
class Queue extends BaseController
{
    public function join(int $productId)
    {
        if (! mm_is_approved()) {
            return redirect()->back()->with('error', mm_q('need_verify'));
        }
        $p = (new ProductModel())->find($productId);
        if (! $p || $p['status'] !== 'coming') {
            return redirect()->back()->with('error', mm_q('not_coming'));
        }
        $already = mm_my_queue($productId) !== null;
        if (! $already && ! $this->request->getPost('agree')) {
            return redirect()->back()->with('error', mm_q('agree_required'));
        }
        (new QueueService())->join((int) mm_user()['id'], $p, (int) $this->request->getPost('qty'));
        return redirect()->to('/products/' . $productId)->with('success', mm_q($already ? 'updated_msg' : 'joined_msg'));
    }

    public function cancel(int $productId)
    {
        (new QueueService())->cancel((int) mm_user()['id'], $productId);
        return redirect()->back()->with('success', mm_q('cancelled_msg'));
    }

    public function mine(): string
    {
        $u    = mm_user();
        $rows = (new QueueModel())->where('user_id', $u['id'])->orderBy('updated_at', 'DESC')->findAll(100);
        $pm   = new ProductModel();
        foreach ($rows as &$r) {
            $r['p'] = $pm->find($r['product_id']);
        }
        unset($r);
        return $this->render('mypage/queue', [
            'title' => mm_q('menu'),
            'rows'  => array_values(array_filter($rows, static fn ($r) => $r['p'] !== null)),
            'stats' => (new QueueService())->stats(array_column($rows, 'product_id')),
        ]);
    }

    /** 알림 목록. 열어 보면 모두 읽음 처리(읽기 전 상태는 화면에 표시). */
    public function notifications(): string
    {
        $u    = mm_user();
        $nm   = new NotificationModel();
        $rows = $nm->where('user_id', $u['id'])->orderBy('id', 'DESC')->findAll(100);
        $nm->where('user_id', $u['id'])->where('read_at', null)->set(['read_at' => date('Y-m-d H:i:s')])->update();
        return $this->render('mypage/notifications', ['title' => '알림', 'rows' => $rows]);
    }
}
