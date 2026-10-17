<div class="two">
<div>
  <div class="card"><h3><?= esc($o['order_no']) ?> <span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span> <span class="muted small" style="font-weight:400"><?= mm_dt($o['created_at']) ?></span></h3>
    <table class="kv">
      <tr><th>구매자</th><td><a href="<?= site_url('admin/buyers/' . $o['user_id']) ?>"><b><?= esc($buyer['company_name'] ?? '-') ?></b></a> · <?= esc($buyer['name'] ?? '') ?> · 전화 <?= esc($buyer['phone'] ?? '') ?> · Viber <?= esc($buyer['viber'] ?? $buyer['phone'] ?? '') ?></td></tr>
      <tr><th>배송지</th><td><?= esc($o['ship_name']) ?> · <?= esc($o['ship_phone']) ?><br><?= esc($o['ship_city']) ?> <?= esc($o['ship_address']) ?><?php if (! empty($o['ship_lat'])): ?> <a class="label1 text-primary" target="_blank" rel="noopener" href="https://www.google.com/maps?q=<?= esc($o['ship_lat']) ?>,<?= esc($o['ship_lng']) ?>">지도 보기 ›</a><?php endif ?></td></tr>
      <tr><th>결제 방법</th><td><?= mm_payment_label($o['payment_method']) ?></td></tr>
      <?php if ($o['memo']): ?><tr><th>구매자 요청</th><td><?= nl2br(esc($o['memo'])) ?></td></tr><?php endif ?>
      <tr><th>송금자</th><td><?php if ($o['payment_note']): ?><b><?= esc($o['payment_note']) ?></b> <span class="small muted">요청 <?= mm_dt($o['proof_at']) ?></span><?php else: ?><span class="muted">아직 입금 확인 요청 없음</span><?php endif ?><?php if ($o['payment_proof']): ?> · <a href="<?= base_url($o['payment_proof']) ?>" target="_blank">캡처 보기</a><?php endif ?></td></tr>
      <tr><th>일시</th><td class="small">확인요청 <?= mm_dt($o['proof_at']) ?> · 입금확인 <?= mm_dt($o['paid_at']) ?> · 출고 <?= mm_dt($o['shipped_at']) ?> · 완료 <?= mm_dt($o['delivered_at']) ?></td></tr>
    </table>
    <div class="oitems mt16">
      <?php foreach ($items as $it): ?><div class="it"><img src="<?= mm_thumb($it['thumb']) ?>" alt=""><div class="n"><a href="<?= site_url('admin/products/' . $it['product_id']) ?>"><?= esc($it['product_name']) ?></a><div class="s">공급사 <?= esc($it['supplier']['company_name'] ?? '-') ?> · 단가 <?= mm_price($it['unit_price']) ?> · <?= $it['item_status'] === 'shipped' ? '<span class="st st-shipped">출고</span>' : '<span class="st">미출고</span>' ?></div></div><div class="q"><?= number_format((int) $it['qty']) ?>개</div><div class="p"><?= mm_price($it['line_total']) ?></div></div><?php endforeach ?>
    </div>
    <div class="osum" style="max-width:none;margin-top:16px">
      <dl>
        <div><dt>상품 합계</dt><dd><?= mm_price($o['subtotal']) ?></dd></div>
        <?php if ((int) $o['shipping_fee'] > 0): ?><div><dt>배송비</dt><dd><?= mm_price($o['shipping_fee']) ?></dd></div><?php endif ?>
        <?php if ((int) $o['points_used'] > 0): ?><div><dt>포인트 사용</dt><dd>-<?= number_format((int) $o['points_used']) ?> P</dd></div><?php endif ?>
      </dl>
      <div class="osum-total"><span>결제 금액</span><b><?= mm_price($o['total']) ?></b></div>
    </div>
  </div>
</div>
<div>
  <?php if (in_array($o['status'], ['requested', 'quoted', 'proof'], true)): ?>
  <div class="act" style="border:2px solid var(--pri)"><h3>1. 입금 확인</h3><div class="small" style="margin-bottom:8px"><?= $o['payment_note'] ? 'KBZPay 내역에서 송금자 <b>' . esc($o['payment_note']) . '</b> · ' . mm_price($o['total']) . ' 입금을 확인하세요.' : '<span class="muted">구매자가 아직 입금 확인을 요청하지 않았습니다.</span>' ?></div>
    <form method="post" action="<?= site_url('admin/orders/' . $o['id'] . '/status') ?>" onsubmit="return confirm('입금을 확인했습니까? 포인트 적립과 공급사 발주 알림이 진행됩니다.')"><?= csrf_field() ?><input type="hidden" name="status" value="paid"><button class="btn btn-g btn-block" type="submit" style="height:42px">입금 확인 → 발주</button></form></div>
  <?php endif ?>
  <?php if (in_array($o['status'], ['paid', 'preparing'], true)): ?>
  <div class="act" style="border:2px solid var(--pri)"><h3>2. 출고 처리</h3>
    <form method="post" action="<?= site_url('admin/orders/' . $o['id'] . '/status') ?>"><?= csrf_field() ?><input type="hidden" name="status" value="shipped">
      <?php $si = mm_ship_info($o); ?>
      <div class="field"><label>배송 방법</label><div class="shipm"><?php foreach (mm_ship_methods() as $k => $l): ?><label><input type="radio" name="ship_method" value="<?= $k ?>" <?= ($si['method'] ?: 'courier') === $k ? 'checked' : '' ?>><span><?= $l ?></span></label><?php endforeach ?></div></div>
      <div class="g2">
        <div class="field"><label>배송사·기사</label><input class="inp" name="ship_carrier" value="<?= esc($si['carrier']) ?>" placeholder="예: Royal Express"></div>
        <div class="field"><label>송장번호</label><input class="inp" name="ship_tracking_no" value="<?= esc($si['tracking_no']) ?>"></div>
        <div class="field"><label>발송일</label><input class="inp" type="date" name="ship_sent_on" value="<?= esc($si['sent_on'] ?: date('Y-m-d')) ?>"></div>
        <div class="field"><label>예상 도착일</label><input class="inp" type="date" name="ship_eta" value="<?= esc($si['eta']) ?>"></div>
      </div>
      <div class="field"><label>배송 조회 링크 <span class="muted">(있으면)</span></label><input class="inp" name="ship_tracking_url" value="<?= esc($si['tracking_url']) ?>" placeholder="https://"></div>
      <div class="field"><label>구매자에게 보일 배송 메모</label><textarea class="ta" name="ship_note" style="height:70px" placeholder="예: 박스 3개로 나눠 발송, 도착 전 기사가 Viber로 연락합니다."><?= esc($si['note']) ?></textarea></div>
      <button class="btn btn-g btn-block" type="submit" style="height:42px">배송중으로 변경</button></form></div>
  <?php endif ?>
  <?php if (in_array($o['status'], ['shipped', 'delivered'], true)): ?>
  <div class="act"><h3>배송 정보 수정</h3>
    <form method="post" action="<?= site_url('admin/orders/' . $o['id'] . '/ship') ?>"><?= csrf_field() ?>
      <?php $si = mm_ship_info($o); ?>
      <div class="field"><label>배송 방법</label><div class="shipm"><?php foreach (mm_ship_methods() as $k => $l): ?><label><input type="radio" name="ship_method" value="<?= $k ?>" <?= ($si['method'] ?: 'courier') === $k ? 'checked' : '' ?>><span><?= $l ?></span></label><?php endforeach ?></div></div>
      <div class="g2">
        <div class="field"><label>배송사·기사</label><input class="inp" name="ship_carrier" value="<?= esc($si['carrier']) ?>" placeholder="예: Royal Express"></div>
        <div class="field"><label>송장번호</label><input class="inp" name="ship_tracking_no" value="<?= esc($si['tracking_no']) ?>"></div>
        <div class="field"><label>발송일</label><input class="inp" type="date" name="ship_sent_on" value="<?= esc($si['sent_on'] ?: date('Y-m-d')) ?>"></div>
        <div class="field"><label>예상 도착일</label><input class="inp" type="date" name="ship_eta" value="<?= esc($si['eta']) ?>"></div>
      </div>
      <div class="field"><label>배송 조회 링크 <span class="muted">(있으면)</span></label><input class="inp" name="ship_tracking_url" value="<?= esc($si['tracking_url']) ?>" placeholder="https://"></div>
      <div class="field"><label>구매자에게 보일 배송 메모</label><textarea class="ta" name="ship_note" style="height:70px" placeholder="예: 박스 3개로 나눠 발송, 도착 전 기사가 Viber로 연락합니다."><?= esc($si['note']) ?></textarea></div>
      <button class="btn btn-block" type="submit">배송 정보 저장</button></form></div>
  <?php endif ?>
  <?php if ($o['status'] === 'shipped'): ?>
  <div class="act" style="border:2px solid var(--pri)"><form method="post" action="<?= site_url('admin/orders/' . $o['id'] . '/status') ?>"><?= csrf_field() ?><input type="hidden" name="status" value="delivered"><button class="btn btn-g btn-block" type="submit">배송 완료 처리</button></form></div>
  <?php endif ?>
  <div class="act"><h3>상태 직접 변경</h3>
    <form method="post" action="<?= site_url('admin/orders/' . $o['id'] . '/status') ?>" class="inl"><?= csrf_field() ?><select class="sel" name="status"><?php foreach (['requested', 'proof', 'paid', 'preparing', 'shipped', 'delivered', 'cancelled'] as $s): ?><option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= mm_order_status_label($s) ?></option><?php endforeach ?></select><button class="btn btn-sm" type="submit">변경</button></form>
    <div class="small muted mt8">취소하면 사용 포인트가 환원됩니다.</div></div>
  <div class="act"><h3>내부 메모 / 구매자 안내</h3>
    <form method="post" action="<?= site_url('admin/orders/' . $o['id'] . '/memo') ?>"><?= csrf_field() ?><textarea class="ta" name="admin_memo" style="height:80px"><?= esc($o['admin_memo']) ?></textarea><button class="btn btn-sm mt8" type="submit">저장</button></form></div>
</div>
</div>
