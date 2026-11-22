<?php $sym = mm_setting('currency_symbol', 'Ks'); $isBuyer = $user && $user['role'] === 'buyer'; ?>
<div class="contents-title-area depth-1 title3 font-bold mb-[20px]"><div class="title-area"><h3 class="title"><?= t('관심상품') ?> <span class="text-primary"><?= count($items) ?></span></h3></div></div>

<?php if (! $approved): ?>
<div class="wish-note">
  <?php if (! $isBuyer): ?>
    <p><b><?= t('담아 둔 상품은 가입한 뒤에도 그대로 남습니다.') ?></b> <?= t('비즈니스 인증을 받으면 도매가를 확인하고 여기서 바로 장바구니에 담을 수 있습니다.') ?></p>
    <div class="wish-note-btns"><a class="is-main" href="<?= site_url('register') ?>"><?= t('회원가입하고 인증하기') ?></a><a href="<?= site_url('login') ?>"><?= t('로그인') ?></a></div>
  <?php else: ?>
    <p><b><?= t('비즈니스 인증이 끝나면') ?></b> <?= t('도매가를 확인하고 여기서 바로 장바구니에 담을 수 있습니다. (') ?><?= esc(t(mm_setting('verify_sla_text'))) ?>)</p>
    <div class="wish-note-btns"><a class="is-main" href="<?= site_url('mypage/verify') ?>"><?= t('인증 상태 보기') ?></a></div>
  <?php endif ?>
</div>
<?php endif ?>

<?php if (! $items): ?>
  <div class="flex flex-col items-center justify-center rounded-[16px] bg-gray9 py-[90px]"><p class="sub-head1 font-bold"><?= t('담아 둔 관심상품이 없습니다.') ?></p><p class="body3 text-gray3 mt-[8px]"><?= t('상품 사진의 하트를 누르면 여기에 모입니다.') ?></p><a href="<?= site_url('products') ?>" class="mt-[16px] text-white bg-primary min-h-[44px] px-[24px] py-[12px] body3 !leading-none rounded-full font-bold inline-flex items-center"><?= t('상품 보러가기') ?></a></div>
<?php else: ?>
<form method="post" action="<?= site_url('wishlist/cart') ?>" id="wishForm"><?= csrf_field() ?>
  <?php if ($approved): ?>
  <div class="wish-bar"><label class="wish-all"><input type="checkbox" id="wishAll" checked> <?= t('전체 선택') ?></label><button type="submit" class="wish-cart"><?= t('선택 상품 장바구니 담기') ?></button></div>
  <?php endif ?>
  <ul class="border-t border-black">
    <?php foreach ($items as $it): $p = $it['p']; ?>
    <li class="wish-li flex items-center gap-[20px] border-b border-gray7 py-[20px]">
      <?php if ($approved): ?><input type="checkbox" class="wish-chk" name="ids[]" value="<?= $p['id'] ?>" checked aria-label="<?= t('선택') ?>"><?php endif ?>
      <a href="<?= site_url('products/' . $p['id']) ?>" class="block size-[96px] shrink-0 overflow-hidden rounded-[12px] bg-gray9"><img src="<?= mm_thumb($p['thumb']) ?>" alt="" class="size-full object-cover"></a>
      <div class="flex-1 min-w-0">
        <p class="label1 text-gray3"><?= esc(mm_sname($it['supplier'] ?? null) ?: t($p['brand'] ?? '')) ?></p>
        <a href="<?= site_url('products/' . $p['id']) ?>" class="body2 font-medium block mt-[2px]"><?= esc(mm_pname($p)) ?></a>
        <p class="label1 text-gray4 mt-[4px]"><?= t('최소') ?> <?= number_format((int) $p['moq']) ?><?= esc(t($p['unit'])) ?><?= t('부터') ?><?= in_array((int) $p['id'], mm_repeat_ids(), true) ? t(' · <span class="text-primary font-bold">재구매 많음</span>') : '' ?></p>
      </div>
      <div class="wish-price text-right">
        <?php if ($approved): ?><b class="head-line1 font-bold"><?= mm_price($p['price'], false) ?></b> <span class="body3"><?= esc($sym) ?></span>
        <?php else: ?><b class="body2 font-bold"><?= t('회원 혜택가') ?></b><?php if ($p['retail_price']): ?><p class="label1 text-gray5"><?= t('소비자가') ?> <?= mm_price($p['retail_price'], false) ?></p><?php endif ?><?php endif ?>
      </div>
      <button type="button" class="wish-btn on wish-sm" data-wish="<?= $p['id'] ?>" data-wish-remove aria-label="<?= t('관심상품에서 빼기') ?>" aria-pressed="true"></button>
    </li>
    <?php endforeach ?>
  </ul>
</form>
<?php if ($approved): ?>
<script>
(function () {
  var all = document.getElementById('wishAll');
  all.onchange = function () { document.querySelectorAll('.wish-chk').forEach(function (c) { c.checked = all.checked; }); };
})();
</script>
<?php endif ?>
<?php endif ?>
