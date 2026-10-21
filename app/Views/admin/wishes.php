<div class="rkpi">
  <div><span>관심상품을 담은 회원</span><b><?= number_format($kpi['members']) ?><small>명</small></b><em>로그인한 회원 기준</em></div>
  <div><span>담긴 횟수</span><b><?= number_format($kpi['total']) ?><small>번</small></b><em>최근 7일 +<?= number_format($kpi['week']) ?></em></div>
  <div><span>담긴 상품</span><b><?= number_format($kpi['products']) ?><small>개</small></b><em>한 명 이상이 담은 상품</em></div>
</div>

<div class="card rsec">
  <div class="rsec-h"><h3>많이 담긴 상품</h3><span class="muted small">비회원이 담은 관심상품은 가입·로그인한 뒤부터 집계됩니다. 재입고·발주·기획전 참고용.</span></div>
  <?php if (! $rows): ?><div class="muted rempty">아직 관심상품을 담은 회원이 없습니다.</div><?php else: ?>
  <table class="tbl"><thead><tr><th>순위</th><th class="l">상품</th><th>상태</th><th>담은 회원</th><th>마지막으로 담긴 날</th><th></th></tr></thead><tbody>
  <?php foreach ($rows as $i => $r): $p = $r['p']; ?>
    <tr>
      <td><b><?= $i + 1 ?></b></td>
      <td class="l"><a href="<?= site_url('admin/wishes/' . $p['id']) ?>" class="qadm-p"><img src="<?= mm_thumb($p['thumb']) ?>" alt=""><b><?= esc($p['name']) ?></b></a></td>
      <td><span class="st st-<?= esc($p['status']) ?>"><?= mm_product_status_label($p['status']) ?></span></td>
      <td><b class="qadm-qty"><?= number_format((int) $r['n']) ?></b>명</td>
      <td class="small"><?= mm_dt($r['last'], 'Y.m.d') ?></td>
      <td><a class="btn btn-xs" href="<?= site_url('admin/wishes/' . $p['id']) ?>">담은 회원</a></td>
    </tr>
  <?php endforeach ?>
  </tbody></table><?php endif ?>
</div>
