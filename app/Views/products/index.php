<?php $approved = mm_is_approved(); $sym = mm_setting('currency_symbol', 'Ks'); $base = current_url() . '?' . http_build_query(array_filter(['q' => $q])); ?>
<div class="crumb"><a href="<?= site_url('/') ?>">홈</a><?php if (! empty($supplierPage)): ?><a href="<?= site_url('products') ?>">공급사</a><span><?= esc($supplierPage['company_name']) ?></span><?php elseif ($catRow): ?><a href="<?= site_url('products') ?>">전체상품</a><span><?= esc($catRow['name']) ?></span><?php else: ?><span>전체상품</span><?php endif ?></div>

<?php if (! empty($supplierPage)): ?>
<div class="suphead">
  <div class="lg"><?php if ($supplierPage['logo']): ?><img src="<?= base_url($supplierPage['logo']) ?>" alt=""><?php else: ?><?= esc(mb_substr($supplierPage['company_name'], 0, 1)) ?><?php endif ?></div>
  <div><h1><?= esc($supplierPage['company_name']) ?><?php if ($supplierPage['brand']): ?> <span class="tag"><?= esc($supplierPage['brand']) ?></span><?php endif ?></h1><p><?= esc($supplierPage['intro']) ?></p></div>
</div>
<?php else: ?>
<div class="ltop"><h1><?= esc($title) ?></h1></div>
<div class="chips mt24">
  <a href="<?= site_url('products') ?>" class="<?= ! $cat ? 'on' : '' ?>">전체</a>
  <?php foreach ($categories as $c): ?><a href="<?= site_url('category/' . $c['id']) ?>" class="<?= $cat == $c['id'] ? 'on' : '' ?>"><?= esc($c['name']) ?></a><?php endforeach ?>
</div>
<?php endif ?>

<div class="lbody<?= ! empty($supplierPage) ? ' mt40' : '' ?>">
  <aside class="filt">
    <h4>필터</h4>
    <div class="grp"><div class="t">결과 내 재검색</div>
      <form class="srch" method="get"><input type="hidden" name="cat" value="<?= (int) $cat ?>"><input name="q" value="<?= esc($q) ?>" placeholder="검색어 입력"><button type="submit"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg></button></form></div>
    <div class="grp"><div class="t">카테고리</div>
      <a href="<?= site_url('products') ?>" class="<?= ! $cat ? 'on' : '' ?>">전체</a>
      <?php foreach ($categories as $c): ?><a href="<?= site_url('category/' . $c['id']) ?>" class="<?= $cat == $c['id'] ? 'on' : '' ?>"><?= esc($c['name']) ?></a><?php endforeach ?></div>
    <div class="grp"><div class="t">공급사</div>
      <?php foreach (array_slice($suppliers, 0, 8) as $s): ?><a href="<?= site_url('suppliers/' . $s['id']) ?>"><?= esc($s['company_name']) ?></a><?php endforeach ?>
      <?php if (! $suppliers): ?><a>-</a><?php endif ?></div>
    <div class="grp"><div class="t">도매가 안내</div>
      <div class="small muted" style="line-height:1.7"><?= $approved ? '사업자 인증 회원으로 도매가가 표시됩니다.' : '도매가는 사업자 인증 회원에게만 표시됩니다. <a href="' . site_url('register') . '" style="color:var(--pri);font-weight:700">회원가입 ›</a>' ?></div></div>
  </aside>

  <div>
    <div class="lbar">
      <div class="cnt">전체 <b><?= number_format($total) ?></b>개</div>
      <div class="sort">
        <?php foreach (['new' => '최신순', 'pop' => '인기순', 'low' => '낮은가격순', 'high' => '높은가격순'] as $k => $v): ?>
          <a href="<?= $base ?>&sort=<?= $k ?>" class="<?= $sort === $k ? 'on' : '' ?>"><?= $v ?></a>
        <?php endforeach ?>
      </div>
    </div>
    <?php if (! $products): ?><div class="empty">등록된 상품이 없습니다.</div><?php endif ?>
    <div class="grid4">
      <?php foreach ($products as $p): echo view('_card', ['p' => $p, 'approved' => $approved, 'sup' => $suppliers[$p['supplier_id']] ?? null]); endforeach ?>
    </div>
    <?= $pager->links('p', 'mm_pager') ?>
  </div>
</div>
