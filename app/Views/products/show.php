<?php $sym = mm_setting('currency_symbol', 'Ks'); usort($tiers, static fn ($a, $b) => $a['min_qty'] <=> $b['min_qty']); ?>
<div class="crumb"><a href="<?= site_url('/') ?>">홈</a><a href="<?= site_url('products') ?>">전체상품</a><?php if ($category): ?><a href="<?= site_url('category/' . $category['id']) ?>"><?= esc($category['name']) ?></a><?php endif ?><?= esc($p['name']) ?></div>

<div class="pd">
  <div class="gal">
    <div class="main"><img src="<?= mm_thumb($images[0] ?? $p['thumb']) ?>" alt=""></div>
    <?php if (count($images) > 1): ?>
    <div class="th"><?php foreach ($images as $i => $im): ?><img src="<?= mm_thumb($im) ?>" data-src="<?= mm_thumb($im) ?>" class="<?= $i === 0 ? 'on' : '' ?>" alt=""><?php endforeach ?></div>
    <?php endif ?>
  </div>
  <div class="info">
    <div class="sup">
      <?php if ($supplier): ?><a href="<?= site_url('suppliers/' . $supplier['id']) ?>">공급사 <b><?= esc($supplier['company_name']) ?></b> ›</a><?php else: ?><span></span><?php endif ?>
      <span class="no">상품번호 <?= esc($p['code'] ?: $p['id']) ?> · 조회 <?= number_format((int) $p['views']) ?></span>
    </div>
    <h1><?= esc($p['name']) ?></h1>

    <?php if ($approved): ?>
      <div class="amt"><span class="big"><?= mm_price($p['price'], false) ?><small> <?= esc($sym) ?></small></span><?php if ($p['retail_price']): ?><span class="rt">소비자가 <?= mm_price($p['retail_price']) ?></span><?php endif ?></div>
    <?php else: ?>
      <div class="amt"><span class="lk">🔒 도매가는 사업자 인증 회원에게만 표시됩니다<?php if (! $user): ?><a href="<?= site_url('login') ?>">로그인</a><?php endif ?></span></div>
    <?php endif ?>

    <div class="rows">
      <div class="r"><div class="k">최소구매수량</div><div class="v"><b><?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?></b> 단위로 주문</div></div>
      <?php if ($approved && $tiers): ?>
      <div class="r"><div class="k">수량별 단가</div><div class="v tiers"><span><?= number_format((int) $p['moq']) ?>개~ <b><?= mm_price($p['price'], false) ?></b></span><?php foreach ($tiers as $t): ?><span><?= number_format((int) $t['min_qty']) ?>개~ <b><?= mm_price($t['price'], false) ?></b></span><?php endforeach ?></div></div>
      <?php endif ?>
      <?php if ($p['brand']): ?><div class="r"><div class="k">브랜드</div><div class="v"><?= esc($p['brand']) ?></div></div><?php endif ?>
      <div class="r"><div class="k">원산지</div><div class="v"><?= esc($p['origin']) ?></div></div>
      <div class="r"><div class="k">재고수량</div><div class="v"><?= number_format((int) $p['stock']) ?><?= esc($p['unit']) ?></div></div>
      <div class="r"><div class="k">결제·배송</div><div class="v">주문 신청 후 배송비 포함 최종 금액 안내 → KBZPay 선불 송금 → 입금 확인 후 한국 발주·미얀마 배송</div></div>
    </div>

    <?php if ($approved): ?>
      <form method="post" action="<?= site_url('cart/add') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
        <div class="qty">
          <span>수량</span>
          <span class="stp"><button type="button" class="minus">−</button><input type="text" name="qty" value="<?= (int) $p['moq'] ?>" data-moq="<?= (int) $p['moq'] ?>" data-price="<?= (int) $p['price'] ?>" data-tiers="<?= esc(json_encode($tiers)) ?>"><button type="button" class="plus">+</button></span>
          <span class="muted small">(<?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?> 단위)</span>
          <span class="tot">단가 <span id="unitPrice"></span> · 합계 <b><span id="totPrice"></span> <?= esc($sym) ?></b></span>
        </div>
        <div class="btns">
          <button type="button" class="btn btn-navy" data-modal="inqModal">문의하기</button>
          <button type="submit" class="btn btn-og" name="go" value="cart">장바구니</button>
          <button type="submit" class="btn btn-g" name="go" value="checkout">주문신청</button>
        </div>
      </form>
    <?php elseif ($user && $user['role'] === 'buyer'): ?>
      <div class="locked">
        <b><?= $user['status'] === 'pending' ? '사업자 인증 검수 중입니다' : ($user['status'] === 'rejected' ? '인증 자료가 반려되었습니다' : '이용이 제한된 계정입니다') ?></b>
        <?= $user['status'] === 'pending' ? esc(mm_setting('verify_sla_text')) . ' 승인되면 도매가와 주문 신청이 열립니다.' : ($user['status'] === 'rejected' ? '반려 사유를 확인하고 자료를 다시 제출해 주세요.' : '고객센터로 문의해 주세요.') ?>
        <div><a class="btn" href="<?= site_url('mypage/verify') ?>">인증 상태 보기</a><button type="button" class="btn btn-navy" data-modal="inqModal">문의하기</button></div>
      </div>
    <?php else: ?>
      <div class="locked">
        <b>회원 전용 도매가</b>
        미얀마 소매상·온라인 셀러 전용 폐쇄몰입니다. 가입 후 사업자 인증(<?= esc(mm_setting('verify_sla_text')) ?>)을 받으면 도매가 확인과 주문 신청이 가능합니다.
        <div><a class="btn btn-g" href="<?= site_url('register') ?>">회원가입</a><a class="btn" href="<?= site_url('login') ?>">로그인</a></div>
      </div>
    <?php endif ?>
  </div>
</div>

<div class="ptabs"><a href="#desc" class="on" data-tab="desc">상품 상세정보</a><a href="#sup" data-tab="sup">공급사 정보</a><a href="#notice" data-tab="notice">주문·배송 안내</a></div>
<div class="pdesc ptab" id="desc">
  <?= $p['description'] ?: '<p class="muted">상세 설명이 아직 등록되지 않았습니다.</p>' ?>
</div>
<?php if ($supplier): ?>
<div id="sup" class="sec ptab hidden"><div class="sec-h"><h2>공급사 정보</h2><a href="<?= site_url('suppliers/' . $supplier['id']) ?>">이 공급사 상품 전체보기 ></a></div>
<table class="kv"><tr><th>공급사</th><td><?= esc($supplier['company_name']) ?><?php if ($supplier['brand']): ?> (<?= esc($supplier['brand']) ?>)<?php endif ?></td></tr><tr><th>소개</th><td><?= esc($supplier['intro']) ?></td></tr><tr><th>소재지</th><td><?= esc($supplier['address']) ?></td></tr></table></div>
<?php endif ?>
<div id="notice" class="sec ptab hidden"><div class="sec-h"><h2>주문·배송 안내</h2></div>
<div class="box-note">① 장바구니 → 주문 신청(배송지 입력) ② 담당자가 상품 금액 + 배송비를 합산한 최종 금액을 안내 ③ KBZPay 송금 후 마이페이지에서 송금 캡처 업로드 ④ 입금 확인 즉시 한국 공급사 발주 ⑤ 미얀마 도착 후 배송. 포인트는 주문 신청 시 상품 금액의 <?= (int) mm_setting('points_use_max_pct', 30) ?>%까지 사용할 수 있습니다.</div></div>

<?php if ($related): ?>
<section class="sec"><div class="sec-h"><h2>같은 카테고리 상품</h2></div>
<div class="grid5"><?php foreach ($related as $r): echo view('_card', ['p' => $r, 'approved' => $approved, 'sup' => null]); endforeach ?></div></section>
<?php endif ?>

<?php if ($user && $user['role'] === 'buyer'): ?>
<div class="modal" id="inqModal"><div class="in">
  <h3>상품 문의</h3>
  <form method="post" action="<?= site_url('inquiry') ?>"><?= csrf_field() ?><input type="hidden" name="product_id" value="<?= $p['id'] ?>">
    <div class="field"><label>상품</label><div class="small"><?= esc($p['name']) ?></div></div>
    <div class="field"><label>문의 내용</label><textarea class="ta" name="body" placeholder="수량, 납기, 샘플, 배송 등 궁금한 점을 적어 주세요."></textarea></div>
    <div class="row"><button class="btn btn-g" type="submit">문의 보내기</button><button class="btn close" type="button">닫기</button></div>
  </form>
</div></div>
<?php endif ?>
