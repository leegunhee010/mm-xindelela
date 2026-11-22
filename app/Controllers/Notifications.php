<?php

namespace App\Controllers;

use App\Models\NotificationModel;

/** 사이트 안 알림 (관리자 '알림 보내기' 등). */
class Notifications extends BaseController
{
    public function index(): string
    {
        $u    = mm_user();
        $nm   = new NotificationModel();
        $rows = $nm->where('user_id', $u['id'])->orderBy('id', 'DESC')->findAll(100);
        $nm->where('user_id', $u['id'])->where('read_at', null)->set(['read_at' => date('Y-m-d H:i:s')])->update();
        return $this->render('mypage/notifications', ['title' => t('알림'), 'rows' => $rows]);
    }
}
