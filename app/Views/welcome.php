<?php /* 가입 랜딩 (광고·영업 링크용). 슈퍼딜리버리 해외 가입 랜딩(superdelivery.com/p/entry/mail.do) 섹션 구성·문법을 오렌지 톤으로 옮김.
   히어로 사진은 임시 데모 사진. 실제 상품 사진이 오면 $heroPics 교체. */
$approved = mm_is_approved();
$isBuyer  = $user && $user['role'] === 'buyer';
$sla      = mm_setting('verify_sla_text');
$cur      = mm_setting('currency_symbol', 'Ks');
$refSign  = (int) mm_setting('points_referral_signup', 0);
$refFirst = (int) mm_setting('points_referral_first_order', 0);
$usePct   = (int) mm_setting('points_use_max_pct', 30);
$LP       = mm_welcome(); // 관리자 › 가입 랜딩 에서 편집
$heroPics = array_values($LP['pics']);
$picAt    = static fn (int $i) => $heroPics ? base_url($heroPics[$i % count($heroPics)]) : '';
$regUrl   = site_url('register');

// 업종 일러스트 (120×100, 브랜드 팔레트)
$O = '#ff6200'; $P = '#ffd9bf'; $N = '#1f2a44'; $Y = '#ffc93c'; $M = '#bfe6d6'; $W = '#fff';
$types = [
    ['화장품·잡화 가게', "<rect x='18' y='38' width='84' height='50' rx='3' fill='$W' stroke='$N' stroke-width='3'/><path d='M14 38 22 18h76l8 20z' fill='$O' stroke='$N' stroke-width='3' stroke-linejoin='round'/><path d='M35 18l-5 20M52 18l-2 20M68 18l2 20M85 18l5 20' stroke='$N' stroke-width='2.5'/><rect x='28' y='50' width='30' height='22' fill='$M' stroke='$N' stroke-width='3'/><rect x='68' y='50' width='22' height='38' fill='$Y' stroke='$N' stroke-width='3'/><circle cx='84' cy='70' r='2' fill='$N'/>"],
    ['미니마트·식료품점', "<path d='M22 40h76l-8 44H30z' fill='$Y' stroke='$N' stroke-width='3' stroke-linejoin='round'/><path d='M40 40 52 18M80 40 68 18' stroke='$N' stroke-width='3' stroke-linecap='round'/><rect x='34' y='26' width='14' height='18' rx='2' fill='$O' stroke='$N' stroke-width='2.5'/><circle cx='64' cy='34' r='9' fill='$M' stroke='$N' stroke-width='2.5'/><rect x='70' y='22' width='10' height='22' rx='3' fill='$W' stroke='$N' stroke-width='2.5'/><path d='M42 56v18M60 56v18M78 56v18' stroke='$N' stroke-width='2.5' stroke-linecap='round'/>"],
    ['페이스북 페이지 판매', "<rect x='38' y='10' width='44' height='80' rx='7' fill='$W' stroke='$N' stroke-width='3'/><rect x='44' y='20' width='32' height='26' rx='3' fill='$P'/><path d='M60 41v-9a4 4 0 0 1 4-4h3M56 34h10' stroke='$O' stroke-width='3' stroke-linecap='round'/><path d='M46 54h28M46 62h20' stroke='$N' stroke-width='2.5' stroke-linecap='round'/><circle cx='92' cy='30' r='13' fill='$O' stroke='$N' stroke-width='3'/><path d='M92 37s-6-3.6-6-7.6a3.4 3.4 0 0 1 6-2.2 3.4 3.4 0 0 1 6 2.2c0 4-6 7.6-6 7.6z' fill='$W'/><rect x='14' y='56' width='26' height='22' rx='4' fill='$Y' stroke='$N' stroke-width='3'/><path d='M20 56v-5a7 7 0 0 1 14 0v5' fill='none' stroke='$N' stroke-width='3'/>"],
    ['틱톡·라이브 판매', "<rect x='38' y='10' width='44' height='80' rx='7' fill='$N' stroke='$N' stroke-width='3'/><rect x='43' y='18' width='34' height='60' rx='3' fill='$M'/><circle cx='60' cy='40' r='9' fill='$W' stroke='$N' stroke-width='2.5'/><path d='M48 70a12 12 0 0 1 24 0' fill='$O' stroke='$N' stroke-width='2.5'/><rect x='46' y='22' width='16' height='8' rx='2' fill='$O'/><text x='54' y='28.5' font-size='6' font-weight='700' text-anchor='middle' fill='$W'>LIVE</text><path d='M90 22l6-6M94 34h8M90 46l6 6' stroke='$O' stroke-width='3' stroke-linecap='round'/><rect x='14' y='50' width='20' height='28' rx='3' fill='$Y' stroke='$N' stroke-width='3'/><rect x='19' y='42' width='10' height='9' rx='2' fill='$W' stroke='$N' stroke-width='2.5'/>"],
    ['온라인 쇼핑몰', "<rect x='12' y='14' width='96' height='60' rx='5' fill='$W' stroke='$N' stroke-width='3'/><path d='M50 74v12M70 74v12M40 88h40' stroke='$N' stroke-width='3' stroke-linecap='round'/><path d='M34 32h8l6 22h26l6-16H46' fill='none' stroke='$N' stroke-width='3' stroke-linejoin='round'/><rect x='52' y='26' width='10' height='12' rx='2' fill='$O' stroke='$N' stroke-width='2'/><rect x='64' y='30' width='10' height='8' rx='2' fill='$Y' stroke='$N' stroke-width='2'/><circle cx='52' cy='62' r='3.5' fill='$N'/><circle cx='70' cy='62' r='3.5' fill='$N'/><path d='M88 26l4 4M94 22l-2 6' stroke='$O' stroke-width='3' stroke-linecap='round'/>"],
    ['Viber·Telegram 그룹 판매', "<path d='M14 24a8 8 0 0 1 8-8h44a8 8 0 0 1 8 8v24a8 8 0 0 1-8 8H36l-12 10V56h-2a8 8 0 0 1-8-8z' fill='$O' stroke='$N' stroke-width='3' stroke-linejoin='round'/><path d='M26 30h36M26 42h24' stroke='$W' stroke-width='3' stroke-linecap='round'/><path d='M50 54a8 8 0 0 1 8-8h40a8 8 0 0 1 8 8v20a8 8 0 0 1-8 8h-2v10L84 82H58a8 8 0 0 1-8-8z' fill='$M' stroke='$N' stroke-width='3' stroke-linejoin='round'/><circle cx='66' cy='64' r='3.5' fill='$N'/><circle cx='78' cy='64' r='3.5' fill='$N'/><circle cx='90' cy='64' r='3.5' fill='$N'/>"],
    ['도매·납품 (거래처 공급)', "<rect x='10' y='34' width='60' height='40' rx='3' fill='$O' stroke='$N' stroke-width='3'/><path d='M70 46h20l14 14v14H70z' fill='$Y' stroke='$N' stroke-width='3' stroke-linejoin='round'/><path d='M78 50h10l8 10H78z' fill='$W' stroke='$N' stroke-width='2.5' stroke-linejoin='round'/><circle cx='30' cy='78' r='8' fill='$W' stroke='$N' stroke-width='3'/><circle cx='86' cy='78' r='8' fill='$W' stroke='$N' stroke-width='3'/><rect x='22' y='18' width='22' height='16' fill='$P' stroke='$N' stroke-width='3'/><rect x='44' y='22' width='18' height='12' fill='$M' stroke='$N' stroke-width='3'/><path d='M33 18v16' stroke='$N' stroke-width='2'/>"],
    ['뷰티숍·미용실', "<ellipse cx='42' cy='42' rx='22' ry='28' fill='$M' stroke='$N' stroke-width='3'/><ellipse cx='42' cy='42' rx='14' ry='20' fill='$W'/><path d='M42 70v16M30 88h24' stroke='$N' stroke-width='3' stroke-linecap='round'/><rect x='72' y='38' width='16' height='48' rx='4' fill='$O' stroke='$N' stroke-width='3'/><rect x='75' y='26' width='10' height='13' rx='2' fill='$N'/><rect x='92' y='54' width='14' height='32' rx='4' fill='$Y' stroke='$N' stroke-width='3'/><path d='M96 54v-8h6v8' fill='$W' stroke='$N' stroke-width='2.5'/><path d='M36 30l6 6' stroke='$P' stroke-width='4' stroke-linecap='round'/>"],
];
// 도매몰 소개 4개 동그라미 아이콘
$whatIc = [
    "<path d='M18 34 34 18h14v14L32 48z' fill='$W' stroke='$N' stroke-width='3' stroke-linejoin='round'/><circle cx='41' cy='25' r='3' fill='$N'/><text x='30' y='38' font-size='10' font-weight='800' fill='$O' text-anchor='middle' transform='rotate(-45 30 34)'>%</text>",
    "<rect x='14' y='24' width='36' height='26' rx='3' fill='$W' stroke='$N' stroke-width='3'/><path d='M14 32h36' stroke='$N' stroke-width='3'/><path d='M24 24v-6a8 8 0 0 1 16 0v6' fill='none' stroke='$N' stroke-width='3'/><circle cx='32' cy='41' r='4' fill='$O'/>",
    "<rect x='14' y='30' width='12' height='20' rx='2' fill='$W' stroke='$N' stroke-width='3'/><rect x='28' y='22' width='12' height='28' rx='2' fill='$Y' stroke='$N' stroke-width='3'/><rect x='42' y='14' width='10' height='36' rx='2' fill='$O' stroke='$N' stroke-width='3'/>",
    "<rect x='18' y='12' width='28' height='42' rx='5' fill='$W' stroke='$N' stroke-width='3'/><path d='M26 34l5 5 9-10' fill='none' stroke='$O' stroke-width='4' stroke-linecap='round' stroke-linejoin='round'/><path d='M28 18h8' stroke='$N' stroke-width='2.5' stroke-linecap='round'/>",
];
// 거래 흐름 동그라미 아이콘
$stepIc = [
    "<rect x='22' y='14' width='36' height='48' rx='4' fill='$W' stroke='$N' stroke-width='3'/><path d='M30 28h20M30 38h20M30 48h12' stroke='$N' stroke-width='3' stroke-linecap='round'/><path d='M44 52l12-12 6 6-12 12h-6z' fill='$Y' stroke='$N' stroke-width='3' stroke-linejoin='round'/>",
    "<path d='M40 12l22 8v16c0 14-10 22-22 28-12-6-22-14-22-28V20z' fill='$W' stroke='$N' stroke-width='3' stroke-linejoin='round'/><path d='M30 38l7 7 14-15' fill='none' stroke='$O' stroke-width='4.5' stroke-linecap='round' stroke-linejoin='round'/>",
    "<path d='M14 20h8l7 28h28l7-20H26' fill='none' stroke='$N' stroke-width='3.5' stroke-linejoin='round'/><rect x='34' y='14' width='12' height='14' rx='2' fill='$O' stroke='$N' stroke-width='2.5'/><rect x='48' y='20' width='10' height='8' rx='2' fill='$Y' stroke='$N' stroke-width='2.5'/><circle cx='32' cy='58' r='4' fill='$N'/><circle cx='54' cy='58' r='4' fill='$N'/>",
    "<rect x='24' y='10' width='32' height='56' rx='6' fill='$W' stroke='$N' stroke-width='3'/><rect x='30' y='20' width='20' height='14' rx='2' fill='$O'/><text x='40' y='30' font-size='8' font-weight='800' text-anchor='middle' fill='$W'>KBZ</text><path d='M31 44h18M31 52h10' stroke='$N' stroke-width='3' stroke-linecap='round'/>",
    "<rect x='8' y='24' width='38' height='28' rx='3' fill='$O' stroke='$N' stroke-width='3'/><path d='M46 32h14l10 10v10H46z' fill='$Y' stroke='$N' stroke-width='3' stroke-linejoin='round'/><circle cx='22' cy='56' r='6' fill='$W' stroke='$N' stroke-width='3'/><circle cx='58' cy='56' r='6' fill='$W' stroke='$N' stroke-width='3'/>",
];
$svg = static fn (string $inner, string $vb = '0 0 120 100') => "<svg viewBox='$vb' xmlns='http://www.w3.org/2000/svg' aria-hidden='true'>$inner</svg>";

// 가입 카드 (히어로·마무리 공용)
$joinCard = function (string $id) use ($approved, $isBuyer, $bonus, $cur, $ref, $regUrl, $user, $sla) {
    ob_start(); ?>
    <div class="lp-card">
      <div class="lp-card-h">가입 혜택</div>
      <div class="lp-card-b">
        <?php if ($approved): ?>
          <p class="lp-card-t">인증이 끝난 회원입니다</p>
          <div class="lp-card-row"><a class="lp-go" href="<?= site_url('products') ?>">도매가 보러 가기</a></div>
        <?php elseif ($isBuyer): ?>
          <p class="lp-card-t">비즈니스 인증 <mark><?= $user['status'] === 'rejected' ? '자료 보완 필요' : '검수 중' ?></mark></p>
          <div class="lp-card-row"><a class="lp-go" href="<?= site_url('mypage/verify') ?>">내 인증 상태 보기</a></div>
        <?php else: ?>
          <p class="lp-card-t"><?php if ($bonus > 0): ?>가입 축하 <mark><?= number_format($bonus) ?>P</mark> 적립<?php else: ?><mark>무료</mark> 회원가입<?php endif ?></p>
          <form class="lp-card-row" method="get" action="<?= $regUrl ?>">
            <label class="blind" for="<?= $id ?>">이메일 주소</label>
            <input id="<?= $id ?>" type="email" name="email" placeholder="이메일 주소 입력" autocomplete="email">
            <?php if ($ref !== ''): ?><input type="hidden" name="ref" value="<?= esc($ref) ?>"><?php endif ?>
            <button type="submit">이메일로 가입 시작</button>
          </form>
        <?php endif ?>
        <div class="lp-card-notes">
          <div><span class="lp-card-ic lp-ic-doc"></span><p><b>비즈니스 인증이 필요합니다</b>매장, 온라인 판매, 판매망 중 하나만 증빙하면 됩니다. 휴대폰 캡처도 괜찮습니다.</p></div>
          <div><span class="lp-card-ic lp-ic-pay"></span><p><b>KBZPay 선불 결제입니다</b><?php if ($bonus > 0): ?>포인트는 인증 승인 때 적립되고, 1P = 1 <?= esc($cur) ?>로 첫 주문부터 씁니다.<?php else: ?>입금이 확인되면 바로 출고합니다.<?php endif ?></p></div>
        </div>
        <?php if (! $user): ?><p class="lp-card-f">이미 회원이신 분은 <a href="<?= site_url('login') ?>">로그인</a><?php if ($ref !== ''): ?><span>추천 코드 <b><?= esc($ref) ?></b> 가 가입 화면에 자동으로 입력됩니다</span><?php endif ?></p><?php endif ?>
      </div>
    </div>
    <?php return ob_get_clean();
};
?>
<link rel="stylesheet" href="<?= base_url('assets/css/welcome.css?v=5') ?>">
<div class="lp">

<!-- 1. 히어로 -->
<section class="lp-hero">
  <div class="lp-hero-pics" aria-hidden="true">
    <?php for ($i = 0; $i < 8 && $heroPics; $i++): ?><span class="lp-hp lp-hp<?= $i + 1 ?>"><img src="<?= $picAt($i) ?>" alt=""></span><?php endfor ?>
  </div>
  <div class="lp-hero-in">
    <?php if ($LP['tag'] !== ''): ?><p class="lp-hero-tag"><?= esc($LP['tag']) ?></p><?php endif ?>
    <h1><?= esc($LP['title1']) ?><br><em><?= esc($LP['title2']) ?></em></h1>
    <p class="lp-hero-sub"><?= esc($LP['sub']) ?></p>
  </div>
  <div class="lp-wave" aria-hidden="true"></div>
</section>
<div class="lp-cardwrap"><?= $joinCard('lpEmail1') ?></div>

<!-- 2. 도매몰 소개 -->
<section class="lp-sec">
  <div class="lp-in">
    <h2 class="lp-big">XINDELELA 도매몰이란?</h2>
    <ul class="lp-what">
      <?php foreach ([
          [$offHi > 0 ? '소비자가보다<br><b>' . ($offLo === $offHi ? $offHi : $offLo . '~' . $offHi) . '% 낮은 도매가</b>' : '인증 회원에게만<br><b>도매가 공개</b>', $whatIc[0]],
          ['<b>한국 K-브랜드</b><br>' . esc(implode(' · ', array_map(static fn ($c) => explode('·', $c['name'])[0], $categories))), $whatIc[1]],
          ['상품마다 정한 최소수량부터<br><b>1개씩 늘려 주문</b>', $whatIc[2]],
          ['KBZPay 선불 송금<br><b>입금 확인 후 바로 출고</b>', $whatIc[3]],
      ] as [$t, $ic]): ?>
      <li><span class="lp-what-ic"><?= $svg($ic, '0 0 64 64') ?></span><p><?= $t ?></p></li>
      <?php endforeach ?>
    </ul>
    <div class="lp-center"><a class="lp-pill" href="<?= site_url('products') ?>">도매몰 둘러보기</a></div>
  </div>
</section>

<!-- 3. 이용 대상 -->
<section class="lp-sec lp-cream">
  <div class="lp-in">
    <h2 class="lp-tt"><span>누가 이용할 수 있나요?</span></h2>
    <p class="lp-tt-sub">XINDELELA 도매몰은 물건을 파는 모든 분을 환영합니다.<br>가게를 하는 분부터 페이스북·틱톡으로 파는 분까지 함께 이용하고 있습니다.</p>
    <ul class="lp-types">
      <?php foreach ($types as [$label, $ic]): ?><li><?= $svg($ic) ?><b><?= esc($label) ?></b></li><?php endforeach ?>
    </ul>
    <p class="lp-hl"><mark>어떤 판매 방식이든 가입할 수 있습니다</mark></p>
  </div>
</section>

<!-- 4. 인증 자료 -->
<section class="lp-sec">
  <div class="lp-in">
    <h2 class="lp-tt"><span>인증 자료는 이렇게</span></h2>
    <p class="lp-tt-sub">아래 세 가지 중 <b>하나만</b> 올리면 됩니다. 담당자가 직접 확인하고 <?= esc($sla) ?>합니다.</p>
    <div class="lp-docs">
      <?php foreach (array_values(mm_verify_types()) as $i => $t): ?>
      <div class="lp-doc">
        <span class="lp-doc-n">0<?= $i + 1 ?></span>
        <b><?= esc($t['label']) ?></b>
        <p><?= esc($t['desc']) ?></p>
        <ul><?php foreach ($t['docs'] as $d): ?><li><?= esc($d) ?></li><?php endforeach ?></ul>
      </div>
      <?php endforeach ?>
    </div>
  </div>
</section>

<!-- 5. 거래 흐름 -->
<section class="lp-flow">
  <div class="lp-flow-pics" aria-hidden="true"><?php for ($i = 0; $i < 9 && $heroPics; $i++): ?><img src="<?= $picAt($i + 1) ?>" alt=""><?php endfor ?></div>
  <div class="lp-flow-main">
    <div class="lp-laptop" aria-hidden="true"><div class="lp-laptop-scr"><img src="<?= base_url('assets/img/welcome/site-pc.jpg') ?>" alt=""></div><div class="lp-laptop-base"></div></div>
    <h2 class="lp-big">XINDELELA 거래 흐름</h2>
    <ol class="lp-steps">
      <?php foreach ([
          ['회원가입', '회원가입은 무료입니다. 상호, 연락처, 지역을 적고 인증 자료를 올립니다.'],
          ['비즈니스 인증', '담당자가 직접 확인하고 ' . $sla . '합니다.' . ($bonus > 0 ? ' 승인되면 ' . number_format($bonus) . 'P가 적립됩니다.' : '')],
          ['도매가 확인 · 주문 신청', '도매가와 수량별 단가를 보고 장바구니에 담아 주문을 신청합니다.'],
          ['KBZPay 송금', '안내된 금액을 KBZPay로 보내고, 송금자 이름으로 입금 확인을 요청합니다.'],
          ['출고 · 배송', '입금이 확인되면 바로 출고합니다. 배송 정보는 주문 상세에서 확인합니다.'],
      ] as $i => [$t, $d]): ?>
      <li><span class="lp-step-ic lp-step-ic<?= $i % 3 ?>"><?= $svg($stepIc[$i], '0 0 80 76') ?></span><div><b><em><?= $i + 1 ?>.</em><?= esc($t) ?></b><p><?= esc($d) ?></p></div></li>
      <?php endforeach ?>
    </ol>
  </div>
</section>

<div class="lp-stripe" aria-hidden="true"></div>

<!-- 6. 판매 중인 상품 -->
<?php if ($products): ?>
<section class="lp-sec lp-dots">
  <div class="lp-in">
    <h2 class="lp-tt"><span>지금 판매 중인 상품</span></h2>
    <p class="lp-tt-sub"><?= esc(implode(', ', array_column($categories, 'name'))) ?> 상품을 한곳에서 고릅니다</p>
    <ul class="lp-tiles">
      <?php foreach ($products as $p): ?>
      <li><a href="<?= site_url('products/' . $p['id']) ?>"><span class="lp-tile-img"><img src="<?= mm_thumb($p['thumb']) ?>" alt="" loading="lazy"></span><b><?= esc($p['name']) ?></b><small><?= $approved ? mm_price($p['price']) : '회원 혜택가' ?></small></a></li>
      <?php endforeach ?>
    </ul>
    <p class="lp-more"><a href="<?= site_url('products') ?>"><b>전체 상품 보기</b> 카테고리: <?= esc(implode(' / ', array_column($categories, 'name'))) ?></a></p>
  </div>
</section>
<?php endif ?>

<!-- 7. 친구 소개 -->
<?php if ($refSign > 0 || $refFirst > 0): ?>
<section class="lp-sec">
  <div class="lp-in">
    <div class="lp-refbox">
      <div class="lp-refbox-t"><span>가입 후 혜택</span><h2>친구를 소개하면<br>포인트가 더 쌓입니다</h2><p>포인트는 주문할 때 상품 금액의 <?= $usePct ?>%까지 쓸 수 있습니다.</p></div>
      <div class="lp-refbox-n">
        <?php if ($refSign > 0): ?><div><small>친구가 인증을 통과하면</small><b><?= number_format($refSign) ?>P</b></div><?php endif ?>
        <?php if ($refFirst > 0): ?><div><small>친구가 첫 주문 입금을 마치면</small><b><?= number_format($refFirst) ?>P</b></div><?php endif ?>
      </div>
    </div>
  </div>
</section>
<?php endif ?>

<!-- 8. FAQ -->
<section class="lp-sec lp-faqsec">
  <div class="lp-in lp-narrow">
    <h2 class="lp-big lp-faq-h"><span>Q</span>FAQ</h2>
    <p class="lp-tt-sub">자주 묻는 질문을 정리했습니다</p>
    <div class="lp-faq">
      <?php foreach ($LP['faq'] as $f): ?>
      <details><summary><?= esc($f['q']) ?></summary><p><?= nl2br(esc($f['a'])) ?></p></details>
      <?php endforeach ?>
    </div>
    <p class="lp-cs">더 궁금한 점은 Viber <b><?= esc(mm_setting('cs_viber', '')) ?></b> 로 문의해 주세요. (월~금 09:00~18:00)</p>
  </div>
</section>

<!-- 9. 쿠폰 + 가입 카드 다시 -->
<section class="lp-end">
  <?php if ($bonus > 0): ?>
  <div class="lp-ticket" aria-hidden="true"><span>WELCOME POINT</span><b><?= number_format($bonus) ?>P</b><small>1P = 1 <?= esc($cur) ?></small></div>
  <?php endif ?>
  <div class="lp-cardwrap is-end"><?= $joinCard('lpEmail2') ?></div>
</section>
<section class="lp-band">
  <div class="lp-band-pics" aria-hidden="true"><?php for ($i = 0; $i < 8 && $heroPics; $i++): ?><img src="<?= $picAt($i + 3) ?>" alt=""><?php endfor ?></div>
  <p><?= esc($LP['band1']) ?><br><b><?= esc($LP['band2']) ?></b></p>
</section>

</div>
<?php if (! $approved): ?><div class="lp-sticky"><a class="lp-go" href="<?= $isBuyer ? site_url('mypage/verify') : site_url('register' . ($ref !== '' ? '?ref=' . $ref : '')) ?>"><?= $isBuyer ? '내 인증 상태 보기' : ($bonus > 0 ? '무료 가입하고 ' . number_format($bonus) . 'P 받기' : '무료 회원가입') ?></a></div><?php endif ?>
