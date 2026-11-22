<?php /* 아이스크림몰 /member/join/step2 마크업(contents-title-area + 체크박스 + input-item + btn-grid02) */
$inp = 'h-[44px] w-full px-3 bg-white body3 text-black placeholder:text-gray4 focus:outline-none border-gray6 focus:border-black border rounded-[8px]'; ?>
<div class="contents-wrap member pc:pt-[44px]">
<form method="post" action="<?= site_url('register') ?>" enctype="multipart/form-data">
<?= csrf_field() ?>
<section class="corner-section last-of-type:mb-0 pc:mx-auto pc:w-[544px] mx-auto pc:mb-[60px]">
  <div class="mb-[40px]">
    <div class="contents-title-area depth-1 title3 mb-[28px] font-bold"><div class="title-area"><h3 class="title"><span class="text-primary"><?= t('회원가입') ?></span><?= t('과') ?> <br><?= t('비즈니스 인증을 신청해 주세요.') ?></h3></div></div>
    <p class="body3 text-gray4"><?= t('가입 즉시 상품을 볼 수 있고, 담당자가 제출 자료를 확인한 뒤') ?> <?= esc(t(mm_setting('verify_sla_text'))) ?> <?= t('승인되면 도매가와 주문 신청이 열립니다.') ?></p>
    <?php if (($bonus = (int) mm_setting('points_signup_bonus', 0)) > 0): ?><p class="mt-[14px] rounded-[12px] bg-[#fff3eb] px-[16px] py-[12px] body3 text-black"><?= t('인증이 승인되면') ?> <b class="text-primary"><?= t('가입 축하') ?> <?= number_format($bonus) ?>P</b><?= t('가 적립됩니다. (1P = 1') ?> <?= esc(mm_setting('currency_symbol', 'Ks')) ?>)</p><?php endif ?>
  </div>

  <div class="mb-[40px]">
    <strong class="sub-head1 font-bold"><?= t('계정 정보') ?></strong>
    <hr class="divider bg-gray6 border-0 h-px my-[18px]">
    <div class="flex flex-col gap-y-[14px]">
      <label class="block"><span class="label1 text-gray3 mb-[6px] block"><?= t('이메일') ?> <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="email" name="email" value="<?= esc(old('email', (string) service('request')->getGet('email'))) ?>" required></div></label>
      <label class="block"><span class="label1 text-gray3 mb-[6px] block"><?= t('담당자 이름') ?> <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="text" name="name" value="<?= esc(old('name')) ?>" required></div></label>
      <div class="grid grid-cols-2 gap-[10px]">
        <label class="block"><span class="label1 text-gray3 mb-[6px] block"><?= t('비밀번호') ?> <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="password" name="password" required placeholder="<?= t('6자 이상') ?>"></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block"><?= t('비밀번호 확인') ?> <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="password" name="password2" required></div></label>
      </div>
      <div class="grid grid-cols-2 gap-[10px]">
        <label class="block"><span class="label1 text-gray3 mb-[6px] block"><?= t('전화번호') ?> <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="text" name="phone" value="<?= esc(old('phone')) ?>" placeholder="09-XXX-XXX-XXX" required></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block"><?= t('Viber 번호') ?></span><div class="input-item"><input class="<?= $inp ?>" type="text" name="viber" value="<?= esc(old('viber')) ?>" placeholder="<?= t('전화번호와 같으면 비움') ?>"></div></label>
      </div>
    </div>
  </div>

  <div class="mb-[40px]">
    <strong class="sub-head1 font-bold"><?= t('사업 정보') ?></strong>
    <hr class="divider bg-gray6 border-0 h-px my-[18px]">
    <div class="flex flex-col gap-y-[14px]">
      <label class="block"><span class="label1 text-gray3 mb-[6px] block"><?= t('상호 (가게·페이지 이름)') ?> <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="text" name="company_name" value="<?= esc(old('company_name')) ?>" required></div></label>
      <div class="grid grid-cols-2 gap-[10px]">
        <label class="block"><span class="label1 text-gray3 mb-[6px] block"><?= t('지역(도시)') ?> <i class="text-primary not-italic">*</i></span><div class="input-item"><input class="<?= $inp ?>" type="text" name="city" value="<?= esc(old('city')) ?>" placeholder="Yangon, Mandalay ..." required></div></label>
        <label class="block"><span class="label1 text-gray3 mb-[6px] block"><?= t('주소') ?></span><div class="input-item"><input class="<?= $inp ?>" type="text" name="address" value="<?= esc(old('address')) ?>"></div></label>
      </div>
    </div>
  </div>

  <div class="mb-[40px]">
    <strong class="sub-head1 font-bold"><?= t('비즈니스 인증') ?> <i class="text-primary not-italic">*</i></strong>
    <hr class="divider bg-gray6 border-0 h-px my-[18px]">
    <?= view('_verify_fields', ['sel' => (array) old('verify_types', []), 'link' => (string) old('fb_page')]) ?>
  </div>


  <div class="btn-area shrink-0 flex btn-grid02 sub-head2 mt-[16px] gap-[8px]">
    <a href="<?= site_url('login') ?>" class="text-black bg-white border border-gray6 min-h-[44px] px-[16px] py-[12px] body3 !leading-none w-full rounded-full font-bold text-center"><?= t('이전') ?></a>
    <button type="submit" class="text-white bg-primary min-h-[44px] px-[16px] py-[12px] body3 !leading-none w-full rounded-full font-bold"><?= t('가입하고 인증 신청') ?></button>
  </div>
</section>
</form>
</div>
