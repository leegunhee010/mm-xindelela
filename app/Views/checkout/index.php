<?php $sym = mm_setting('currency_symbol', 'Ks'); $inp = 'h-[44px] w-full px-3 bg-white body3 text-black placeholder:text-gray4 focus:outline-none border-gray6 focus:border-black border rounded-[8px]'; ?>
<div class="contents-title-area depth-1 title3 font-bold mb-[20px]"><div class="title-area"><h3 class="title">주문 신청</h3><span class="body3 font-regular text-gray3">1 장바구니 › <b class="text-primary">2 주문 신청</b> › 3 KBZPay 송금 › 4 입금 확인·발주 › 5 배송</span></div></div>

<form method="post" action="<?= site_url('checkout') ?>" class="flex justify-between gap-[40px] items-start"><?= csrf_field() ?>
<div class="flex-1 min-w-0">
  <div class="mb-[40px]">
    <strong class="sub-head1 font-bold">주문 상품 <span class="label1 font-regular text-gray4"><?= count($items) ?>종</span></strong><hr class="divider bg-black border-0 h-px my-[14px]">
    <ul>
      <?php foreach ($items as $it): ?>
      <li class="flex items-center gap-[16px] border-b border-gray7 py-[16px]"><img src="<?= mm_thumb($it['p']['thumb']) ?>" alt="" class="size-[72px] rounded-[8px] object-cover bg-gray9"><div class="flex-1 min-w-0"><p class="body3"><?= esc($it['p']['name']) ?></p><p class="label1 text-gray4 mt-[2px]"><?= esc($it['supplier']['company_name'] ?? '') ?> · 단가 <?= mm_price($it['unit']) ?></p></div><div class="w-[90px] text-center body3"><?= number_format($it['qty']) ?><?= esc($it['p']['unit']) ?></div><div class="w-[140px] text-right sub-head1 font-bold"><?= mm_price($it['line'], false) ?> <span class="body3 font-medium"><?= esc($sym) ?></span></div></li>
      <?php endforeach ?>
    </ul>
  </div>

  <div class="mb-[40px]">
    <strong class="sub-head1 font-bold">배송지</strong><hr class="divider bg-gray6 border-0 h-px my-[18px]">
    <div class="grid grid-cols-2 gap-x-[12px] gap-y-[20px]">
      <label class="block"><span class="label1 text-gray3 mb-[6px] block">받는 사람 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" name="ship_name" value="<?= esc(old('ship_name', $user['name'])) ?>" required></div></label>
      <label class="block"><span class="label1 text-gray3 mb-[6px] block">연락처 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" name="ship_phone" value="<?= esc(old('ship_phone', $user['phone'])) ?>" required></div></label>
      <label class="block"><span class="label1 text-gray3 mb-[6px] block">도시 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" name="ship_city" id="shipCity" value="<?= esc(old('ship_city', $user['city'])) ?>" required></div></label>
      <div class="block"><label for="shipAddr" class="label1 text-gray3 mb-[6px] block">상세 주소 <i class="text-primary not-italic">*</i></label>
        <div class="addr-search relative"><div class="input-item"><input id="shipAddr" class="<?= $inp ?>" name="ship_address" value="<?= esc(old('ship_address', $user['address'])) ?>" required autocomplete="off"<?= $mapsKey ? ' placeholder="주소나 가게 이름을 입력하면 찾아 드립니다"' : '' ?>></div>
          <ul class="addr-sug" id="addrSug" hidden></ul></div><?php if ($mapsKey): ?><p class="addr-hint">주소나 가게 이름을 치면 구글맵에서 찾아 드립니다</p><?php endif ?>
        <input type="hidden" name="ship_lat" id="shipLat" value="<?= esc(old('ship_lat')) ?>"><input type="hidden" name="ship_lng" id="shipLng" value="<?= esc(old('ship_lng')) ?>">
        <a id="addrMap" class="mt-[6px] label1 text-primary" style="display:none" target="_blank" rel="noopener">지도에서 위치 확인 ›</a>
      </div>
    </div>
    <label class="block mt-[20px]"><span class="label1 text-gray3 mb-[6px] block">요청 사항</span><textarea name="memo" class="w-full px-3 py-[10px] bg-white body3 border border-gray6 rounded-[8px] h-[70px] focus:outline-none focus:border-black" placeholder="배송·포장 요청, 희망 납기 등"><?= esc(old('memo')) ?></textarea></label>
  </div>

  <div class="mb-[40px]">
    <strong class="sub-head1 font-bold">결제 방법</strong><hr class="divider bg-gray6 border-0 h-px my-[18px]">
    <div class="flex flex-col gap-[8px]">
      <label class="flex gap-[12px] items-start rounded-[12px] border border-primary bg-[#fff3eb] p-[16px] cursor-pointer"><input type="radio" name="payment_method" value="kbzpay" checked class="mt-[3px] accent-[#ff6200]"><span><b class="body3 font-bold block">KBZPay 선불 (100% 선입금)</b><span class="label1 text-gray3 block mt-[2px]">주문 신청 후 KBZPay로 송금하고 송금자 이름으로 입금 확인을 요청해 주세요. 확인 즉시 발주합니다.</span></span></label>
      <label class="flex gap-[12px] items-start rounded-[12px] border border-gray6 p-[16px] <?= $codEnabled ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed' ?>"><input type="radio" name="payment_method" value="cod" <?= $codEnabled ? '' : 'disabled' ?> class="mt-[3px] accent-[#ff6200]"><span><b class="body3 font-bold block">착불 (COD)</b><span class="label1 text-gray3 block mt-[2px]"><?= $codEnabled ? '상품 수령 시 현금 결제' : '준비 중입니다' ?></span></span></label>
    </div>
  </div>

  <div class="mb-[40px]">
    <strong class="sub-head1 font-bold">포인트 사용 <span class="label1 font-regular text-gray4">보유 <?= number_format((int) $user['points_balance']) ?>P · 이번 주문 최대 <?= number_format($maxPoints) ?>P (<?= (int) mm_setting('points_use_max_pct', 30) ?>%)</span></strong><hr class="divider bg-gray6 border-0 h-px my-[18px]">
    <div class="flex items-center gap-[10px]"><div class="input-item !flex-none w-[200px]"><input class="<?= $inp ?>" type="number" id="points" name="points" value="0" min="0" max="<?= $maxPoints ?>" data-subtotal="<?= $subtotal ?>"></div><span class="body3">P</span><button type="button" id="ptAll" class="text-black bg-white border border-gray6 min-h-[36px] px-[14px] py-[8px] label1 font-bold !leading-none rounded-full">최대 사용</button><span class="label1 text-gray4">최소 <?= number_format((int) mm_setting('points_min_use', 0)) ?>P부터</span></div>
  </div>
</div>

<div class="w-[360px] shrink-0 rounded-[16px] border border-black p-[24px] sticky top-[20px]">
  <h4 class="sub-head1 font-bold mb-[16px]">신청 금액</h4>
  <div class="flex justify-between body3 py-[8px] border-b border-gray7"><span class="text-gray3">상품 합계</span><span><?= mm_price($subtotal, false) ?> <?= esc($sym) ?></span></div>
  <div class="flex justify-between body3 py-[8px] border-b border-gray7"><span class="text-gray3">포인트 사용</span><span id="ckPoints">-0</span></div>
  <div class="flex justify-between items-baseline pt-[14px]"><span class="sub-head1 font-bold">결제 금액</span><span class="title3 font-bold text-primary"><span id="ckTotal"><?= mm_price($subtotal, false) ?></span> <span class="body3 font-medium text-black"><?= esc($sym) ?></span></span></div>
  <p class="label1 text-gray4 mt-[8px]">신청하면 KBZPay 계좌가 안내됩니다. 송금 후 주문 상세에서 송금자 이름으로 입금 확인을 요청해 주세요.</p>
  <button type="submit" class="mt-[20px] text-white bg-primary min-h-[50px] px-[16px] py-[12px] body2 !leading-none w-full rounded-full font-bold">주문 신청하기</button>
</div>
</form>

<?= view('_addr_search', ['mapsKey' => $mapsKey]) ?>
