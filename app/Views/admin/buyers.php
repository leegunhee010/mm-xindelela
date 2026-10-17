<div class="tabs">
  <?php foreach (['pending' => '검수 대기', 'approved' => '승인', 'rejected' => '반려', 'suspended' => '정지', 'all' => '전체'] as $k => $l): ?><a href="<?= site_url('admin/buyers?status=' . $k) ?>" class="<?= $st === $k ? 'on' : '' ?>"><?= $l ?><?php if (isset($counts[$k])): ?><span class="n"><?= $counts[$k] ?></span><?php endif ?></a><?php endforeach ?>
  <form class="srch" method="get"><input type="hidden" name="status" value="<?= esc($st) ?>"><input name="q" value="<?= esc($q) ?>" placeholder="상호·이름·이메일·전화"><button class="btn btn-sm" type="submit">검색</button></form>
</div>
<?php if ($st === 'pending'): ?><div class="note">검수 순서: 페이스북 페이지 열어 개설일·최근 게시물·판매글 확인 → 매장 사진·허가증 확인 → 승인 또는 반려(사유 필수). 목표 처리 시간: <?= esc(mm_setting('verify_sla_text')) ?>.</div><?php endif ?>
<table class="tbl"><thead><tr><th>ID</th><th class="l">상호 / 담당자</th><th>지역</th><th>페이스북</th><th>주문</th><th>입금 확인 금액</th><th>장바구니</th><th class="l">추천인</th><th>포인트</th><th>가입</th><th>상태</th><th></th></tr></thead><tbody>
<?php if (! $rows): ?><tr><td colspan="12" style="padding:40px">해당 회원이 없습니다.</td></tr><?php endif ?>
<?php foreach ($rows as $b): $docs = mm_json($b['doc_files']); ?>
<tr>
  <td><?= $b['id'] ?></td>
  <td class="l"><a href="<?= site_url('admin/buyers/' . $b['id']) ?>"><b><?= esc($b['company_name']) ?></b></a><br><span class="muted"><?= esc($b['name']) ?> · <?= esc($b['phone']) ?><?php if ($b['viber'] && $b['viber'] !== $b['phone']): ?> · V <?= esc($b['viber']) ?><?php endif ?></span></td>
  <td><?= esc($b['city']) ?></td>
  <td><?php if ($b['fb_page']): ?><a href="<?= esc($b['fb_page']) ?>" target="_blank" class="btn btn-xs">열기</a><?php else: ?>-<?php endif ?></td>
  <?php $os = $stats[$b['id']] ?? null; ?>
  <td><?php if ($os): ?><a href="<?= site_url('admin/buyers/' . $b['id'] . '?tab=orders') ?>"><?= $os['n'] ?>건</a><?php else: ?>-<?php endif ?></td>
  <td><?= $os && $os['paid'] ? mm_price($os['paid']) : '-' ?></td>
  <td><?php if (! empty($carts[$b['id']])): ?><a href="<?= site_url('admin/buyers/' . $b['id'] . '?tab=cart') ?>"><?= $carts[$b['id']] ?>종</a><?php else: ?>-<?php endif ?></td>
  <td class="l"><?php $rf = $refs[$b['referred_by']] ?? null; if ($rf): ?><a href="<?= site_url('admin/buyers/' . $rf['id']) ?>"><?= esc($rf['company_name']) ?></a><?php else: ?>-<?php endif ?></td>
  <td><?= number_format((int) $b['points_balance']) ?></td>
  <td><?= mm_dt($b['created_at'], 'm.d H:i') ?></td>
  <td><span class="st st-<?= esc($b['status']) ?>"><?= mm_status_label($b['status']) ?></span></td>
  <td><a class="btn btn-xs <?= $b['status'] === 'pending' ? 'btn-g' : '' ?>" href="<?= site_url('admin/buyers/' . $b['id']) ?>"><?= $b['status'] === 'pending' ? '검수' : '상세' ?></a></td>
</tr>
<?php endforeach ?>
</tbody></table>
<?= $pager->links('p', 'mm_pager') ?>
