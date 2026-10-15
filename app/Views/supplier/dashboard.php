<div class="kpi" style="grid-template-columns:repeat(4,1fr)">
  <div>판매중 상품<b><?= $kpi['active'] ?></b></div><div>검수 대기 상품<b><?= $kpi['pending'] ?></b></div><div>출고 필요 주문<b class="<?= $kpi['toship'] ? 'warn' : '' ?>"><?= $kpi['toship'] ?></b></div><div>누적 주문<b><?= $kpi['orders'] ?></b></div>
</div>
<div class="note">흐름: 상품 등록 → 관리자 검수 → 판매중 노출 → 구매자 주문·입금 확인(관리자) → <b>주문·출고</b> 메뉴에 표시 → 출고 처리. 입금 확인 전 주문은 보이지 않습니다.</div>
<div class="card"><h3>최근 주문 <a class="btn btn-xs" href="<?= site_url('supplier/orders') ?>" style="float:right">전체</a></h3>
<?php $vis = array_filter($recent, static fn ($o) => in_array($o['status'], ['paid', 'preparing', 'shipped', 'delivered'], true)); ?>
<?php if (! $vis): ?><div class="muted">입금 확인된 주문이 아직 없습니다.</div><?php else: ?>
<table class="tbl"><thead><tr><th>주문번호</th><th>신청일</th><th>상태</th><th></th></tr></thead><tbody>
<?php foreach ($vis as $o): ?><tr><td><b><?= esc($o['order_no']) ?></b></td><td><?= mm_dt($o['created_at']) ?></td><td><span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></td><td><a class="btn btn-xs" href="<?= site_url('supplier/orders/' . $o['id']) ?>">상세</a></td></tr><?php endforeach ?>
</tbody></table><?php endif ?></div>
<div class="mmrow"><a class="btn btn-g" href="<?= site_url('supplier/products/new') ?>">+ 상품 등록</a><a class="btn" href="<?= site_url('supplier/products') ?>">상품 관리</a></div>
