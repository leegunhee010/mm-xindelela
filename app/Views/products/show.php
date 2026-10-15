<?php $sym = mm_setting('currency_symbol', 'Ks'); usort($tiers, static fn ($a, $b) => $a['min_qty'] <=> $b['min_qty']); ?>
<div class="crumb"><a href="<?= site_url('/') ?>">홈</a><a href="<?= site_url('products') ?>">전체상품</a><?php if ($category): ?><a href="<?= site_url('category/' . $category['id']) ?>"><?= esc($category['name']) ?></a><?php endif ?><span><?= esc(mb_strimwidth($p['name'], 0, 40, '…')) ?></span></div>

<div class="pd">
  <div class="gal">
    <div class="th">
      <?php foreach ($images ?: [$p['thumb']] as $i => $im): ?><img src="<?= mm_thumb($im) ?>" data-src="<?= mm_thumb($im) ?>" class="<?= $i === 0 ? 'on' : '' ?>" alt=""><?php endforeach ?>
    </div>
    <div class="main"><img src="<?= mm_thumb($images[0] ?? $p['thumb']) ?>" alt=""></div>
  </div>
  <div class="info">
    <div class="sup">
      <?php if ($p['brand']): ?><b><?= esc($p['brand']) ?></b><?php endif ?>
      <?php if ($supplier): ?><a href="<?= site_url('suppliers/' . $supplier['id']) ?>"><?= esc($supplier['company_name']) ?> ›</a><?php endif ?>
      <span class="no">상품번호 <?= esc($p['code'] ?: $p['id']) ?></span>
    </div>
    <h1><?= esc($p['name']) ?></h1>
    <?php if ($p['summary']): ?><div class="muted" style="font-size:14px"><?= esc($p['summary']) ?></div><?php endif ?>

    <?php if ($approved): ?>
      <div class="amt"><span class="big"><?= mm_price($p['price'], false) ?><small> <?= esc($sym) ?></small></span><?php if ($p['retail_price']): ?><span class="rt">소비자가 <?= mm_price($p['retail_price']) ?></span><?php endif ?></div>
    <?php else: ?>
      <div class="amt"><span class="lk">회원 혜택가<small>사업자 인증 회원에게만 도매가가 표시됩니다<?php if ($p['retail_price']): ?> · 소비자가 <?= mm_price($p['retail_price']) ?><?php endif ?></small></span></div>
    <?php endif ?>
    <div class="meta">조회 <b><?= number_format((int) $p['views']) ?></b> · 주문 <?= number_format((int) $p['order_count']) ?>건</div>

    <div class="rows">
      <div class="r"><div class="k b">최소수량</div><div class="v"><b><?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?></b> 단위로 주문 (박스 입수 기준)</div></div>
      <?php if ($approved && $tiers): ?>
      <div class="r"><div class="k b">수량별 단가</div><div class="v tiers"><span><?= number_format((int) $p['moq']) ?>개~ <b><?= mm_price($p['price'], false) ?></b></span><?php foreach ($tiers as $t): ?><span><?= number_format((int) $t['min_qty']) ?>개~ <b><?= mm_price($t['price'], false) ?></b></span><?php endforeach ?></div></div>
      <?php endif ?>
      <div class="r"><div class="k">배송정보</div><div class="v">입금 확인 후 한국 발주 → 미얀마 배송. 배송비는 주문 신청 후 담당자가 안내합니다.</div></div>
      <div class="r"><div class="k">결제</div><div class="v">KBZPay 100% 선불 (주문 신청 → 금액 안내 → 송금 → 캡처 업로드)</div></div>
      <div class="r"><div class="k">원산지</div><div class="v"><?= esc($p['origin']) ?></div></div>
      <div class="r"><div class="k">재고</div><div class="v"><?= number_format((int) $p['stock']) ?><?= esc($p['unit']) ?></div></div>
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
          <button type="button" class="btn" data-modal="inqModal">문의</button>
          <button type="submit" class="btn btn-og" name="go" value="cart">장바구니</button>
          <button type="submit" class="btn btn-g w2" name="go" value="checkout">주문 신청하기</button>
        </div>
      </form>
    <?php elseif ($user && $user['role'] === 'buyer'): ?>
      <div class="locked">
        <b><?= $user['status'] === 'pending' ? '사업자 인증 검수 중입니다' : ($user['status'] === 'rejected' ? '인증 자료가 반려되었습니다' : '이용이 제한된 계정입니다') ?></b>
        <?= $user['status'] === 'pending' ? esc(mm_setting('verify_sla_text')) . ' 승인되면 도매가와 주문 신청이 열립니다.' : ($user['status'] === 'rejected' ? '반려 사유를 확인하고 자료를 다시 제출해 주세요.' : '고객센터로 문의해 주세요.') ?>
        <div><a class="btn" href="<?= site_url('mypage/verify') ?>">인증 상태 보기</a><button type="button" class="btn btn-og" data-modal="inqModal">문의하기</button></div>
      </div>
    <?php else: ?>
      <div class="locked">
        <b>회원 혜택가는 사업자 인증 후 열립니다</b>
        미얀마 소매상·온라인 셀러 전용 폐쇄몰입니다. 가입 후 사업자 인증(<?= esc(mm_setting('verify_sla_text')) ?>)을 받으면 도매가 확인과 주문 신청이 가능합니다.
        <div><a class="btn btn-g" href="<?= site_url('register') ?>">회원가입</a><a class="btn" href="<?= site_url('login') ?>">로그인</a></div>
      </div>
    <?php endif ?>
  </div>
</div>

<div class="ptabs"><a href="#desc" class="on" data-tab="desc">상품상세</a><a href="#sup" data-tab="sup">공급사 정보</a><a href="#notice" data-tab="notice">주문·배송 안내</a><a href="#inq" data-tab="inq">문의</a></div>
<div class="pdesc ptab" id="desc">
  <?= $p['description'] ?: '<p class="muted">상세 설명이 아직 등록되지 않았습니다.</p>' ?>
</div>
<div id="sup" class="ptab hidden" style="padding:40px 0">
<?php if ($supplier): ?>
<table class="kv"><tr><th>공급사</th><td><?= esc($supplier['company_name']) ?><?php if ($supplier['brand']): ?> (<?= esc($supplier['brand']) ?>)<?php endif ?></td></tr><tr><th>소개</th><td><?= esc($supplier['intro']) ?></td></tr><tr><th>소재지</th><td><?= esc($supplier['address']) ?></td></tr></table>
<a class="btn mt16" href="<?= site_url('suppliers/' . $supplier['id']) ?>">이 공급사 상품 전체보기</a>
<?php else: ?><div class="muted">공급사 정보가 없습니다.</div><?php endif ?>
</div>
<div id="notice" class="ptab hidden" style="padding:40px 0">
  <div class="acc"><div class="t">주문 절차</div><div class="muted" style="padding-bottom:20px;font-size:14px;line-height:1.8">① 장바구니 → 주문 신청(배송지 입력) ② 담당자가 상품 금액 + 배송비를 합산한 최종 금액을 안내 ③ KBZPay 송금 후 마이페이지에서 송금 캡처 업로드 ④ 입금 확인 즉시 한국 공급사 발주 ⑤ 미얀마 도착 후 배송</div></div>
  <div class="acc"><div class="t">포인트</div><div class="muted" style="padding-bottom:20px;font-size:14px;line-height:1.8">주문 신청 시 상품 금액의 <?= (int) mm_setting('points_use_max_pct', 30) ?>%까지 사용할 수 있습니다. 주문을 취소하면 사용한 포인트는 환원됩니다.</div></div>
</div>
<div id="inq" class="ptab hidden" style="padding:40px 0">
  <?php if ($user && $user['role'] === 'buyer'): ?><button type="button" class="btn btn-g" data-modal="inqModal">이 상품 문의하기</button><?php else: ?><div class="muted">상품 문의는 로그인 후 가능합니다. <a href="<?= site_url('login') ?>" style="color:var(--pri);font-weight:700">로그인</a></div><?php endif ?>
</div>

<?php if ($related): ?>
<section class="sec"><div class="sec-h"><h2>같은 카테고리
다른 상품도 함께</h2><?php if ($category): ?><a href="<?= site_url('category/' . $category['id']) ?>"><?= esc($category['name']) ?> 더보기</a><?php endif ?></div>
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
