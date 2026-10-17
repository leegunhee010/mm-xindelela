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
  <div class="contents-title-area depth-1 title3 font-bold !pt-0 mb-[20px]"><div class="title-area !pt-0"><h3 class="title otitle">주문 상세 <span class="otitle-no"><?= esc($o['order_no']) ?></span><span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></h3></div></div>
  <?php if ($o['status'] !== 'cancelled'): ?>
  <div class="timeline"><?php foreach (array_values($flow) as $i => $l): ?><div class="<?= $i < $idx ? 'done' : ($i === $idx ? 'on' : '') ?>"><?= $l ?></div><?php endforeach ?></div>
  <?php endif ?>

  <?php if (in_array($o['status'], ['requested', 'quoted', 'proof'], true)): $req = $o['status'] === 'proof'; ?>
    <div class="paybox<?= $req ? ' is-req' : '' ?>">
      <?php if ($req): ?>
        <p class="paybox-t">입금 확인 중입니다</p>
        <p class="paybox-d">송금자 <b><?= esc($o['payment_note']) ?></b> 이름으로 확인하고 있습니다. 확인되면 바로 발주합니다.</p>
      <?php else: ?>
        <p class="paybox-t"><b><?= mm_price($o['total']) ?></b> 을 KBZPay로 송금해 주세요</p>
        <div class="paybox-acct"><?= esc(mm_setting('kbzpay_account', '')) ?></div>
      <?php endif ?>
      <?php if ($o['admin_memo']): ?><p class="paybox-d">담당자 안내: <?= nl2br(esc($o['admin_memo'])) ?></p><?php endif ?>
      <form class="paybox-f" method="post" action="<?= site_url('mypage/orders/' . $o['id'] . '/proof') ?>"><?= csrf_field() ?>
        <label for="payName">송금한 사람 이름 <span style="font-weight:400;color:#888">(KBZPay에 표시되는 내 이름)</span></label>
        <div class="paybox-row">
          <input id="payName" class="<?= $inp ?>" name="payment_note" value="<?= esc($o['payment_note']) ?>" placeholder="KBZPay 계정 이름" required>
          <button type="submit"><?= $req ? '이름 수정' : '입금 확인 요청' ?></button>
        </div>
      </form>
    </div>
  <?php elseif ($o['status'] === 'shipped'): ?>
    <div class="vstat"><h2>배송 중입니다</h2><p>도착 예정일과 송장은 아래 배송 정보에서 확인하세요.</p></div>
  <?php elseif ($o['status'] === 'cancelled'): ?>
    <div class="vstat"><h2>취소된 주문입니다</h2><p>사용한 포인트는 환원되었습니다.</p></div>
  <?php endif ?>

  <table class="kv">
    <tr><th>주문번호</th><td><?= esc($o['order_no']) ?> · <?= mm_dt($o['created_at']) ?></td></tr>
    <tr><th>결제 방법</th><td><?= mm_payment_label($o['payment_method']) ?></td></tr>
    <?php if ($o['memo']): ?><tr><th>요청 사항</th><td><?= nl2br(esc($o['memo'])) ?></td></tr><?php endif ?>
  </table>
  <?php $si = mm_ship_info($o); if (in_array($o['status'], ['shipped', 'delivered'], true) && ($si['method'] || $si['carrier'] || $si['tracking_no'] || $si['eta'] || $si['note'] || $o['tracking'])): ?>
  <div class="oship">
    <h4>배송 정보</h4>
    <dl>
      <?php if ($si['method']): ?><div><dt>배송 방법</dt><dd><?= esc(mm_ship_methods()[$si['method']] ?? '') ?></dd></div><?php endif ?>
      <?php if ($si['carrier']): ?><div><dt>배송사</dt><dd><?= esc($si['carrier']) ?></dd></div><?php endif ?>
      <?php if ($si['tracking_no']): ?><div><dt>송장번호</dt><dd><b><?= esc($si['tracking_no']) ?></b><?php if ($si['tracking_url']): ?> <a href="<?= esc($si['tracking_url']) ?>" target="_blank" rel="noopener">배송 조회 ›</a><?php endif ?></dd></div><?php endif ?>
      <?php if ($si['sent_on']): ?><div><dt>발송일</dt><dd><?= esc(str_replace('-', '.', $si['sent_on'])) ?></dd></div><?php endif ?>
      <?php if ($si['eta']): ?><div><dt>도착 예정</dt><dd><b><?= esc(str_replace('-', '.', $si['eta'])) ?></b></dd></div><?php endif ?>
      <?php if ($si['note']): ?><div><dt>배송 메모</dt><dd><?= nl2br(esc($si['note'])) ?></dd></div><?php endif ?>
      <?php if (! $si['method'] && ! $si['carrier'] && $o['tracking']): ?><div><dt>배송 정보</dt><dd><?= esc($o['tracking']) ?></dd></div><?php endif ?>
    </dl>
  </div>
  <?php endif ?>

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
            <ul class="addr-sug" id="addrSug" hidden></ul></div><?php if ($mapsKey): ?><p class="addr-hint">미얀마 주소나 가게 이름을 영어로 치면 구글맵에서 찾아 드립니다</p><?php endif ?>
          <input type="hidden" name="ship_lat" id="shipLat" value="<?= esc($o['ship_lat'] ?? '') ?>"><input type="hidden" name="ship_lng" id="shipLng" value="<?= esc($o['ship_lng'] ?? '') ?>">
        </div>
      </div>
      <?php if ($mapsKey): ?><div class="addr-map-wrap"><div id="addrMapBox" class="addr-map"></div><p class="addr-hint">주소를 고르면 핀이 꽂힙니다. 핀을 끌거나 지도를 누르면 그 위치의 주소로 바뀝니다.</p></div><?php endif ?>
      <div class="flex justify-end gap-[8px] mt-[20px]">
        <button type="button" class="oaddr-btn" id="oaddrCancel">취소</button>
        <button type="submit" class="inline-flex items-center text-white bg-primary min-h-[40px] px-[20px] label1 font-bold !leading-none rounded-full">배송지 저장</button>
      </div>
    </form>
    <script>
    (function () {
      var v = document.getElementById('oaddrView'), f = document.getElementById('oaddrForm'), e = document.getElementById('oaddrEdit');
      function edit(on) { v.hidden = on; f.hidden = !on; e.hidden = on; if (on) window.dispatchEvent(new Event('mm-addr-show')); }
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
