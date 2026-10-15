<?php $left = count(array_filter($items, static fn ($i) => $i['item_status'] !== 'shipped')); ?>
<div class="two">
<div class="card">
  <h3><?= esc($o['order_no']) ?> <span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></h3>
  <table class="kv">
    <tr><th>받는 사람</th><td><?= esc($o['ship_name']) ?> · <?= esc($o['ship_phone']) ?></td></tr>
    <tr><th>배송지</th><td><?= esc($o['ship_city']) ?> <?= esc($o['ship_address']) ?></td></tr>
    <tr><th>구매자</th><td><?= esc($buyer['company_name'] ?? '-') ?></td></tr>
    <?php if ($o['memo']): ?><tr><th>요청 사항</th><td><?= nl2br(esc($o['memo'])) ?></td></tr><?php endif ?>
    <tr><th>입금 확인</th><td><?= mm_dt($o['paid_at']) ?></td></tr>
  </table>
  <div class="oitems mt16"><?php foreach ($items as $it): ?><div class="it"><img src="<?= mm_thumb($it['thumb']) ?>" alt=""><div class="n"><?= esc($it['product_name']) ?><div class="s">단가 <?= mm_price($it['unit_price']) ?> · <?= $it['item_status'] === 'shipped' ? '<span class="st st-shipped">출고</span>' : '<span class="st st-pending">미출고</span>' ?></div></div><div class="q"><?= number_format((int) $it['qty']) ?>개</div><div class="p"><?= mm_price($it['line_total']) ?></div></div><?php endforeach ?></div>
</div>
<div class="act" style="border:2px solid var(--green)"><h3>출고 처리</h3>
  <?php if ($left): ?>
  <form method="post" action="<?= site_url('supplier/orders/' . $o['id'] . '/ship') ?>"><?= csrf_field() ?><div class="field"><label>송장·배송 정보 (선택)</label><input class="inp" name="tracking" placeholder="택배사 / 송장번호"></div><button class="btn btn-g btn-block" type="submit" style="height:42px">내 품목 전체 출고</button></form>
  <div class="small muted mt8">다른 공급사 품목까지 모두 출고되면 주문이 배송중으로 바뀝니다.</div>
  <?php else: ?><div class="muted">내 품목은 모두 출고되었습니다.</div><?php endif ?>
</div>
</div>
