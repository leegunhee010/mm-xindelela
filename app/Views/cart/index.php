<?php $sym = mm_setting('currency_symbol', 'Ks'); ?>
<div class="contents-title-area depth-1 title3 font-bold mb-[20px]"><div class="title-area"><h3 class="title"><?= t('장바구니') ?></h3><span class="body3 font-regular text-gray3"><?= t('1 장바구니 › 2 주문 신청 › 3 KBZPay 송금 › 4 입금 확인·발주 › 5 배송') ?></span></div></div>

<?php if (! $items): ?>
  <div class="flex flex-col items-center justify-center rounded-[16px] bg-gray9 py-[90px]"><p class="sub-head1 font-bold"><?= t('장바구니가 비어 있습니다.') ?></p><a href="<?= site_url('products') ?>" class="mt-[16px] text-white bg-primary min-h-[44px] px-[24px] py-[12px] body3 !leading-none rounded-full font-bold inline-flex items-center"><?= t('상품 보러가기') ?></a></div>
<?php else: ?>
<form method="post" action="<?= site_url('cart/update') ?>" id="cartForm"><?= csrf_field() ?>
<div class="cartwrap flex justify-between gap-[40px] items-start">
  <ul class="flex-1 border-t border-black">
    <?php foreach ($items as $it): $p = $it['p']; ?>
    <li class="cart-li flex items-center gap-[20px] border-b border-gray7 py-[24px]">
      <a href="<?= site_url('products/' . $p['id']) ?>" class="block size-[96px] shrink-0 overflow-hidden rounded-[12px] bg-gray9"><img src="<?= mm_thumb($p['thumb']) ?>" alt="" class="size-full object-cover"></a>
      <div class="flex-1 min-w-0">
        <p class="label1 text-gray3"><?= esc($it['supplier']['company_name'] ?? '') ?></p>
        <a href="<?= site_url('products/' . $p['id']) ?>" class="body2 font-medium block mt-[2px]"><?= esc($p['name']) ?></a>
        <p class="label1 text-gray4 mt-[4px]"><?= t('단가') ?> <?= mm_price($it['unit']) ?> <?= t('· 최소') ?> <?= number_format((int) $p['moq']) ?><?= esc(t($p['unit'])) ?><?= t('부터 · 수량별 단가 적용') ?></p>
      </div>
      <span class="stp cstp" data-moq="<?= (int) $p['moq'] ?>"><button type="button" class="minus" aria-label="<?= t('빼기') ?>">−</button><input type="text" inputmode="numeric" name="qty[<?= $it['cart']['id'] ?>]" value="<?= $it['qty'] ?>" aria-label="<?= t('수량') ?>"><button type="button" class="plus" aria-label="<?= t('더하기') ?>">+</button></span>
      <div class="w-[150px] text-right head-line1 font-bold"><?= mm_price($it['line'], false) ?> <span class="body3 font-medium"><?= esc($sym) ?></span></div>
      <button type="submit" form="rm<?= $it['cart']['id'] ?>" class="size-[32px] rounded-full border border-gray7 text-gray4" title="<?= t('삭제') ?>">✕</button>
    </li>
    <?php endforeach ?>
  </ul>
  <div class="w-[360px] shrink-0 rounded-[16px] border border-gray6 p-[24px] sticky top-[20px]">
    <h4 class="sub-head1 font-bold mb-[16px]"><?= t('신청 금액') ?></h4>
    <div class="flex justify-between body3 py-[8px] border-b border-gray7"><span class="text-gray3"><?= t('상품 합계') ?></span><span><?= mm_price($subtotal) ?></span></div>
    <div class="flex justify-between items-baseline pt-[14px]"><span class="sub-head1 font-bold"><?= t('결제 금액') ?></span><span class="title3 font-bold text-primary"><?= mm_price($subtotal, false) ?> <span class="body3 font-medium text-black"><?= esc($sym) ?></span></span></div>
    <p class="label1 text-gray4 mt-[8px]"><?= t('포인트는 주문 신청 단계에서 사용할 수 있습니다.') ?></p>
    <div class="btn-area shrink-0 flex btn-grid01 body3 flex-col gap-[10px] mt-[20px]">
      <a href="<?= site_url('checkout') ?>" class="text-white bg-primary min-h-[44px] px-[16px] py-[12px] body3 !leading-none w-full rounded-full font-bold text-center"><?= t('주문 신청하기') ?></a>
    </div>
  </div>
</div>
</form>
<?php foreach ($items as $it): ?><form id="rm<?= $it['cart']['id'] ?>" method="post" action="<?= site_url('cart/remove') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $it['cart']['id'] ?>"></form><?php endforeach ?>
<script>
(function () {
  var f = document.getElementById('cartForm'), t = null;
  document.querySelectorAll('.cstp').forEach(function (w) {
    var inp = w.querySelector('input'), moq = parseInt(w.dataset.moq || '1', 10);
    function set(v) { v = parseInt(v, 10); if (isNaN(v) || v < moq) v = moq; inp.value = v; clearTimeout(t); t = setTimeout(function () { f.submit(); }, 500); }
    w.querySelector('.minus').onclick = function () { set(parseInt(inp.value, 10) - 1); };
    w.querySelector('.plus').onclick = function () { set(parseInt(inp.value, 10) + 1); };
    inp.addEventListener('change', function () { set(inp.value); });
  });
})();
</script>
<?php endif ?>
