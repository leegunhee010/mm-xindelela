<?php

namespace App\Controllers\Admin;

use App\Libraries\Uploader;

/**
 * 관리자 이미지 업로드 (상세 블록 편집기용, AJAX).
 * 브라우저에서 이미 줄이거나 잘라 낸 조각(JPEG/PNG)을 한 장씩 받아 저장하고 경로를 돌려준다.
 * CSRF 토큰이 요청마다 바뀌므로 새 토큰을 함께 돌려준다.
 */
class Upload extends Base
{
    public function image()
    {
        try {
            $path = Uploader::save($this->request->getFile('file'), 'products/detail', ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], 20);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['ok' => false, 'error' => $e->getMessage(), 'csrf' => csrf_hash()]);
        }
        if (! $path) {
            return $this->response->setJSON(['ok' => false, 'error' => '파일을 받지 못했습니다.', 'csrf' => csrf_hash()]);
        }
        return $this->response->setJSON(['ok' => true, 'path' => $path, 'url' => base_url($path), 'csrf' => csrf_hash()]);
    }
}
