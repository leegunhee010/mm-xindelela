<?php $inp = 'h-[44px] w-full px-3 bg-white body3 text-black placeholder:text-gray4 focus:outline-none border-gray6 focus:border-black border rounded-[8px]'; ?>
<div class="contents-wrap member pc:pt-[44px]">
<form method="post" action="<?= site_url('supplier/register') ?>"><?= csrf_field() ?>
<section class="corner-section last-of-type:mb-0 pc:mx-auto pc:w-[544px] mx-auto pc:mb-[60px]">
  <div class="mb-[40px]">
    <div class="contents-title-area depth-1 title3 mb-[28px] font-bold"><div class="title-area"><h3 class="title"><span class="text-primary">공급사 입점</span>을 <br>신청해 주세요.</h3></div></div>
    <p class="body3 text-gray4">한국 브랜드·제조사·유통사가 미얀마 소매상에게 도매로 공급합니다. 담당자 승인 후 공급사센터에서 상품을 직접 등록할 수 있습니다.</p>
  </div>
  <div class="mb-[40px]">
    <strong class="sub-head1 font-bold">계정</strong><hr class="divider bg-gray6 border-0 h-px my-[18px]">
    <div class="flex flex-col gap-y-[14px]">
      <div class="grid grid-cols-2 gap-[10px]">
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">이메일 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="email" name="email" value="<?= esc(old('email')) ?>" required></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">담당자 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" name="contact_name" value="<?= esc(old('contact_name')) ?>" required></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">비밀번호 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="password" name="password" required></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">비밀번호 확인 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="password" name="password2" required></div></label>
      </div>
    </div>
  </div>
  <div class="mb-[40px]">
    <strong class="sub-head1 font-bold">회사 정보</strong><hr class="divider bg-gray6 border-0 h-px my-[18px]">
    <div class="flex flex-col gap-y-[14px]">
      <div class="grid grid-cols-2 gap-[10px]">
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">회사명 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" name="company_name" value="<?= esc(old('company_name')) ?>" required></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">브랜드</span><div class="input-item"><input class="<?= $inp ?>" name="brand" value="<?= esc(old('brand')) ?>"></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">연락처 <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" name="phone" value="<?= esc(old('phone')) ?>" required></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block">사업자등록번호</span><div class="input-item"><input class="<?= $inp ?>" name="biz_no" value="<?= esc(old('biz_no')) ?>"></div></label>
      </div>
      <label class="block"><span class="label1 text-gray3 mb-[6px] block">주소</span><div class="input-item"><input class="<?= $inp ?>" name="address" value="<?= esc(old('address')) ?>"></div></label>
      <label class="block"><span class="label1 text-gray3 mb-[6px] block">회사·상품 소개</span><textarea name="intro" class="w-full px-3 py-[10px] bg-white body3 text-black placeholder:text-gray4 focus:outline-none border-gray6 focus:border-black border rounded-[8px] h-[110px]"><?= esc(old('intro')) ?></textarea></label>
    </div>
  </div>
  <div class="btn-area shrink-0 flex btn-grid02 sub-head2 mt-[16px] gap-[8px]">
    <a href="<?= site_url('supplier/login') ?>" class="text-black bg-white border border-gray6 min-h-[44px] px-[16px] py-[12px] body3 !leading-none w-full rounded-full font-bold text-center">이전</a>
    <button type="submit" class="text-white bg-black min-h-[44px] px-[16px] py-[12px] body3 !leading-none w-full rounded-full font-bold">입점 신청</button>
  </div>
</section>
</form>
</div>
