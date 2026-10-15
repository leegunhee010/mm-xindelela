<h1 class="auth-h">공급사 입점 신청</h1>
<p class="tc muted" style="margin-top:-12px;margin-bottom:20px">한국 브랜드·제조사·유통사가 미얀마 소매상에게 도매로 공급합니다. 승인 후 상품을 직접 등록할 수 있습니다.</p>
<form class="form wide" method="post" action="<?= site_url('supplier/register') ?>"><?= csrf_field() ?>
  <div class="card"><h3>계정</h3>
    <div class="g2">
      <div class="field"><label>이메일 <i>*</i></label><input class="inp" type="email" name="email" value="<?= esc(old('email')) ?>" required></div>
      <div class="field"><label>담당자 <i>*</i></label><input class="inp" name="contact_name" value="<?= esc(old('contact_name')) ?>" required></div>
      <div class="field"><label>비밀번호 <i>*</i></label><input class="inp" type="password" name="password" required></div>
      <div class="field"><label>비밀번호 확인 <i>*</i></label><input class="inp" type="password" name="password2" required></div>
    </div></div>
  <div class="card mt16"><h3>회사 정보</h3>
    <div class="g2">
      <div class="field"><label>회사명 <i>*</i></label><input class="inp" name="company_name" value="<?= esc(old('company_name')) ?>" required></div>
      <div class="field"><label>브랜드</label><input class="inp" name="brand" value="<?= esc(old('brand')) ?>"></div>
      <div class="field"><label>연락처 <i>*</i></label><input class="inp" name="phone" value="<?= esc(old('phone')) ?>" required></div>
      <div class="field"><label>사업자등록번호</label><input class="inp" name="biz_no" value="<?= esc(old('biz_no')) ?>"></div>
    </div>
    <div class="field"><label>주소</label><input class="inp" name="address" value="<?= esc(old('address')) ?>"></div>
    <div class="field"><label>회사·상품 소개</label><textarea class="ta" name="intro"><?= esc(old('intro')) ?></textarea></div>
  </div>
  <button class="btn btn-dark btn-block mt16" style="height:50px;font-size:16px" type="submit">입점 신청</button>
  <div class="auth-sub">이미 공급사이신가요? <a href="<?= site_url('supplier/login') ?>">공급사 로그인</a></div>
</form>
