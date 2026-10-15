<h1 class="auth-h">공급사 로그인</h1>
<form class="form" method="post" action="<?= site_url('supplier/login') ?>" style="width:400px"><?= csrf_field() ?>
  <div class="field"><label>이메일</label><input class="inp" type="email" name="email" value="<?= esc(old('email')) ?>" required autofocus></div>
  <div class="field"><label>비밀번호</label><input class="inp" type="password" name="password" required></div>
  <button class="btn btn-dark btn-block" style="height:46px;font-size:15px" type="submit">로그인</button>
  <div class="auth-sub">입점을 원하시나요? <a href="<?= site_url('supplier/register') ?>">공급사 입점 신청</a></div>
</form>
<div class="box-note" style="width:400px;margin:24px auto 0">데모 공급사: supplier@demo.kr / supplier1234</div>
