<div class="card cat-add">
  <form method="post" action="<?= site_url('admin/categories/save') ?>" class="cat-row cat-row-add"><?= csrf_field() ?>
    <input class="inp" type="number" name="sort" value="<?= count($rows) + 1 ?>" title="순서">
    <input class="inp" name="name" placeholder="새 카테고리 이름" required>
    <span></span>
    <label class="cat-sw"><input type="checkbox" name="is_active" value="1" checked><i></i><span>노출</span></label>
    <button class="btn btn-g cat-btn" type="submit">+ 추가</button>
    <span></span>
  </form>
</div>

<div class="card cat-list">
  <div class="cat-row cat-head"><span>순서</span><span>카테고리 이름</span><span>상품</span><span>노출</span><span></span><span></span></div>
  <?php if (! $rows): ?><div class="muted" style="padding:30px;text-align:center">카테고리가 없습니다.</div><?php endif ?>
  <?php foreach ($rows as $c): ?>
  <div class="cat-row">
    <form id="cf<?= $c['id'] ?>" method="post" action="<?= site_url('admin/categories/save') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $c['id'] ?>"></form>
    <input class="inp" form="cf<?= $c['id'] ?>" type="number" name="sort" value="<?= $c['sort'] ?>" title="순서">
    <input class="inp" form="cf<?= $c['id'] ?>" name="name" value="<?= esc($c['name']) ?>" required>
    <a class="cat-cnt" href="<?= site_url('admin/products?cat=' . $c['id']) ?>"><?= number_format($c['pcnt']) ?>개</a>
    <label class="cat-sw"><input form="cf<?= $c['id'] ?>" type="checkbox" name="is_active" value="1" <?= $c['is_active'] ? 'checked' : '' ?>><i></i><span><?= $c['is_active'] ? '노출' : '숨김' ?></span></label>
    <button class="btn cat-btn" form="cf<?= $c['id'] ?>" type="submit">저장</button>
    <?php if ($c['pcnt']): ?>
      <button class="btn cat-btn" type="button" disabled title="상품이 있는 카테고리는 삭제할 수 없습니다. 숨김으로 바꿔 주세요.">삭제</button>
    <?php else: ?>
      <form method="post" action="<?= site_url('admin/categories/' . $c['id'] . '/delete') ?>" onsubmit="return confirm('<?= esc($c['name'], 'js') ?> 카테고리를 삭제할까요?')"><?= csrf_field() ?><button class="btn btn-red cat-btn" type="submit">삭제</button></form>
    <?php endif ?>
  </div>
  <?php endforeach ?>
</div>
<p class="small muted">순서는 숫자가 작을수록 앞에 나옵니다(사이트 메뉴·필터 순서). 상품이 있는 카테고리는 삭제 대신 숨김으로 바꿔 주세요.</p>
<script>document.querySelectorAll('.cat-sw input').forEach(function (c) { c.addEventListener('change', function () { var s = c.parentNode.querySelector('span'); if (s && c.form && c.form.id) s.textContent = c.checked ? '노출' : '숨김'; }); });</script>
