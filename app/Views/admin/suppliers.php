<div class="ptop"><div class="tabs" style="margin:0">
  <?php foreach (['' => '전체', 'approved' => '승인', 'rejected' => '반려', 'suspended' => '정지'] as $k => $l): ?><a href="<?= site_url('admin/suppliers?status=' . $k) ?>" class="<?= $st === $k ? 'on' : '' ?>"><?= $l ?></a><?php endforeach ?>
</div><a href="<?= site_url('admin/suppliers/new') ?>" class="btn btn-g ptop-add">+ 공급사 추가</a></div>
<table class="tbl"><thead><tr><th>ID</th><th class="l">공급사</th><th>브랜드</th><th>담당자</th><th>연락처</th><th>상품</th><th>수수료</th><th>등록일</th><th>상태</th><th></th></tr></thead><tbody>
<?php if (! $rows): ?><tr><td colspan="10" style="padding:40px">없습니다.</td></tr><?php endif ?>
<?php foreach ($rows as $s): ?>
<tr><td><?= $s['id'] ?></td><td class="l"><a href="<?= site_url('admin/suppliers/' . $s['id']) ?>"><b><?= esc($s['company_name']) ?></b></a></td><td><?= esc($s['brand']) ?></td><td><?= esc($s['contact_name']) ?></td><td><?= esc($s['phone']) ?><br><span class="muted"><?= esc($s['user']['email'] ?? '') ?></span></td><td><?= $s['pcnt'] ?></td><td><?= (float) $s['commission_rate'] ?>%</td><td><?= mm_dt($s['created_at'], 'Y.m.d') ?></td><td><span class="st st-<?= esc($s['status']) ?>"><?= mm_status_label($s['status']) ?></span></td><td><a class="btn btn-xs" href="<?= site_url('admin/suppliers/' . $s['id']) ?>">상세</a></td></tr>
<?php endforeach ?>
</tbody></table>
<?= $pager->links('p', 'mm_pager') ?>
