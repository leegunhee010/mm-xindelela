<?php

namespace App\Controllers\Admin;

use App\Models\NoticeModel;

class Notices extends Base
{
    public function index(): string
    {
        $nm = new NoticeModel();
        return $this->page('admin/notices', ['title' => '공지사항', 'rows' => $nm->orderBy('is_pinned', 'DESC')->orderBy('id', 'DESC')->paginate(30, 'p'), 'pager' => $nm->pager]);
    }

    public function form(?int $id = null): string
    {
        $n = $id ? (new NoticeModel())->find($id) : null;
        return $this->page('admin/notice_form', ['title' => $n ? '공지 수정' : '공지 작성', 'n' => $n]);
    }

    public function save()
    {
        $nm   = new NoticeModel();
        $id   = (int) $this->request->getPost('id');
        $data = ['title' => trim((string) $this->request->getPost('title')), 'body' => (string) $this->request->getPost('body'), 'is_pinned' => $this->request->getPost('is_pinned') ? 1 : 0];
        if ($data['title'] === '') {
            return redirect()->back()->withInput()->with('error', '제목을 입력해 주세요.');
        }
        $id ? $nm->update($id, $data) : $nm->insert($data);
        return redirect()->to('/admin/notices')->with('success', '저장했습니다.');
    }

    public function delete(int $id)
    {
        (new NoticeModel())->delete($id);
        return redirect()->to('/admin/notices')->with('success', '삭제했습니다.');
    }
}
