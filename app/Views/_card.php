<?php /** 상품 카드 (그리드용). $p, $approved, $sup(optional) */ ?>
<a class="pc" href="<?= site_url('products/' . $p['id']) ?>">
  <div class="im"><img src="<?= mm_thumb($p['thumb']) ?>" alt=""><?php if ($p['is_best']): ?><span class="bst">BEST</span><?php endif ?></div>
  <h3><?= esc($p['name']) ?></h3>
  <div class="pr">
    <?php if ($approved): ?>
      <span class="price"><?= mm_price($p['price'], false) ?><small><?= esc(mm_setting('currency_symbol', 'Ks')) ?></small></span>
    <?php else: ?>
      <span class="lock">회원 전용 도매가</span>
    <?php endif ?>
  </div>
  <div class="mq">최소 <?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?><?php if ($p['retail_price']): ?> · 소비자가 <?= mm_price($p['retail_price'], false) ?><?php endif ?></div>
  <?php if (! empty($sup)): ?><div class="sup"><?= esc($sup['company_name']) ?></div><?php endif ?>
</a>
