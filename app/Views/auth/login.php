<?php /* 아이스크림몰 /member/mypage 로그인 게이트 마크업(tab type02 + 아바타 + 버튼 2개 + 링크행)에 우리 폼을 넣음 */ ?>
<div class="contents-wrap member login pc:pt-[44px]">
<section class="corner-section last-of-type:mb-0 pc:mx-auto pc:w-[544px] mx-auto pc:mb-[60px]">
  <div class="tab type02"><div class="flex tab-menu">
    <a href="<?= site_url('login') ?>" class="flex" data-state="active"><span>구매자 회원</span></a>
    <a href="<?= site_url('supplier/login') ?>" class="flex" data-state="inactive"><span>공급사 회원</span></a>
  </div>
  <div class="tab-cont mb-[39px] pt-[39px]">
    <picture><img alt="구매자 회원" class="mx-auto mb-[39px] size-[100px]" src="/images/member/unified_member.svg"></picture>
    <div class="text-center"><h4 class="headline2 mb-[4px] font-bold">구매자 회원</h4><p class="body3 font-medium text-gray4">미얀마 소매상 · 온라인 셀러 · 매장 운영자</p></div>
    <form method="post" action="<?= site_url('login') ?>" class="mt-[32px]"><?= csrf_field() ?>
      <div class="flex flex-col gap-[10px]">
        <div class="input-item"><input type="email" name="email" value="<?= esc(old('email')) ?>" placeholder="이메일" required autofocus class="h-[44px] w-full px-3 bg-white body3 text-black placeholder:text-gray4 focus:outline-none border-gray6 focus:border-black border rounded-[8px]"></div>
        <div class="input-item"><input type="password" name="password" placeholder="비밀번호" required class="h-[44px] w-full px-3 bg-white body3 text-black placeholder:text-gray4 focus:outline-none border-gray6 focus:border-black border rounded-[8px]"></div>
      </div>
      <div class="btn-area shrink-0 flex btn-grid01 body3 flex-col gap-[10px] mt-[24px]">
        <button type="submit" class="text-white bg-primary min-h-[44px] px-[16px] py-[12px] body3 !leading-none w-full rounded-full font-bold">로그인</button>
        <a href="<?= site_url('register') ?>" class="text-black bg-white border border-gray6 min-h-[44px] px-[16px] py-[12px] body3 font-bold !leading-none w-full rounded-full text-center">회원가입 · 사업자 인증 신청</a>
      </div>
    </form>
    <div class="btn-area shrink-0 label1 mt-[16px] flex items-center justify-center gap-[10px]">
      <a href="<?= site_url('mypage/inquiries') ?>" class="text-gray3">비밀번호 문의</a><span class="h-[10px] w-[1px] bg-gray6"></span><a href="<?= site_url('guide') ?>" class="text-primary">이용안내</a>
    </div>
    <p class="mt-[30px] rounded-[8px] bg-gray9 p-[14px] text-label1 text-gray3">데모 계정: buyer@demo.mm / buyer1234 (승인됨) · pending@demo.mm / buyer1234 (검수 대기)</p>
  </div></div>
</section>
</div>
