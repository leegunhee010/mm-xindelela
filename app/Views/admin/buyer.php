<div class="two">
<div>
  <div class="card">
    <h3><?= esc($b['company_name']) ?> <span class="st st-<?= esc($b['status']) ?>"><?= mm_status_label($b['status']) ?></span></h3>
    <table class="kv">
      <tr><th>담당자</th><td><?= esc($b['name']) ?> · <?= esc($b['email']) ?></td></tr>
      <tr><th>연락처</th><td>전화 <?= esc($b['phone']) ?> · Viber <?= esc($b['viber'] ?: $b['phone']) ?></td></tr>
      <tr><th>판매 형태</th><td><?= ['online' => '온라인', 'offline' => '오프라인 매장', 'both' => '온·오프라인'][$b['business_type']] ?? '-' ?></td></tr>
      <tr><th>지역·주소</th><td><?= esc($b['city']) ?> <?= esc($b['address']) ?></td></tr>
      <tr><th>페이스북 페이지</th><td><?php if ($b['fb_page']): ?><a href="<?= esc($b['fb_page']) ?>" target="_blank" style="color:var(--navy);text-decoration:underline"><?= esc($b['fb_page']) ?></a> <a class="btn btn-xs" href="<?= esc($b['fb_page']) ?>" target="_blank">새 창</a><?php else: ?>-<?php endif ?></td></tr>
      <tr><th>추천인</th><td><?php if ($referrer): ?><a href="<?= site_url('admin/buyers/' . $referrer['id']) ?>"><?= esc($referrer['company_name']) ?></a> (<?= esc($referrer['referral_code']) ?>)<?php else: ?>-<?php endif ?></td></tr>
      <tr><th>내 추천코드</th><td><?= esc($b['referral_code']) ?> · 소개한 회원 <?= count($referred) ?>명</td></tr>
      <tr><th>가입 / 승인</th><td><?= mm_dt($b['created_at']) ?> / <?= mm_dt($b['approved_at']) ?></td></tr>
      <?php if ($b['reject_reason']): ?><tr><th>반려 사유</th><td style="color:var(--price)"><?= nl2br(esc($b['reject_reason'])) ?></td></tr><?php endif ?>
    </table>
  </div>
  <div class="card">
    <h3>제출 자료 <?= count($docs) ?>건</h3>
    <?php if (! $docs): ?><div class="muted">업로드된 자료가 없습니다. (페이스북 페이지만 제출)</div><?php else: ?>
    <div class="docgrid"><?php foreach ($docs as $d): $isImg = preg_match('/\.(jpe?g|png|webp|gif)$/i', $d['path']); ?><a href="<?= base_url($d['path']) ?>" target="_blank"><?php if ($isImg): ?><img src="<?= base_url($d['path']) ?>" alt=""><?php else: ?><div style="height:120px;display:flex;align-items:center;justify-content:center;background:var(--bg);font-size:24px">PDF</div><?php endif ?><?= esc($d['name']) ?></a><?php endforeach ?></div>
    <?php endif ?>
  </div>
  <div class="card">
    <h3>주문 <?= count($orders) ?>건 (최근)</h3>
    <?php if ($orders): ?><table class="tbl"><thead><tr><th>주문번호</th><th>신청일</th><th>금액</th><th>상태</th></tr></thead><tbody><?php foreach ($orders as $o): ?><tr><td><a href="<?= site_url('admin/orders/' . $o['id']) ?>"><?= esc($o['order_no']) ?></a></td><td><?= mm_dt($o['created_at']) ?></td><td><?= mm_price($o['total']) ?></td><td><span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></td></tr><?php endforeach ?></tbody></table><?php else: ?><div class="muted">주문 없음</div><?php endif ?>
  </div>
  <div class="card">
    <h3>포인트 내역 (최근)</h3>
    <?php if ($points): ?><table class="tbl"><thead><tr><th>일시</th><th class="l">내용</th><th>증감</th><th>잔액</th></tr></thead><tbody><?php foreach ($points as $l): ?><tr><td><?= mm_dt($l['created_at']) ?></td><td class="l"><?= mm_point_type_label($l['type']) ?> <span class="muted"><?= esc($l['memo']) ?></span></td><td class="<?= $l['amount'] >= 0 ? 'pt-plus' : 'pt-minus' ?>"><?= $l['amount'] >= 0 ? '+' : '' ?><?= number_format((int) $l['amount']) ?></td><td><?= number_format((int) $l['balance_after']) ?></td></tr><?php endforeach ?></tbody></table><?php else: ?><div class="muted">내역 없음</div><?php endif ?>
  </div>
</div>
<div>
  <?php if ($b['status'] !== 'approved'): ?>
  <div class="act" style="border:2px solid var(--green)">
    <h3>검수 결과</h3>
    <form method="post" action="<?= site_url('admin/buyers/' . $b['id'] . '/approve') ?>" onsubmit="return confirm('승인할까요? 도매가와 주문 신청이 열리고, 추천인 포인트가 지급됩니다.')"><?= csrf_field() ?><button class="btn btn-g btn-block" type="submit" style="height:44px;font-size:15px">승인</button></form>
    <form method="post" action="<?= site_url('admin/buyers/' . $b['id'] . '/reject') ?>" class="mt8"><?= csrf_field() ?>
      <textarea class="ta" name="reason" style="height:70px" placeholder="반려 사유 (회원에게 그대로 표시). 예: 페이스북 페이지에서 판매 활동을 확인할 수 없습니다. 매장 사진이나 판매 글 캡처를 추가로 올려 주세요."><?= esc($b['reject_reason']) ?></textarea>
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
  <?php if ($referred): ?>
  <div class="act"><h3>소개한 회원</h3><ul class="doclist"><?php foreach ($referred as $r): ?><li><a href="<?= site_url('admin/buyers/' . $r['id']) ?>"><?= esc($r['company_name']) ?></a><span class="st st-<?= esc($r['status']) ?>"><?= mm_status_label($r['status']) ?></span></li><?php endforeach ?></ul></div>
  <?php endif ?>
</div>
</div>
