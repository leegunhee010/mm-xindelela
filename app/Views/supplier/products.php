<div class="tabs">
  <?php foreach (['' => '전체', 'active' => '판매중', 'pending' => '검수 대기', 'draft' => '임시저장', 'hidden' => '숨김', 'rejected' => '반려'] as $k => $l): ?><a href="<?= site_url('supplier/products?status=' . $k) ?>" class="<?= $st === $k ? 'on' : '' ?>"><?= $l ?></a><?php endforeach ?>
  <a class="btn btn-g btn-sm" href="<?= site_url('supplier/products/new') ?>" style="margin-left:auto">+ 상품 등록</a>
</div>
<table class="tbl"><thead><tr><th>ID</th><th>이미지</th><th class="l">상품</th><th>카테고리</th><th>도매가</th><th>MOQ</th><th>재고</th><th>조회/주문</th><th>상태</th><th></th></tr></thead><tbody>
<?php if (! $products): ?><tr><td colspan="10" style="padding:40px">상품이 없습니다. 오른쪽 위 버튼으로 등록하세요.</td></tr><?php endif ?>
<?php foreach ($products as $p): ?>
<tr><td><?= $p['id'] ?></td><td><img class="thumb" src="<?= mm_thumb($p['thumb']) ?>" alt="" style="width:44px;height:44px"></td><td class="l"><a href="<?= site_url('supplier/products/' . $p['id'] . '/edit') ?>"><b><?= esc($p['name']) ?></b></a><?php if ($p['status'] === 'rejected' && $p['reject_reason']): ?><br><span style="color:var(--price)" class="small">반려: <?= esc($p['reject_reason']) ?></span><?php endif ?></td><td><?= esc($cats[$p['category_id']] ?? '-') ?></td><td><?= mm_price($p['price']) ?></td><td><?= $p['moq'] ?></td><td><?= number_format((int) $p['stock']) ?></td><td><?= $p['views'] ?> / <?= $p['order_count'] ?></td><td><span class="st st-<?= esc($p['status']) ?>"><?= mm_product_status_label($p['status']) ?></span></td>
<td class="inl"><a class="btn btn-xs" href="<?= site_url('supplier/products/' . $p['id'] . '/edit') ?>">수정</a><form method="post" action="<?= site_url('supplier/products/' . $p['id'] . '/delete') ?>" onsubmit="return confirm('삭제할까요?')"><?= csrf_field() ?><button class="btn btn-xs btn-red" type="submit">삭제</button></form></td></tr>
<?php endforeach ?>
</tbody></table>
<?= $pager->links('p', 'mm_pager') ?>
