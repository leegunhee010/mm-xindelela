<div class="tabs">
  <a href="<?= site_url('admin/orders') ?>" class="<?= $st === '' ? 'on' : '' ?>">전체</a>
  <?php foreach ($counts as $k => $n): ?><a href="<?= site_url('admin/orders?status=' . $k) ?>" class="<?= $st === $k ? 'on' : '' ?>"><?= mm_order_status_label($k) ?><span class="n"><?= $n ?></span></a><?php endforeach ?>
  <form class="srch" method="get"><input name="q" value="<?= esc($q) ?>" placeholder="주문번호"><button class="btn btn-sm" type="submit">검색</button></form>
</div>
<div class="note">처리 순서: 주문 신청(입금 대기) → 구매자 KBZPay 송금·캡처 업로드(입금 확인중) → <b>입금 확인</b> 클릭 → 공급사 발주·출고(배송중) → 배송 완료</div>
<table class="tbl"><thead><tr><th>주문번호</th><th class="l">구매자</th><th>품목</th><th>상품금액</th><th>배송비</th><th>포인트</th><th>최종</th><th>결제</th><th>캡처</th><th>신청일</th><th>상태</th><th></th></tr></thead><tbody>
<?php if (! $rows): ?><tr><td colspan="12" style="padding:40px">주문이 없습니다.</td></tr><?php endif ?>
<?php foreach ($rows as $o): ?>
<tr><td><a href="<?= site_url('admin/orders/' . $o['id']) ?>"><b><?= esc($o['order_no']) ?></b></a></td><td class="l"><?= esc($o['buyer']['company_name'] ?? '-') ?><br><span class="muted"><?= esc($o['ship_city']) ?></span></td><td><?= $o['icnt'] ?>종</td><td><?= mm_price($o['subtotal'], false) ?></td><td><?= $o['status'] === 'requested' ? '-' : mm_price($o['shipping_fee'], false) ?></td><td>-<?= number_format((int) $o['points_used']) ?></td><td><b><?= mm_price($o['total'], false) ?></b></td><td><?= mm_payment_label($o['payment_method']) ?></td><td><?= $o['payment_proof'] ? '<a href="' . base_url($o['payment_proof']) . '" target="_blank" class="btn btn-xs">보기</a>' : '-' ?></td><td><?= mm_dt($o['created_at'], 'm.d H:i') ?></td><td><span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></td><td><a class="btn btn-xs <?= in_array($o['status'], ['requested', 'proof']) ? 'btn-g' : '' ?>" href="<?= site_url('admin/orders/' . $o['id']) ?>">처리</a></td></tr>
<?php endforeach ?>
</tbody></table>
<?= $pager->links('p', 'mm_pager') ?>
