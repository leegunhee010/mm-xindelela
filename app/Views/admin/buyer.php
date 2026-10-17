<div class="two">
<div>
  <?php $sym = mm_setting('currency_symbol', 'Ks'); $cartSum = array_sum(array_column($cart, 'line'));
  $tabs = ['info' => '기본 정보', 'orders' => '주문 내역 ' . count($orders), 'cart' => '장바구니 ' . count($cart), 'points' => '포인트', 'referral' => '추천 ' . count($referred)]; ?>
  <div class="mhead">
    <div class="mhead-t"><h2><?= esc($b['company_name']) ?></h2><span class="st st-<?= esc($b['status']) ?>"><?= mm_status_label($b['status']) ?></span><span class="mhead-id">#<?= $b['id'] ?> · <?= esc($b['email']) ?></span></div>
    <div class="mstat">
      <a href="?tab=orders"><span>주문 (취소 제외)</span><b><?= number_format($stat['n']) ?>건</b></a>
      <a href="?tab=orders"><span>입금 확인 금액</span><b><?= mm_price($stat['paid']) ?></b></a>
      <a href="?tab=cart"><span>장바구니</span><b><?= count($cart) ?>종<?php if ($cartSum): ?> <small><?= mm_price($cartSum) ?></small><?php endif ?></b></a>
      <a href="?tab=points"><span>포인트</span><b><?= number_format((int) $b['points_balance']) ?>P</b></a>
      <a href="?tab=referral"><span>소개한 회원</span><b><?= count($referred) ?>명</b></a>
      <div><span>마지막 주문</span><b><?= $stat['last'] ? mm_dt($stat['last'], 'Y.m.d') : '-' ?></b></div>
    </div>
    <div class="mtabs"><?php foreach ($tabs as $k => $l): ?><a href="?tab=<?= $k ?>" class="<?= $tab === $k ? 'on' : '' ?>"><?= $l ?></a><?php endforeach ?></div>
  </div>

  <?php if ($tab === 'info'): ?>
  <div class="card">
    <table class="kv">
      <tr><th>담당자</th><td><?= esc($b['name']) ?> · <?= esc($b['email']) ?></td></tr>
      <tr><th>연락처</th><td>전화 <?= esc($b['phone']) ?> · Viber <?= esc($b['viber'] ?: $b['phone']) ?></td></tr>
      <tr><th>인증 유형</th><td><b><?= mm_verify_labels($b['verify_types']) ?></b></td></tr>
      <tr><th>지역·주소</th><td><?= esc($b['city']) ?> <?= esc($b['address']) ?></td></tr>
      <tr><th>판매 채널 링크</th><td><?php if ($b['fb_page']): ?><a href="<?= esc($b['fb_page']) ?>" target="_blank" style="color:var(--navy);text-decoration:underline"><?= esc($b['fb_page']) ?></a> <a class="btn btn-xs" href="<?= esc($b['fb_page']) ?>" target="_blank">새 창</a><?php else: ?>-<?php endif ?></td></tr>
      <tr><th>추천인</th><td><?php if ($referrer): ?><a href="<?= site_url('admin/buyers/' . $referrer['id']) ?>"><?= esc($referrer['company_name']) ?></a> (<?= esc($referrer['referral_code']) ?>)<?php else: ?>-<?php endif ?></td></tr>
      <tr><th>내 추천코드</th><td><?= esc($b['referral_code']) ?></td></tr>
      <tr><th>가입 / 승인</th><td><?= mm_dt($b['created_at']) ?> / <?= mm_dt($b['approved_at']) ?></td></tr>
      <tr><th>최근 로그인</th><td><?= mm_dt($b['last_login_at']) ?></td></tr>
      <?php if ($b['reject_reason']): ?><tr><th>반려 사유</th><td style="color:var(--price)"><?= nl2br(esc($b['reject_reason'])) ?></td></tr><?php endif ?>
    </table>
  </div>
  <div class="card">
    <h3>제출 자료 <?= count($docs) ?>건</h3>
    <?php if (! $docs): ?><div class="muted">업로드된 자료가 없습니다. (판매 채널 링크만 제출)</div><?php else: ?>
    <div class="docgrid"><?php foreach ($docs as $d): $isImg = preg_match('/\.(jpe?g|png|webp|gif)$/i', $d['path']); ?><a href="<?= base_url($d['path']) ?>" target="_blank"><?php if ($isImg): ?><img src="<?= base_url($d['path']) ?>" alt=""><?php else: ?><div style="height:120px;display:flex;align-items:center;justify-content:center;background:var(--bg);font-size:24px">PDF</div><?php endif ?><?= esc($d['name']) ?></a><?php endforeach ?></div>
    <?php endif ?>
  </div>

  <?php elseif ($tab === 'orders'): ?>
  <div class="card">
    <?php if (! $orders): ?><div class="muted mempty">주문이 없습니다.</div><?php else: ?>
    <table class="tbl"><thead><tr><th>주문번호</th><th>신청일</th><th>결제 금액</th><th>포인트</th><th class="l">송금자</th><th>상태</th></tr></thead><tbody>
    <?php foreach ($orders as $o): ?><tr><td><a href="<?= site_url('admin/orders/' . $o['id']) ?>"><b><?= esc($o['order_no']) ?></b></a></td><td><?= mm_dt($o['created_at']) ?></td><td><?= mm_price($o['total']) ?></td><td><?= (int) $o['points_used'] ? '-' . number_format((int) $o['points_used']) . 'P' : '-' ?></td><td class="l"><?= esc($o['payment_note'] ?: '-') ?></td><td><span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></td></tr><?php endforeach ?>
    </tbody></table><?php endif ?>
  </div>

  <?php elseif ($tab === 'cart'): ?>
  <div class="card">
    <?php if (! $cart): ?><div class="muted mempty">장바구니가 비어 있습니다.</div><?php else: ?>
    <table class="tbl"><thead><tr><th class="l">상품</th><th>수량</th><th>단가</th><th>금액</th><th>담은 날</th></tr></thead><tbody>
    <?php foreach ($cart as $it): ?><tr><td class="l"><a href="<?= site_url('admin/products/' . $it['p']['id']) ?>"><?= esc($it['p']['name']) ?></a></td><td><?= number_format((int) $it['c']['qty']) ?><?= esc($it['p']['unit']) ?></td><td><?= mm_price($it['unit']) ?></td><td><b><?= mm_price($it['line']) ?></b></td><td><?= mm_dt($it['c']['created_at'] ?? null, 'm.d H:i') ?></td></tr><?php endforeach ?>
    <tr><td class="l"><b>합계</b></td><td></td><td></td><td><b><?= mm_price($cartSum) ?></b></td><td></td></tr>
    </tbody></table><?php endif ?>
  </div>

  <?php elseif ($tab === 'points'): ?>
  <div class="card">
    <?php if (! $points): ?><div class="muted mempty">포인트 내역이 없습니다.</div><?php else: ?>
    <table class="tbl"><thead><tr><th>일시</th><th class="l">내용</th><th>증감</th><th>잔액</th></tr></thead><tbody><?php foreach ($points as $l): ?><tr><td><?= mm_dt($l['created_at']) ?></td><td class="l"><?= mm_point_type_label($l['type']) ?> <span class="muted"><?= esc($l['memo']) ?></span></td><td class="<?= $l['amount'] >= 0 ? 'pt-plus' : 'pt-minus' ?>"><?= $l['amount'] >= 0 ? '+' : '' ?><?= number_format((int) $l['amount']) ?></td><td><?= number_format((int) $l['balance_after']) ?></td></tr><?php endforeach ?></tbody></table><?php endif ?>
  </div>

  <?php else: ?>
  <div class="card">
    <h3>추천인</h3>
    <?php if ($referrer): ?><p class="mref"><a href="<?= site_url('admin/buyers/' . $referrer['id']) ?>"><b><?= esc($referrer['company_name']) ?></b></a> <span class="muted"><?= esc($referrer['referral_code']) ?></span> <span class="st st-<?= esc($referrer['status']) ?>"><?= mm_status_label($referrer['status']) ?></span></p><?php else: ?><div class="muted">추천인 없이 가입했습니다.</div><?php endif ?>
  </div>
  <div class="card">
    <h3>이 회원이 소개한 회원 <?= count($referred) ?>명 <span class="muted" style="font-weight:400">추천코드 <?= esc($b['referral_code']) ?></span></h3>
    <?php if (! $referred): ?><div class="muted">아직 없습니다.</div><?php else: ?>
    <table class="tbl"><thead><tr><th class="l">회원</th><th>가입일</th><th>인증</th><th>주문</th><th>입금 확인 금액</th></tr></thead><tbody>
    <?php foreach ($referred as $r): $rs = $refStats[$r['id']] ?? null; ?><tr><td class="l"><a href="<?= site_url('admin/buyers/' . $r['id']) ?>"><?= esc($r['company_name']) ?></a></td><td><?= mm_dt($r['created_at'], 'Y.m.d') ?></td><td><span class="st st-<?= esc($r['status']) ?>"><?= mm_status_label($r['status']) ?></span></td><td><?= $rs ? $rs['n'] . '건' : '-' ?></td><td><?= $rs && $rs['paid'] ? mm_price($rs['paid']) : '-' ?></td></tr><?php endforeach ?>
    </tbody></table><?php endif ?>
  </div>
  <?php endif ?>
</div>
<div>
  <?php if ($b['status'] !== 'approved'): ?>
  <div class="act" style="border:2px solid var(--pri)">
    <h3>검수 기준</h3>
    <div class="vchk">
      <p class="vchk-rule">세 가지 중 <b>하나만</b> 확인되면 승인합니다. 사진·캡처도 인정합니다.</p>
      <?php $vts = mm_verify_types(); $mine = array_filter(explode(',', (string) $b['verify_types'])); foreach ($vts as $k => $t): if ($mine && ! in_array($k, $mine, true)) { continue; } ?>
      <div class="vchk-t"><b><?= $t['label'] ?></b><ul><?php foreach ($t['check'] as $c): ?><li><?= $c ?></li><?php endforeach ?></ul></div>
      <?php endforeach ?>
    </div>
    <h3 style="margin-top:14px">검수 결과</h3>
    <form method="post" action="<?= site_url('admin/buyers/' . $b['id'] . '/approve') ?>" onsubmit="return confirm('승인할까요? 도매가와 주문 신청이 열리고, 추천인 포인트가 지급됩니다.')"><?= csrf_field() ?><button class="btn btn-g btn-block" type="submit" style="height:44px;font-size:15px">승인</button></form>
    <form method="post" action="<?= site_url('admin/buyers/' . $b['id'] . '/reject') ?>" class="mt8"><?= csrf_field() ?>
      <div class="vrej"><?php foreach (mm_verify_reject_reasons() as $r): ?><button type="button" data-r="<?= esc($r) ?>"><?= esc(mb_strimwidth($r, 0, 34, '…')) ?></button><?php endforeach ?></div>
      <textarea class="ta" id="rejReason" name="reason" style="height:70px" placeholder="반려 사유 (회원에게 그대로 표시). 위 문구를 누르면 채워집니다."><?= esc($b['reject_reason']) ?></textarea>
      <button class="btn btn-red btn-block mt8" type="submit">반려 (자료 재요청)</button>
    </form>
  </div>
  <?php endif ?>
  <div class="act">
    <h3>상태 변경</h3>
    <form method="post" action="<?= site_url('admin/buyers/' . $b['id'] . '/status') ?>" class="inl"><?= csrf_field() ?>
      <select class="sel" name="status"><?php foreach (['pending', 'approved', 'rejected', 'suspended'] as $s): ?><option value="<?= $s ?>" <?= $b['status'] === $s ? 'selected' : '' ?>><?= mm_status_label($s) ?></option><?php endforeach ?></select>
      <button class="btn btn-sm" type="submit">변경</button>
    </form>
    <div class="small muted mt8">정지 = 로그인 차단. 승인 → 정지 → 다시 승인해도 포인트는 중복 지급되지 않습니다.</div>
  </div>
  <div class="act">
    <h3>포인트 조정 <span class="muted" style="font-weight:400">잔액 <?= number_format((int) $b['points_balance']) ?>P</span></h3>
    <form method="post" action="<?= site_url('admin/buyers/' . $b['id'] . '/points') ?>"><?= csrf_field() ?>
      <div class="inl"><input class="inp" type="number" name="amount" placeholder="+지급 / -차감" style="width:130px"><input class="inp" name="memo" placeholder="사유" style="width:180px"></div>
      <button class="btn btn-sm mt8" type="submit">적용</button>
    </form>
  </div>
  <div class="act">
    <h3>관리자 메모</h3>
    <form method="post" action="<?= site_url('admin/buyers/' . $b['id'] . '/note') ?>"><?= csrf_field() ?><textarea class="ta" name="admin_note" style="height:90px"><?= esc($b['admin_note']) ?></textarea><button class="btn btn-sm mt8" type="submit">저장</button></form>
  </div>
</div>
  <?= view('admin/_history', ['type' => 'buyer', 'id' => (int) $b['id']]) ?>
</div>

<script>document.querySelectorAll('.vrej button').forEach(function (b) { b.onclick = function () { document.getElementById('rejReason').value = b.dataset.r; }; });</script>
