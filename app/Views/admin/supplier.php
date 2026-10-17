<div class="two">
<div>
  <div class="card"><h3><?= esc($s['company_name']) ?> <span class="st st-<?= esc($s['status']) ?>"><?= mm_status_label($s['status']) ?></span></h3>
    <table class="kv">
      <tr><th>브랜드</th><td><?= esc($s['brand']) ?></td></tr>
      <tr><th>소개</th><td><?= nl2br(esc($s['intro'])) ?></td></tr>
      <tr><th>담당자</th><td><?= esc(implode(' · ', array_filter([$s['contact_name'], $s['phone'], $u['email'] ?? '']))) ?: '-' ?></td></tr>
      <tr><th>주소</th><td><?= esc($s['address']) ?></td></tr>
      <tr><th>사업자번호</th><td><?= esc($s['biz_no']) ?></td></tr>
      <tr><th>홈페이지</th><td><?php if (! empty($s['website'])): ?><a href="<?= esc($s['website']) ?>" target="_blank" style="text-decoration:underline"><?= esc($s['website']) ?></a><?php else: ?>-<?php endif ?></td></tr>
      <tr><th>제출 자료</th><td><?php $sd = mm_json($s['doc_files'] ?? null); if ($sd): ?><ul class="doclist"><?php foreach ($sd as $d): ?><li><a href="<?= base_url($d['path']) ?>" target="_blank"><?= esc($d['name']) ?></a></li><?php endforeach ?></ul><?php else: ?>-<?php endif ?></td></tr>
      <tr><th>신청일</th><td><?= mm_dt($s['created_at']) ?></td></tr>
    </table></div>
  <div class="card"><h3>상품 <?= count($products) ?></h3>
    <?php if ($products): ?><table class="tbl"><thead><tr><th>ID</th><th class="l">상품</th><th>도매가</th><th>MOQ</th><th>상태</th></tr></thead><tbody><?php foreach ($products as $p): ?><tr><td><?= $p['id'] ?></td><td class="l"><a href="<?= site_url('admin/products/' . $p['id']) ?>"><?= esc($p['name']) ?></a></td><td><?= mm_price($p['price']) ?></td><td><?= $p['moq'] ?></td><td><span class="st st-<?= esc($p['status']) ?>"><?= mm_product_status_label($p['status']) ?></span></td></tr><?php endforeach ?></tbody></table><?php else: ?><div class="muted">상품 없음</div><?php endif ?></div>
</div>
<div>
  <div class="act"><a class="btn btn-g btn-block" href="<?= site_url('admin/suppliers/' . $s['id'] . '/edit') ?>">공급사 정보 수정</a><a class="btn btn-block mt8" href="<?= site_url('admin/products/new?supplier=' . $s['id']) ?>">이 공급사 상품 등록</a></div>
  <div class="act"><h3>거래 상태</h3>
    <form method="post" action="<?= site_url('admin/suppliers/' . $s['id'] . '/status') ?>" class="inl"><?= csrf_field() ?><select class="sel" name="status"><?php foreach (['pending', 'approved', 'rejected', 'suspended'] as $k): ?><option value="<?= $k ?>" <?= $s['status'] === $k ? 'selected' : '' ?>><?= mm_status_label($k) ?></option><?php endforeach ?></select><button class="btn btn-sm btn-g" type="submit">변경</button></form>
    <div class="small muted mt8">승인 외 상태로 바꾸면 이 공급사의 판매중 상품이 숨김 처리됩니다.</div></div>
  <div class="act"><h3>수수료율</h3>
    <form method="post" action="<?= site_url('admin/suppliers/' . $s['id'] . '/save') ?>" class="inl"><?= csrf_field() ?><input class="inp" type="number" step="0.1" name="commission_rate" value="<?= (float) $s['commission_rate'] ?>" style="width:90px"> %<button class="btn btn-sm" type="submit">저장</button></form></div>
</div>
</div>
