<?php /* 아이스크림몰 카테고리 필터 사이드바(217px, btn-filter-tit) 문법 */ $cur = uri_string(); ?>
<aside class="w-[217px] shrink-0">
  <div class="rounded-[12px] bg-gray9 p-[20px] mb-[8px]">
    <p class="sub-head1 font-bold"><?= esc($user['company_name'] ?: $user['name']) ?></p>
    <span class="st st-<?= esc($user['status']) ?> mt-[6px] inline-block"><?= mm_verify_status_label($user['status']) ?></span>
    <p class="label1 text-gray3 mt-[10px]">포인트 <b class="text-primary body2"><?= number_format((int) $user['points_balance']) ?> P</b></p>
  </div>
  <div class="border-t border-black">
    <?php foreach ([['마이페이지 홈', 'mypage'], ['주문 내역', 'mypage/orders'], ['비즈니스 인증', 'mypage/verify'], ['포인트', 'mypage/points'], ['친구 소개', 'mypage/referral'], ['문의 내역', 'mypage/inquiries'], ['회원 정보', 'mypage/profile']] as [$l, $u]): $on = $cur === $u || ($u !== 'mypage' && str_starts_with($cur, $u)); ?>
      <a href="<?= site_url($u) ?>" class="btn-filter-tit flex justify-between items-center border-b border-gray7 py-[14px] body2 <?= $on ? 'font-bold text-primary' : 'text-black' ?>"><?= $l ?><?php if ($on): ?><i class="icon icon-arrow-right-tight-16 h-[14px] w-[7px]"></i><?php endif ?></a>
    <?php endforeach ?>
  </div>
</aside>
