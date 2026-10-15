<div class="my">
<?= view('mypage/_side') ?>
<div class="main">
  <h1>포인트 <span style="font-size:14px;font-weight:400;color:var(--gray)">보유 <b style="color:var(--green-d);font-size:18px"><?= number_format((int) $user['points_balance']) ?></b> P</span></h1>
  <div class="box-note" style="margin-bottom:16px">주문 신청 시 상품 금액의 <?= (int) mm_setting('points_use_max_pct', 30) ?>%까지(최소 <?= number_format((int) mm_setting('points_min_use', 0)) ?>P) 사용할 수 있습니다. 1P = 1 <?= esc(mm_setting('currency_symbol', 'Ks')) ?></div>
  <?php if (! $logs): ?><div class="empty">포인트 내역이 없습니다.</div><?php else: ?>
  <table class="tbl"><thead><tr><th style="width:140px">일시</th><th>내용</th><th style="width:110px">증감</th><th style="width:110px">잔액</th></tr></thead><tbody>
  <?php foreach ($logs as $l): ?><tr><td><?= mm_dt($l['created_at']) ?></td><td class="l"><b><?= mm_point_type_label($l['type']) ?></b><?php if ($l['memo']): ?> <span class="muted">· <?= esc($l['memo']) ?></span><?php endif ?></td><td class="<?= $l['amount'] >= 0 ? 'pt-plus' : 'pt-minus' ?>"><?= $l['amount'] >= 0 ? '+' : '' ?><?= number_format((int) $l['amount']) ?></td><td><?= number_format((int) $l['balance_after']) ?></td></tr><?php endforeach ?>
  </tbody></table>
  <?= $pager->links('p', 'mm_pager') ?>
  <?php endif ?>
</div>
</div>
