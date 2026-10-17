<?php $sym = mm_setting('currency_symbol', 'Ks'); ?>
<div class="odash">
  <a class="odash-c is-hot <?= $dash['proof'] ? '' : 'is-zero' ?>" href="?status=proof">
    <span class="odash-k">① 입금 확인하기</span><b><?= $dash['proof'] ?><small>건</small></b>
    <em><?= $dash['proof'] ? 'KBZPay 내역에서 송금자 이름 확인' : '확인 요청 없음' ?><?= $dash['proofOld'] ? ' · <i>하루 넘은 요청 ' . $dash['proofOld'] . '건</i>' : '' ?></em>
  </a>
  <a class="odash-c <?= $dash['toShip'] ? 'is-warn' : 'is-zero' ?>" href="?status=paid">
    <span class="odash-k">② 발주·출고하기</span><b><?= $dash['toShip'] ?><small>건</small></b>
    <em><?= $dash['toShip'] ? '입금 확인 끝, 송장 입력 후 배송중으로' : '출고 대기 없음' ?></em>
  </a>
  <a class="odash-c <?= $dash['requested'] ? '' : 'is-zero' ?>" href="?status=requested">
    <span class="odash-k">입금 기다리는 중</span><b><?= $dash['requested'] ?><small>건</small></b>
    <em><?= $dash['reqStale'] ? '<i>3일 넘게 송금 없음 ' . $dash['reqStale'] . '건 · 연락 필요</i>' : '구매자 송금 대기' ?></em>
  </a>
  <a class="odash-c <?= $dash['shipped'] ? '' : 'is-zero' ?>" href="?status=shipped">
    <span class="odash-k">배송 중</span><b><?= $dash['shipped'] ?><small>건</small></b>
    <em><?= $dash['etaOver'] ? '<i>도착 예정일 지남 ' . $dash['etaOver'] . '건</i>' : '도착하면 배송 완료 처리' ?></em>
  </a>
</div>
<div class="ostat">
  <div><span>오늘 들어온 주문</span><b><?= number_format($dash['todayN']) ?>건</b></div>
  <div><span>오늘 입금 확인</span><b><?= number_format($dash['todayPaid']) ?> <?= esc($sym) ?></b></div>
  <div><span>이번 달 입금 확인</span><b><?= number_format($dash['monthPaid']) ?> <?= esc($sym) ?></b></div>
</div>
<?php if ($queue && $st === ''): ?>
<div class="card oq">
  <div class="oq-h"><h3>지금 확인할 입금 요청</h3><span class="muted small">오래된 순 · KBZPay 앱에서 송금자 이름과 금액이 맞으면 '입금 확인'</span></div>
  <table class="tbl"><thead><tr><th>주문번호</th><th class="l">구매자</th><th>송금자</th><th>금액</th><th>요청 시각</th><th></th></tr></thead><tbody>
  <?php foreach ($queue as $o): ?><tr>
    <td><a href="<?= site_url('admin/orders/' . $o['id']) ?>"><b><?= esc($o['order_no']) ?></b></a></td>
    <td class="l"><?= esc($o['buyer']['company_name'] ?? '-') ?></td>
    <td><b><?= esc($o['payment_note'] ?: '-') ?></b></td>
    <td><b><?= mm_price($o['total']) ?></b></td>
    <td><?= mm_dt($o['proof_at'], 'm.d H:i') ?></td>
    <td><form method="post" action="<?= site_url('admin/orders/' . $o['id'] . '/status') ?>" onsubmit="return confirm('<?= esc($o['payment_note'], 'js') ?> 님 <?= number_format((int) $o['total']) ?> 입금을 확인했습니까?')"><?= csrf_field() ?><input type="hidden" name="status" value="paid"><button class="btn btn-xs btn-g" type="submit">입금 확인</button></form></td>
  </tr><?php endforeach ?>
  </tbody></table>
</div>
<?php endif ?>
<div class="tabs">
  <a href="<?= site_url('admin/orders') ?>" class="<?= $st === '' ? 'on' : '' ?>">전체</a>
  <?php foreach ($counts as $k => $n): ?><a href="<?= site_url('admin/orders?status=' . $k) ?>" class="<?= $st === $k ? 'on' : '' ?>"><?= mm_order_status_label($k) ?><span class="n"><?= $n ?></span></a><?php endforeach ?>
  <form class="srch" method="get"><input name="q" value="<?= esc($q) ?>" placeholder="주문번호"><button class="btn btn-sm" type="submit">검색</button></form>
</div>
<table class="tbl"><thead><tr><th>주문번호</th><th class="l">구매자</th><th>품목</th><th>상품금액</th><th>배송비</th><th>포인트</th><th>최종</th><th>입금</th><th>송금자</th><th>신청일</th><th>상태</th><th></th></tr></thead><tbody>
<?php if (! $rows): ?><tr><td colspan="12" style="padding:40px">주문이 없습니다.</td></tr><?php endif ?>
<?php foreach ($rows as $o): ?>
<tr class="<?= $o['status'] === 'requested' && $o['created_at'] < $stale ? 'o-stale' : '' ?>"><td><a href="<?= site_url('admin/orders/' . $o['id']) ?>"><b><?= esc($o['order_no']) ?></b></a></td><td class="l"><?= esc($o['buyer']['company_name'] ?? '-') ?><br><span class="muted"><?= esc($o['ship_city']) ?></span></td><td><?= $o['icnt'] ?>종</td><td><?= mm_price($o['subtotal'], false) ?></td><td><?= (int) $o['shipping_fee'] ? mm_price($o['shipping_fee'], false) : '-' ?></td><td><?= (int) $o['points_used'] ? '-' . number_format((int) $o['points_used']) : '-' ?></td><td><b><?= mm_price($o['total'], false) ?></b></td><td><span class="pay pay-<?= esc(mm_order_pay_label($o['status']) === '결제 완료' ? 'done' : ($o['status'] === 'proof' ? 'req' : ($o['status'] === 'cancelled' ? 'x' : 'no'))) ?>"><?= mm_order_pay_label($o['status']) ?></span></td><td><?= $o['payment_note'] ? esc($o['payment_note']) : '-' ?></td><td><?= mm_dt($o['created_at'], 'm.d H:i') ?></td><td><span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></td><td><a class="btn btn-xs <?= in_array($o['status'], ['requested', 'proof']) ? 'btn-g' : '' ?>" href="<?= site_url('admin/orders/' . $o['id']) ?>">처리</a></td></tr>
<?php endforeach ?>
</tbody></table>
<?= $pager->links('p', 'mm_pager') ?>
