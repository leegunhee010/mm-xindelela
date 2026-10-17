<?php $sym = mm_setting('currency_symbol', 'Ks'); ?>
<div class="rkpi">
  <div><span>추천 가입</span><b><?= number_format($kpi['total']) ?><small>명</small></b><em>이번 달 +<?= number_format($kpi['month']) ?></em></div>
  <div><span>인증 승인율</span><b><?= $kpi['approveRt'] ?><small>%</small></b><em>추천 가입 중 승인된 비율</em></div>
  <div><span>첫 주문 전환율</span><b><?= $kpi['orderRt'] ?><small>%</small></b><em>추천 가입 중 첫 주문까지 간 비율</em></div>
  <div><span>추천 회원 매출</span><b><?= number_format($kpi['sales']) ?><small><?= esc($sym) ?></small></b><em>입금 확인 기준</em></div>
  <div><span>지급한 추천 포인트</span><b><?= number_format($kpi['points']) ?><small>P</small></b><em>승인 <?= number_format($rules['signup']) ?>P · 첫 주문 <?= number_format($rules['first']) ?>P</em></div>
</div>

<div class="card rsec">
  <?php $showAll = service('request')->getGet('rank') === 'all'; $rankTotal = count($rank); $rankView = $showAll ? $rank : array_slice($rank, 0, 10); ?>
  <div class="rsec-h"><h3>추천인 순위</h3><span class="muted small">소개 수 많은 순 · <?= $showAll ? '전체 ' . $rankTotal . '명' : '상위 ' . min(10, $rankTotal) . '명' . ($rankTotal > 10 ? ' / 전체 ' . $rankTotal . '명' : '') ?></span>
    <?php if ($rankTotal > 10): ?><a class="btn btn-sm" style="margin-left:auto" href="?rank=<?= $showAll ? 'top' : 'all' ?><?= $f !== 'all' ? '&f=' . esc($f) : '' ?>"><?= $showAll ? '상위 10위만' : '전체 보기' ?></a><?php endif ?></div>
  <?php if (! $rank): ?><div class="muted rempty">아직 추천으로 가입한 회원이 없습니다.</div><?php else: ?>
  <table class="tbl"><thead><tr><th>순위</th><th class="l">추천인</th><th>추천코드</th><th>소개</th><th>승인</th><th>첫 주문</th><th>소개 회원 매출</th><th>받은 포인트</th></tr></thead><tbody>
  <?php foreach ($rankView as $i => $r): $u = $r['user']; ?>
    <tr><td><b><?= $i + 1 ?></b></td>
      <td class="l"><?php if ($u): ?><a href="<?= site_url('admin/buyers/' . $u['id'] . '?tab=referral') ?>"><b><?= esc($u['company_name']) ?></b></a> <span class="st st-<?= esc($u['status']) ?>"><?= mm_verify_status_label($u['status']) ?></span><?php else: ?>(삭제된 회원)<?php endif ?></td>
      <td class="muted"><?= esc($u['referral_code'] ?? '-') ?></td>
      <td><b><?= $r['n'] ?></b>명</td><td><?= $r['approved'] ?>명</td><td><?= $r['ordered'] ?>명</td>
      <td><?= $r['sales'] ? mm_price($r['sales']) : '-' ?></td><td><?= $r['points'] ? number_format($r['points']) . 'P' : '-' ?></td></tr>
  <?php endforeach ?>
  </tbody></table><?php endif ?>
</div>

<div class="card rsec">
  <div class="rsec-h"><h3>추천으로 가입한 회원</h3>
    <div class="tabs" style="margin:0 0 0 auto"><?php foreach (['all' => '전체', 'pending' => '인증 검수 중', 'approved' => '승인·주문 전', 'ordered' => '첫 주문 완료'] as $k => $l): ?><a href="?f=<?= $k ?><?= service('request')->getGet('rank') === 'all' ? '&rank=all' : '' ?>" class="<?= $f === $k ? 'on' : '' ?>"><?= $l ?><span class="n"><?= $counts[$k] ?></span></a><?php endforeach ?></div>
  </div>
  <?php if (! $list): ?><div class="muted rempty">해당하는 회원이 없습니다.</div><?php else: ?>
  <table class="tbl"><thead><tr><th class="l">가입 회원</th><th>가입일</th><th>인증</th><th>첫 주문</th><th>매출</th><th class="l">추천인</th><th>추천 포인트 지급</th></tr></thead><tbody>
  <?php foreach ($list as $m): $ref = $refs[$m['referred_by']] ?? null; $pd = $paid[$m['id']] ?? []; ?>
    <tr><td class="l"><a href="<?= site_url('admin/buyers/' . $m['id']) ?>"><b><?= esc($m['company_name']) ?></b></a><br><span class="muted"><?= esc($m['city']) ?></span></td>
      <td><?= mm_dt($m['created_at'], 'Y.m.d') ?></td>
      <td><span class="st st-<?= esc($m['status']) ?>"><?= mm_verify_status_label($m['status']) ?></span></td>
      <td><?= $m['first_order_at'] ? mm_dt($m['first_order_at'], 'Y.m.d') : '-' ?></td>
      <td><?= ! empty($sales[$m['id']]) ? mm_price($sales[$m['id']]) : '-' ?></td>
      <td class="l"><?php if ($ref): ?><a href="<?= site_url('admin/buyers/' . $ref['id'] . '?tab=referral') ?>"><?= esc($ref['company_name']) ?></a><?php else: ?>-<?php endif ?></td>
      <td><span class="rpay <?= isset($pd['referral_signup']) ? 'on' : '' ?>">승인 <?= isset($pd['referral_signup']) ? '+' . number_format($pd['referral_signup']) : '대기' ?></span><span class="rpay <?= isset($pd['referral_first_order']) ? 'on' : '' ?>">첫 주문 <?= isset($pd['referral_first_order']) ? '+' . number_format($pd['referral_first_order']) : '대기' ?></span></td></tr>
  <?php endforeach ?>
  </tbody></table><?php endif ?>
</div>
