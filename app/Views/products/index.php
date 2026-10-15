<?php $approved = mm_is_approved(); $sym = mm_setting('currency_symbol', 'Ks'); ?>
<div class="crumb"><a href="<?= site_url('/') ?>">홈</a><?php if (! empty($supplierPage)): ?><a href="<?= site_url('products') ?>">공급사</a><?= esc($supplierPage['company_name']) ?><?php elseif ($catRow): ?><a href="<?= site_url('products') ?>">전체상품</a><?= esc($catRow['name']) ?><?php else: ?>전체상품<?php endif ?></div>

<?php if (! empty($supplierPage)): ?>
<div class="suphead">
  <div class="lg"><?php if ($supplierPage['logo']): ?><img src="<?= base_url($supplierPage['logo']) ?>" alt=""><?php else: ?><?= esc(mb_substr($supplierPage['company_name'], 0, 1)) ?><?php endif ?></div>
  <div><h1><?= esc($supplierPage['company_name']) ?><?php if ($supplierPage['brand']): ?> <span class="tag tag-g"><?= esc($supplierPage['brand']) ?></span><?php endif ?></h1><p><?= esc($supplierPage['intro']) ?></p></div>
</div>
<?php else: ?>
<div class="ltop"><h1><?= esc($title) ?><small>총 <?= number_format($total) ?>개</small></h1></div>
<div class="catbar">
  <a href="<?= site_url('products') ?>" class="<?= ! $cat ? 'on' : '' ?>">전체</a>
  <?php foreach ($categories as $c): ?><a href="<?= site_url('category/' . $c['id']) ?>" class="<?= $cat == $c['id'] ? 'on' : '' ?>"><?= esc($c['name']) ?></a><?php endforeach ?>
</div>
<?php endif ?>

<?php $base = current_url() . '?' . http_build_query(array_filter(['q' => $q])) ; ?>
<div class="sortbar">
  <div class="s">
    <?php foreach (['new' => '최근등록순', 'pop' => '인기상품순', 'low' => '낮은가격순', 'high' => '높은가격순'] as $k => $v): ?>
      <a href="<?= $base ?>&sort=<?= $k ?>" class="<?= $sort === $k ? 'on' : '' ?>"><?= $v ?></a>
    <?php endforeach ?>
  </div>
  <div><?= $approved ? '도매가 표시 중' : '🔒 도매가는 사업자 인증 회원에게만 표시됩니다 <a href="' . site_url('register') . '" style="color:var(--green-d);font-weight:700;margin-left:6px">회원가입</a>' ?></div>
</div>

<?php if (! $products): ?>
  <div class="empty mt16">등록된 상품이 없습니다.</div>
<?php endif ?>

<?php foreach ($products as $p): $s = $suppliers[$p['supplier_id']] ?? null; $tiers = mm_json($p['tiers']); ?>
<div class="lrow">
  <a class="im" href="<?= site_url('products/' . $p['id']) ?>"><img src="<?= mm_thumb($p['thumb']) ?>" alt=""><?php if ($p['is_best']): ?><span class="bst">BEST</span><?php endif ?></a>
  <div>
    <h3><?php if ($p['brand']): ?><span class="tag tag-gr"><?= esc($p['brand']) ?></span><?php endif ?><a href="<?= site_url('products/' . $p['id']) ?>"><?= esc($p['name']) ?></a></h3>
    <?php if ($p['summary']): ?><div class="sm"><?= esc($p['summary']) ?></div><?php endif ?>
    <div class="meta">원산지 <b><?= esc($p['origin']) ?></b> · 재고 <b><?= number_format((int) $p['stock']) ?></b> · 조회 <b><?= number_format((int) $p['views']) ?></b></div>
  </div>
  <div class="pcol">
    <?php if ($approved): ?>
      <div class="price"><?= mm_price($p['price'], false) ?><small><?= esc($sym) ?></small></div>
      <div class="tier">최소 <?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?><?php if ($tiers): usort($tiers, static fn ($a, $b) => $b['min_qty'] <=> $a['min_qty']); ?> · <?= number_format((int) $tiers[0]['min_qty']) ?>개↑ <b><?= mm_price($tiers[0]['price'], false) ?></b><?php endif ?></div>
    <?php else: ?>
      <div class="lock">회원 전용 도매가</div>
      <div class="tier">최소 <?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?><?php if ($p['retail_price']): ?> · 소비자가 <?= mm_price($p['retail_price'], false) ?><?php endif ?></div>
    <?php endif ?>
  </div>
  <div class="scol"><?php if ($s): ?><a href="<?= site_url('suppliers/' . $s['id']) ?>"><b><?= esc($s['company_name']) ?></b></a><?= esc($s['brand']) ?><?php endif ?></div>
</div>
<?php endforeach ?>

<?= $pager->links('p', 'mm_pager') ?>
