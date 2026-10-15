<div class="contents-title-area depth-1 title3 font-bold mb-[20px]"><div class="title-area"><h3 class="title">장바구니</h3></div></div>
<div class="flex flex-col items-center justify-center rounded-[16px] bg-[#fff3eb] py-[70px] px-[40px] text-center">
  <?php if ($user['status'] === 'pending'): ?>
    <p class="sub-head1 font-bold">사업자 인증 검수 중입니다</p><p class="body3 text-gray3 mt-[8px]"><?= esc(mm_setting('verify_sla_text')) ?> 승인되면 도매가 확인과 장바구니·주문 신청이 열립니다.</p>
  <?php elseif ($user['status'] === 'rejected'): ?>
    <p class="sub-head1 font-bold">인증 자료가 반려되었습니다</p><p class="body3 text-gray3 mt-[8px]">반려 사유: <?= esc($user['reject_reason'] ?: '-') ?></p><a class="mt-[16px] text-white bg-primary min-h-[44px] px-[24px] py-[12px] body3 !leading-none rounded-full font-bold inline-flex items-center" href="<?= site_url('mypage/verify') ?>">자료 다시 제출</a>
  <?php else: ?><p class="sub-head1 font-bold">이용이 제한된 계정입니다.</p><p class="body3 text-gray3 mt-[8px]">고객센터로 문의해 주세요.</p><?php endif ?>
</div>
