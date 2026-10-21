<?php /* 가입 랜딩 (광고·영업 링크용). 슈퍼딜리버리 해외 가입 랜딩 구성 참고: 혜택 → 소개 → 가입 대상 → 절차 → 상품 → FAQ → 혜택 다시 */
$approved = mm_is_approved();
$isBuyer  = $user && $user['role'] === 'buyer';
$sla      = mm_setting('verify_sla_text');
$cur      = mm_setting('currency_symbol', 'Ks');
$refSign  = (int) mm_setting('points_referral_signup', 0);
$refFirst = (int) mm_setting('points_referral_first_order', 0);
$usePct   = (int) mm_setting('points_use_max_pct', 30);
$sm = new \App\Models\SupplierModel(); $sups = [];
$cta = function (string $cls = '') use ($isBuyer, $approved, $joinUrl) {
    if ($approved) {
        return '<a class="lp-btn ' . $cls . '" href="' . site_url('products') . '">도매가 보러 가기</a>';
    }
    if ($isBuyer) {
        return '<a class="lp-btn ' . $cls . '" href="' . site_url('mypage/verify') . '">내 인증 상태 보기</a>';
    }
    return '<a class="lp-btn ' . $cls . '" href="' . $joinUrl . '">무료 회원가입</a>';
};
?>
<link rel="stylesheet" href="<?= base_url('assets/css/welcome.css?v=1') ?>">
<div class="lp">

<!-- 1. 가입 혜택 -->
<section class="lp-hero">
  <div class="lp-in lp-hero-in">
    <div class="lp-hero-txt">
      <p class="lp-eyebrow">XINDELELA 미얀마 도매몰</p>
      <h1>한국 브랜드 상품을<br><em>도매가</em>로 받아 보세요</h1>
      <p class="lp-lead">미얀마 소매상과 온라인 셀러를 위한 회원제 도매몰입니다. 상품은 누구나 볼 수 있고, 도매가와 주문 신청은 비즈니스 인증을 받은 회원에게 열립니다.</p>
      <div class="lp-hero-cta"><?= $cta() ?><?php if (! $user): ?><a class="lp-btn lp-btn-line" href="<?= site_url('login') ?>">로그인</a><?php endif ?></div>
      <?php if ($ref !== ''): ?><p class="lp-refnote">추천 코드 <b><?= esc($ref) ?></b> 가 가입 화면에 자동으로 입력됩니다.</p><?php endif ?>
    </div>
    <?php if ($bonus > 0): ?>
    <div class="lp-coupon">
      <p class="lp-coupon-k">가입 축하 포인트</p>
      <p class="lp-coupon-v"><?= number_format($bonus) ?><small>P</small></p>
      <p class="lp-coupon-d">비즈니스 인증이 승인되면 바로 적립됩니다.<br>1P = 1 <?= esc($cur) ?>, 첫 주문부터 사용할 수 있습니다.</p>
    </div>
    <?php endif ?>
  </div>
</section>

<!-- 2. 어떤 곳인가 -->
<section class="lp-sec">
  <div class="lp-in">
    <h2 class="lp-h2">XINDELELA 도매몰은<br>이런 곳입니다</h2>
    <div class="lp-facts">
      <div class="lp-fact"><b>한국 K-브랜드</b><p><?= esc(implode(', ', array_map(static fn ($c) => str_replace('·', ' · ', $c['name']), $categories))) ?> 상품을 한곳에서 고릅니다.</p></div>
      <?php if ($offHi > 0): ?><div class="lp-fact"><b>소비자가보다 <?= $offLo === $offHi ? $offHi : $offLo . '~' . $offHi ?>% 낮은 도매가</b><p>인증 회원에게만 공개되는 가격입니다.</p></div><?php endif ?>
      <div class="lp-fact"><b>수량별 단가</b><p>상품마다 정해진 최소수량부터 1개씩 담을 수 있고, 많이 담을수록 단가가 낮아집니다.</p></div>
      <div class="lp-fact"><b>KBZPay 선불</b><p>주문을 신청하고 KBZPay로 송금하면, 입금 확인 후 바로 출고합니다.</p></div>
    </div>
  </div>
</section>

<!-- 3. 가입할 수 있는 분 -->
<section class="lp-sec lp-gray">
  <div class="lp-in">
    <h2 class="lp-h2">물건을 파는 분이라면<br>누구나 가입할 수 있습니다</h2>
    <p class="lp-sub">아래 세 가지 중 <b>하나만</b> 증빙하면 됩니다. 휴대폰 캡처나 사진도 괜찮습니다.</p>
    <div class="lp-types">
      <?php foreach (mm_verify_types() as $k => $t): ?>
      <div class="lp-type">
        <span class="lp-type-ic lp-ic-<?= $k ?>"></span>
        <b><?= esc($t['label']) ?></b>
        <p class="lp-type-d"><?= esc($t['desc']) ?></p>
        <ul><?php foreach ($t['docs'] as $d): ?><li><?= esc($d) ?></li><?php endforeach ?></ul>
      </div>
      <?php endforeach ?>
    </div>
  </div>
</section>

<!-- 4. 이용 절차 -->
<section class="lp-sec">
  <div class="lp-in">
    <h2 class="lp-h2">가입부터 배송까지</h2>
    <ol class="lp-steps">
      <?php foreach ([['무료 회원가입', '상호, 연락처, 지역을 적고 증빙 자료를 올립니다.'], ['비즈니스 인증', '담당자가 직접 확인합니다. ' . $sla . '.' . ($bonus > 0 ? ' 승인되면 ' . number_format($bonus) . 'P가 적립됩니다.' : '')], ['도매가 확인 · 주문 신청', '도매가와 수량별 단가를 보고 장바구니에 담아 신청합니다.'], ['KBZPay 송금', '안내된 금액을 송금하고 송금자 이름으로 입금 확인을 요청합니다.'], ['출고 · 배송', '입금이 확인되면 바로 출고합니다.']] as $i => [$t, $d]): ?>
      <li><span><?= $i + 1 ?></span><b><?= esc($t) ?></b><p><?= esc($d) ?></p></li>
      <?php endforeach ?>
    </ol>
  </div>
</section>

<!-- 5. 상품 미리보기 -->
<?php if ($products): ?>
<section class="lp-sec lp-gray">
  <div class="lp-in">
    <div class="lp-h2row"><h2 class="lp-h2">지금 판매 중인 상품</h2><a class="lp-more" href="<?= site_url('products') ?>">전체 상품 보기</a></div>
    <ul class="plist flex flex-wrap gap-x-[27px] gap-y-9">
      <?php foreach ($products as $p): $sup = $sups[$p['supplier_id']] ??= $sm->find($p['supplier_id']); echo view('_card', ['p' => $p, 'approved' => $approved, 'sup' => $sup, 'w' => 'w-[234px]']); endforeach ?>
    </ul>
  </div>
</section>
<?php endif ?>

<!-- 6. 친구 소개 -->
<?php if ($refSign > 0 || $refFirst > 0): ?>
<section class="lp-sec">
  <div class="lp-in">
    <div class="lp-refbox">
      <h2>가입 후 친구를 소개하면<br>포인트가 더 쌓입니다</h2>
      <div class="lp-refrow">
        <?php if ($refSign > 0): ?><div><small>친구가 인증을 통과하면</small><b><?= number_format($refSign) ?>P</b></div><?php endif ?>
        <?php if ($refFirst > 0): ?><div><small>친구가 첫 주문 입금을 마치면</small><b><?= number_format($refFirst) ?>P</b></div><?php endif ?>
      </div>
      <p>포인트는 주문할 때 상품 금액의 <?= $usePct ?>%까지 사용할 수 있습니다.</p>
    </div>
  </div>
</section>
<?php endif ?>

<!-- 7. FAQ -->
<section class="lp-sec lp-gray">
  <div class="lp-in lp-narrow">
    <h2 class="lp-h2">자주 묻는 질문</h2>
    <div class="lp-faq">
      <?php foreach ([
          ['누구나 회원이 될 수 있나요?', '오프라인 매장, 온라인 판매, 판매 네트워크 중 하나라도 운영하고 있다면 가입할 수 있습니다. 개인 사용 목적의 구매는 승인되지 않습니다.'],
          ['인증은 얼마나 걸리나요?', '담당자가 제출 자료를 직접 확인하며 ' . $sla . '합니다. 자료가 부족하면 이유를 알려 드리고, 마이페이지에서 자료를 추가해 다시 제출할 수 있습니다.'],
          ['가입 축하 포인트는 언제 받나요?', $bonus > 0 ? '비즈니스 인증이 승인되는 순간 ' . number_format($bonus) . 'P가 적립됩니다. 1P는 1 ' . $cur . '이며, 주문할 때 상품 금액의 ' . $usePct . '%까지 사용할 수 있습니다.' : '현재 진행 중인 가입 혜택이 없습니다.'],
          ['몇 개부터 주문할 수 있나요?', '상품마다 최소주문수량이 정해져 있고, 그 수량부터 1개씩 늘릴 수 있습니다. 수량이 많을수록 낮은 단가가 자동으로 적용됩니다.'],
          ['결제는 어떻게 하나요?', '주문을 신청하면 결제 금액과 KBZPay 계좌가 안내됩니다. 송금한 뒤 주문 상세에서 송금자 이름을 적고 입금 확인을 요청하면 됩니다.'],
          ['가입비가 있나요?', '가입과 인증은 무료입니다.'],
      ] as [$q, $a]): ?>
      <details><summary><?= esc($q) ?></summary><p><?= esc($a) ?></p></details>
      <?php endforeach ?>
    </div>
    <p class="lp-cs">더 궁금한 점은 Viber <?= esc(mm_setting('cs_viber', '')) ?> 로 문의해 주세요. (월~금 09:00~18:00)</p>
  </div>
</section>

<!-- 8. 혜택 다시 + 가입 -->
<section class="lp-end">
  <div class="lp-in">
    <?php if ($bonus > 0 && ! $approved): ?><p class="lp-end-k">지금 가입하고 인증받으면</p><p class="lp-end-v"><?= number_format($bonus) ?>P 적립</p><?php else: ?><p class="lp-end-v">한국 브랜드 도매가, 지금 확인하세요</p><?php endif ?>
    <?= $cta('lp-btn-white') ?>
  </div>
</section>

</div>
<?php if (! $approved): ?><div class="lp-sticky"><?= $cta() ?></div><?php endif ?>
