<!DOCTYPE html>
<html lang="ko"><head><meta charset="UTF-8"><title>관리자 로그인</title><meta name="robots" content="noindex">
<link rel="stylesheet" href="<?= base_url('assets/css/mm.css?v=1') ?>"><link rel="stylesheet" href="<?= base_url('assets/css/admin.css?v=1') ?>"></head>
<body><div class="login-wrap"><form class="login-box" method="post" action="<?= site_url('admin/login') ?>"><?= csrf_field() ?>
  <h1>XINDELELA 관리자</h1>
  <?= mm_flash() ?>
  <div class="field"><label>이메일</label><input class="inp" type="email" name="email" required autofocus></div>
  <div class="field"><label>비밀번호</label><input class="inp" type="password" name="password" required></div>
  <button class="btn btn-dark btn-block" type="submit">로그인</button>
  <div class="small muted mt16 tc">초기 계정 admin@xindelela.com / admin1234 (배포 전 변경)</div>
</form></div></body></html>
