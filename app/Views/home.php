<?php $approved = mm_is_approved(); $sm = new \App\Models\SupplierModel(); $sups = []; ?>
<div class="hero">
  <div class="bn">
    <?php foreach ($banners as $i => $b): ?>
      <div class="s<?= $i === 0 ? ' on' : '' ?>" style="background:<?= esc($b['bg'] ?: '#eef5e3') ?>">
        <div class="k"><?= esc($b['subtitle']) ?></div>
        <h2><?= esc($b['title']) ?></h2>
        <?php if ($b['link']): ?><a class="b" href="<?= site_url($b['link']) ?>">자세히 보기</a><?php endif ?>
      </div>
    <?php endforeach ?>
    <?php if (! $banners): ?><div class="s on" style="background:#eef5e3"><h2>XINDELELA 미얀마 도매몰</h2></div><?php endif ?>
    <div class="ctr"><button class="prev">‹</button><span class="n">1 / <?= max(1, count($banners)) ?></span><button class="next">›</button></div>
  </div>
  <div class="side">
    <?php if ($user && $user['role'] === 'buyer'): ?>
      <div class="bx">내 인증 상태<b><span class="st st-<?= esc($user['status']) ?>" style="font-size:13px"><?= mm_status_label($user['status']) ?></span></b>
        <?php if ($user['status'] === 'pending'): ?><div class="mt8"><?= esc(mm_setting('verify_sla_text')) ?></div><?php elseif ($user['status'] === 'rejected'): ?><div class="mt8"><a href="<?= site_url('mypage/verify') ?>" style="color:var(--price);font-weight:700">자료 다시 제출 →</a></div><?php endif ?>
      </div>
      <div class="bx g">보유 포인트<b><?= number_format((int) $user['points_balance']) ?> P</b><div class="sub">주문 시 최대 <?= (int) mm_setting('points_use_max_pct', 30) ?>%까지 사용</div></div>
    <?php else: ?>
      <div class="bx">도매가 열람·주문은<b style="font-size:14px">사업자 인증 회원만</b><div class="mt8"><?= esc(mm_setting('verify_sla_text')) ?></div></div>
      <a class="bx g" href="<?= site_url('register') ?>">신규가입<b>회원가입 하기</b><div class="sub">페이스북 페이지·매장 사진으로 인증</div></a>
    <?php endif ?>
    <div class="bx">고객센터 (Viber)<div class="ph"><?= esc(mm_setting('cs_viber', '')) ?></div></div>
  </div>
</div>

<div class="cats">
  <?php foreach ($categories as $c): ?><a href="<?= site_url('category/' . $c['id']) ?>"><?= esc($c['name']) ?></a><?php endforeach ?>
</div>

<section class="sec">
  <div class="sec-h"><h2>이렇게 이용하세요</h2><a href="<?= site_url('guide') ?>">이용안내 전체보기 ></a></div>
  <div class="howto">
    <div><i>1</i><b>회원가입</b>페이스북 페이지·매장 사진 등 자료 제출</div>
    <div><i>2</i><b>사업자 인증</b>담당자 확인 후 영업일 1일 이내 승인</div>
    <div><i>3</i><b>도매가 확인·주문 신청</b>박스 단위 수량별 단가 적용</div>
    <div><i>4</i><b>KBZPay 선불 송금</b>최종 금액 안내 후 송금·캡처 업로드</div>
    <div><i>5</i><b>발주·배송</b>입금 확인 즉시 한국 공급사 발주</div>
  </div>
</section>

<section class="sec">
  <div class="sec-h"><h2>신상품</h2><a href="<?= site_url('products?sort=new') ?>">더보기 ></a></div>
  <div class="grid5">
    <?php foreach ($newest as $p): $sup = $sups[$p['supplier_id']] ??= $sm->find($p['supplier_id']); echo view('_card', ['p' => $p, 'approved' => $approved, 'sup' => $sup]); endforeach ?>
  </div>
</section>

<?php if ($best): ?>
<section class="sec">
  <div class="sec-h"><h2>베스트 상품</h2><a href="<?= site_url('products?sort=pop') ?>">더보기 ></a></div>
  <div class="grid5">
    <?php foreach ($best as $p): $sup = $sups[$p['supplier_id']] ??= $sm->find($p['supplier_id']); echo view('_card', ['p' => $p, 'approved' => $approved, 'sup' => $sup]); endforeach ?>
  </div>
</section>
<?php endif ?>

<section class="sec home2">
  <div>
    <div class="sec-h"><h2>공지사항</h2><a href="<?= site_url('notices') ?>">더보기 ></a></div>
    <ul class="nlist">
      <?php foreach ($notices as $n): ?><li><a href="<?= site_url('notices/' . $n['id']) ?>"><?php if ($n['is_pinned']): ?><span class="pin">공지</span><?php endif ?><?= esc($n['title']) ?></a><span class="d"><?= mm_dt($n['created_at'], 'Y.m.d') ?></span></li><?php endforeach ?>
    </ul>
  </div>
  <div>
    <div class="sec-h"><h2>친구 소개 포인트</h2><a href="<?= site_url('mypage/referral') ?>">내 추천코드 ></a></div>
    <div class="box-note" style="font-size:13px;color:var(--ink)">
      내 추천 코드로 가입한 회원이 <b>사업자 인증을 통과하면 <?= number_format((int) mm_setting('points_referral_signup', 0)) ?>P</b>,<br>
      그 회원이 <b>첫 주문 입금을 완료하면 <?= number_format((int) mm_setting('points_referral_first_order', 0)) ?>P</b>가 추가 적립됩니다.<br>
      <span class="muted small">포인트는 주문 신청 시 상품 금액의 <?= (int) mm_setting('points_use_max_pct', 30) ?>%까지 차감 사용할 수 있습니다.</span>
    </div>
  </div>
</section>
