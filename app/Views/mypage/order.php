<?php
$flow = ['requested' => '신청 접수', 'quoted' => '금액 안내', 'proof' => '입금 확인중', 'paid' => '입금 확인', 'preparing' => '발주·준비', 'shipped' => '배송중', 'delivered' => '배송 완료'];
$idx  = array_search($o['status'], array_keys($flow), true);
?>
<div class="my">
<?= view('mypage/_side') ?>
<div class="main">
  <h1>주문 상세 <span class="muted" style="font-size:13px;font-weight:400"><?= esc($o['order_no']) ?></span> <span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></h1>
  <?php if ($o['status'] !== 'cancelled'): ?>
  <div class="timeline"><?php foreach (array_values($flow) as $i => $l): ?><div class="<?= $i < $idx ? 'done' : ($i === $idx ? 'on' : '') ?>"><?= $l ?></div><?php endforeach ?></div>
  <?php endif ?>

  <?php if ($o['status'] === 'requested'): ?>
    <div class="vstat"><h2>신청이 접수되었습니다</h2><p>담당자가 재고·배송비를 확인한 뒤 최종 금액을 안내해 드립니다. 안내 전까지는 취소할 수 있습니다.</p></div>
  <?php elseif ($o['status'] === 'quoted'): ?>
    <div class="vstat" style="border-color:var(--green)"><h2>최종 금액 <?= mm_price($o['total']) ?> 을 KBZPay로 송금해 주세요</h2>
      <div class="acct"><?= esc(mm_setting('kbzpay_account', '')) ?></div>
      <?php if ($o['admin_memo']): ?><p class="mt8">담당자 안내: <?= nl2br(esc($o['admin_memo'])) ?></p><?php endif ?>
      <form class="mt16" method="post" action="<?= site_url('mypage/orders/' . $o['id'] . '/proof') ?>" enctype="multipart/form-data"><?= csrf_field() ?>
        <div class="field"><label>송금 캡처 이미지 <i>*</i></label><input type="file" name="proof" accept="image/*" required></div>
        <div class="field"><label>송금자명·메모</label><input class="inp" name="payment_note" placeholder="송금한 KBZPay 계정 이름"></div>
        <button class="btn btn-g" type="submit">입금 캡처 올리기</button>
      </form>
    </div>
  <?php elseif ($o['status'] === 'proof'): ?>
    <div class="vstat"><h2>입금 캡처 확인 중</h2><p>담당자가 입금을 확인하면 바로 발주됩니다. 다른 캡처로 바꾸려면 아래에서 다시 올려 주세요.</p>
      <?php if ($o['payment_proof']): ?><a href="<?= base_url($o['payment_proof']) ?>" target="_blank"><img src="<?= base_url($o['payment_proof']) ?>" alt="" style="max-width:240px;border:1px solid var(--line);margin:8px 0"></a><?php endif ?>
      <form method="post" action="<?= site_url('mypage/orders/' . $o['id'] . '/proof') ?>" enctype="multipart/form-data" class="mmrow"><?= csrf_field() ?><input type="file" name="proof" accept="image/*" required><input class="inp" name="payment_note" value="<?= esc($o['payment_note']) ?>" placeholder="송금자명" style="width:200px"><button class="btn btn-sm" type="submit">다시 올리기</button></form>
    </div>
  <?php elseif ($o['status'] === 'shipped'): ?>
    <div class="vstat"><h2>배송 중입니다</h2><p>송장·배송 정보: <?= esc($o['tracking'] ?: '담당자 안내 예정') ?></p></div>
  <?php elseif ($o['status'] === 'cancelled'): ?>
    <div class="vstat"><h2>취소된 주문입니다</h2><p>사용한 포인트는 환원되었습니다.</p></div>
  <?php endif ?>

  <table class="kv">
    <tr><th>주문번호</th><td><?= esc($o['order_no']) ?> · <?= mm_dt($o['created_at']) ?></td></tr>
    <tr><th>결제 방법</th><td><?= mm_payment_label($o['payment_method']) ?></td></tr>
    <tr><th>배송지</th><td><?= esc($o['ship_name']) ?> · <?= esc($o['ship_phone']) ?><br><?= esc($o['ship_city']) ?> <?= esc($o['ship_address']) ?></td></tr>
    <?php if ($o['memo']): ?><tr><th>요청 사항</th><td><?= nl2br(esc($o['memo'])) ?></td></tr><?php endif ?>
    <?php if ($o['tracking']): ?><tr><th>배송 정보</th><td><?= esc($o['tracking']) ?></td></tr><?php endif ?>
  </table>

  <div class="oitems mt24">
    <?php foreach ($items as $it): ?><div class="it"><img src="<?= mm_thumb($it['thumb']) ?>" alt=""><div class="n"><a href="<?= site_url('products/' . $it['product_id']) ?>"><?= esc($it['product_name']) ?></a><div class="s">단가 <?= mm_price($it['unit_price']) ?> · <?= $it['item_status'] === 'shipped' ? '<span class="st st-shipped">출고</span>' : '' ?></div></div><div class="q"><?= number_format((int) $it['qty']) ?>개</div><div class="p"><?= mm_price($it['line_total']) ?></div></div><?php endforeach ?>
  </div>
  <div class="card mt16" style="background:var(--bg2)">
    <div class="mmrow sp"><span>상품 합계</span><span><?= mm_price($o['subtotal']) ?></span></div>
    <div class="mmrow sp"><span>포인트 사용</span><span>-<?= number_format((int) $o['points_used']) ?> P</span></div>
    <div class="mmrow sp"><span>배송비</span><span><?= $o['status'] === 'requested' ? '안내 예정' : mm_price($o['shipping_fee']) ?></span></div>
    <div class="mmrow sp" style="border-top:1px solid var(--line);padding-top:8px;margin-top:8px;font-weight:700;font-size:15px"><span><?= $o['status'] === 'requested' ? '예상 금액' : '최종 금액' ?></span><span style="color:var(--price);font-size:20px"><?= mm_price($o['total']) ?></span></div>
  </div>
  <?php if (in_array($o['status'], ['requested', 'quoted'], true)): ?>
  <form class="mt16" method="post" action="<?= site_url('mypage/orders/' . $o['id'] . '/cancel') ?>" onsubmit="return confirm('주문 신청을 취소할까요?')"><?= csrf_field() ?><button class="btn btn-red btn-sm" type="submit">주문 신청 취소</button></form>
  <?php endif ?>
</div>
</div>
