<div class="two">
<div>
  <div class="card"><h3><?= esc($o['order_no']) ?> <span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span> <span class="muted small" style="font-weight:400"><?= mm_dt($o['created_at']) ?></span></h3>
    <table class="kv">
      <tr><th>구매자</th><td><a href="<?= site_url('admin/buyers/' . $o['user_id']) ?>"><b><?= esc($buyer['company_name'] ?? '-') ?></b></a> · <?= esc($buyer['name'] ?? '') ?> · 전화 <?= esc($buyer['phone'] ?? '') ?> · Viber <?= esc($buyer['viber'] ?? $buyer['phone'] ?? '') ?></td></tr>
      <tr><th>배송지</th><td><?= esc($o['ship_name']) ?> · <?= esc($o['ship_phone']) ?><br><?= esc($o['ship_city']) ?> <?= esc($o['ship_address']) ?></td></tr>
      <tr><th>결제 방법</th><td><?= mm_payment_label($o['payment_method']) ?></td></tr>
      <?php if ($o['memo']): ?><tr><th>구매자 요청</th><td><?= nl2br(esc($o['memo'])) ?></td></tr><?php endif ?>
      <tr><th>입금 캡처</th><td><?php if ($o['payment_proof']): ?><a href="<?= base_url($o['payment_proof']) ?>" target="_blank"><img class="proof" src="<?= base_url($o['payment_proof']) ?>" alt=""></a><div class="small muted"><?= esc($o['payment_note']) ?> · <?= mm_dt($o['proof_at']) ?></div><?php else: ?><span class="muted">아직 없음</span><?php endif ?></td></tr>
      <tr><th>일시</th><td class="small">금액확정 <?= mm_dt($o['quoted_at']) ?> · 입금확인 <?= mm_dt($o['paid_at']) ?> · 출고 <?= mm_dt($o['shipped_at']) ?> · 완료 <?= mm_dt($o['delivered_at']) ?></td></tr>
    </table>
    <div class="oitems mt16">
      <?php foreach ($items as $it): ?><div class="it"><img src="<?= mm_thumb($it['thumb']) ?>" alt=""><div class="n"><a href="<?= site_url('admin/products/' . $it['product_id']) ?>"><?= esc($it['product_name']) ?></a><div class="s">공급사 <?= esc($it['supplier']['company_name'] ?? '-') ?> · 단가 <?= mm_price($it['unit_price']) ?> · <?= $it['item_status'] === 'shipped' ? '<span class="st st-shipped">출고</span>' : '<span class="st">미출고</span>' ?></div></div><div class="q"><?= number_format((int) $it['qty']) ?>개</div><div class="p"><?= mm_price($it['line_total']) ?></div></div><?php endforeach ?>
    </div>
    <div class="card mt16" style="background:var(--bg2);margin:16px 0 0">
      <div class="row sp"><span>상품 합계</span><span><?= mm_price($o['subtotal']) ?></span></div>
      <div class="row sp"><span>배송비</span><span><?= mm_price($o['shipping_fee']) ?></span></div>
      <div class="row sp"><span>포인트 사용</span><span>-<?= number_format((int) $o['points_used']) ?> P</span></div>
      <div class="row sp" style="border-top:1px solid var(--line);padding-top:8px;margin-top:8px;font-weight:700;font-size:15px"><span>최종 금액</span><span style="color:var(--price);font-size:20px"><?= mm_price($o['total']) ?></span></div>
    </div>
  </div>
</div>
<div>
  <?php if (in_array($o['status'], ['requested', 'quoted'], true)): ?>
  <div class="act" style="border:2px solid var(--green)"><h3>1. 금액 확정 → 입금 대기</h3>
    <form method="post" action="<?= site_url('admin/orders/' . $o['id'] . '/quote') ?>"><?= csrf_field() ?>
      <div class="field"><label>상품 합계 (조정 가능)</label><input class="inp" type="number" name="subtotal" value="<?= (int) $o['subtotal'] ?>"></div>
      <div class="field"><label>배송비</label><input class="inp" type="number" name="shipping_fee" value="<?= (int) $o['shipping_fee'] ?>"></div>
      <div class="field"><label>구매자에게 보이는 안내</label><textarea class="ta" name="admin_memo" style="height:70px" placeholder="예: 양곤 시내 배송 3일, 송금 시 메모에 주문번호를 적어 주세요."><?= esc($o['admin_memo']) ?></textarea></div>
      <button class="btn btn-g btn-block" type="submit">최종 금액 확정 (구매자에게 송금 안내)</button>
      <div class="small muted mt8">확정 후 구매자 주문 상세에 KBZPay 계좌와 금액이 표시됩니다. Viber로도 알려 주세요.</div>
    </form></div>
  <?php endif ?>
  <?php if (in_array($o['status'], ['quoted', 'proof'], true)): ?>
  <div class="act" style="border:2px solid var(--green)"><h3>2. 입금 확인</h3>
    <form method="post" action="<?= site_url('admin/orders/' . $o['id'] . '/status') ?>" onsubmit="return confirm('입금을 확인했습니까? 포인트 적립과 공급사 발주 알림이 진행됩니다.')"><?= csrf_field() ?><input type="hidden" name="status" value="paid"><button class="btn btn-g btn-block" type="submit" style="height:42px">입금 확인 → 발주</button></form></div>
  <?php endif ?>
  <?php if (in_array($o['status'], ['paid', 'preparing'], true)): ?>
  <div class="act" style="border:2px solid var(--green)"><h3>3. 출고 처리</h3>
    <form method="post" action="<?= site_url('admin/orders/' . $o['id'] . '/status') ?>"><?= csrf_field() ?><input type="hidden" name="status" value="shipped"><div class="field"><label>송장·배송 정보</label><input class="inp" name="tracking" value="<?= esc($o['tracking']) ?>" placeholder="배송사 / 송장번호 / 예상 도착일"></div><button class="btn btn-g btn-block" type="submit">배송중으로 변경</button></form></div>
  <?php endif ?>
  <?php if ($o['status'] === 'shipped'): ?>
  <div class="act" style="border:2px solid var(--green)"><form method="post" action="<?= site_url('admin/orders/' . $o['id'] . '/status') ?>"><?= csrf_field() ?><input type="hidden" name="status" value="delivered"><button class="btn btn-g btn-block" type="submit">배송 완료 처리</button></form></div>
  <?php endif ?>
  <div class="act"><h3>상태 직접 변경</h3>
    <form method="post" action="<?= site_url('admin/orders/' . $o['id'] . '/status') ?>" class="inl"><?= csrf_field() ?><select class="sel" name="status"><?php foreach (['requested', 'quoted', 'proof', 'paid', 'preparing', 'shipped', 'delivered', 'cancelled'] as $s): ?><option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= mm_order_status_label($s) ?></option><?php endforeach ?></select><button class="btn btn-sm" type="submit">변경</button></form>
    <div class="small muted mt8">취소하면 사용 포인트가 환원됩니다.</div></div>
  <div class="act"><h3>내부 메모 / 구매자 안내</h3>
    <form method="post" action="<?= site_url('admin/orders/' . $o['id'] . '/memo') ?>"><?= csrf_field() ?><textarea class="ta" name="admin_memo" style="height:80px"><?= esc($o['admin_memo']) ?></textarea><button class="btn btn-sm mt8" type="submit">저장</button></form></div>
</div>
</div>
