<?php $sym = mm_setting('currency_symbol', 'Ks'); usort($tiers, static fn ($a, $b) => $a['min_qty'] <=> $b['min_qty']); $imgs = $images ?: [$p['thumb']]; ?>
<nav class="breadcrumb m-auto w-large pb-[40px] pt-[24px]"><ol class="breadcrumb-list flex items-center">
  <li class="breadcrumb-item flex items-center"><a class="breadcrumb-item-link block px-[4px]" href="<?= site_url('/') ?>"><?= t('홈') ?></a><span><picture><img alt="" class="size-[12px]" src="/images/icons/common/ico_chevron_right_light_16.svg"></picture></span></li>
  <li class="breadcrumb-item flex items-center"><a class="block px-[4px]" href="<?= site_url('products') ?>"><?= t('전체상품') ?></a><span><picture><img alt="" class="size-[12px]" src="/images/icons/common/ico_chevron_right_light_16.svg"></picture></span></li>
  <?php if ($category): ?><li class="breadcrumb-item flex items-center"><a class="block px-[4px]" href="<?= site_url('category/' . $category['id']) ?>"><?= esc(t($category['name'])) ?></a><span><picture><img alt="" class="size-[12px]" src="/images/icons/common/ico_chevron_right_light_16.svg"></picture></span></li><?php endif ?>
  <li class="breadcrumb-item flex items-center"><a class="block px-[4px] font-medium text-black"><?= esc(mb_strimwidth(mm_pname($p), 0, 40, '…')) ?></a></li>
</ol></nav>

<section class="corner-section last-of-type:mb-0 pc:mx-auto pc:w-large flex justify-between pc:mb-[40px]">
  <!-- 갤러리: 세로 썸네일 + 메인 603 -->
  <div class="swiper-thumbnail-type goods-main-slide">
    <div class="swiper main-thumb" id="pdMain"><div class="swiper-wrapper">
      <?php foreach ($imgs as $im): ?><div class="swiper-slide w-full pc:w-[486px]" style="width:603px"><div class="thumb-container overflow-hidden pc:rounded-[12px]"><picture><img src="<?= mm_thumb($im) ?>" class="thumb-item"></picture></div></div><?php endforeach ?>
    </div><div class="swiper-pagination swiper-pagination-fraction swiper-pagination-horizontal" id="pdPg"><span class="swiper-pagination-current">1</span> / <span class="swiper-pagination-total"><?= count($imgs) ?></span></div></div>
    <div class="swiper sub-thumb" id="pdThumbs"><div class="swiper-wrapper">
      <?php foreach ($imgs as $i => $im): ?><div class="swiper-slide <?= $i === 0 ? 'swiper-slide-thumb-active' : '' ?>"><div class="thumb-container overflow-hidden rounded-[8px]"><picture><img src="<?= mm_thumb($im) ?>" class="thumb-item"></picture></div></div><?php endforeach ?>
    </div></div>
  </div>

  <!-- 상품 정보 480 -->
  <div class="goods-info pc:w-[480px]">
    <!-- 브랜드 · 공급사 -->
    <div class="pd-top">
      <?php if ($p['brand']): ?><b><?= esc(t($p['brand'])) ?></b><?php endif ?>
      <?php if ($supplier): ?><a href="<?= site_url('suppliers/' . $supplier['id']) ?>"><?= esc(mm_sname($supplier)) ?> ›</a><?php endif ?>
      <?php $wOn = in_array((int) $p['id'], mm_wish_ids(), true); ?><button type="button" class="wish-btn wish-pd<?= $wOn ? ' on' : '' ?>" data-wish="<?= $p['id'] ?>" aria-pressed="<?= $wOn ? 'true' : 'false' ?>"><span><?= t('관심상품') ?></span></button>
    </div>
    <h2 class="pd-name"><?= esc(mm_pname($p)) ?></h2>
    <?php if (in_array((int) $p['id'], mm_repeat_ids(), true)): ?><p class="pd-repeat"><?= t('재구매 많음 · 여러 셀러가 다시 주문한 상품입니다') ?></p><?php endif ?>

    <!-- 가격 -->
    <?php if ($approved): ?>
      <div class="pd-price"><b><?= mm_price($p['price'], false) ?></b><span><?= esc($sym) ?></span><?php if ($p['retail_price']): ?><del><?= t('소비자가') ?> <?= mm_price($p['retail_price']) ?></del><?php endif ?></div>
    <?php else: ?>
      <div class="pd-price is-lock"><span class="pd-price-l"><?= t('도매가') ?></span><b><?= t('🔒 인증 회원 공개') ?></b><?php if ($p['retail_price']): ?><del><?= t('소비자가') ?> <?= mm_price($p['retail_price']) ?></del><?php endif ?></div>
    <?php endif ?>

    <!-- 핵심 조건 한 줄 -->
    <dl class="pd-facts">
      <div><dt><?= t('최소 주문') ?></dt><dd><b><?= number_format((int) $p['moq']) ?><?= esc(t($p['unit'])) ?></b><?= t('부터') ?></dd></div>
      <?php if ($p['origin']): ?><div><dt><?= t('원산지') ?></dt><dd><?= esc(t($p['origin'])) ?></dd></div><?php endif ?>
      <div><dt><?= t('결제') ?></dt><dd><?= t('KBZPay 100% 선불') ?></dd></div>
      <?php if ($approved): ?><div><dt><?= t('재고') ?></dt><dd><?= number_format((int) $p['stock']) ?><?= esc(t($p['unit'])) ?></dd></div><?php endif ?>
    </dl>

    <?php if (trim((string) ($p['sale_note'] ?? '')) !== ''): ?>
    <div class="pd-rule"><b><?= t('판매 시 지켜 주세요') ?></b><p><?= nl2br(esc(trim($p['sale_note']))) ?></p></div>
    <?php endif ?>

    <!-- 수량별 도매가 -->
    <?php if ($approved): ?>
    <div class="pd-tier">
      <p class="pd-tier-tit"><?= t('수량별 도매가') ?></p>
      <ul>
        <li data-min="<?= (int) $p['moq'] ?>"><span><?= number_format((int) $p['moq']) ?><?= esc(t($p['unit'])) ?> <?= t('이상') ?></span><b><?= mm_price($p['price'], false) ?> <?= esc($sym) ?></b></li>
        <?php foreach ($tiers as $t): ?><li data-min="<?= (int) $t['min_qty'] ?>"><span><?= number_format((int) $t['min_qty']) ?><?= esc(t($p['unit'])) ?> <?= t('이상') ?></span><b><?= mm_price($t['price'], false) ?> <?= esc($sym) ?></b></li><?php endforeach ?>
      </ul>
    </div>
    <?php endif ?>

    <?php if ($approved): ?>
      <form method="post" action="<?= site_url('cart/add') ?>">
        <?= csrf_field() ?><input type="hidden" name="product_id" value="<?= $p['id'] ?>">
        <div class="qty" data-sym="<?= esc($sym) ?>">
          <span class="stp"><button type="button" class="minus" aria-label="<?= t('빼기') ?>">−</button><input type="text" name="qty" value="<?= (int) $p['moq'] ?>" data-moq="<?= (int) $p['moq'] ?>" data-price="<?= (int) $p['price'] ?>" data-tiers="<?= esc(json_encode($tiers)) ?>" aria-label="<?= t('수량') ?>"><button type="button" class="plus" aria-label="<?= t('더하기') ?>">+</button></span>
          <span class="tot"><em><?= t('단가') ?> <span id="unitPrice"></span></em><b><span id="totPrice"></span> <?= esc($sym) ?></b></span>
        </div>
        <div class="btn-area shrink-0 flex gap-[8px] pt-[16px]">
          <button type="button" data-modal="inqModal" class="inline-flex size-[50px] shrink-0 items-center justify-center rounded-full border border-gray7" title="<?= t('문의') ?>"><picture><img src="/images/icons/common/ico_share_all_24.svg" alt="<?= t('문의') ?>" class=""></picture></button>
          <button type="submit" name="go" value="cart" class="text-primary bg-white border border-primary rounded-[99px] min-h-[44px] px-[16px] py-[12px] body3 font-bold !leading-none w-[150px]"><?= t('장바구니') ?></button>
          <button type="submit" name="go" value="checkout" class="text-white bg-primary rounded-[99px] min-h-[44px] px-[16px] py-[12px] body3 font-bold !leading-none w-full"><?= t('주문 신청하기') ?></button>
        </div>
      </form>
      <?php
      // 판매 글용 사진: 갤러리 + 상세 이미지 블록
      $photos = array_map(static fn ($im) => mm_thumb($im), $imgs);
      foreach (mm_detail_blocks($p) as $b) {
          if ($b['type'] === 'img' && ! empty($b['src'])) {
              $photos[] = str_starts_with($b['src'], 'http') ? $b['src'] : base_url($b['src']);
          }
      }
      $photos = array_values(array_unique(array_filter($photos)));
      ?>
      <?php if ($photos): ?>
      <button type="button" class="pd-photos" id="pdPhotos" data-name="<?= esc(($p['code'] ?: 'product-' . $p['id']) . ' ' . mm_pname($p)) ?>" data-urls="<?= esc(json_encode($photos, JSON_UNESCAPED_SLASHES)) ?>"><b><?= t('상품 사진 내려받기') ?></b><span><?= count($photos) ?><?= t('장 · 판매 글에 쓸 수 있습니다') ?></span></button>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
      <?php endif ?>
    <?php elseif ($user && $user['role'] === 'buyer'): $vs = $user['status']; ?>
      <div class="pd-lock">
        <div class="pd-lock-h"><span class="pd-lock-ic <?= $vs === 'rejected' ? 'is-warn' : '' ?>"><?= $vs === 'pending' ? '⏳' : '!' ?></span><div><b><?= $vs === 'pending' ? t('비즈니스 인증 검수 중입니다') : ($vs === 'rejected' ? t('인증 자료를 다시 올려 주세요') : t('이용이 제한된 계정입니다')) ?></b><p><?= $vs === 'pending' ? esc(t(mm_setting('verify_sla_text'))) . t(' 승인되면 도매가와 주문이 열립니다.') : ($vs === 'rejected' ? t('마이페이지에서 반려 사유를 확인하고 자료를 보완해 주세요.') : t('고객센터로 문의해 주세요.')) ?></p></div></div>
        <ol class="pd-lock-steps"><li class="done"><em>✓</em><?= t('회원가입') ?></li><li class="<?= $vs === 'pending' ? 'on' : ($vs === 'rejected' ? 'warn' : '') ?>"><em>2</em><?= t('비즈니스 인증') ?></li><li><em>3</em><?= t('도매가·주문') ?></li></ol>
        <div class="pd-lock-btns"><a class="is-main" href="<?= site_url('mypage/verify') ?>"><?= $vs === 'rejected' ? t('자료 다시 올리기') : t('인증 상태 보기') ?></a><button type="button" data-modal="inqModal"><?= t('문의하기') ?></button></div>
      </div>
    <?php else: ?>
      <div class="pd-lock">
        <div class="pd-lock-h"><span class="pd-lock-ic">🔒</span><div><b><?= t('비즈니스 인증하고 도매가 보기') ?></b><p><?= t('인증은') ?> <?= esc(t(mm_setting('verify_sla_text'))) ?> <?= t('끝납니다. 인증 회원은 상품 사진을 한 번에 내려받아 판매 글에 쓸 수 있습니다.') ?></p></div></div>
        <ol class="pd-lock-steps"><li class="on"><em>1</em><?= t('회원가입') ?></li><li><em>2</em><?= t('비즈니스 인증') ?></li><li><em>3</em><?= t('도매가·주문') ?></li></ol>
        <div class="pd-lock-btns"><a class="is-main" href="<?= site_url('register') ?>"><?= t('회원가입하고 인증하기') ?></a><a href="<?= site_url('login') ?>"><?= t('로그인') ?></a></div>
      </div>
    <?php endif ?>
  </div>
</section>

<!-- 상세 하단: 고정 탭(균등 4칸) + 섹션 한 페이지 -->
<?php $cat = t($category['name']) ?? ''; ?>
<nav class="pdt-nav" id="pdtNav"><div class="pdt-nav-in">
  <?php foreach (['pdt-info' => t('상품 정보'), 'pdt-sup' => t('공급사'), 'pdt-guide' => t('주문·배송 안내'), 'pdt-inq' => t('문의')] as $k => $l): ?><a href="#<?= $k ?>" data-sec="<?= $k ?>"><?= $l ?></a><?php endforeach ?>
</div></nav>

<div class="pdt-wrap">
  <section class="pdt-sec" id="pdt-info">
    <h3 class="pdt-h"><?= t('상품 정보') ?></h3>
    <dl class="pdt-spec">
      <div><dt><?= t('상품명') ?></dt><dd><?= esc(mm_pname($p)) ?></dd></div>
      <div><dt><?= t('브랜드') ?></dt><dd><?= esc(t($p['brand'] ?: '-')) ?></dd></div>
      <div><dt><?= t('카테고리') ?></dt><dd><?= esc($cat ?: '-') ?></dd></div>
      <div><dt><?= t('상품코드') ?></dt><dd><?= esc($p['code'] ?: '-') ?></dd></div>
      <div><dt><?= t('원산지') ?></dt><dd><?= esc(t($p['origin'] ?: '-')) ?></dd></div>
      <div><dt><?= t('최소 주문') ?></dt><dd><?= number_format((int) $p['moq']) ?><?= esc(t($p['unit'])) ?><?= t('부터') ?></dd></div>
      <div><dt><?= t('재고') ?></dt><dd><?= $approved ? number_format((int) $p['stock']) . esc(t($p['unit'])) : t('인증 회원에게 공개') ?></dd></div>
      <div><dt><?= t('공급사') ?></dt><dd><?= esc(mm_sname($supplier) ?: '-') ?></dd></div>
    </dl>
    <?php $blocks = mm_detail_blocks($p); $nImg = 0; ?>
    <?php if (! $blocks): ?><p class="pdt-empty"><?= t('상세 설명이 아직 등록되지 않았습니다.') ?></p><?php else: ?>
    <div class="pdt-body">
      <?php for ($i = 0, $n = count($blocks); $i < $n; $i++): $b = $blocks[$i]; ?>
        <?php if ($b['type'] === 'img' && ! empty($b['seq'])): ?>
          <div class="pdt-strip"><?php while ($i < $n && $blocks[$i]['type'] === 'img' && ! empty($blocks[$i]['seq'])): $s2 = $blocks[$i]; $nImg++; ?><img src="<?= esc(str_starts_with($s2['src'], 'http') ? $s2['src'] : base_url($s2['src'])) ?>" alt="<?= esc(mm_pname($p)) ?> <?= t('상세') ?>"<?= ! empty($s2['w']) ? ' width="' . (int) $s2['w'] . '" height="' . (int) $s2['h'] . '"' : '' ?> loading="<?= $nImg <= 2 ? 'eager' : 'lazy' ?>"><?php $i++; endwhile; $i--; ?></div>
        <?php elseif ($b['type'] === 'img'): $nImg++; ?>
          <img class="pdt-img" src="<?= esc(str_starts_with($b['src'], 'http') ? $b['src'] : base_url($b['src'])) ?>" alt="<?= esc(mm_pname($p)) ?>" loading="<?= $nImg <= 2 ? 'eager' : 'lazy' ?>">
        <?php elseif ($b['type'] === 'p'): ?>
          <p class="pdt-p"><?= nl2br(esc(mm_tr_lines($b['text']))) ?></p>
        <?php elseif ($b['type'] === 'video' && ($emb = mm_video_embed($b['src']))): ?>
          <div class="pdt-video"><iframe src="<?= esc($emb) ?>" loading="lazy" allowfullscreen title="<?= t('상품 영상') ?>"></iframe></div>
        <?php elseif ($b['type'] === 'html'): ?>
          <div class="pdt-desc"><?= $b['html'] ?></div>
        <?php endif ?>
      <?php endfor ?>
    </div>
    <?php endif ?>
  </section>

  <section class="pdt-sec" id="pdt-sup">
    <h3 class="pdt-h"><?= t('공급사') ?></h3>
    <?php if ($supplier): ?>
    <div class="pdt-sup">
      <span class="pdt-sup-ic"><?= esc(mb_substr(mm_sname($supplier), 0, 1)) ?></span>
      <div class="pdt-sup-b"><b><?= esc(mm_sname($supplier)) ?></b><?php if ($supplier['brand']): ?><span><?= esc(t($supplier['brand'])) ?></span><?php endif ?><?php if ($supplier['intro']): ?><p><?= esc(t($supplier['intro'])) ?></p><?php endif ?></div>
      <a class="pdt-btn" href="<?= site_url('suppliers/' . $supplier['id']) ?>"><?= t('이 공급사 상품 전체 ›') ?></a>
    </div>
    <?php else: ?><p class="pdt-empty"><?= t('공급사 정보가 없습니다.') ?></p><?php endif ?>
  </section>

  <section class="pdt-sec" id="pdt-guide">
    <h3 class="pdt-h"><?= t('주문·배송 안내') ?></h3>
    <ol class="pdt-steps">
      <li><em>1</em><b><?= t('주문 신청') ?></b><span><?= t('장바구니에서 배송지 입력') ?></span></li>
      <li><em>2</em><b><?= t('KBZPay 송금') ?></b><span><?= t('안내된 계좌로 100% 선불') ?></span></li>
      <li><em>3</em><b><?= t('입금 확인 요청') ?></b><span><?= t('주문 상세에서 송금자 이름 입력') ?></span></li>
      <li><em>4</em><b><?= t('출고·배송') ?></b><span><?= t('입금 확인 후 바로 출고') ?></span></li>
    </ol>
    <ul class="pdt-notes">
      <li><?= t('배송지는 입금 확인 전까지 주문 상세에서 직접 바꿀 수 있습니다.') ?></li>
      <li><?= t('포인트는 주문 신청 때 상품 금액의') ?> <?= (int) mm_setting('points_use_max_pct', 30) ?><?= t('%까지 쓸 수 있고, 주문을 취소하면 돌려드립니다.') ?></li>
      <li><?= t('궁금한 점은 고객센터 Viber') ?> <?= esc(mm_setting('cs_viber', '')) ?> <?= t('로 문의해 주세요.') ?></li>
    </ul>
  </section>

  <section class="pdt-sec" id="pdt-inq">
    <h3 class="pdt-h"><?= t('문의') ?></h3>
    <div class="pdt-inq">
      <p><?= t('수량·납기·샘플 등 이 상품에 대해 궁금한 점을 남겨 주세요. 답변은 마이페이지 › 문의 내역에서 확인할 수 있습니다.') ?></p>
      <?php if ($user && $user['role'] === 'buyer'): ?><button type="button" class="pdt-btn is-main" data-modal="inqModal"><?= t('이 상품 문의하기') ?></button><?php else: ?><a class="pdt-btn is-main" href="<?= site_url('login') ?>"><?= t('로그인하고 문의하기') ?></a><?php endif ?>
    </div>
  </section>
</div>
<script>
(function () {
  var nav = document.getElementById('pdtNav'); if (!nav) return;
  var links = nav.querySelectorAll('a'), secs = [].map.call(links, function (a) { return document.getElementById(a.dataset.sec); });
  links.forEach(function (a, i) { a.addEventListener('click', function (e) { e.preventDefault(); window.scrollTo({ top: secs[i].getBoundingClientRect().top + window.scrollY - nav.offsetHeight - 8, behavior: 'smooth' }); }); });
  function spy() { var cur = 0; secs.forEach(function (s, i) { if (s.getBoundingClientRect().top - nav.offsetHeight - 20 <= 0) cur = i; }); links.forEach(function (a, i) { a.classList.toggle('on', i === cur); }); }
  window.addEventListener('scroll', spy, { passive: true }); spy();
})();
</script>

<?php if ($related): ?>
<section class="corner-section last-of-type:mb-0 pc:mx-auto pc:w-large pc:mb-[80px] pc:mt-[80px]">
  <div class="section-title pc:heading2 mb-2.5 title1 font-bold pc:mb-[40px]"><div class="flex items-center justify-between"><h2 class="title1 mo:head-line1 font-bold"><?= t('같은 카테고리') ?> <br><?= t('다른 상품도 함께') ?></h2><?php if ($category): ?><a href="<?= site_url('category/' . $category['id']) ?>" class="h-auto my-auto body2 btn-arrow-right after-bg-gray5 text-gray3"><?= esc(t($category['name'])) ?> <?= t('더보기') ?></a><?php endif ?></div></div>
  <ul class="plist flex flex-wrap gap-x-[27px] gap-y-9"><?php foreach ($related as $r): echo view('_card', ['p' => $r, 'approved' => $approved, 'sup' => null, 'w' => 'w-[234px]']); endforeach ?></ul>
</section>
<?php endif ?>

<?php if ($user && $user['role'] === 'buyer'): ?>
<div class="modal" id="inqModal"><div class="in">
  <h3 class="font-bold"><?= t('상품 문의') ?></h3>
  <form method="post" action="<?= site_url('inquiry') ?>"><?= csrf_field() ?><input type="hidden" name="product_id" value="<?= $p['id'] ?>">
    <div class="field"><label><?= t('상품') ?></label><div class="small"><?= esc(mm_pname($p)) ?></div></div>
    <div class="field"><label><?= t('문의 내용') ?></label><textarea class="ta" name="body" placeholder="<?= t('수량, 납기, 샘플, 배송 등 궁금한 점을 적어 주세요.') ?>"></textarea></div>
    <div class="mmrow"><button class="btn btn-g" type="submit"><?= t('문의 보내기') ?></button><button class="btn close" type="button"><?= t('닫기') ?></button></div>
  </form>
</div></div>
<?php endif ?>
