<div class="two">
<div>
<?php foreach ($rows as $b): ?>
<form method="post" action="<?= site_url('admin/banners/save') ?>" enctype="multipart/form-data" class="card"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $b['id'] ?>">
  <div class="g2">
    <div class="field"><label>제목 (줄바꿈 = 줄 나눔)</label><textarea class="ta" name="title" style="height:60px"><?= esc($b['title']) ?></textarea></div>
    <div class="field"><label>부제</label><input class="inp" name="subtitle" value="<?= esc($b['subtitle']) ?>"><label class="mt8">링크</label><input class="inp" name="link" value="<?= esc($b['link']) ?>"></div>
    <div class="field"><label>배경색</label><input class="inp" name="bg" value="<?= esc($b['bg']) ?>"></div>
    <div class="field"><label>순서 / 노출</label><div class="inl"><input class="inp" type="number" name="sort" value="<?= $b['sort'] ?>" style="width:70px"><label><input type="checkbox" name="is_active" value="1" <?= $b['is_active'] ? 'checked' : '' ?>> 노출</label></div></div>
    <div class="field"><label>이미지 (선택, 735×300)</label><input type="file" name="image" accept="image/*"><?php if ($b['image']): ?><img src="<?= base_url($b['image']) ?>" alt="" style="height:60px;margin-top:6px"><?php endif ?></div>
  </div>
  <div class="row"><button class="btn btn-sm btn-g" type="submit">저장</button><button class="btn btn-sm btn-red" type="submit" formaction="<?= site_url('admin/banners/' . $b['id'] . '/delete') ?>" formnovalidate onclick="return confirm('삭제할까요?')">삭제</button></div>
</form>
<?php endforeach ?>
</div>
<form method="post" action="<?= site_url('admin/banners/save') ?>" enctype="multipart/form-data" class="act"><?= csrf_field() ?><h3>배너 추가</h3>
  <div class="field"><label>제목</label><textarea class="ta" name="title" style="height:60px"></textarea></div>
  <div class="field"><label>부제</label><input class="inp" name="subtitle"></div>
  <div class="field"><label>링크</label><input class="inp" name="link" value="/products"></div>
  <div class="field"><label>배경색</label><input class="inp" name="bg" value="#eef5e3"></div>
  <div class="field"><label>순서</label><input class="inp" type="number" name="sort" value="<?= count($rows) + 1 ?>"></div>
  <div class="field"><label>이미지</label><input type="file" name="image" accept="image/*"></div>
  <input type="hidden" name="is_active" value="1">
  <button class="btn btn-g" type="submit">추가</button>
</form>
</div>
