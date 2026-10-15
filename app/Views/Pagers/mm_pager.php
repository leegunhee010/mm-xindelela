<?php
/** @var \CodeIgniter\Pager\PagerRenderer $pager */
$pager->setSurroundCount(3);
if ($pager->getPageCount() <= 1) {
    return;
}
?>
<div class="pager">
  <?php if ($pager->hasPrevious()): ?><a href="<?= $pager->getPrevious() ?>">‹</a><?php endif ?>
  <?php foreach ($pager->links() as $l): ?>
    <?php if ($l['active']): ?><strong><?= $l['title'] ?></strong><?php else: ?><a href="<?= $l['uri'] ?>"><?= $l['title'] ?></a><?php endif ?>
  <?php endforeach ?>
  <?php if ($pager->hasNext()): ?><a href="<?= $pager->getNext() ?>">›</a><?php endif ?>
</div>
