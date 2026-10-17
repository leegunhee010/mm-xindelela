<div class="tabs">
  <a href="<?= site_url('admin/products/new') ?>" class="on" style="order:9;margin-left:auto;background:var(--pri);border-color:var(--pri)">+ 상품 등록</a>
  <?php foreach (['all' => '전체', 'active' => '판매중', 'hidden' => '숨김', 'draft' => '임시 저장'] as $k => $l): ?><a href="<?= site_url('admin/products?status=' . $k) ?>" class="<?= $st === $k ? 'on' : '' ?>"><?= $l ?><?php if (isset($counts[$k])): ?><span class="n"><?= $counts[$k] ?></span><?php endif ?></a><?php endforeach ?>
  <form class="srch" method="get"><input type="hidden" name="status" value="<?= esc($st) ?>"><input name="q" value="<?= esc($q) ?>" placeholder="상품명"><button class="btn btn-sm" type="submit">검색</button></form>
</div>
<table class="tbl"><thead><tr><th>ID</th><th>이미지</th><th class="l">상품</th><th>공급사</th><th>카테고리</th><th>도매가</th><th>MOQ</th><th>재고</th><th>BEST</th><th>상태</th><th>관리</th></tr></thead><tbody>
<?php if (! $rows): ?><tr><td colspan="11" style="padding:40px">없습니다.</td></tr><?php endif ?>
<?php foreach ($rows as $p): ?>
<tr><td><?= $p['id'] ?></td><td><img class="thumb" src="<?= mm_thumb($p['thumb']) ?>" alt="" style="width:44px;height:44px"></td><td class="l"><a href="<?= site_url('admin/products/' . $p['id'] . '/edit') ?>"><b><?= esc($p['name']) ?></b></a><br><span class="muted"><?= esc($p['brand']) ?> <?= esc($p['code']) ?></span></td><td><?= esc($p['supplier']['company_name'] ?? '-') ?></td><td><?= esc($p['cat']['name'] ?? '-') ?></td><td><?= mm_price($p['price']) ?></td><td><?= $p['moq'] ?></td><td><?= number_format((int) $p['stock']) ?></td>
<td><form method="post" action="<?= site_url('admin/products/' . $p['id'] . '/best') ?>"><?= csrf_field() ?><button class="btn btn-xs <?= $p['is_best'] ? 'btn-dark' : '' ?>" type="submit"><?= $p['is_best'] ? 'BEST' : '-' ?></button></form></td>
<td><span class="st st-<?= esc($p['status']) ?>"><?= mm_product_status_label($p['status']) ?></span></td>
<td class="pact">
  <a class="btn btn-xs btn-g" href="<?= site_url('admin/products/' . $p['id'] . '/edit') ?>">수정</a>
  <form method="post" action="<?= site_url('admin/products/' . $p['id'] . '/status') ?>"><?= csrf_field() ?><input type="hidden" name="status" value="<?= $p['status'] === 'active' ? 'hidden' : 'active' ?>"><button class="btn btn-xs" type="submit"><?= $p['status'] === 'active' ? '숨김' : '노출' ?></button></form>
  <form method="post" action="<?= site_url('admin/products/' . $p['id'] . '/delete') ?>" onsubmit="return confirm('<?= esc($p['name'], 'js') ?> 상품을 삭제할까요?')"><?= csrf_field() ?><button class="btn btn-xs btn-red" type="submit">삭제</button></form>
</td></tr>
<?php endforeach ?>
</tbody></table>
<?= $pager->links('p', 'mm_pager') ?>
