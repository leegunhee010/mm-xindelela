<div class="crumb"><a href="<?= site_url('/') ?>">홈</a>공지사항</div>
<div class="ltop"><h1>공지사항</h1></div>
<table class="tbl mt16"><thead><tr><th style="width:70px">번호</th><th class="l">제목</th><th style="width:120px">등록일</th><th style="width:80px">조회</th></tr></thead><tbody>
<?php if (! $rows): ?><tr><td colspan="4" style="padding:50px">공지사항이 없습니다.</td></tr><?php endif ?>
<?php foreach ($rows as $n): ?><tr><td><?= $n['is_pinned'] ? '<span class="tag tag-fg">공지</span>' : $n['id'] ?></td><td class="l"><a href="<?= site_url('notices/' . $n['id']) ?>"><?= esc($n['title']) ?></a></td><td><?= mm_dt($n['created_at'], 'Y.m.d') ?></td><td><?= number_format((int) $n['views']) ?></td></tr><?php endforeach ?>
</tbody></table>
<?= $pager->links('p', 'mm_pager') ?>
