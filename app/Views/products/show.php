<?php $sym = mm_setting('currency_symbol', 'Ks'); usort($tiers, static fn ($a, $b) => $a['min_qty'] <=> $b['min_qty']); $imgs = $images ?: [$p['thumb']]; ?>
<nav class="breadcrumb m-auto w-large pb-[40px] pt-[24px]"><ol class="breadcrumb-list flex items-center">
  <li class="breadcrumb-item flex items-center"><a class="breadcrumb-item-link block px-[4px]" href="<?= site_url('/') ?>">홈</a><span><picture><img alt="" class="size-[12px]" src="/images/icons/common/ico_chevron_right_light_16.svg"></picture></span></li>
  <li class="breadcrumb-item flex items-center"><a class="block px-[4px]" href="<?= site_url('products') ?>">전체상품</a><span><picture><img alt="" class="size-[12px]" src="/images/icons/common/ico_chevron_right_light_16.svg"></picture></span></li>
  <?php if ($category): ?><li class="breadcrumb-item flex items-center"><a class="block px-[4px]" href="<?= site_url('category/' . $category['id']) ?>"><?= esc($category['name']) ?></a><span><picture><img alt="" class="size-[12px]" src="/images/icons/common/ico_chevron_right_light_16.svg"></picture></span></li><?php endif ?>
  <li class="breadcrumb-item flex items-center"><a class="block px-[4px] font-medium text-black"><?= esc(mb_strimwidth($p['name'], 0, 40, '…')) ?></a></li>
</ol></nav>

<section class="corner-section last-of-type:mb-0 pc:mx-auto pc:w-large flex justify-between pc:mb-[40px]">
  <!-- 갤러리: 세로 썸네일 + 메인 603 -->
  <div class="swiper-thumbnail-type goods-main-slide">
    <div class="swiper main-thumb" id="pdMain"><div class="swiper-wrapper">
      <?php foreach ($imgs as $im): ?><div class="swiper-slide w-full pc:w-[486px]" style="width:603px"><div class="thumb-container overflow-hidden pc:rounded-[12px]"><picture><img src="<?= mm_thumb($im) ?>" class="thumb-item"></picture></div></div><?php endforeach ?>
    </div><div class="swiper-pagination swiper-pagination-fraction swiper-pagination-horizontal" id="pdPg"><span class="swiper-pagination-current">1</span> / <span class="swiper-pagination-total"><?= count($imgs) ?></span></div></div>
    <div class="swiper sub-thumb" id="pdThumbs"><div class="swiper-wrapper">
      <?php foreach ($imgs as $i => $im): ?><div class="swiper-slide <?= $i === 0 ? 'swiper-slide-thumb-active' : '' ?>"><div class="thumb-container overflow-hidden rounded-[8px]"><picture><img src="<?= mm_thumb($im) ?>" class="thumb-item"></picture></div></div><?php endforeach ?>
    </div></div>
  </div>

  <!-- 상품 정보 480 -->
  <div class="goods-info pc:w-[480px]">
    <div class="mb-[8px] flex"><div class="flex gap-[8px]">
      <?php if ($p['brand']): ?><p class="body1 font-bold"><?= esc($p['brand']) ?></p><?php endif ?>
      <?php if ($supplier): ?><a class="body3 mr-[4px] inline-flex items-center gap-1 text-gray3 after:inline-block after:h-[12px] after:w-[6px] after:bg-[url('/_ext/cdn.i-screammall.co.kr/files/x2bee-fo-cdn/public/images/icons/common/ico_chevron_right_tight_light_16.svg')] after:bg-cover after:bg-center after:content-['']" href="<?= site_url('suppliers/' . $supplier['id']) ?>"><?= esc($supplier['company_name']) ?></a><?php endif ?>
    </div><div class="ml-auto flex items-center justify-center gap-[12px] text-label1 text-gray5">상품번호 <?= esc($p['code'] ?: $p['id']) ?></div></div>
    <div><p class="title2 mo:head-line1 mb-[8px] font-medium"><?= esc($p['name']) ?></p><p class="sub-head2 break-words text-gray3"><?= esc($p['summary']) ?></p></div>

    <!-- 가격 -->
    <div class="pt-[20px] pb-[24px]">
      <?php if ($approved): ?>
        <div class="flex items-baseline gap-[10px]"><span class="title3 mo:head-line1 font-bold"><?= mm_price($p['price'], false) ?> <span class="text-[16px] font-medium"><?= esc($sym) ?></span></span><?php if ($p['retail_price']): ?><span class="body3 text-gray5 line-through">소비자가 <?= mm_price($p['retail_price']) ?></span><?php endif ?></div>
      <?php else: ?>
        <div class="title3 mo:head-line1 font-bold">회원 혜택가</div>
        <p class="mt-[6px] body3 text-gray3">도매가는 사업자 인증 회원에게만 표시됩니다<?php if ($p['retail_price']): ?> · 소비자가 <?= mm_price($p['retail_price']) ?><?php endif ?></p>
      <?php endif ?>
    </div>

    <!-- 도매 단가표 -->
    <div class="pd-tier<?= $approved ? '' : ' is-lite' ?>">
      <?php if ($approved): ?>
        <p class="pd-tier-tit">수량별 도매가</p>
        <ul>
          <li data-min="<?= (int) $p['moq'] ?>"><span><?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?> 이상</span><b><?= mm_price($p['price'], false) ?> <?= esc($sym) ?></b></li>
          <?php foreach ($tiers as $t): ?><li data-min="<?= (int) $t['min_qty'] ?>"><span><?= number_format((int) $t['min_qty']) ?><?= esc($p['unit']) ?> 이상</span><b><?= mm_price($t['price'], false) ?> <?= esc($sym) ?></b></li><?php endforeach ?>
        </ul>
      <?php endif ?>
      <p class="pd-tier-foot"><b>최소 <?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?>부터 주문</b><span>재고 <?= number_format((int) $p['stock']) ?><?= esc($p['unit']) ?></span><?php if ($p['origin']): ?><span>원산지 <?= esc($p['origin']) ?></span><?php endif ?></p>
    </div>

    <!-- 결제·배송 -->
    <dl class="pd-info">
      <div><dt>결제</dt><dd>KBZPay 100% 선불 <span>(주문 신청 후 바로 송금)</span></dd></div>
      <div><dt>배송</dt><dd>입금 확인 후 한국에서 발송 <span>(배송비 별도 안내)</span></dd></div>
    </dl>

    <?php if ($approved): ?>
      <form method="post" action="<?= site_url('cart/add') ?>">
        <?= csrf_field() ?><input type="hidden" name="product_id" value="<?= $p['id'] ?>">
        <div class="qty" data-sym="<?= esc($sym) ?>">
          <span class="stp"><button type="button" class="minus" aria-label="빼기">−</button><input type="text" name="qty" value="<?= (int) $p['moq'] ?>" data-moq="<?= (int) $p['moq'] ?>" data-price="<?= (int) $p['price'] ?>" data-tiers="<?= esc(json_encode($tiers)) ?>" aria-label="수량"><button type="button" class="plus" aria-label="더하기">+</button></span>
          <span class="tot"><em>단가 <span id="unitPrice"></span></em><b><span id="totPrice"></span> <?= esc($sym) ?></b></span>
        </div>
        <div class="btn-area shrink-0 flex gap-[8px] pt-[16px]">
          <button type="button" data-modal="inqModal" class="inline-flex size-[50px] shrink-0 items-center justify-center rounded-full border border-gray7" title="문의"><picture><img src="/images/icons/common/ico_share_all_24.svg" alt="문의" class=""></picture></button>
          <button type="submit" name="go" value="cart" class="text-primary bg-white border border-primary rounded-[99px] min-h-[44px] px-[16px] py-[12px] body3 font-bold !leading-none w-[150px]">장바구니</button>
          <button type="submit" name="go" value="checkout" class="text-white bg-primary rounded-[99px] min-h-[44px] px-[16px] py-[12px] body3 font-bold !leading-none w-full">주문 신청하기</button>
        </div>
      </form>
    <?php elseif ($user && $user['role'] === 'buyer'): ?>
      <div class="rounded-[16px] bg-[#fff3eb] p-[24px] text-body3 leading-[1.7]">
        <b class="mb-[6px] block text-[17px] text-black"><?= $user['status'] === 'pending' ? '사업자 인증 검수 중입니다' : ($user['status'] === 'rejected' ? '인증 자료가 반려되었습니다' : '이용이 제한된 계정입니다') ?></b>
        <?= $user['status'] === 'pending' ? esc(mm_setting('verify_sla_text')) . ' 승인되면 도매가와 주문 신청이 열립니다.' : ($user['status'] === 'rejected' ? '반려 사유를 확인하고 자료를 다시 제출해 주세요.' : '고객센터로 문의해 주세요.') ?>
        <div class="mt-[14px] flex gap-[8px]"><a class="rounded-[99px] border border-gray7 bg-white px-[16px] py-[10px] body3 font-bold" href="<?= site_url('mypage/verify') ?>">인증 상태 보기</a><button type="button" class="rounded-[99px] border border-primary bg-white px-[16px] py-[10px] body3 font-bold text-primary" data-modal="inqModal">문의하기</button></div>
      </div>
    <?php else: ?>
      <div class="rounded-[16px] bg-[#fff3eb] p-[24px] text-body3 leading-[1.7]">
        <b class="mb-[6px] block text-[17px] text-black">회원 혜택가는 사업자 인증 후 열립니다</b>
        미얀마 소매상·온라인 셀러 전용 폐쇄몰입니다. 가입 후 사업자 인증(<?= esc(mm_setting('verify_sla_text')) ?>)을 받으면 도매가 확인과 주문 신청이 가능합니다.
        <div class="mt-[14px] flex gap-[8px]"><a class="rounded-[99px] bg-primary px-[20px] py-[10px] body3 font-bold text-white" href="<?= site_url('register') ?>">회원가입</a><a class="rounded-[99px] border border-gray7 bg-white px-[20px] py-[10px] body3 font-bold" href="<?= site_url('login') ?>">로그인</a></div>
      </div>
    <?php endif ?>
  </div>
</section>

<!-- 탭 (아이스크림몰 type05 알약 탭) -->
<div class="sticky z-10 mx-auto overflow-hidden bg-white pb-0 pc:top-0 pc:max-w-screen-xl pc:w-large"><div class="tab type05"><div class="tab-menu"><div class="flex gap-[8px] py-[12px]">
  <?php foreach (['desc' => '상품상세', 'sup' => '공급사 정보', 'notice' => '주문·배송 안내', 'inq' => '문의'] as $k => $l): ?><a href="#<?= $k ?>" data-tab="<?= $k ?>" class="ptab-btn body3 rounded-[21px] border border-[#d5d5d5] px-3 py-[7px] text-gray3" data-state="<?= $k === 'desc' ? 'active' : 'inactive' ?>"><span><?= $l ?></span></a><?php endforeach ?>
</div></div></div></div>

<section class="corner-section pc:mx-auto pc:w-large">
  <div class="pdesc ptab" id="desc"><?= $p['description'] ?: '<p class="text-gray3">상세 설명이 아직 등록되지 않았습니다.</p>' ?></div>
  <div id="sup" class="ptab hidden py-[40px]">
    <?php if ($supplier): ?>
    <ul class="border-t border-gray7 py-[20px] text-body3 [&>li]:flex [&>li]:items-start [&>li]:gap-[29px]">
      <li class="mb-[14px]"><span class="flex w-[74px] items-center gap-[6px] font-medium">공급사</span><div><?= esc($supplier['company_name']) ?><?php if ($supplier['brand']): ?> (<?= esc($supplier['brand']) ?>)<?php endif ?></div></li>
      <li class="mb-[14px]"><span class="flex w-[74px] items-center gap-[6px] text-gray3">소개</span><div><?= esc($supplier['intro']) ?></div></li>
      <li class="mb-[14px]"><span class="flex w-[74px] items-center gap-[6px] text-gray3">소재지</span><div><?= esc($supplier['address']) ?></div></li>
    </ul>
    <a class="inline-flex rounded-[99px] border border-gray7 px-[20px] py-[10px] body3 font-bold" href="<?= site_url('suppliers/' . $supplier['id']) ?>">이 공급사 상품 전체보기</a>
    <?php else: ?><p class="text-gray3">공급사 정보가 없습니다.</p><?php endif ?>
  </div>
  <div id="notice" class="ptab hidden py-[40px]">
    <div class="item border-b border-gray7"><div class="accordian-trigger subtitle2 flex flex-1 items-center py-[20px] font-medium">주문 절차</div><div class="pb-[20px] text-body3 text-gray3 leading-[1.8]">① 장바구니 → 주문 신청(배송지 입력) ② 담당자가 상품 금액 + 배송비를 합산한 최종 금액을 안내 ③ KBZPay 송금 후 마이페이지에서 송금 캡처 업로드 ④ 입금 확인 즉시 한국 공급사 발주 ⑤ 미얀마 도착 후 배송</div></div>
    <div class="item border-b border-gray7"><div class="accordian-trigger subtitle2 flex flex-1 items-center py-[20px] font-medium">포인트</div><div class="pb-[20px] text-body3 text-gray3 leading-[1.8]">주문 신청 시 상품 금액의 <?= (int) mm_setting('points_use_max_pct', 30) ?>%까지 사용할 수 있습니다. 주문을 취소하면 사용한 포인트는 환원됩니다.</div></div>
  </div>
  <div id="inq" class="ptab hidden py-[40px]">
    <?php if ($user && $user['role'] === 'buyer'): ?><button type="button" class="rounded-[99px] bg-primary px-[20px] py-[10px] body3 font-bold text-white" data-modal="inqModal">이 상품 문의하기</button><?php else: ?><p class="text-gray3">상품 문의는 로그인 후 가능합니다. <a href="<?= site_url('login') ?>" class="font-bold text-primary">로그인</a></p><?php endif ?>
  </div>
</section>

<?php if ($related): ?>
<section class="corner-section last-of-type:mb-0 pc:mx-auto pc:w-large pc:mb-[80px] pc:mt-[80px]">
  <div class="section-title pc:heading2 mb-2.5 title1 font-bold pc:mb-[40px]"><div class="flex items-center justify-between"><h2 class="title1 mo:head-line1 font-bold">같은 카테고리 <br>다른 상품도 함께</h2><?php if ($category): ?><a href="<?= site_url('category/' . $category['id']) ?>" class="h-auto my-auto body2 btn-arrow-right after-bg-gray5 text-gray3"><?= esc($category['name']) ?> 더보기</a><?php endif ?></div></div>
  <ul class="flex flex-wrap gap-x-[27px] gap-y-9"><?php foreach ($related as $r): echo view('_card', ['p' => $r, 'approved' => $approved, 'sup' => null, 'w' => 'w-[234px]']); endforeach ?></ul>
</section>
<?php endif ?>

<?php if ($user && $user['role'] === 'buyer'): ?>
<div class="modal" id="inqModal"><div class="in">
  <h3 class="font-bold">상품 문의</h3>
  <form method="post" action="<?= site_url('inquiry') ?>"><?= csrf_field() ?><input type="hidden" name="product_id" value="<?= $p['id'] ?>">
    <div class="field"><label>상품</label><div class="small"><?= esc($p['name']) ?></div></div>
    <div class="field"><label>문의 내용</label><textarea class="ta" name="body" placeholder="수량, 납기, 샘플, 배송 등 궁금한 점을 적어 주세요."></textarea></div>
    <div class="mmrow"><button class="btn btn-g" type="submit">문의 보내기</button><button class="btn close" type="button">닫기</button></div>
  </form>
</div></div>
<?php endif ?>
