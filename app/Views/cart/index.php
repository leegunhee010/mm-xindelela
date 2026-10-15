<?php $sym = mm_setting('currency_symbol', 'Ks'); ?>
<div class="contents-title-area depth-1 title3 font-bold mb-[20px]"><div class="title-area"><h3 class="title">장바구니</h3><span class="body3 font-regular text-gray3">1 장바구니 › 2 주문 신청 › 3 금액 안내·KBZPay 송금 › 4 입금 확인·발주 › 5 배송</span></div></div>

<?php if (! $items): ?>
  <div class="flex flex-col items-center justify-center rounded-[16px] bg-gray9 py-[90px]"><p class="sub-head1 font-bold">장바구니가 비어 있습니다.</p><a href="<?= site_url('products') ?>" class="mt-[16px] text-white bg-primary min-h-[44px] px-[24px] py-[12px] body3 !leading-none rounded-full font-bold inline-flex items-center">상품 보러가기</a></div>
<?php else: ?>
<form method="post" action="<?= site_url('cart/update') ?>" id="cartForm"><?= csrf_field() ?>
<div class="flex justify-between gap-[40px] items-start">
  <ul class="flex-1 border-t border-black">
    <?php foreach ($items as $it): $p = $it['p']; ?>
    <li class="flex items-center gap-[20px] border-b border-gray7 py-[24px]">
      <a href="<?= site_url('products/' . $p['id']) ?>" class="block size-[96px] shrink-0 overflow-hidden rounded-[12px] bg-gray9"><img src="<?= mm_thumb($p['thumb']) ?>" alt="" class="size-full object-cover"></a>
      <div class="flex-1 min-w-0">
        <p class="label1 text-gray3"><?= esc($it['supplier']['company_name'] ?? '') ?></p>
        <a href="<?= site_url('products/' . $p['id']) ?>" class="body2 font-medium block mt-[2px]"><?= esc($p['name']) ?></a>
        <p class="label1 text-gray4 mt-[4px]">단가 <?= mm_price($it['unit']) ?> · 최소 <?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?> 단위 · 수량별 단가 적용</p>
      </div>
      <div class="input-item !flex-none w-[110px]"><input type="number" name="qty[<?= $it['cart']['id'] ?>]" value="<?= $it['qty'] ?>" min="<?= (int) $p['moq'] ?>" step="<?= (int) $p['moq'] ?>" class="h-[40px] w-full px-3 text-center bg-white body3 border border-gray6 rounded-[8px] focus:outline-none focus:border-black"></div>
      <div class="w-[150px] text-right head-line1 font-bold"><?= mm_price($it['line'], false) ?> <span class="body3 font-medium"><?= esc($sym) ?></span></div>
      <button type="submit" form="rm<?= $it['cart']['id'] ?>" class="size-[32px] rounded-full border border-gray7 text-gray4" title="삭제">✕</button>
    </li>
    <?php endforeach ?>
  </ul>
  <div class="w-[360px] shrink-0 rounded-[16px] border border-gray6 p-[24px] sticky top-[20px]">
    <h4 class="sub-head1 font-bold mb-[16px]">신청 금액</h4>
    <div class="flex justify-between body3 py-[8px] border-b border-gray7"><span class="text-gray3">상품 합계</span><span><?= mm_price($subtotal) ?></span></div>
    <div class="flex justify-between body3 py-[8px] border-b border-gray7"><span class="text-gray3">배송비</span><span class="text-gray4">신청 후 안내</span></div>
    <div class="flex justify-between items-baseline pt-[14px]"><span class="sub-head1 font-bold">예상 금액</span><span class="title3 font-bold text-primary"><?= mm_price($subtotal, false) ?> <span class="body3 font-medium text-black"><?= esc($sym) ?></span></span></div>
    <p class="label1 text-gray4 mt-[8px]">포인트는 주문 신청 단계에서 사용할 수 있습니다.</p>
    <div class="btn-area shrink-0 flex btn-grid01 body3 flex-col gap-[10px] mt-[20px]">
      <a href="<?= site_url('checkout') ?>" class="text-white bg-primary min-h-[44px] px-[16px] py-[12px] body3 !leading-none w-full rounded-full font-bold text-center">주문 신청하기</a>
      <button type="submit" class="text-black bg-white border border-gray6 min-h-[44px] px-[16px] py-[12px] body3 font-bold !leading-none w-full rounded-full">수량 변경 저장</button>
    </div>
  </div>
</div>
</form>
<?php foreach ($items as $it): ?><form id="rm<?= $it['cart']['id'] ?>" method="post" action="<?= site_url('cart/remove') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $it['cart']['id'] ?>"></form><?php endforeach ?>
<?php endif ?>
