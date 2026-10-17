<?php /* 아이스크림몰 /member/join/step2 마크업(contents-title-area + 체크박스 + input-item + btn-grid02) */
$inp = 'h-[44px] w-full px-3 bg-white body3 text-black placeholder:text-gray4 focus:outline-none border-gray6 focus:border-black border rounded-[8px]'; ?>
<div class="contents-wrap member pc:pt-[44px]">
<form method="post" action="<?= site_url('register') ?>" enctype="multipart/form-data">
<?= csrf_field() ?>
<section class="corner-section last-of-type:mb-0 pc:mx-auto pc:w-[544px] mx-auto pc:mb-[60px]">
  <div class="mb-[40px]">
    <div class="contents-title-area depth-1 title3 mb-[28px] font-bold"><div class="title-area"><h3 class="title"><span class="text-primary">회원가입</span>과 <br>비즈니스 인증을 신청해 주세요.</h3></div></div>
    <p class="body3 text-gray4">가입 즉시 상품을 볼 수 있고, 담당자가 제출 자료를 확인한 뒤 <?= esc(mm_setting('verify_sla_text')) ?> 승인되면 도매가와 주문 신청이 열립니다.</p>
  </div>

  <div class="mb-[40px]">
    <strong class="sub-head1 font-bold">계정 정보</strong>
    <hr class="divider bg-gray6 border-0 h-px my-[18px]">
    <div class="flex flex-col gap-y-[14px]">
      <label class="block"><span class="label1 text-gray3 mb-[6px] block">이메일 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="email" name="email" value="<?= esc(old('email')) ?>" required></div></label>
      <label class="block"><span class="label1 text-gray3 mb-[6px] block">담당자 이름 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="text" name="name" value="<?= esc(old('name')) ?>" required></div></label>
      <div class="grid grid-cols-2 gap-[10px]">
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">비밀번호 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="password" name="password" required placeholder="6자 이상"></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">비밀번호 확인 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="password" name="password2" required></div></label>
      </div>
      <div class="grid grid-cols-2 gap-[10px]">
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">전화번호 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="text" name="phone" value="<?= esc(old('phone')) ?>" placeholder="09-XXX-XXX-XXX" required></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">Viber 번호</span><div class="input-item"><input class="<?= $inp ?>" type="text" name="viber" value="<?= esc(old('viber')) ?>" placeholder="전화번호와 같으면 비움"></div></label>
      </div>
    </div>
  </div>

  <div class="mb-[40px]">
    <strong class="sub-head1 font-bold">사업 정보</strong>
    <hr class="divider bg-gray6 border-0 h-px my-[18px]">
    <div class="flex flex-col gap-y-[14px]">
      <label class="block"><span class="label1 text-gray3 mb-[6px] block">상호 (가게·페이지 이름) <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="text" name="company_name" value="<?= esc(old('company_name')) ?>" required></div></label>
      <div class="grid grid-cols-2 gap-[10px]">
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">지역(도시) <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="text" name="city" value="<?= esc(old('city')) ?>" placeholder="Yangon, Mandalay ..." required></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">주소</span><div class="input-item"><input class="<?= $inp ?>" type="text" name="address" value="<?= esc(old('address')) ?>"></div></label>
      </div>
    </div>
  </div>

  <div class="mb-[40px]">
    <strong class="sub-head1 font-bold">비즈니스 인증 <i class="text-primary not-italic">*</i></strong>
    <hr class="divider bg-gray6 border-0 h-px my-[18px]">
    <?= view('_verify_fields', ['sel' => (array) old('verify_types', []), 'link' => (string) old('fb_page')]) ?>
  </div>

  <div class="mb-[40px]">
    <strong class="sub-head1 font-bold">추천 코드 <span class="label1 font-regular text-gray4">(있으면 입력)</span></strong>
    <hr class="divider bg-gray6 border-0 h-px my-[18px]">
    <div class="input-item"><input class="<?= $inp ?> uppercase" type="text" name="ref" value="<?= esc(old('ref', $ref)) ?>" placeholder="예: AB12CD34"></div>
    <p class="mt-[6px] label1 text-gray4">내가 승인되면 소개한 회원에게 포인트가 적립됩니다.</p>
  </div>

  <div class="mb-[50px]">
    <div><input class="mm-chk size-5 inline-block cursor-pointer align-middle border border-gray6 rounded-[6px] bg-white shrink-0" type="checkbox" id="agree-all" required><label class="text-black ml-2 sub-head1 font-bold" for="agree-all">전체 동의</label></div>
    <hr class="divider bg-gray6 border-0 h-px my-[18px]">
    <div class="flex flex-col gap-y-[20px]">
      <div class="flex items-center"><input class="mm-chk size-5 inline-block cursor-pointer align-middle border border-gray6 rounded-[6px] bg-white" type="checkbox" id="agree-1" required><label class="ml-2 sub-head2 text-gray3" for="agree-1">[필수] 이용약관 · 회원제 도매몰 운영 규정</label><a href="<?= site_url('guide') ?>" class="btn-arrow-right after-bg-gray4 ml-auto body3 font-bold" title="상세보기"></a></div>
      <div class="flex items-center"><input class="mm-chk size-5 inline-block cursor-pointer align-middle border border-gray6 rounded-[6px] bg-white" type="checkbox" id="agree-2" required><label class="ml-2 sub-head2 text-gray3" for="agree-2">[필수] 개인정보 · 제출 자료 수집 및 이용</label></div>
    </div>
  </div>

  <div class="btn-area shrink-0 flex btn-grid02 sub-head2 mt-[16px] gap-[8px]">
    <a href="<?= site_url('login') ?>" class="text-black bg-white border border-gray6 min-h-[44px] px-[16px] py-[12px] body3 !leading-none w-full rounded-full font-bold text-center">이전</a>
    <button type="submit" class="text-white bg-primary min-h-[44px] px-[16px] py-[12px] body3 !leading-none w-full rounded-full font-bold">가입하고 인증 신청</button>
  </div>
</section>
</form>
</div>
