<div class="kpi">
  <?php foreach ($kpi as $l => $v): ?><div><?= $l ?><b class="<?= ($l === '검수 대기 구매자' || $l === '입금 확인 필요') && $v > 0 ? 'warn' : '' ?>"><?= number_format($v) ?></b></div><?php endforeach ?>
</div>
<?php $g = $growth; $sym = mm_setting('currency_symbol', 'Ks'); $rl = $g['ranges'][$g['range']];
$cards = [
  'signups' => ['신규 가입', '명', 'bar'],
  'orders'  => ['주문 수', '건', 'bar'],
  'amount'  => ['주문 금액 (입금 확인)', $sym, 'line'],
]; ?>
<section class="gr">
  <div class="gr-h"><h2>플랫폼 성장</h2><span class="gr-sub">직전 같은 기간 대비</span>
    <div class="gr-tabs"><?php foreach ($g['ranges'] as $k => $r): ?><a href="?range=<?= $k ?>" class="<?= $g['range'] === $k ? 'on' : '' ?>"><?= $r['label'] ?></a><?php endforeach ?></div>
  </div>
  <div class="gr-grid">
    <?php foreach ($cards as $key => [$label, $unit, $type]): $m = $g[$key]; ?>
    <div class="gr-card">
      <div class="gr-label"><?= $label ?></div>
      <div class="gr-num"><?= number_format($m['total']) ?><small><?= esc($unit) ?></small></div>
      <div class="gr-cmp">
        <?php if ($m['growth'] === null): ?><span class="gr-badge flat">직전 기간 데이터 없음</span>
        <?php else: $up = $m['growth'] >= 0; ?><span class="gr-badge <?= $up ? 'up' : 'down' ?>"><?= $up ? '▲' : '▼' ?> <?= abs($m['growth']) ?>%</span><?php endif ?>
        <span>직전 <?= number_format($m['prev']) ?><?= esc($unit) ?></span>
      </div>
      <div class="gr-chart"><canvas data-key="<?= $key ?>" data-type="<?= $type ?>" data-unit="<?= esc($unit) ?>" aria-label="<?= $label ?> <?= $rl['unit'] ?>별 추이"></canvas></div>
    </div>
    <?php endforeach ?>
  </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
  var labels = <?= json_encode($g['labels']) ?>;
  var data = <?= json_encode(['signups' => $g['signups']['series'], 'orders' => $g['orders']['series'], 'amount' => $g['amount']['series']]) ?>;
  var C = '#ff6200', GRID = '#eeeeee', TXT = '#888888';
  function fmt(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
  function short(n) { return n >= 1e6 ? (n / 1e6).toFixed(n % 1e6 ? 1 : 0) + 'M' : n >= 1e3 ? (n / 1e3).toFixed(0) + 'K' : n; }
  document.querySelectorAll('.gr-chart canvas').forEach(function (cv) {
    var key = cv.dataset.key, line = cv.dataset.type === 'line', unit = cv.dataset.unit;
    new Chart(cv, {
      type: line ? 'line' : 'bar',
      data: { labels: labels, datasets: [{
        data: data[key], backgroundColor: line ? 'rgba(255,98,0,.08)' : C, borderColor: C,
        borderWidth: line ? 2 : 0, borderRadius: 4, maxBarThickness: 18, fill: line,
        pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: C, pointHoverBorderColor: '#fff', pointHoverBorderWidth: 2, tension: .3
      }] },
      options: {
        responsive: true, maintainAspectRatio: false, animation: false,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { display: false }, tooltip: { displayColors: false, padding: 10, callbacks: { label: function (c) { return fmt(c.parsed.y) + ' ' + unit; } } } },
        scales: {
          x: { grid: { display: false }, border: { color: GRID }, ticks: { color: TXT, font: { size: 11 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 7 } },
          y: { beginAtZero: true, grid: { color: GRID }, border: { display: false }, ticks: { color: TXT, font: { size: 11 }, precision: 0, maxTicksLimit: 5, callback: function (v) { return short(v); } } }
        }
      }
    });
  });
})();
</script>
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
