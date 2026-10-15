<?php $cur = uri_string(); ?>
<aside class="side">
  <div class="me"><b><?= esc($user['company_name'] ?: $user['name']) ?></b><span class="st st-<?= esc($user['status']) ?>"><?= mm_status_label($user['status']) ?></span>
    <div class="pt">포인트 <b><?= number_format((int) $user['points_balance']) ?> P</b></div></div>
  <ul>
    <?php foreach (['mypage' => '마이페이지 홈', 'mypage/orders' => '주문 내역', 'mypage/verify' => '사업자 인증', 'mypage/points' => '포인트', 'mypage/referral' => '친구 소개', 'mypage/inquiries' => '문의 내역', 'mypage/profile' => '회원 정보'] as $u => $l): ?>
      <a href="<?= site_url($u) ?>" class="<?= $cur === $u || ($u !== 'mypage' && str_starts_with($cur, $u)) ? 'on' : '' ?>"><?= $l ?></a>
    <?php endforeach ?>
  </ul>
</aside>
