<?php

namespace App\Controllers;

/**
 * 배포 확인용. MARKER 를 올리고 푸시한 뒤 `curl https://mm.xindelela.com/deploy-check` 로 대조한다.
 * DB 연결까지 확인하므로 부팅·DB 양쪽 장애를 한 번에 잡는다.
 */
class DeployCheck extends BaseController
{
    public const MARKER = 1;

    public function index()
    {
        $db = false;
        try {
            $db = (bool) db_connect()->query('SELECT 1')->getRow();
        } catch (\Throwable $e) {
            $db = false;
        }
        return $this->response->setJSON(['ok' => $db, 'marker' => self::MARKER, 'env' => ENVIRONMENT, 'time' => date('c')]);
    }
}
