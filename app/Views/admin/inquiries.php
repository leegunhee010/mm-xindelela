<?php if (! $rows): ?><div class="empty">문의가 없습니다.</div><?php endif ?>
<?php foreach ($rows as $r): ?>
<div class="card">
  <div class="row sp"><div><a href="<?= site_url('admin/buyers/' . $r['user_id']) ?>"><b><?= esc($r['user']['company_name'] ?? '#' . $r['user_id']) ?></b></a> <span class="muted small"><?= esc($r['user']['phone'] ?? '') ?> · <?= mm_dt($r['created_at']) ?></span><?php if ($r['product']): ?> · <a href="<?= site_url('admin/products/' . $r['product']['id']) ?>" style="color:var(--navy)"><?= esc($r['product']['name']) ?></a><?php endif ?></div><?= $r['answer'] ? '<span class="st st-approved">답변 완료</span>' : '<span class="st st-pending">답변 대기</span>' ?></div>
  <p class="mt8" style="font-size:13px"><?= nl2br(esc($r['body'])) ?></p>
  <form method="post" action="<?= site_url('admin/inquiries/' . $r['id'] . '/answer') ?>" class="mt8"><?= csrf_field() ?><textarea class="ta" name="answer" style="height:60px" placeholder="답변 (회원 마이페이지에 표시)"><?= esc($r['answer']) ?></textarea><button class="btn btn-sm btn-g mt8" type="submit"><?= $r['answer'] ? '답변 수정' : '답변 저장' ?></button></form>
</div>
<?php endforeach ?>
<?= $pager->links('p', 'mm_pager') ?>
