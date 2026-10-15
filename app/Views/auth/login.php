<h1 class="auth-h">로그인</h1>
<form class="form" method="post" action="<?= site_url('login') ?>" style="width:400px">
  <?= csrf_field() ?>
  <div class="field"><label>이메일</label><input class="inp" type="email" name="email" value="<?= esc(old('email')) ?>" required autofocus></div>
  <div class="field"><label>비밀번호</label><input class="inp" type="password" name="password" required></div>
  <button class="btn btn-g btn-block" style="height:46px;font-size:15px" type="submit">로그인</button>
  <div class="auth-sub">아직 회원이 아니신가요? <a href="<?= site_url('register') ?>">회원가입</a> | <a href="<?= site_url('supplier/login') ?>">공급사 로그인</a></div>
</form>
<div class="box-note" style="width:400px;margin:24px auto 0">데모 계정: buyer@demo.mm / buyer1234 (승인됨) · pending@demo.mm / buyer1234 (검수 대기)</div>
