<?php $hist = (new \App\Models\AdminLogModel())->where('target_type', $type)->where('target_id', $id)->orderBy('id', 'DESC')->findAll(20); ?>
<div class="act hist"><h3>처리 기록</h3>
  <?php if (! $hist): ?><div class="small muted">아직 기록이 없습니다.</div><?php else: ?>
  <ul><?php foreach ($hist as $h): ?><li><b><?= esc($h['admin_name']) ?></b><span><?= esc(preg_replace('/^(주문|회원|상품|공급사) \S+ /u', '', $h['summary'])) ?></span><em><?= mm_dt($h['created_at'], 'm.d H:i') ?></em></li><?php endforeach ?></ul>
  <?php endif ?>
</div>
