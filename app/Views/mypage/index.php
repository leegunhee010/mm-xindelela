<div class="flex gap-[60px] items-start">
<?= view('mypage/_side') ?>
<div class="flex-1 min-w-0">
  <div class="contents-title-area depth-1 title3 font-bold !pt-0 mb-[20px]"><div class="title-area !pt-0"><h3 class="title">마이페이지</h3></div></div>
  <?php if ($user['status'] !== 'approved'): ?>
  <div class="rounded-[16px] bg-[#fff3eb] p-[28px] mb-[24px]">
    <p class="sub-head1 font-bold"><?= $user['status'] === 'pending' ? '비즈니스 인증 검수 중' : ($user['status'] === 'rejected' ? '인증 자료 반려' : '계정 정지') ?></p>
    <p class="body3 text-gray3 mt-[6px] leading-[1.7]"><?= $user['status'] === 'pending' ? '제출하신 자료를 담당자가 확인하고 있습니다. ' . esc(mm_setting('verify_sla_text')) . ' 승인되며, 승인 후 도매가와 주문 신청이 열립니다.' : ($user['status'] === 'rejected' ? '반려 사유: ' . esc($user['reject_reason'] ?: '-') : '고객센터로 문의해 주세요.') ?></p>
    <?php if ($user['status'] !== 'suspended'): ?><a class="mt-[14px] inline-flex text-black bg-white border border-gray6 min-h-[40px] px-[16px] py-[10px] body3 font-bold !leading-none rounded-full items-center" href="<?= site_url('mypage/verify') ?>">인증 자료 보기·재제출</a><?php endif ?>
  </div>
  <?php endif ?>
  <div class="grid grid-cols-6 gap-[8px] mb-[28px]">
    <?php foreach (mm_order_flow() as $k => $l): ?>
      <div class="rounded-[12px] bg-gray9 py-[18px] text-center"><b class="block title3 <?= $counts[$k] ? 'text-primary' : '' ?>"><?= $counts[$k] ?></b><span class="label1 text-gray3"><?= $l ?></span></div>
    <?php endforeach ?>
  </div>
  <div class="flex items-center justify-between border-b border-black pb-[12px] mb-[4px]"><p class="sub-head1 font-bold">최근 주문</p><a href="<?= site_url('mypage/orders') ?>" class="body3 btn-arrow-right after-bg-gray5 text-gray3">전체보기</a></div>
  <?php if (! $recent): ?><div class="rounded-[12px] bg-gray9 py-[50px] text-center body3 text-gray3 mt-[12px]">주문 내역이 없습니다.</div><?php else: ?>
  <table class="tbl"><thead><tr><th>주문번호</th><th>신청일</th><th>금액</th><th>결제</th><th>상태</th></tr></thead><tbody>
  <?php foreach ($recent as $o): ?><tr><td><a href="<?= site_url('mypage/orders/' . $o['id']) ?>" class="font-bold"><?= esc($o['order_no']) ?></a></td><td><?= mm_dt($o['created_at']) ?></td><td><?= mm_price($o['total']) ?></td><td><?= mm_payment_label($o['payment_method']) ?></td><td><span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></td></tr><?php endforeach ?>
  </tbody></table><?php endif ?>
  <div class="flex items-center justify-between border-b border-black pb-[12px] mb-[12px] mt-[40px]"><p class="sub-head1 font-bold">친구 소개</p><a href="<?= site_url('mypage/referral') ?>" class="body3 btn-arrow-right after-bg-gray5 text-gray3">자세히</a></div>
  <div class="rounded-[12px] bg-gray9 p-[16px] body3">내 추천 코드 <b class="text-primary sub-head1 tracking-[2px] ml-[6px]"><?= esc($user['referral_code']) ?></b> <span class="text-gray3 ml-[12px]">소개한 회원 <b class="text-black"><?= $refCount ?></b>명</span></div>
</div>
</div>
