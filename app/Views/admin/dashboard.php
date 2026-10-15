<div class="kpi">
  <?php foreach ($kpi as $l => $v): ?><div><?= $l ?><b class="<?= ($l === '검수 대기 구매자' || $l === '신청 접수(금액 안내 필요)' || $l === '입금 확인 필요') && $v > 0 ? 'warn' : '' ?>"><?= number_format($v) ?></b></div><?php endforeach ?>
</div>
<div class="two" style="grid-template-columns:1fr 1fr">
  <div class="card">
    <h3>검수 대기 구매자 <a class="btn btn-xs" href="<?= site_url('admin/buyers?status=pending') ?>" style="float:right">전체</a></h3>
    <?php if (! $pendingBuyers): ?><div class="muted">대기 중인 회원이 없습니다.</div><?php else: ?>
    <table class="tbl"><thead><tr><th class="l">상호</th><th>지역</th><th>가입</th><th></th></tr></thead><tbody>
    <?php foreach ($pendingBuyers as $b): ?><tr><td class="l"><b><?= esc($b['company_name']) ?></b><br><span class="muted"><?= esc($b['name']) ?> · <?= esc($b['phone']) ?></span></td><td><?= esc($b['city']) ?></td><td><?= mm_dt($b['created_at'], 'm.d H:i') ?></td><td><a class="btn btn-xs btn-g" href="<?= site_url('admin/buyers/' . $b['id']) ?>">검수</a></td></tr><?php endforeach ?>
    </tbody></table><?php endif ?>
  </div>
  <div class="card">
    <h3>최근 주문 <a class="btn btn-xs" href="<?= site_url('admin/orders') ?>" style="float:right">전체</a></h3>
    <?php if (! $recentOrders): ?><div class="muted">주문이 없습니다.</div><?php else: ?>
    <table class="tbl"><thead><tr><th>주문번호</th><th class="l">구매자</th><th>금액</th><th>상태</th></tr></thead><tbody>
    <?php foreach ($recentOrders as $o): $b = $buyersMap[$o['user_id']] ?? null; ?><tr><td><a href="<?= site_url('admin/orders/' . $o['id']) ?>"><b><?= esc($o['order_no']) ?></b></a></td><td class="l"><?= esc($b['company_name'] ?? '-') ?></td><td><?= mm_price($o['total']) ?></td><td><span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></td></tr><?php endforeach ?>
    </tbody></table><?php endif ?>
  </div>
</div>
