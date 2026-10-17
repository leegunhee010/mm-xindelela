<?php /** 상품 카드 — 아이스크림몰 목록 카드 마크업 그대로. $p, $approved, $sup(optional), $w(폭 클래스, 기본 235) */
$w = $w ?? 'w-[235px]'; $isNew = isset($p['created_at']) && strtotime($p['created_at']) > strtotime('-14 days'); ?>
<li class="flex flex-wrap flex-none <?= $w ?>" style="width:<?= (int) preg_replace('/\D/', '', $w) ?>px">
<a class="block w-full cursor-pointer" href="<?= site_url('products/' . $p['id']) ?>">
  <div class="relative w-full"><div class="relative">
    <div class="relative w-full overflow-hidden aspect-square mb-[10px] flex justify-center items-center pc:rounded-[12px] bg-gray9">
      <div class="absolute inset-0 origin-center transition-transform duration-300 scale-100 hover:scale-[1.04]"><picture><img alt="<?= esc($p['name']) ?>" class="size-full object-cover w-full" src="<?= mm_thumb($p['thumb']) ?>"></picture></div>
    </div>
    <div class="absolute left-[12px] top-[12px] flex flex-wrap gap-[4px] pr-[20px]">
      <?php if ($p['is_best']): ?><span class="inline-flex h-[26px] items-center rounded-[99px] bg-primary px-[10px] text-[12px] font-bold text-white">BEST</span><?php elseif ($isNew): ?><span class="inline-flex h-[26px] items-center rounded-[99px] bg-[#22c55e] px-[10px] text-[12px] font-bold text-white">NEW</span><?php endif ?>
    </div>
    <div class="absolute right-[12px] top-[12px] flex size-[48px] items-center justify-center rounded-full bg-primary/90 text-center text-[11px] font-bold leading-[1.1] text-white">최소<br><?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?></div>
  </div>
  <div class="relative flex flex-auto flex-col items-start justify-end">
    <div class="flex w-full flex-col justify-end">
      <p class="sub-head2 mo:label1 truncate text-[#777777]"><?= esc(! empty($sup) ? $sup['company_name'] : ($p['brand'] ?: '')) ?></p>
      <p class="body2 sub-head2 ellipsis-2 mo:body3 mt-[2px]"><?= esc($p['name']) ?></p>
    </div>
    <div class="mt-1 flex w-full flex-wrap items-center gap-x-1">
      <?php if ($approved): ?>
        <span class="head-line1 mo:body2 font-bold"><?= mm_price($p['price'], false) ?><span class="text-[14px] font-medium ml-[2px]"><?= esc(mm_setting('currency_symbol', 'Ks')) ?></span></span>
        <?php if ($p['retail_price']): ?><span class="body3 text-gray5 line-through"><?= mm_price($p['retail_price'], false) ?></span><?php endif ?>
      <?php else: ?>
        <span class="head-line1 mo:body2 font-bold">회원 혜택가</span>
      <?php endif ?>
    </div>
    <?php if (! $approved && $p['retail_price']): ?><p class="mt-[2px] text-[12px] text-gray5">소비자가 <?= mm_price($p['retail_price'], false) ?></p><?php endif ?>
  </div></div>
</a>
</li>
