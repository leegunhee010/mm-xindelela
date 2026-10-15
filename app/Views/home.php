<?php $approved = mm_is_approved(); $sm = new \App\Models\SupplierModel(); $sups = []; $n = max(1, count($banners)); ?>
<!-- 히어로: 아이스크림몰 corner-main-visual (가운데 630px 카드 + 양옆 peek) -->
<section class="corner-section last-of-type:mb-0 pc:mx-auto w-full !px-0 corner-main-visual relative mx-auto box-content overflow-hidden pc:!mb-[60px] pc:w-full pc:pb-[60px] pc:pt-[8px]">
  <div class="swiper w-full relative pc:h-[416px]" id="heroSwiper"><div class="swiper-wrapper">
    <?php foreach ($banners as $b): $dark = ! $b['image']; ?>
    <div class="swiper-slide" style="width:630px;margin-right:20px">
      <a class="relative flex pc:w-[630px] justify-start items-end pc:items-center h-full overflow-hidden rounded-[20px] bg-gray9" href="<?= site_url($b['link'] ?: 'products') ?>" style="background:<?= esc($b['bg'] ?: '#f7f7f7') ?>">
        <?php if ($b['image']): ?><picture><img alt="" class="w-full h-full object-cover" src="<?= base_url($b['image']) ?>"></picture><?php else: ?><div class="w-full h-[416px]"></div><?php endif ?>
        <div class="absolute size-full inset-0 flex flex-col justify-end pb-[50px] pl-[50px] tracking-[-0.075em] <?= $dark ? 'text-black' : 'text-white' ?>">
          <?php if ($b['subtitle']): ?><div class="relative inline-block w-fit max-w-full mb-1"><div class="pointer-events-none absolute inset-0 rounded-3xl bg-black/20 mix-blend-soft-light" aria-hidden="true"></div><div class="relative z-[1] px-[14px] py-[6px] pc:body2 font-bold opacity-80"><?= esc($b['subtitle']) ?></div></div><?php endif ?>
          <div class="pc:display1 mb-[6px] font-bold whitespace-pre-wrap"><?= esc($b['title']) ?></div>
          <div class="pc:sub-head3 opacity-80 font-medium whitespace-pre-wrap">자세히 보기 →</div>
        </div>
      </a>
    </div>
    <?php endforeach ?>
    <?php if (! $banners): ?><div class="swiper-slide" style="width:630px"><a class="relative flex pc:w-[630px] items-center h-full overflow-hidden rounded-[20px] bg-gray9" href="<?= site_url('products') ?>"><div class="w-full h-[416px]"></div><div class="absolute inset-0 flex items-end pb-[50px] pl-[50px] pc:display1 font-bold">XINDELELA 미얀마 도매몰</div></a></div><?php endif ?>
  </div></div>
  <div class="main-swiper-control"><div class="swiper-button-prev mo:hidden" id="heroPrev"><span class="blind">이전 슬라이드</span></div><div class="main-swiper-pagination swiper-pagination-fraction swiper-pagination-horizontal" id="heroPg"><span class="swiper-pagination-current">1</span> / <span class="swiper-pagination-total"><?= $n ?></span></div><div class="swiper-button-next mo:hidden" id="heroNext"><span class="blind">다음 슬라이드</span></div><button type="button" class="btn-play-control" id="heroPlay"><picture><img alt="Pause" class="" src="/images/icons/common/ico_stop_12.svg"></picture></button></div>
</section>

<!-- 인증·포인트·고객센터 상태 띠 (아이스크림몰 서비스 스트립 자리) -->
<section class="corner-section pc:mx-auto pc:w-large pc:mb-[80px]">
  <div class="flex gap-[16px]">
    <?php if ($user && $user['role'] === 'buyer'): ?>
      <div class="flex flex-1 items-center justify-between rounded-[16px] border border-gray6 px-[26px] py-[22px]"><div class="text-body3 text-gray3">내 인증 상태<b class="mt-[2px] block text-[20px] text-black"><?= mm_status_label($user['status']) ?></b></div><?php if ($user['status'] === 'approved'): ?><span class="rounded-[99px] bg-[#e6f6d6] px-[10px] py-[3px] text-[12px] font-bold text-[#2f6300]">도매가 열람중</span><?php elseif ($user['status'] === 'rejected'): ?><a class="rounded-[99px] bg-primary px-[16px] py-[8px] text-body3 font-bold text-white" href="<?= site_url('mypage/verify') ?>">자료 다시 제출</a><?php else: ?><span class="text-label1 text-gray3"><?= esc(mm_setting('verify_sla_text')) ?></span><?php endif ?></div>
      <div class="flex flex-1 items-center justify-between rounded-[16px] bg-primary px-[26px] py-[22px] text-white"><div class="text-body3 opacity-90">보유 포인트<b class="mt-[2px] block text-[20px]"><?= number_format((int) $user['points_balance']) ?> P</b></div><a class="rounded-[99px] bg-white px-[16px] py-[8px] text-body3 font-bold text-primary" href="<?= site_url('mypage/referral') ?>">친구 소개하기</a></div>
    <?php else: ?>
      <div class="flex flex-1 items-center justify-between rounded-[16px] border border-gray6 px-[26px] py-[22px]"><div class="text-body3 text-gray3">도매가 열람·주문은<b class="mt-[2px] block text-[20px] text-black">사업자 인증 회원만</b></div><span class="text-label1 text-gray3"><?= esc(mm_setting('verify_sla_text')) ?></span></div>
      <div class="flex flex-1 items-center justify-between rounded-[16px] bg-primary px-[26px] py-[22px] text-white"><div class="text-body3 opacity-90">신규가입<b class="mt-[2px] block text-[20px]">페이스북 페이지로 간단 인증</b></div><a class="rounded-[99px] bg-white px-[16px] py-[8px] text-body3 font-bold text-primary" href="<?= site_url('register') ?>">회원가입</a></div>
    <?php endif ?>
    <div class="flex flex-1 items-center justify-between rounded-[16px] border border-gray6 px-[26px] py-[22px]"><div class="text-body3 text-gray3">고객센터 (Viber)<b class="mt-[2px] block text-[20px] text-black"><?= esc(mm_setting('cs_viber', '')) ?></b></div><span class="text-label1 text-gray3">평일 09:00~18:00</span></div>
  </div>
</section>

<!-- 신상품 (MD가 추천하는 섹션 마크업) -->
<section class="corner-section last-of-type:mb-0 pc:mx-auto pc:w-large pc:mb-[80px]">
  <div class="section-title pc:heading2 mb-2.5 title1 font-bold pc:mb-[40px]"><div class="flex items-center justify-between"><h2 class="title1 mo:head-line1 font-bold">인증 회원에게만 열리는 <br>도매가, 이번 주 신상품</h2>
    <div class="btn-area shrink-0 flex"><a href="<?= site_url('products?sort=new') ?>" class="btn-more mr-[44px] px-[20px] py-[10px] body3 font-bold text-white bg-primary rounded-[99px] relative after:block after:absolute after:inset-y-0 after:right-[-5px] after:m-auto after:translate-y-[1px] after:bg-[url('/_ext/cdn.i-screammall.co.kr/files/x2bee-fo-cdn/public/images/common/btn_Subtract.svg')] after:w-[6px] after:h-[12px] after:bg-no-repeat before:block before:size-[40px] before:bg-primary before:absolute before:inset-y-0 before:right-[-44px] before:rounded-[50%] before:bg-[url('/_ext/cdn.i-screammall.co.kr/files/x2bee-fo-cdn/public/images/common/ico_white_arrow.svg')] before:bg-[length:10px_12px] before:bg-center before:bg-no-repeat">신상품 더보기</a></div>
  </div></div>
  <div class="tab type04 mb-6"><div class="flex flex-wrap gap-[8px]">
    <a href="<?= site_url('products') ?>" class="body3 rounded-[21px] border border-[#d5d5d5] px-3 py-[7px] text-gray3" data-state="active"><span>전체</span></a>
    <?php foreach ($categories as $c): ?><a href="<?= site_url('category/' . $c['id']) ?>" class="body3 rounded-[21px] border border-[#d5d5d5] px-3 py-[7px] text-gray3" data-state="inactive"><span><?= esc($c['name']) ?></span></a><?php endforeach ?>
  </div></div>
  <ul class="flex flex-wrap gap-x-[27px] gap-y-9">
    <?php foreach ($newest as $p): $sup = $sups[$p['supplier_id']] ??= $sm->find($p['supplier_id']); echo view('_card', ['p' => $p, 'approved' => $approved, 'sup' => $sup, 'w' => 'w-[234px]']); endforeach ?>
  </ul>
</section>

<!-- 이용 절차 (서비스 스트립 bg 활용) -->
<section class="corner-section last-of-type:mb-0 pc:mx-auto pc:w-large pc:mb-[80px]">
  <div class="section-title pc:heading2 mb-2.5 title1 font-bold pc:mb-[40px]"><div class="flex items-center justify-between"><h2 class="title1 mo:head-line1 font-bold">가입부터 배송까지 <br>이렇게 이용하세요</h2><a href="<?= site_url('guide') ?>" class="h-auto my-auto body2 btn-arrow-right after-bg-gray5 text-gray3">이용안내 전체보기</a></div></div>
  <div class="grid grid-cols-5 gap-[16px]">
    <?php foreach ([['회원가입', '페이스북 페이지·매장 사진 등 자료 제출'], ['사업자 인증', '담당자 확인 후 영업일 1일 이내 승인'], ['도매가 확인·주문 신청', '박스 단위 수량별 단가 자동 적용'], ['KBZPay 선불 송금', '최종 금액 안내 후 송금·캡처 업로드'], ['발주·배송', '입금 확인 즉시 한국 공급사 발주']] as $i => [$t, $d]): ?>
    <div class="rounded-[16px] bg-gray9 px-[22px] py-[24px]"><span class="inline-flex size-[28px] items-center justify-center rounded-full bg-primary text-[13px] font-bold text-white"><?= $i + 1 ?></span><b class="mt-[12px] mb-[6px] block text-[17px] font-bold"><?= $t ?></b><p class="text-body3 text-gray3 leading-[1.6]"><?= $d ?></p></div>
    <?php endforeach ?>
  </div>
</section>

<?php if ($best): ?>
<section class="corner-section last-of-type:mb-0 pc:mx-auto pc:w-large pc:mb-[80px]">
  <div class="section-title pc:heading2 mb-2.5 title1 font-bold pc:mb-[40px]"><div class="flex items-center justify-between"><h2 class="title1 mo:head-line1 font-bold">미얀마 셀러들이 <br>가장 많이 주문한 베스트</h2>
    <div class="btn-area shrink-0 flex"><a href="<?= site_url('products?sort=pop') ?>" class="btn-more mr-[44px] px-[20px] py-[10px] body3 font-bold text-white bg-primary rounded-[99px] relative after:block after:absolute after:inset-y-0 after:right-[-5px] after:m-auto after:translate-y-[1px] after:bg-[url('/_ext/cdn.i-screammall.co.kr/files/x2bee-fo-cdn/public/images/common/btn_Subtract.svg')] after:w-[6px] after:h-[12px] after:bg-no-repeat before:block before:size-[40px] before:bg-primary before:absolute before:inset-y-0 before:right-[-44px] before:rounded-[50%] before:bg-[url('/_ext/cdn.i-screammall.co.kr/files/x2bee-fo-cdn/public/images/common/ico_white_arrow.svg')] before:bg-[length:10px_12px] before:bg-center before:bg-no-repeat">베스트 더보기</a></div>
  </div></div>
  <ul class="flex flex-wrap gap-x-[27px] gap-y-9">
    <?php foreach ($best as $p): $sup = $sups[$p['supplier_id']] ??= $sm->find($p['supplier_id']); echo view('_card', ['p' => $p, 'approved' => $approved, 'sup' => $sup, 'w' => 'w-[234px]']); endforeach ?>
  </ul>
</section>
<?php endif ?>

<!-- 공지 + 친구소개 (아이스크림몰 sec11 공지 마크업) -->
<section class="corner-section last-of-type:mb-0 pc:mx-auto pc:w-large pc:mb-[80px] !mb-0">
  <div class="flex gap-[48px]">
    <div class="w-[616px]">
      <div class="flex items-center justify-between border-gray6 pc:border-b pc:pb-[26px]"><p class="mo:head-line1 text-title3 font-bold text-gray2 pc:leading-[1em]">공지사항</p><a href="<?= site_url('notices') ?>" class="h-auto my-auto body2 btn-arrow-right after-bg-gray5 text-gray3">전체보기</a></div>
      <ul class="flex flex-col pc:gap-4 pc:pt-[26px]">
        <?php foreach ($notices as $nt): ?><li><a class="body2 flex justify-between border-gray6 pc:gap-[38px]" href="<?= site_url('notices/' . $nt['id']) ?>"><p class="ellipsis-1 w-[480px]"><?php if ($nt['is_pinned']): ?><span class="mr-[8px] rounded-[99px] bg-primary px-[8px] py-[1px] text-[11px] font-bold text-white align-[1px]">공지</span><?php endif ?><?= esc($nt['title']) ?></p><p class="text-gray3"><?= mm_dt($nt['created_at'], 'Y.m.d') ?></p></a></li><?php endforeach ?>
      </ul>
    </div>
    <div class="flex-1 rounded-[20px] bg-primary px-[40px] py-[36px] text-white">
      <p class="text-[26px] font-bold leading-[1.3] tracking-[-0.02em]">친구를 소개하면<br>포인트가 두 번 쌓입니다</p>
      <p class="mt-[12px] text-[15px] leading-[1.7] opacity-90">소개한 회원이 사업자 인증을 통과하면 <b><?= number_format((int) mm_setting('points_referral_signup', 0)) ?>P</b>, 첫 주문 입금을 완료하면 <b><?= number_format((int) mm_setting('points_referral_first_order', 0)) ?>P</b> 추가 적립. 주문 시 상품 금액의 <?= (int) mm_setting('points_use_max_pct', 30) ?>%까지 사용할 수 있습니다.</p>
      <a class="mt-[22px] inline-flex rounded-[99px] bg-white px-[20px] py-[10px] text-body3 font-bold text-primary" href="<?= site_url('mypage/referral') ?>">내 추천 코드 보기</a>
    </div>
  </div>
</section>
