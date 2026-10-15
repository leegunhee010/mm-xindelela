<div class="tabs">
  <?php foreach (['' => '전체', 'paid' => '입금 확인(출고 필요)', 'preparing' => '준비중', 'shipped' => '배송중', 'delivered' => '완료'] as $k => $l): ?><a href="<?= site_url('supplier/orders?status=' . $k) ?>" class="<?= $st === $k ? 'on' : '' ?>"><?= $l ?></a><?php endforeach ?>
</div>
<?php if (! $orders): ?><div class="empty">입금 확인된 주문이 없습니다.</div><?php endif ?>
<?php foreach ($orders as $o): $mine = array_sum(array_column($o['items'], 'line_total')); $left = count(array_filter($o['items'], static fn ($i) => $i['item_status'] !== 'shipped')); ?>
<div class="card">
  <div class="row sp"><div><b style="font-size:14px"><?= esc($o['order_no']) ?></b> <span class="muted small"><?= mm_dt($o['created_at']) ?> · 입금확인 <?= mm_dt($o['paid_at']) ?></span> <span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></div><div><?= $left ? '<span class="st st-pending">내 품목 미출고 ' . $left . '</span>' : '<span class="st st-shipped">출고 완료</span>' ?> <a class="btn btn-sm" href="<?= site_url('supplier/orders/' . $o['id']) ?>">상세·출고</a></div></div>
  <div class="oitems mt8" style="border-top:1px solid var(--line)"><?php foreach ($o['items'] as $it): ?><div class="it" style="padding:8px 0"><img src="<?= mm_thumb($it['thumb']) ?>" alt="" style="width:44px;height:44px"><div class="n"><?= esc($it['product_name']) ?></div><div class="q"><?= number_format((int) $it['qty']) ?>개</div><div class="p"><?= mm_price($it['line_total']) ?></div></div><?php endforeach ?></div>
  <div class="tr small mt8">내 품목 합계 <b><?= mm_price($mine) ?></b></div>
</div>
<?php endforeach ?>
<?= $pager ? $pager->links('p', 'mm_pager') : '' ?>
