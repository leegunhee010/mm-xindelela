<!DOCTYPE html>
<html lang="ko"><head><meta charset="UTF-8"><title>관리자 로그인</title><?= view('_icons') ?><meta name="robots" content="noindex">
<link rel="stylesheet" href="<?= base_url('assets/css/mm.css?v=1') ?>"><link rel="stylesheet" href="<?= base_url('assets/css/admin.css?v=1') ?>"></head>
<body><div class="login-wrap"><form class="login-box" method="post" action="<?= site_url('admin/login') ?>"><?= csrf_field() ?>
  <h1>XINDELELA 관리자</h1>
  <?= mm_flash() ?>
  <div class="field"><label>이메일</label><input class="inp" type="email" name="email" required autofocus></div>
  <div class="field"><label>비밀번호</label><input class="inp" type="password" name="password" required></div>
  <button class="btn btn-dark btn-block" type="submit">로그인</button>
</form></div></body></html>
