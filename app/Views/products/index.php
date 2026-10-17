<?php $approved = mm_is_approved(); $base = current_url() . '?' . http_build_query(array_filter(['q' => $q, 'sf' => service('request')->getGet('sf')])); ?>
<nav class="breadcrumb m-auto w-large pb-[40px] pt-[24px]"><ol class="breadcrumb-list flex items-center">
  <li class="breadcrumb-item flex items-center"><a class="breadcrumb-item-link block px-[4px]" href="<?= site_url('/') ?>">홈</a><span><picture><img alt="" class="size-[12px]" src="/images/icons/common/ico_chevron_right_light_16.svg"></picture></span></li>
  <?php if (! empty($supplierPage)): ?><li class="breadcrumb-item flex items-center"><a class="block px-[4px]" href="<?= site_url('products') ?>">공급사</a><span><picture><img alt="" class="size-[12px]" src="/images/icons/common/ico_chevron_right_light_16.svg"></picture></span></li><li class="breadcrumb-item flex items-center"><a class="block px-[4px]"><?= esc($supplierPage['company_name']) ?></a></li>
  <?php elseif ($catRow): ?><li class="breadcrumb-item flex items-center"><a class="block px-[4px]" href="<?= site_url('products') ?>">전체상품</a><span><picture><img alt="" class="size-[12px]" src="/images/icons/common/ico_chevron_right_light_16.svg"></picture></span></li><li class="breadcrumb-item flex items-center"><a class="block px-[4px]"><?= esc($catRow['name']) ?></a></li>
  <?php else: ?><li class="breadcrumb-item flex items-center"><a class="block px-[4px]">전체상품</a></li><?php endif ?>
</ol></nav>

<?php if (! empty($supplierPage)): ?>
<section class="corner-section pc:mx-auto pc:w-large pc:mb-[40px]">
  <div class="flex items-center gap-[24px] rounded-[20px] bg-gray9 p-[28px]">
    <div class="flex size-[88px] shrink-0 items-center justify-center overflow-hidden rounded-full bg-white text-[28px] font-bold text-primary"><?php if ($supplierPage['logo']): ?><img src="<?= base_url($supplierPage['logo']) ?>" alt="" class="size-full object-cover"><?php else: ?><?= esc(mb_substr($supplierPage['company_name'], 0, 1)) ?><?php endif ?></div>
    <div><h2 class="title4 font-bold"><?= esc($supplierPage['company_name']) ?><?php if ($supplierPage['brand']): ?> <span class="ml-[6px] rounded-[99px] bg-[#fff3eb] px-[8px] py-[2px] text-[11px] font-bold text-primary align-middle"><?= esc($supplierPage['brand']) ?></span><?php endif ?></h2><p class="mt-[6px] text-body3 text-gray3"><?= esc($supplierPage['intro']) ?></p></div>
  </div>
</section>
<?php else: ?>
<section class="corner-section pc:mx-auto pc:w-large pc:mb-[40px]">
  <h2 class="title4 mo:head-line1 font-bold text-gray2 mb-[24px]"><?= esc($title) ?></h2>
  <div class="tab type04"><div class="flex flex-wrap gap-[8px]">
    <a href="<?= site_url('products') ?>" class="body3 rounded-[21px] border border-[#d5d5d5] px-3 py-2 text-gray3" data-state="<?= ! $cat ? 'active' : 'inactive' ?>"><span>전체</span></a>
    <?php foreach ($categories as $c): ?><a href="<?= site_url('category/' . $c['id']) ?>" class="body3 rounded-[21px] border border-[#d5d5d5] px-3 py-2 text-gray3" data-state="<?= $cat == $c['id'] ? 'active' : 'inactive' ?>"><span><?= esc($c['name']) ?></span></a><?php endforeach ?>
  </div></div>
</section>
<?php endif ?>

<section class="corner-section last-of-type:mb-0 pc:mx-auto pc:w-large pc:mb-[60px] !mb-0 flex justify-between">
  <!-- 필터 사이드바 (아이스크림몰 217px) -->
  <div class="mo:hidden"><div class="border-gray-300 bg-white pc:w-[217px]">
    <div class="z-10 mb-1 flex items-center justify-between bg-white"><p class="head-line1 font-bold mb-3">필터</p></div>
    <div class="text-left item border-b border-gray7 pc:pb-[16px]">
      <strong class="btn-filter-tit block justify-between pl-0 pc:py-4">결과 내 재검색</strong>
      <form class="relative" method="get"><input type="hidden" name="cat" value="<?= (int) $cat ?>"><div class="input-item"><input type="text" name="q" placeholder="검색어 입력" class="h-[44px] w-full px-3 bg-white body3 text-black placeholder:text-gray4 focus:outline-none border-gray6 focus:border-black border rounded-[8px]" value="<?= esc($q) ?>"></div><button type="submit" class="text-black h-auto my-auto absolute right-[10px] top-[10px]"><span class="blind">검색하기</span><i class="icon icon-search-24 size-6"></i></button></form>
    </div>
    <div class="cont-accordion">
      <div class="item border-b border-gray7"><h3 class="flex"><button type="button" class="accordian-trigger subtitle2 flex flex-1 items-center btn-filter-tit justify-between pl-0 pc:py-4" data-state="open">카테고리</button></h3>
        <div class="p-0 pb-4 flex flex-col gap-[10px]">
          <a href="<?= site_url('products') ?>" class="body3 <?= ! $cat ? 'font-bold text-primary' : 'text-gray3' ?>">전체</a>
          <?php foreach ($categories as $c): ?><a href="<?= site_url('category/' . $c['id']) ?>" class="body3 <?= $cat == $c['id'] ? 'font-bold text-primary' : 'text-gray3' ?>"><?= esc($c['name']) ?></a><?php endforeach ?>
        </div></div>
      <div class="item border-b border-gray7"><h3 class="flex"><button type="button" class="accordian-trigger subtitle2 flex flex-1 items-center btn-filter-tit justify-between pl-0 pc:py-4" data-state="open">공급사</button></h3>
        <div class="p-0 pb-4 flex flex-col gap-[10px]">
          <?php foreach (array_slice($suppliers, 0, 8) as $s): ?><a href="<?= site_url('suppliers/' . $s['id']) ?>" class="body3 text-gray3"><?= esc($s['company_name']) ?></a><?php endforeach ?>
          <?php if (! $suppliers): ?><span class="body3 text-gray5">-</span><?php endif ?>
        </div></div>
      <div class="item border-b border-gray7"><h3 class="flex"><button type="button" class="accordian-trigger subtitle2 flex flex-1 items-center btn-filter-tit justify-between pl-0 pc:py-4" data-state="open">도매가 안내</button></h3>
        <div class="p-0 pb-4 body3 text-gray3 leading-[1.7]"><?= $approved ? '비즈니스 인증 회원으로 도매가가 표시됩니다.' : '도매가는 비즈니스 인증 회원에게만 표시됩니다.<br><a href="' . site_url('register') . '" class="font-bold text-primary">회원가입 ›</a>' ?></div></div>
    </div>
  </div></div>

  <!-- 목록 -->
  <div class="pc:w-[989px]">
    <div class="mb-[20px] flex items-center justify-between">
      <div class="sub-head2 body3 font-regular">전체 <span class="font-bold text-primary"><?= number_format($total) ?></span>개</div>
      <div class="flex gap-[16px] body3">
        <?php foreach (['new' => '최신순', 'pop' => '인기순', 'low' => '낮은가격순', 'high' => '높은가격순'] as $k => $v): ?><a href="<?= $base ?>&sort=<?= $k ?>" class="<?= $sort === $k ? 'font-bold text-black' : 'text-gray3' ?>"><?= $v ?></a><?php endforeach ?>
      </div>
    </div>
    <?php if (! $products): ?><div class="rounded-[16px] bg-gray9 py-[90px] text-center text-body2 text-gray3">등록된 상품이 없습니다.</div><?php endif ?>
    <ul class="plist flex flex-wrap gap-x-[13px] gap-y-7 pc:gap-x-4 pc:gap-y-9">
      <?php foreach ($products as $p): echo view('_card', ['p' => $p, 'approved' => $approved, 'sup' => $suppliers[$p['supplier_id']] ?? null]); endforeach ?>
    </ul>
    <?= $pager->links('p', 'mm_pager') ?>
  </div>
</section>
