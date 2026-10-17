<div class="two">
<div>
  <div class="card"><h3><?= esc($p['name']) ?> <span class="st st-<?= esc($p['status']) ?>"><?= mm_product_status_label($p['status']) ?></span> <a class="btn btn-xs" href="<?= site_url('products/' . $p['id']) ?>" target="_blank" style="float:right">구매자 화면으로 보기</a></h3>
    <div class="imgs" style="margin-bottom:12px"><?php foreach ($images as $im): ?><label><img src="<?= mm_thumb($im) ?>" alt=""></label><?php endforeach ?><?php if (! $images): ?><span class="muted">이미지 없음</span><?php endif ?></div>
    <table class="kv">
      <tr><th>공급사</th><td><a href="<?= site_url('admin/suppliers/' . $p['supplier_id']) ?>"><?= esc($supplier['company_name'] ?? '-') ?></a></td></tr>
      <tr><th>카테고리 / 브랜드</th><td><?= esc($cat['name'] ?? '-') ?> / <?= esc($p['brand']) ?></td></tr>
      <tr><th>코드</th><td><?= esc($p['code']) ?></td></tr>
      <tr><th>도매가</th><td><b><?= mm_price($p['price']) ?></b> <?php if ($p['retail_price']): ?>(소비자가 <?= mm_price($p['retail_price']) ?>)<?php endif ?></td></tr>
      <tr><th>수량별 단가</th><td><?php if ($tiers): foreach ($tiers as $t): ?><span class="tag tag-gr"><?= $t['min_qty'] ?>개↑ <?= mm_price($t['price'], false) ?></span><?php endforeach; else: ?>-<?php endif ?></td></tr>
      <tr><th>MOQ / 단위 / 재고</th><td><?= $p['moq'] ?> / <?= esc($p['unit']) ?> / <?= number_format((int) $p['stock']) ?></td></tr>
      <tr><th>원산지</th><td><?= esc($p['origin']) ?></td></tr>
      <tr><th>요약</th><td><?= esc($p['summary']) ?></td></tr>
      <?php if ($p['reject_reason']): ?><tr><th>반려 사유</th><td style="color:var(--price)"><?= esc($p['reject_reason']) ?></td></tr><?php endif ?>
    </table>
    <h3 class="mt24">상세 설명</h3>
    <div class="pdesc" style="padding:10px;border:1px solid var(--line);background:#fff"><?= $p['description'] ?: '<span class="muted">없음</span>' ?></div>
  </div>
</div>
<div>
  <div class="act" style="border:2px solid var(--pri)"><h3>검수</h3>
    <form method="post" action="<?= site_url('admin/products/' . $p['id'] . '/status') ?>"><?= csrf_field() ?><input type="hidden" name="status" value="active"><button class="btn btn-g btn-block" type="submit" style="height:42px">승인 (판매중)</button></form>
    <form method="post" action="<?= site_url('admin/products/' . $p['id'] . '/status') ?>" class="mt8"><?= csrf_field() ?><input type="hidden" name="status" value="hidden"><button class="btn btn-block" type="submit">숨김</button></form>
    <form method="post" action="<?= site_url('admin/products/' . $p['id'] . '/status') ?>" class="mt8"><?= csrf_field() ?><input type="hidden" name="status" value="rejected"><input class="inp" name="reason" placeholder="반려 사유 (공급사에게 표시)"><button class="btn btn-red btn-block mt8" type="submit">반려</button></form>
  </div>
  <div class="act"><form method="post" action="<?= site_url('admin/products/' . $p['id'] . '/best') ?>"><?= csrf_field() ?><button class="btn btn-block <?= $p['is_best'] ? 'btn-dark' : '' ?>" type="submit"><?= $p['is_best'] ? 'BEST 해제' : 'BEST 지정' ?></button></form></div>
</div>
</div>
