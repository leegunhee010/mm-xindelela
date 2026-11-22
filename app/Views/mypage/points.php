<div class="flex gap-[60px] items-start">
<?= view('mypage/_side') ?>
<div class="flex-1 min-w-0">
  <div class="contents-title-area depth-1 title3 font-bold !pt-0 mb-[20px]"><div class="title-area !pt-0"><h3 class="title"><?= t('포인트') ?> <span style="font-size:14px;font-weight:400;color:var(--gray)"><?= t('보유') ?> <b style="color:var(--green-d);font-size:18px"><?= number_format((int) $user['points_balance']) ?></b> P</span></h3></div></div>
  <div class="box-note" style="margin-bottom:16px"><?= t('주문 신청 시 상품 금액의') ?> <?= (int) mm_setting('points_use_max_pct', 30) ?><?= t('%까지(최소') ?> <?= number_format((int) mm_setting('points_min_use', 0)) ?><?= t('P) 사용할 수 있습니다. 1P = 1') ?> <?= esc(mm_setting('currency_symbol', 'Ks')) ?></div>
  <?php if (! $logs): ?><div class="empty"><?= t('포인트 내역이 없습니다.') ?></div><?php else: ?>
  <table class="tbl"><thead><tr><th style="width:140px"><?= t('일시') ?></th><th><?= t('내용') ?></th><th style="width:110px"><?= t('증감') ?></th><th style="width:110px"><?= t('잔액') ?></th></tr></thead><tbody>
  <?php foreach ($logs as $l): ?><tr><td><?= mm_dt($l['created_at']) ?></td><td class="l"><b><?= mm_point_type_label($l['type']) ?></b><?php if ($l['memo']): ?> <span class="muted">· <?= esc($l['memo']) ?></span><?php endif ?></td><td class="<?= $l['amount'] >= 0 ? 'pt-plus' : 'pt-minus' ?>"><?= $l['amount'] >= 0 ? '+' : '' ?><?= number_format((int) $l['amount']) ?></td><td><?= number_format((int) $l['balance_after']) ?></td></tr><?php endforeach ?>
  </tbody></table>
  <?= $pager->links('p', 'mm_pager') ?>
  <?php endif ?>
</div>
</div>
