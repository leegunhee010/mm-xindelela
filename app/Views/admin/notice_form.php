<form method="post" action="<?= site_url('admin/notices/save') ?>" class="card" style="max-width:900px"><?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= $n['id'] ?? '' ?>">
  <div class="field"><label>제목</label><input class="inp" name="title" value="<?= esc(old('title', $n['title'] ?? '')) ?>" required></div>
  <div class="field"><label>미얀마어 제목</label><input class="inp" name="title_my" lang="my" value="<?= esc(old('title_my', $n['title_my'] ?? '')) ?>" placeholder="비우면 미얀마어 화면에도 한국어 제목"></div>
  <div class="field"><label><input type="checkbox" name="is_pinned" value="1" <?= ($n['is_pinned'] ?? 0) ? 'checked' : '' ?>> 상단 고정</label></div>
  <div class="field"><label>본문 (HTML 가능)</label><textarea class="ed" name="body"><?= esc(old('body', $n['body'] ?? '')) ?></textarea></div>
  <div class="field"><label>미얀마어 본문 (HTML 가능, 비우면 한국어 본문)</label><textarea class="ed" name="body_my" lang="my"><?= esc(old('body_my', $n['body_my'] ?? '')) ?></textarea></div>
  <div class="mmrow"><button class="btn btn-g" type="submit">저장</button><a class="btn" href="<?= site_url('admin/notices') ?>">목록</a></div>
</form>
