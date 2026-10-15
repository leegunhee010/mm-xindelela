<div class="two">
<div>
<table class="tbl"><thead><tr><th>ID</th><th class="l">이름</th><th>순서</th><th>상품수</th><th>노출</th><th style="width:260px">수정</th></tr></thead><tbody>
<?php foreach ($rows as $c): ?>
<tr><td><?= $c['id'] ?></td><td class="l"><?= esc($c['name']) ?></td><td><?= $c['sort'] ?></td><td><?= $c['pcnt'] ?></td><td><?= $c['is_active'] ? '<span class="st st-active">노출</span>' : '<span class="st st-hidden">숨김</span>' ?></td>
<td><form method="post" action="<?= site_url('admin/categories/save') ?>" class="inl"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $c['id'] ?>"><input class="inp" name="name" value="<?= esc($c['name']) ?>" style="width:120px"><input class="inp" type="number" name="sort" value="<?= $c['sort'] ?>" style="width:56px"><label class="small"><input type="checkbox" name="is_active" value="1" <?= $c['is_active'] ? 'checked' : '' ?>>노출</label><button class="btn btn-xs" type="submit">저장</button></form>
<form method="post" action="<?= site_url('admin/categories/' . $c['id'] . '/delete') ?>" style="display:inline" onsubmit="return confirm('삭제할까요?')"><?= csrf_field() ?><button class="btn btn-xs btn-red" type="submit">삭제</button></form></td></tr>
<?php endforeach ?>
</tbody></table>
</div>
<div class="act"><h3>카테고리 추가</h3>
<form method="post" action="<?= site_url('admin/categories/save') ?>"><?= csrf_field() ?>
<div class="field"><label>이름</label><input class="inp" name="name" required></div>
<div class="field"><label>순서</label><input class="inp" type="number" name="sort" value="<?= count($rows) + 1 ?>"></div>
<div class="field"><label><input type="checkbox" name="is_active" value="1" checked> 노출</label></div>
<button class="btn btn-g" type="submit">추가</button></form></div>
</div>
