<div class="guide">
  <h1>이용안내</h1>
  <p>XINDELELA 미얀마 도매몰은 미얀마 소매상·온라인 셀러가 한국 K-브랜드 제품을 박스 단위 도매가로 신청하는 회원제 폐쇄몰입니다. 상품은 누구나 볼 수 있지만, 도매가와 주문 신청은 사업자 인증을 받은 회원에게만 열립니다.</p>

  <h2>1. 회원가입과 사업자 인증</h2>
  <p>도매가는 실제로 물건을 파는 분께만 열립니다. 아래 <b>세 가지 중 하나만</b> 증빙하면 됩니다. 사진이나 휴대폰 캡처도 괜찮습니다.</p>
  <table class="vguide"><thead><tr><th>유형</th><th>이런 분</th><th>올릴 자료 (하나 이상)</th></tr></thead><tbody>
  <?php foreach (mm_verify_types() as $t): ?><tr><td><b><?= $t['label'] ?></b></td><td><?= $t['desc'] ?></td><td><?= implode('<br>', $t['docs']) ?></td></tr><?php endforeach ?>
  </tbody></table>
  <ol>
    <li>회원가입 화면에서 상호, 담당자, 연락처, 지역을 입력하고 해당하는 유형을 고릅니다.</li>
    <li>판매 채널 링크를 적거나 사진·캡처를 올립니다.</li>
    <li>담당자가 직접 확인한 뒤 <b><?= esc(mm_setting('verify_sla_text')) ?></b> 승인합니다.</li>
    <li>자료가 부족하면 이유를 알려 드립니다. 마이페이지 &gt; 사업자 인증에서 자료를 추가해 다시 제출할 수 있습니다.</li>
  </ol>

  <h2>2. 도매가 확인과 주문 신청</h2>
  <ul>
    <li>승인 후 모든 상품의 도매가와 수량별 단가가 표시됩니다.</li>
    <li>상품은 최소주문수량부터 1개씩 담을 수 있으며, 수량이 많을수록 낮은 단가가 자동 적용됩니다.</li>
    <li>장바구니에서 주문 신청을 누르고 배송지를 입력하면 신청이 접수됩니다. 이 단계에서는 결제가 일어나지 않습니다.</li>
  </ul>

  <h2>3. 결제 (KBZPay 선불)</h2>
  <ol>
    <li>주문을 신청하면 결제 금액과 KBZPay 계좌가 안내됩니다. 그 금액을 KBZPay로 송금합니다. (<?= esc(mm_setting('kbzpay_account', '')) ?>)</li>
    <li>마이페이지 &gt; 주문 상세에서 송금한 사람 이름을 적고 입금 확인 요청을 누릅니다.</li>
    <li>입금이 확인되면 한국 공급사에 발주하고, 배송이 시작되면 상태가 바뀝니다.</li>
    <li>배송지는 입금 확인 전까지 주문 상세에서 직접 수정할 수 있습니다.</li>
  </ol>
  <p class="muted">착불(COD)은 준비 중입니다.</p>

  <h2>4. 포인트</h2>
  <ul>
    <li>내 추천 코드로 가입한 회원이 인증을 통과하면 <b><?= number_format((int) mm_setting('points_referral_signup', 0)) ?>P</b>, 그 회원이 첫 주문 입금을 완료하면 <b><?= number_format((int) mm_setting('points_referral_first_order', 0)) ?>P</b>가 적립됩니다.</li>
    <li>포인트는 주문 신청 시 상품 금액의 <?= (int) mm_setting('points_use_max_pct', 30) ?>%까지 차감 사용할 수 있습니다. (1P = 1 <?= esc(mm_setting('currency_symbol', 'Ks')) ?>)</li>
    <li>주문을 취소하면 사용한 포인트는 자동으로 환원됩니다.</li>
  </ul>

  <h2>고객센터</h2>
  <p>Viber <?= esc(mm_setting('cs_viber', '')) ?> · 이메일 <?= esc(mm_setting('cs_email', '')) ?> · 월~금 09:00~18:00</p>
</div>
