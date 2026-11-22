<?php
$qs = function (array $over = []) use ($f, $st) {
    $p = array_filter(array_merge(['status' => $st, 'q' => $f['q'], 'cat' => $f['cat'] ?: '', 'sup' => $f['sup'] ?: '', 'best' => $f['best'] ? '1' : '', 'stock' => $f['stock'], 'sort' => $f['sort'] !== 'new' ? $f['sort'] : '', 'per' => $f['per'] !== 30 ? $f['per'] : ''], $over), fn ($v) => $v !== '' && $v !== null);
    return site_url('admin/products') . ($p ? '?' . http_build_query($p) : '');
};
$filtered = $f['q'] !== '' || $f['cat'] || $f['sup'] || $f['best'] || $f['stock'] !== '';
?>
<div class="ptop">
  <div class="tabs" style="margin:0">
    <?php foreach (['all' => '전체', 'active' => '판매중', 'hidden' => '숨김', 'draft' => '임시 저장'] as $k => $l): ?><a href="<?= $qs(['status' => $k]) ?>" class="<?= $st === $k ? 'on' : '' ?>"><?= $l ?><span class="n"><?= $counts[$k] ?? 0 ?></span></a><?php endforeach ?>
  </div>
  <a href="<?= site_url('admin/products/new') ?>" class="btn btn-g ptop-add">+ 상품 등록</a>
</div>
<form class="pfilter" method="get">
  <input type="hidden" name="status" value="<?= esc($st) ?>">
  <input class="inp" name="q" value="<?= esc($f['q']) ?>" placeholder="상품명 · 브랜드 · 상품코드">
  <select class="sel" name="cat" onchange="this.form.submit()"><option value="">카테고리 전체</option><?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= $f['cat'] === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option><?php endforeach ?></select>
  <select class="sel" name="sup" onchange="this.form.submit()"><option value="">공급사 전체</option><?php foreach ($suppliers as $sp): ?><option value="<?= $sp['id'] ?>" <?= $f['sup'] === (int) $sp['id'] ? 'selected' : '' ?>><?= esc($sp['company_name']) ?></option><?php endforeach ?></select>
  <select class="sel" name="stock" onchange="this.form.submit()"><option value="">재고 전체</option><option value="low" <?= $f['stock'] === 'low' ? 'selected' : '' ?>>재고 부족 (최소수량×3 미만)</option><option value="out" <?= $f['stock'] === 'out' ? 'selected' : '' ?>>품절 (최소수량 미만)</option></select>
  <label class="pfilter-chk"><input type="checkbox" name="best" value="1" <?= $f['best'] ? 'checked' : '' ?> onchange="this.form.submit()"> BEST만</label>
  <select class="sel" name="sort" onchange="this.form.submit()"><?php foreach (['new' => '최신 등록순', 'name' => '이름순', 'price' => '도매가 낮은순', 'stock' => '재고 적은순', 'views' => '조회 많은순', 'orders' => '주문 많은순'] as $k => $l): ?><option value="<?= $k ?>" <?= $f['sort'] === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach ?></select>
  <select class="sel" name="per" onchange="this.form.submit()"><?php foreach ([30, 50, 100] as $n): ?><option value="<?= $n ?>" <?= $f['per'] === $n ? 'selected' : '' ?>><?= $n ?>개씩</option><?php endforeach ?></select>
  <button class="btn btn-sm" type="submit">검색</button>
  <?php if ($filtered): ?><a class="pfilter-reset" href="<?= site_url('admin/products?status=' . $st) ?>">필터 초기화</a><?php endif ?>
</form>
<table class="tbl"><thead><tr><th>ID</th><th>이미지</th><th class="l">상품</th><th>공급사</th><th>카테고리</th><th>도매가</th><th>MOQ</th><th>재고</th><th>BEST</th><th>상태</th><th>관리</th></tr></thead><tbody>
<?php if (! $rows): ?><tr><td colspan="11" style="padding:40px"><?= $filtered ? '조건에 맞는 상품이 없습니다.' : '등록된 상품이 없습니다.' ?></td></tr><?php endif ?>
<?php foreach ($rows as $p): ?>
<tr><td><?= $p['id'] ?></td><td><img class="thumb" src="<?= mm_thumb($p['thumb']) ?>" alt="" style="width:44px;height:44px"></td><td class="l"><a href="<?= site_url('admin/products/' . $p['id'] . '/edit') ?>"><b><?= esc($p['name']) ?></b></a><br><span class="muted"><?= esc($p['brand']) ?> <?= esc($p['code']) ?></span></td><td><?php if ($p['supplier']): ?><a href="<?= $qs(['sup' => $p['supplier']['id']]) ?>"><?= esc($p['supplier']['company_name']) ?></a><?php else: ?>-<?php endif ?></td><td><?php if ($p['cat']): ?><a href="<?= $qs(['cat' => $p['cat']['id']]) ?>"><?= esc($p['cat']['name']) ?></a><?php else: ?>-<?php endif ?></td><td><?= mm_price($p['price']) ?></td><td><?= $p['moq'] ?></td><td class="<?= (int) $p['stock'] < (int) $p['moq'] ? 'pstock-out' : ((int) $p['stock'] < (int) $p['moq'] * 3 ? 'pstock-low' : '') ?>"><?= number_format((int) $p['stock']) ?></td>
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
