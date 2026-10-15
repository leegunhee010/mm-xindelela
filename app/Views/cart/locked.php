<div class="crumb"><a href="<?= site_url('/') ?>">홈</a>장바구니</div>
<div class="empty">
  <?php if ($user['status'] === 'pending'): ?>
    <b style="font-size:15px;color:var(--ink)">사업자 인증 검수 중입니다</b><br><br><?= esc(mm_setting('verify_sla_text')) ?> 승인되면 도매가 확인과 장바구니·주문 신청이 열립니다.
  <?php elseif ($user['status'] === 'rejected'): ?>
    <b style="font-size:15px;color:var(--ink)">인증 자료가 반려되었습니다</b><br><br>반려 사유: <?= esc($user['reject_reason'] ?: '-') ?><br><br><a class="btn btn-g" href="<?= site_url('mypage/verify') ?>">자료 다시 제출</a>
  <?php else: ?>
    이용이 제한된 계정입니다. 고객센터로 문의해 주세요.
  <?php endif ?>
</div>
