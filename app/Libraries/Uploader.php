<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * 업로드 공용. 화이트리스트(jpg/png/webp/gif/pdf), 5MB, 파일명 재생성.
 * 반환 = public 기준 상대경로 ('uploads/docs/2026/10/xxx.jpg')
 */
class Uploader
{
    public const IMG = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    public const DOC = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];

    public static function save(?UploadedFile $file, string $dir, array $mimes, int $maxMb = 5): ?string
    {
        if (! $file || ! $file->isValid() || $file->getError() !== UPLOAD_ERR_OK) {
            return null;
        }
        if ($file->getSize() > $maxMb * 1024 * 1024) {
            throw new \RuntimeException('파일 크기는 ' . $maxMb . 'MB 이하여야 합니다.');
        }
        $mime = $file->getMimeType();
        if (! in_array($mime, $mimes, true)) {
            throw new \RuntimeException('허용되지 않는 파일 형식입니다. (' . $mime . ')');
        }
        $ext  = strtolower($file->getExtension() ?: $file->guessExtension() ?: 'bin');
        $ext  = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'bin';
        $sub  = 'uploads/' . trim($dir, '/') . '/' . date('Y/m');
        $abs  = FCPATH . $sub;
        if (! is_dir($abs)) {
            mkdir($abs, 0775, true);
        }
        $name = date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $file->move($abs, $name);
        return $sub . '/' . $name;
    }

    /** 다중 업로드. 실패 항목은 건너뛴다. */
    public static function saveMany(array $files, string $dir, array $mimes, int $maxMb = 5): array
    {
        $out = [];
        foreach ($files as $f) {
            if ($f instanceof UploadedFile && $f->isValid()) {
                $p = self::save($f, $dir, $mimes, $maxMb);
                if ($p) {
                    $out[] = ['name' => $f->getClientName(), 'path' => $p];
                }
            }
        }
        return $out;
    }
}
