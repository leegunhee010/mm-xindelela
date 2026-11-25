<form method="post" action="<?= site_url('admin/password') ?>" class="card" style="max-width:480px"><?= csrf_field() ?>
  <?php if ($weak): ?><p style="color:var(--price);margin:0 0 14px">초기 비밀번호로 로그인했습니다. 새 비밀번호로 바꿔야 다른 화면을 쓸 수 있습니다.</p><?php endif ?>
  <div class="field"><label>현재 비밀번호</label><input class="inp" type="password" name="current" autocomplete="current-password" required></div>
  <div class="field"><label>새 비밀번호 (10자 이상)</label><input class="inp" type="password" name="password" autocomplete="new-password" minlength="10" required></div>
  <div class="field"><label>새 비밀번호 확인</label><input class="inp" type="password" name="password2" autocomplete="new-password" minlength="10" required></div>
  <button class="btn btn-g" type="submit">비밀번호 바꾸기</button>
</form>
