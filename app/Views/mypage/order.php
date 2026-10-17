<?php
$flow = ['requested' => '주문 신청', 'proof' => '입금 확인중', 'paid' => '입금 확인', 'preparing' => '발주·준비', 'shipped' => '배송중', 'delivered' => '배송 완료'];
$stKey = $o['status'] === 'quoted' ? 'requested' : $o['status'];
$idx   = array_search($stKey, array_keys($flow), true);
$canEditAddr = in_array($o['status'], ['requested', 'quoted', 'proof'], true);
$inp = 'h-[44px] w-full px-3 bg-white body3 text-black placeholder:text-gray4 focus:outline-none border-gray6 focus:border-black border rounded-[8px]';
?>
<div class="flex gap-[60px] items-start">
<?= view('mypage/_side') ?>
<div class="flex-1 min-w-0">
  <div class="contents-title-area depth-1 title3 font-bold !pt-0 mb-[20px]"><div class="title-area !pt-0"><h3 class="title">주문 상세 <span class="muted" style="font-size:13px;font-weight:400"><?= esc($o['order_no']) ?></span> <span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></h3></div></div>
  <?php if ($o['status'] !== 'cancelled'): ?>
  <div class="timeline"><?php foreach (array_values($flow) as $i => $l): ?><div class="<?= $i < $idx ? 'done' : ($i === $idx ? 'on' : '') ?>"><?= $l ?></div><?php endforeach ?></div>
  <?php endif ?>

  <?php if (in_array($o['status'], ['requested', 'quoted'], true)): ?>
    <div class="vstat"><h2><?= mm_price($o['total']) ?> 을 KBZPay로 송금해 주세요</h2>
      <div class="acct"><?= esc(mm_setting('kbzpay_account', '')) ?></div>
      <?php if ($o['admin_memo']): ?><p class="mt8">담당자 안내: <?= nl2br(esc($o['admin_memo'])) ?></p><?php endif ?>
      <form class="mt16" method="post" action="<?= site_url('mypage/orders/' . $o['id'] . '/proof') ?>" enctype="multipart/form-data"><?= csrf_field() ?>
        <div class="field"><label>송금 캡처 이미지 <i>*</i></label><input type="file" name="proof" accept="image/*" required></div>
        <div class="field"><label>송금자명·메모</label><input class="<?= $inp ?>" name="payment_note" placeholder="송금한 KBZPay 계정 이름"></div>
        <button class="inline-flex items-center text-white bg-primary min-h-[44px] px-[20px] py-[12px] body3 !leading-none rounded-full font-bold" type="submit">송금 캡처 올리기</button>
      </form>
    </div>
  <?php elseif ($o['status'] === 'proof'): ?>
    <div class="vstat"><h2>입금 확인 중입니다</h2><p>담당자가 입금을 확인하면 바로 발주합니다. 다른 캡처로 바꾸려면 아래에서 다시 올려 주세요.</p>
      <?php if ($o['payment_proof']): ?><a href="<?= base_url($o['payment_proof']) ?>" target="_blank"><img src="<?= base_url($o['payment_proof']) ?>" alt="" style="max-width:240px;border:1px solid var(--line);margin:8px 0"></a><?php endif ?>
      <form method="post" action="<?= site_url('mypage/orders/' . $o['id'] . '/proof') ?>" enctype="multipart/form-data" class="mmrow"><?= csrf_field() ?><input type="file" name="proof" accept="image/*" required><input class="<?= $inp ?>" name="payment_note" value="<?= esc($o['payment_note']) ?>" placeholder="송금자명" style="width:200px"><button class="inline-flex items-center text-black bg-white border border-gray6 min-h-[36px] px-[16px] py-[8px] label1 !leading-none rounded-full font-bold" type="submit">다시 올리기</button></form>
    </div>
  <?php elseif ($o['status'] === 'shipped'): ?>
    <div class="vstat"><h2>배송 중입니다</h2><p>송장·배송 정보: <?= esc($o['tracking'] ?: '담당자 안내 예정') ?></p></div>
  <?php elseif ($o['status'] === 'cancelled'): ?>
    <div class="vstat"><h2>취소된 주문입니다</h2><p>사용한 포인트는 환원되었습니다.</p></div>
  <?php endif ?>

  <table class="kv">
    <tr><th>주문번호</th><td><?= esc($o['order_no']) ?> · <?= mm_dt($o['created_at']) ?></td></tr>
    <tr><th>결제 방법</th><td><?= mm_payment_label($o['payment_method']) ?></td></tr>
    <?php if ($o['memo']): ?><tr><th>요청 사항</th><td><?= nl2br(esc($o['memo'])) ?></td></tr><?php endif ?>
    <?php if ($o['tracking']): ?><tr><th>배송 정보</th><td><?= esc($o['tracking']) ?></td></tr><?php endif ?>
  </table>

  <!-- 배송지 -->
  <div class="oaddr" id="oaddr">
    <div class="oaddr-h"><b>배송지</b>
      <?php if ($canEditAddr): ?><span class="oaddr-note">입금 확인 전까지 수정할 수 있습니다</span><button type="button" class="oaddr-btn" id="oaddrEdit">수정</button>
      <?php else: ?><span class="oaddr-note">입금 확인 후에는 고객센터로 변경을 요청해 주세요</span><?php endif ?>
    </div>
    <dl class="oaddr-view" id="oaddrView">
      <div><dt>받는 사람</dt><dd><?= esc($o['ship_name']) ?></dd></div>
      <div><dt>연락처</dt><dd><?= esc($o['ship_phone']) ?></dd></div>
      <div><dt>도시</dt><dd><?= esc($o['ship_city']) ?></dd></div>
      <div><dt>상세 주소</dt><dd><?= esc($o['ship_address']) ?><?php if (! empty($o['ship_lat'])): ?> <a class="oaddr-map" target="_blank" rel="noopener" href="https://www.google.com/maps?q=<?= esc($o['ship_lat']) ?>,<?= esc($o['ship_lng']) ?>">지도 보기 ›</a><?php endif ?></dd></div>
    </dl>
    <?php if ($canEditAddr): ?>
    <form class="oaddr-form" id="oaddrForm" method="post" action="<?= site_url('mypage/orders/' . $o['id'] . '/address') ?>" hidden><?= csrf_field() ?>
      <div class="grid grid-cols-2 gap-x-[12px] gap-y-[20px]">
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">받는 사람 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" name="ship_name" value="<?= esc(old('ship_name', $o['ship_name'])) ?>" required></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">연락처 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" name="ship_phone" value="<?= esc(old('ship_phone', $o['ship_phone'])) ?>" required></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">도시 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" name="ship_city" id="shipCity" value="<?= esc(old('ship_city', $o['ship_city'])) ?>" required></div></label>
        <div class="block"><label for="shipAddr" class="label1 text-gray3 mb-[6px] block">상세 주소 <i class="text-primary not-italic">*</i></label>
          <div class="addr-search relative"><div class="input-item"><input id="shipAddr" class="<?= $inp ?>" name="ship_address" value="<?= esc(old('ship_address', $o['ship_address'])) ?>" required autocomplete="off"<?= $mapsKey ? ' placeholder="주소나 가게 이름을 입력하면 찾아 드립니다"' : '' ?>></div>
            <ul class="addr-sug" id="addrSug" hidden></ul></div>
          <input type="hidden" name="ship_lat" id="shipLat" value="<?= esc($o['ship_lat'] ?? '') ?>"><input type="hidden" name="ship_lng" id="shipLng" value="<?= esc($o['ship_lng'] ?? '') ?>">
          <a id="addrMap" class="mt-[6px] label1 text-primary" style="display:none" target="_blank" rel="noopener">지도에서 위치 확인 ›</a>
        </div>
      </div>
      <div class="flex justify-end gap-[8px] mt-[20px]">
        <button type="button" class="oaddr-btn" id="oaddrCancel">취소</button>
        <button type="submit" class="inline-flex items-center text-white bg-primary min-h-[40px] px-[20px] label1 font-bold !leading-none rounded-full">배송지 저장</button>
      </div>
    </form>
    <script>
    (function () {
      var v = document.getElementById('oaddrView'), f = document.getElementById('oaddrForm'), e = document.getElementById('oaddrEdit');
      function edit(on) { v.hidden = on; f.hidden = !on; e.hidden = on; }
      e.onclick = function () { edit(true); };
      document.getElementById('oaddrCancel').onclick = function () { edit(false); };
      <?php if (session('errors') || old('ship_address') !== null): ?>edit(true);<?php endif ?>
    })();
    </script>
    <?= view('_addr_search', ['mapsKey' => $mapsKey]) ?>
    <?php endif ?>
  </div>

  <div class="oitems mt24">
    <?php foreach ($items as $it): ?><div class="it"><img src="<?= mm_thumb($it['thumb']) ?>" alt=""><div class="n"><a href="<?= site_url('products/' . $it['product_id']) ?>"><?= esc($it['product_name']) ?></a><div class="s">단가 <?= mm_price($it['unit_price']) ?><?= $it['item_status'] === 'shipped' ? ' · <span class="st st-shipped">출고</span>' : '' ?></div></div><div class="q"><?= number_format((int) $it['qty']) ?>개</div><div class="p"><?= mm_price($it['line_total']) ?></div></div><?php endforeach ?>
  </div>
  <div class="osum">
    <dl>
      <div><dt>상품 합계</dt><dd><?= mm_price($o['subtotal']) ?></dd></div>
      <?php if ((int) $o['points_used'] > 0): ?><div><dt>포인트 사용</dt><dd>-<?= number_format((int) $o['points_used']) ?> P</dd></div><?php endif ?>
      <?php if ((int) $o['shipping_fee'] > 0): ?><div><dt>배송비</dt><dd><?= mm_price($o['shipping_fee']) ?></dd></div><?php endif ?>
    </dl>
    <div class="osum-total"><span>결제 금액</span><b><?= mm_price($o['total']) ?></b></div>
  </div>
  <?php if (in_array($o['status'], ['requested', 'quoted', 'proof'], true)): ?>
  <form class="osum-cancel" method="post" action="<?= site_url('mypage/orders/' . $o['id'] . '/cancel') ?>" onsubmit="return confirm('주문 신청을 취소할까요?')"><?= csrf_field() ?><span>입금 전까지 취소할 수 있습니다.</span><button class="inline-flex items-center text-[#dc2626] bg-white border border-[#f87171] min-h-[40px] px-[18px] py-[8px] label1 !leading-none rounded-full font-bold" type="submit">주문 신청 취소</button></form>
  <?php endif ?>
</div>
</div>
