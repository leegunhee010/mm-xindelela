<form class="pfilter" method="get">
  <select class="sel" name="admin" onchange="this.form.submit()"><option value="">담당자 전체</option><?php foreach ($admins as $a): ?><option value="<?= $a['id'] ?>" <?= $f['admin'] === (int) $a['id'] ? 'selected' : '' ?>><?= esc($a['name']) ?></option><?php endforeach ?></select>
  <select class="sel" name="type" onchange="this.form.submit()"><option value="">대상 전체</option><?php foreach ($types as $k => $l): ?><option value="<?= $k ?>" <?= $f['type'] === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach ?></select>
  <input class="inp" type="date" name="from" value="<?= esc($f['from']) ?>" style="width:150px"> ~ <input class="inp" type="date" name="to" value="<?= esc($f['to']) ?>" style="width:150px">
  <input class="inp" name="q" value="<?= esc($f['q']) ?>" placeholder="주문번호·회원명·상품명">
  <button class="btn btn-sm" type="submit">검색</button>
  <?php if (array_filter($f)): ?><a class="pfilter-reset" href="<?= site_url('admin/logs') ?>">초기화</a><?php endif ?>
</form>
<table class="tbl"><thead><tr><th style="width:130px">일시</th><th style="width:120px">담당자</th><th style="width:90px">대상</th><th class="l">한 일</th><th style="width:90px"></th></tr></thead><tbody>
<?php if (! $rows): ?><tr><td colspan="5" style="padding:40px">기록이 없습니다.</td></tr><?php endif ?>
<?php foreach ($rows as $l): $link = ['order' => 'admin/orders/', 'buyer' => 'admin/buyers/', 'product' => 'admin/products/%d/edit', 'supplier' => 'admin/suppliers/'][$l['target_type']] ?? null; ?>
<tr><td><?= mm_dt($l['created_at'], 'm.d H:i') ?></td><td><b><?= esc($l['admin_name']) ?></b></td><td><?= $types[$l['target_type']] ?? '-' ?></td><td class="l"><?= esc($l['summary']) ?></td>
<td><?php if ($link && $l['target_id']): ?><a class="btn btn-xs" href="<?= site_url(str_contains($link, '%d') ? sprintf($link, $l['target_id']) : $link . $l['target_id']) ?>">보기</a><?php endif ?></td></tr>
<?php endforeach ?>
</tbody></table>
<?= $pager->links('p', 'mm_pager') ?>
