<div class="tabs"><a class="btn btn-g btn-sm" href="<?= site_url('admin/notices/new') ?>">+ 공지 작성</a></div>
<table class="tbl"><thead><tr><th>ID</th><th class="l">제목</th><th>고정</th><th>조회</th><th>등록</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $n): ?><tr><td><?= $n['id'] ?></td><td class="l"><a href="<?= site_url('admin/notices/' . $n['id'] . '/edit') ?>"><?= esc($n['title']) ?></a></td><td><?= $n['is_pinned'] ? '<span class="tag tag-fg">공지</span>' : '' ?></td><td><?= $n['views'] ?></td><td><?= mm_dt($n['created_at'], 'Y.m.d') ?></td><td class="inl"><a class="btn btn-xs" href="<?= site_url('admin/notices/' . $n['id'] . '/edit') ?>">수정</a><form method="post" action="<?= site_url('admin/notices/' . $n['id'] . '/delete') ?>" onsubmit="return confirm('삭제할까요?')"><?= csrf_field() ?><button class="btn btn-xs btn-red" type="submit">삭제</button></form></td></tr><?php endforeach ?>
</tbody></table>
<?= $pager->links('p', 'mm_pager') ?>
