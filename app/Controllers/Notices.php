<?php

namespace App\Controllers;

use App\Models\NoticeModel;

class Notices extends BaseController
{
    public function index(): string
    {
        $nm = new NoticeModel();
        return $this->render('notices/index', [
            'title' => '공지사항',
            'rows'  => $nm->orderBy('is_pinned', 'DESC')->orderBy('id', 'DESC')->paginate(20, 'p'),
            'pager' => $nm->pager,
        ]);
    }

    public function show(int $id): string
    {
        $nm = new NoticeModel();
        $n  = $nm->find($id);
        if (! $n) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $nm->where('id', $id)->set('views', 'views + 1', false)->update();
        return $this->render('notices/show', ['title' => $n['title'], 'n' => $n]);
    }
}
