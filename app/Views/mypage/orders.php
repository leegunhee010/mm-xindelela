<div class="my">
<?= view('mypage/_side') ?>
<div class="main">
  <h1>주문 내역</h1>
  <?php if (! $orders): ?><div class="empty">주문 내역이 없습니다.</div><?php endif ?>
  <?php foreach ($orders as $o): ?>
  <div class="card" style="margin-bottom:12px">
    <div class="mmrow sp"><div><b style="font-size:14px"><?= esc($o['order_no']) ?></b> <span class="muted small"><?= mm_dt($o['created_at']) ?></span> <span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></div><a class="btn btn-sm" href="<?= site_url('mypage/orders/' . $o['id']) ?>">상세·입금 캡처</a></div>
    <div class="oitems mt8" style="border-top:1px solid var(--line)">
      <?php foreach ($o['items'] as $it): ?><div class="it" style="padding:8px 0"><img src="<?= mm_thumb($it['thumb']) ?>" alt="" style="width:44px;height:44px"><div class="n"><?= esc($it['product_name']) ?></div><div class="q"><?= number_format((int) $it['qty']) ?>개</div><div class="p"><?= mm_price($it['line_total']) ?></div></div><?php endforeach ?>
    </div>
    <div class="tr mt8 small">상품 <?= mm_price($o['subtotal']) ?> · 포인트 -<?= number_format((int) $o['points_used']) ?> · 배송비 <?= $o['status'] === 'requested' ? '안내 예정' : mm_price($o['shipping_fee']) ?> · <b style="font-size:14px">합계 <?= mm_price($o['total']) ?></b></div>
  </div>
  <?php endforeach ?>
  <?= $pager->links('p', 'mm_pager') ?>
</div>
</div>
