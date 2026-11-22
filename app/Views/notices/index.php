<div class="contents-title-area depth-1 title3 font-bold mb-[20px]"><div class="title-area"><h3 class="title"><?= t('공지사항') ?></h3></div></div>
<table class="tbl mt16"><thead><tr><th style="width:70px"><?= t('번호') ?></th><th class="l"><?= t('제목') ?></th><th style="width:120px"><?= t('등록일') ?></th><th style="width:80px"><?= t('조회') ?></th></tr></thead><tbody>
<?php if (! $rows): ?><tr><td colspan="4" style="padding:50px"><?= t('공지사항이 없습니다.') ?></td></tr><?php endif ?>
<?php foreach ($rows as $n): ?><tr><td><?= $n['is_pinned'] ? t('<span class="tag tag-fg">공지</span>') : $n['id'] ?></td><td class="l"><a href="<?= site_url('notices/' . $n['id']) ?>"><?= esc(t($n['title'])) ?></a></td><td><?= mm_dt($n['created_at'], 'Y.m.d') ?></td><td><?= number_format((int) $n['views']) ?></td></tr><?php endforeach ?>
</tbody></table>
<?= $pager->links('p', 'mm_pager') ?>
