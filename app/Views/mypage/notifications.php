<div class="flex gap-[60px] items-start">
<?= view('mypage/_side') ?>
<div class="flex-1 min-w-0">
  <div class="contents-title-area depth-1 title3 font-bold !pt-0 mb-[20px]"><div class="title-area !pt-0"><h3 class="title"><?= t('알림') ?></h3></div></div>
  <?php if (! $rows): ?>
    <div class="empty"><?= t('받은 알림이 없습니다.') ?></div>
  <?php else: ?>
  <ul class="nlist">
    <?php foreach ($rows as $n): ?>
    <li class="nlist-li<?= $n['read_at'] ? '' : ' is-new' ?>">
      <a href="<?= $n['link'] ? site_url($n['link']) : '#' ?>">
        <span class="nlist-ic nlist-<?= esc($n['type']) ?>"></span>
        <div><b><?= esc(t($n['title'])) ?></b><?php if ($n['body']): ?><p><?= esc($n['body']) ?></p><?php endif ?><time><?= mm_dt($n['created_at']) ?></time></div>
      </a>
    </li>
    <?php endforeach ?>
  </ul>
  <?php endif ?>
</div>
</div>
