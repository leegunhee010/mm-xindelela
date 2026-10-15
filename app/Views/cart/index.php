<?php $sym = mm_setting('currency_symbol', 'Ks'); ?>
<div class="crumb"><a href="<?= site_url('/') ?>">홈</a>장바구니</div>
<div class="steps"><div class="on">1. 장바구니</div><div>2. 주문 신청</div><div>3. 금액 안내·KBZPay 송금</div><div>4. 입금 확인·발주</div><div>5. 배송</div></div>

<?php if (! $items): ?>
  <div class="empty">장바구니가 비어 있습니다. <a href="<?= site_url('products') ?>" style="color:var(--green-d);font-weight:700">상품 보러가기 ›</a></div>
<?php else: ?>
<form method="post" action="<?= site_url('cart/update') ?>" id="cartForm"><?= csrf_field() ?>
<table class="tbl">
  <thead><tr><th style="width:80px">이미지</th><th class="l">상품</th><th style="width:130px">단가</th><th style="width:130px">수량</th><th style="width:130px">합계</th><th style="width:70px">삭제</th></tr></thead>
  <tbody>
  <?php foreach ($items as $it): $p = $it['p']; ?>
    <tr>
      <td><img class="thumb" src="<?= mm_thumb($p['thumb']) ?>" alt=""></td>
      <td class="l"><a href="<?= site_url('products/' . $p['id']) ?>"><?= esc($p['name']) ?></a><div class="small muted mt8"><?= esc($it['supplier']['company_name'] ?? '') ?> · 최소 <?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?> 단위</div></td>
      <td><?= mm_price($it['unit'], false) ?> <?= esc($sym) ?><div class="small muted">수량별 단가 적용</div></td>
      <td><input class="inp tc" type="number" name="qty[<?= $it['cart']['id'] ?>]" value="<?= $it['qty'] ?>" min="<?= (int) $p['moq'] ?>" step="<?= (int) $p['moq'] ?>" style="width:90px;height:32px"></td>
      <td><b style="font-size:14px"><?= mm_price($it['line'], false) ?></b> <?= esc($sym) ?></td>
      <td><button class="btn btn-xs" type="submit" form="rm<?= $it['cart']['id'] ?>">삭제</button></td>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>
<div class="cart-sum">
  <span>상품 합계 <b><?= mm_price($subtotal, false) ?></b> <?= esc($sym) ?></span>
  <span class="small muted">배송비는 주문 신청 후 담당자가 안내합니다</span>
  <button class="btn" type="submit">수량 변경</button>
  <a class="btn btn-g btn-lg" href="<?= site_url('checkout') ?>" style="font-size:16px;height:48px">주문 신청하기</a>
</div>
</form>
<?php foreach ($items as $it): ?><form id="rm<?= $it['cart']['id'] ?>" method="post" action="<?= site_url('cart/remove') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $it['cart']['id'] ?>"></form><?php endforeach ?>
<?php endif ?>
