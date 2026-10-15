<?php $sym = mm_setting('currency_symbol', 'Ks'); ?>
<div class="crumb"><a href="<?= site_url('/') ?>">홈</a><a href="<?= site_url('cart') ?>">장바구니</a>주문 신청</div>
<div class="steps"><div class="done">1. 장바구니</div><div class="on">2. 주문 신청</div><div>3. 금액 안내·KBZPay 송금</div><div>4. 입금 확인·발주</div><div>5. 배송</div></div>

<form method="post" action="<?= site_url('checkout') ?>" class="ck"><?= csrf_field() ?>
<div>
  <div class="card">
    <h3>주문 상품 <span class="muted small" style="font-weight:400"><?= count($items) ?>종</span></h3>
    <div class="oitems">
      <?php foreach ($items as $it): ?>
      <div class="it"><img src="<?= mm_thumb($it['p']['thumb']) ?>" alt=""><div class="n"><?= esc($it['p']['name']) ?><div class="s"><?= esc($it['supplier']['company_name'] ?? '') ?> · 단가 <?= mm_price($it['unit']) ?></div></div><div class="q"><?= number_format($it['qty']) ?><?= esc($it['p']['unit']) ?></div><div class="p"><?= mm_price($it['line'], false) ?> <?= esc($sym) ?></div></div>
      <?php endforeach ?>
    </div>
  </div>

  <div class="card mt16">
    <h3>배송지</h3>
    <div class="g2">
      <div class="field"><label>받는 사람 <i>*</i></label><input class="inp" name="ship_name" value="<?= esc(old('ship_name', $user['name'])) ?>" required></div>
      <div class="field"><label>연락처 <i>*</i></label><input class="inp" name="ship_phone" value="<?= esc(old('ship_phone', $user['phone'])) ?>" required></div>
      <div class="field"><label>도시 <i>*</i></label><input class="inp" name="ship_city" value="<?= esc(old('ship_city', $user['city'])) ?>" required></div>
      <div class="field"><label>상세 주소 <i>*</i></label><input class="inp" name="ship_address" value="<?= esc(old('ship_address', $user['address'])) ?>" required></div>
    </div>
    <div class="field"><label>요청 사항</label><textarea class="ta" name="memo" style="height:70px" placeholder="배송·포장 요청, 희망 납기 등"><?= esc(old('memo')) ?></textarea></div>
  </div>

  <div class="card mt16">
    <h3>결제 방법</h3>
    <div class="pay">
      <label><input type="radio" name="payment_method" value="kbzpay" checked><span><b>KBZPay 선불 (100% 선입금)</b><div class="d">주문 신청 후 담당자가 배송비 포함 최종 금액을 안내하면 KBZPay로 송금하고 캡처를 올려 주세요. 입금 확인 즉시 발주합니다.</div></span></label>
      <label class="<?= $codEnabled ? '' : 'dis' ?>"><input type="radio" name="payment_method" value="cod" <?= $codEnabled ? '' : 'disabled' ?>><span><b>착불 (COD)</b><div class="d"><?= $codEnabled ? '상품 수령 시 현금 결제' : '준비 중입니다' ?></div></span></label>
    </div>
  </div>

  <div class="card mt16">
    <h3>포인트 사용 <span class="muted small" style="font-weight:400">보유 <?= number_format((int) $user['points_balance']) ?>P · 이번 주문 최대 <?= number_format($maxPoints) ?>P (<?= (int) mm_setting('points_use_max_pct', 30) ?>%)</span></h3>
    <div class="row"><input class="inp" type="number" id="points" name="points" value="0" min="0" max="<?= $maxPoints ?>" data-subtotal="<?= $subtotal ?>" style="width:200px"><span>P</span><button class="btn btn-sm" type="button" id="ptAll">최대 사용</button><span class="small muted">최소 <?= number_format((int) mm_setting('points_min_use', 0)) ?>P부터 사용 가능</span></div>
  </div>
</div>

<div class="sum">
  <h3>신청 금액</h3>
  <div class="ln"><span>상품 합계</span><span><?= mm_price($subtotal, false) ?> <?= esc($sym) ?></span></div>
  <div class="ln"><span>포인트 사용</span><span id="ckPoints">-0</span></div>
  <div class="ln"><span>배송비</span><span class="muted">신청 후 안내</span></div>
  <div class="ln tt"><span>예상 금액</span><b><span id="ckTotal"><?= mm_price($subtotal, false) ?></span> <?= esc($sym) ?></b></div>
  <div class="small muted mt8">배송비 포함 최종 금액은 담당자 확인 후 마이페이지와 Viber로 안내됩니다. 이 단계에서는 결제가 일어나지 않습니다.</div>
  <button class="btn btn-g btn-block mt16" style="height:50px;font-size:17px" type="submit">주문 신청하기</button>
</div>
</form>
