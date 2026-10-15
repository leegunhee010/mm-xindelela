<?php $approved = mm_is_approved(); $sm = new \App\Models\SupplierModel(); $sups = []; $n = max(1, count($banners)); ?>
<div class="hero">
  <div class="track" id="heroTrack">
    <?php foreach ($banners as $i => $b): $dark = ! $b['image']; ?>
      <a class="s<?= $dark ? ' dark' : '' ?>" href="<?= site_url($b['link'] ?: 'products') ?>" style="background:<?= esc($b['bg'] ?: '#f7f7f7') ?>">
        <?php if ($b['image']): ?><img src="<?= base_url($b['image']) ?>" alt=""><?php endif ?>
        <?php if ($b['subtitle']): ?><span class="k"><?= esc($b['subtitle']) ?></span><?php endif ?>
        <h2><?= esc($b['title']) ?></h2>
        <div class="b">자세히 보기 →</div>
      </a>
    <?php endforeach ?>
    <?php if (! $banners): ?><a class="s dark" href="<?= site_url('products') ?>"><h2>XINDELELA 미얀마 도매몰</h2></a><?php endif ?>
  </div>
  <div class="ctr"><button class="prev" aria-label="이전">‹</button><span class="n">1 / <?= $n ?></span><button class="next" aria-label="다음">›</button></div>
</div>

<div class="statusbar">
  <?php if ($user && $user['role'] === 'buyer'): ?>
    <div class="bx"><div>내 인증 상태<b><?= mm_status_label($user['status']) ?></b></div><?php if ($user['status'] === 'approved'): ?><span class="st st-approved">도매가 열람중</span><?php elseif ($user['status'] === 'rejected'): ?><a class="btn btn-sm btn-g" href="<?= site_url('mypage/verify') ?>">자료 다시 제출</a><?php else: ?><span class="small"><?= esc(mm_setting('verify_sla_text')) ?></span><?php endif ?></div>
    <div class="bx o"><div>보유 포인트<b><?= number_format((int) $user['points_balance']) ?> P</b></div><a class="btn btn-sm" href="<?= site_url('mypage/referral') ?>" style="background:#fff;border-color:#fff;color:var(--pri)">친구 소개하기</a></div>
  <?php else: ?>
    <div class="bx"><div>도매가 열람·주문은<b>사업자 인증 회원만</b></div><span class="small"><?= esc(mm_setting('verify_sla_text')) ?></span></div>
    <div class="bx o"><div>신규가입<b>페이스북 페이지로 간단 인증</b></div><a class="btn btn-sm" href="<?= site_url('register') ?>" style="background:#fff;border-color:#fff;color:var(--pri)">회원가입</a></div>
  <?php endif ?>
  <div class="bx"><div>고객센터 (Viber)<b><?= esc(mm_setting('cs_viber', '')) ?></b></div><span class="small">평일 09:00~18:00</span></div>
</div>

<section class="sec">
  <div class="sec-h"><h2>인증 회원에게만 열리는
도매가, 이번 주 신상품</h2><a href="<?= site_url('products?sort=new') ?>">신상품 더보기</a></div>
  <div class="chips">
    <a href="<?= site_url('products') ?>" class="on">전체</a>
    <?php foreach ($categories as $c): ?><a href="<?= site_url('category/' . $c['id']) ?>"><?= esc($c['name']) ?></a><?php endforeach ?>
  </div>
  <div class="grid5">
    <?php foreach ($newest as $p): $sup = $sups[$p['supplier_id']] ??= $sm->find($p['supplier_id']); echo view('_card', ['p' => $p, 'approved' => $approved, 'sup' => $sup]); endforeach ?>
  </div>
</section>

<section class="sec">
  <div class="sec-h"><h2>이렇게 이용하세요</h2><a class="plain" href="<?= site_url('guide') ?>">이용안내 전체보기 ›</a></div>
  <div class="howto">
    <div><i>1</i><b>회원가입</b>페이스북 페이지·매장 사진 등 자료 제출</div>
    <div><i>2</i><b>사업자 인증</b>담당자 확인 후 영업일 1일 이내 승인</div>
    <div><i>3</i><b>도매가 확인·주문 신청</b>박스 단위 수량별 단가 자동 적용</div>
    <div><i>4</i><b>KBZPay 선불 송금</b>최종 금액 안내 후 송금·캡처 업로드</div>
    <div><i>5</i><b>발주·배송</b>입금 확인 즉시 한국 공급사 발주</div>
  </div>
</section>

<?php if ($best): ?>
<section class="sec">
  <div class="sec-h"><h2>미얀마 셀러들이
가장 많이 주문한 베스트</h2><a href="<?= site_url('products?sort=pop') ?>">베스트 더보기</a></div>
  <div class="grid5">
    <?php foreach ($best as $p): $sup = $sups[$p['supplier_id']] ??= $sm->find($p['supplier_id']); echo view('_card', ['p' => $p, 'approved' => $approved, 'sup' => $sup]); endforeach ?>
  </div>
</section>
<?php endif ?>

<section class="sec home2">
  <div>
    <div class="sec-h"><h2>공지사항</h2><a class="plain" href="<?= site_url('notices') ?>">더보기 ›</a></div>
    <ul class="nlist">
      <?php foreach ($notices as $n): ?><li><a href="<?= site_url('notices/' . $n['id']) ?>"><?php if ($n['is_pinned']): ?><span class="pin">공지</span><?php endif ?><?= esc($n['title']) ?></a><span class="d"><?= mm_dt($n['created_at'], 'Y.m.d') ?></span></li><?php endforeach ?>
    </ul>
  </div>
  <div>
    <div class="refcard">
      <div class="t">친구를 소개하면
포인트가 두 번 쌓입니다</div>
      <div class="d">소개한 회원이 사업자 인증을 통과하면 <b><?= number_format((int) mm_setting('points_referral_signup', 0)) ?>P</b>, 첫 주문 입금을 완료하면 <b><?= number_format((int) mm_setting('points_referral_first_order', 0)) ?>P</b> 추가 적립. 주문 시 상품 금액의 <?= (int) mm_setting('points_use_max_pct', 30) ?>%까지 사용할 수 있습니다.</div>
      <a class="btn" href="<?= site_url('mypage/referral') ?>">내 추천 코드 보기</a>
    </div>
  </div>
</section>
