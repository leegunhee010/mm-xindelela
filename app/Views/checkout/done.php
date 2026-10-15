<div class="steps"><div class="done">1. 장바구니</div><div class="done">2. 주문 신청</div><div class="on">3. 금액 안내·KBZPay 송금</div><div>4. 입금 확인·발주</div><div>5. 배송</div></div>
<div class="done-box">
  <h2>주문 신청이 접수되었습니다</h2>
  <div class="no">주문번호<b><?= esc($o['order_no']) ?></b></div>
  <ol>
    <li>담당자가 재고와 배송비를 확인한 뒤 <b>최종 금액</b>을 마이페이지와 Viber로 안내합니다.</li>
    <li>안내받은 금액을 <b>KBZPay</b>로 송금해 주세요.</li>
    <li>마이페이지 &gt; 주문 상세에서 <b>송금 캡처</b>를 올려 주세요.</li>
    <li>입금 확인 즉시 한국 공급사에 발주하고, 배송이 시작되면 알려 드립니다.</li>
  </ol>
  <div class="acct"><?= esc(mm_setting('kbzpay_account', '')) ?></div>
  <div class="mmrow mt24" style="justify-content:center"><a class="btn btn-g" href="<?= site_url('mypage/orders/' . $o['id']) ?>">주문 상세 보기</a><a class="btn" href="<?= site_url('products') ?>">쇼핑 계속하기</a></div>
</div>
