<?php /** 상품 카드 (아이스크림몰 카드 실측: 이미지 정사각 r12 → 브랜드 14 #777 → 상품명 15 2줄 → 가격 20 bold). $p, $approved, $sup(optional) */
$isNew = isset($p['created_at']) && strtotime($p['created_at']) > strtotime('-14 days'); ?>
<a class="pc" href="<?= site_url('products/' . $p['id']) ?>">
  <div class="im"><img src="<?= mm_thumb($p['thumb']) ?>" alt="">
    <?php if ($p['is_best']): ?><span class="bst">BEST</span><?php elseif ($isNew): ?><span class="nw">NEW</span><?php endif ?>
    <span class="mq"><?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?><br>단위</span>
  </div>
  <div class="sup"><?= esc(! empty($sup) ? $sup['company_name'] : ($p['brand'] ?: '')) ?></div>
  <h3><?= esc($p['name']) ?></h3>
  <div class="pr">
    <?php if ($approved): ?>
      <span class="price"><?= mm_price($p['price'], false) ?><small><?= esc(mm_setting('currency_symbol', 'Ks')) ?></small></span>
      <?php if ($p['retail_price']): ?><span class="retail"><?= mm_price($p['retail_price'], false) ?></span><?php endif ?>
    <?php else: ?>
      <span class="lock">회원 혜택가</span>
    <?php endif ?>
  </div>
  <?php if (! $approved && $p['retail_price']): ?><div class="mqtxt">소비자가 <?= mm_price($p['retail_price'], false) ?></div><?php endif ?>
</a>
