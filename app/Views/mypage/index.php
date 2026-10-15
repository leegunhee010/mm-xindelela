<div class="my">
<?= view('mypage/_side') ?>
<div class="main">
  <h1>마이페이지</h1>
  <?php if ($user['status'] !== 'approved'): ?>
  <div class="vstat">
    <h2><?= $user['status'] === 'pending' ? '사업자 인증 검수 중' : ($user['status'] === 'rejected' ? '인증 자료 반려' : '계정 정지') ?></h2>
    <p><?= $user['status'] === 'pending' ? '제출하신 자료를 담당자가 확인하고 있습니다. ' . esc(mm_setting('verify_sla_text')) . ' 승인되며, 승인 후 도매가와 주문 신청이 열립니다.' : ($user['status'] === 'rejected' ? '반려 사유: ' . esc($user['reject_reason'] ?: '-') : '고객센터로 문의해 주세요.') ?></p>
    <?php if ($user['status'] !== 'suspended'): ?><a class="btn mt8" href="<?= site_url('mypage/verify') ?>">인증 자료 보기·재제출</a><?php endif ?>
  </div>
  <?php endif ?>
  <div class="kpis">
    <?php foreach (['requested' => '신청 접수', 'quoted' => '입금 대기', 'proof' => '입금 확인중', 'paid' => '입금 확인', 'preparing' => '발주·준비', 'shipped' => '배송중', 'delivered' => '배송 완료'] as $k => $l): ?>
      <div><b class="<?= $counts[$k] ? 'on' : '' ?>"><?= $counts[$k] ?></b><?= $l ?></div>
    <?php endforeach ?>
  </div>
  <div class="sec-h"><h2 style="font-size:15px">최근 주문</h2><a href="<?= site_url('mypage/orders') ?>">전체보기 ></a></div>
  <?php if (! $recent): ?><div class="empty">주문 내역이 없습니다.</div><?php else: ?>
  <table class="tbl"><thead><tr><th>주문번호</th><th>신청일</th><th>금액</th><th>결제</th><th>상태</th></tr></thead><tbody>
  <?php foreach ($recent as $o): ?><tr><td><a href="<?= site_url('mypage/orders/' . $o['id']) ?>" style="font-weight:700"><?= esc($o['order_no']) ?></a></td><td><?= mm_dt($o['created_at']) ?></td><td><?= mm_price($o['total']) ?></td><td><?= mm_payment_label($o['payment_method']) ?></td><td><span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></td></tr><?php endforeach ?>
  </tbody></table><?php endif ?>
  <div class="sec-h mt40"><h2 style="font-size:15px">친구 소개</h2><a href="<?= site_url('mypage/referral') ?>">자세히 ></a></div>
  <div class="box-note">내 추천 코드 <b style="color:var(--green-d);font-size:15px;letter-spacing:2px"><?= esc($user['referral_code']) ?></b> · 소개한 회원 <b><?= $refCount ?></b>명</div>
</div>
</div>
