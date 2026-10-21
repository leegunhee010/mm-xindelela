<?php $totN = array_sum(array_column($stats, 'n')); $totQ = array_sum(array_column($stats, 'qty')); ?>
<div class="rkpi">
  <div><span>입고 예정 상품</span><b><?= count($coming) ?><small>개</small></b><em>줄서기를 받는 중</em></div>
  <div><span>줄 선 회원</span><b><?= number_format($totN) ?><small>명</small></b><em>상품별 합계 (같은 회원 중복 포함)</em></div>
  <div><span>희망 수량 합계</span><b><?= number_format($totQ) ?><small>개</small></b><em>발주량 판단 참고</em></div>
</div>

<div class="card rsec">
  <div class="rsec-h"><h3>입고 예정 상품</h3><span class="muted small">줄서기는 주문이 아니라 수요 참고용입니다. 입고되면 '입고 완료', 못 들어오면 '입고 취소'를 누르세요.</span>
    <a class="btn btn-sm" style="margin-left:auto" href="<?= site_url('admin/products/new') ?>">상품 등록 (상태: 입고 예정)</a></div>
  <?php if (! $coming): ?><div class="muted rempty">입고 예정 상품이 없습니다. 상품 등록·수정에서 노출 상태를 '입고 예정'으로 고르면 여기에 나옵니다.</div><?php else: ?>
  <table class="tbl"><thead><tr><th class="l">상품</th><th>공급사</th><th>최소 수량</th><th>줄 선 회원</th><th>희망 수량 합계</th><th>관리</th></tr></thead><tbody>
  <?php foreach ($coming as $p): $s = $stats[(int) $p['id']] ?? ['n' => 0, 'qty' => 0]; ?>
    <tr>
      <td class="l"><a href="<?= site_url('admin/queues/' . $p['id']) ?>" class="qadm-p"><img src="<?= mm_thumb($p['thumb']) ?>" alt=""><b><?= esc($p['name']) ?></b></a></td>
      <td class="muted"><?= esc($p['supplier']['company_name'] ?? '-') ?></td>
      <td><?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?></td>
      <td><b><?= number_format($s['n']) ?></b>명</td>
      <td><b class="qadm-qty"><?= number_format($s['qty']) ?></b><?= esc($p['unit']) ?></td>
      <td><a class="btn btn-xs" href="<?= site_url('admin/queues/' . $p['id']) ?>">명단·처리</a> <a class="btn btn-xs" href="<?= site_url('admin/products/' . $p['id'] . '/edit') ?>">상품 수정</a></td>
    </tr>
  <?php endforeach ?>
  </tbody></table><?php endif ?>
</div>

<?php if ($closed): ?>
<div class="card rsec">
  <div class="rsec-h"><h3>최근 닫힌 줄</h3><span class="muted small">입고 완료 또는 입고 취소로 알림을 보낸 상품</span></div>
  <table class="tbl"><thead><tr><th class="l">상품</th><th>결과</th><th>알림 받은 회원</th><th>당시 희망 수량</th><th></th></tr></thead><tbody>
  <?php foreach ($closed as $p): $d = $done[(int) $p['id']] ?? []; $ok = isset($d['arrived']); $r = $d['arrived'] ?? $d['failed'] ?? ['n' => 0, 'qty' => 0]; ?>
    <tr>
      <td class="l"><a href="<?= site_url('admin/queues/' . $p['id']) ?>" class="qadm-p"><img src="<?= mm_thumb($p['thumb']) ?>" alt=""><b><?= esc($p['name']) ?></b></a></td>
      <td><span class="st st-<?= $ok ? 'active' : 'rejected' ?>"><?= $ok ? '입고 완료' : '입고 취소' ?></span></td>
      <td><?= number_format($r['n']) ?>명</td><td><?= number_format($r['qty']) ?><?= esc($p['unit']) ?></td>
      <td><a class="btn btn-xs" href="<?= site_url('admin/queues/' . $p['id']) ?>">명단</a></td>
    </tr>
  <?php endforeach ?>
  </tbody></table>
</div>
<?php endif ?>
