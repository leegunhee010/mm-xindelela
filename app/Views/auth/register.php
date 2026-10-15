<h1 class="auth-h">회원가입 (미얀마 소매상·셀러)</h1>
<form class="form wide" method="post" action="<?= site_url('register') ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="card">
    <h3>계정 정보</h3>
    <div class="g2">
      <div class="field"><label>이메일 <i>*</i></label><input class="inp" type="email" name="email" value="<?= esc(old('email')) ?>" required></div>
      <div class="field"><label>담당자 이름 <i>*</i></label><input class="inp" type="text" name="name" value="<?= esc(old('name')) ?>" required></div>
      <div class="field"><label>비밀번호 <i>*</i></label><input class="inp" type="password" name="password" required><div class="help">6자 이상</div></div>
      <div class="field"><label>비밀번호 확인 <i>*</i></label><input class="inp" type="password" name="password2" required></div>
      <div class="field"><label>전화번호 <i>*</i></label><input class="inp" type="text" name="phone" value="<?= esc(old('phone')) ?>" placeholder="09-XXX-XXX-XXX" required></div>
      <div class="field"><label>Viber 번호</label><input class="inp" type="text" name="viber" value="<?= esc(old('viber')) ?>" placeholder="전화번호와 같으면 비워 두세요"></div>
    </div>
  </div>

  <div class="card mt16">
    <h3>사업 정보</h3>
    <div class="g2">
      <div class="field"><label>상호 (가게·페이지 이름) <i>*</i></label><input class="inp" type="text" name="company_name" value="<?= esc(old('company_name')) ?>" required></div>
      <div class="field"><label>지역(도시) <i>*</i></label><input class="inp" type="text" name="city" value="<?= esc(old('city')) ?>" placeholder="Yangon, Mandalay ..." required></div>
    </div>
    <div class="field"><label>판매 형태 <i>*</i></label>
      <div class="radios">
        <label><input type="radio" name="business_type" value="online" <?= old('business_type') === 'online' ? 'checked' : '' ?>> 온라인(페이스북·틱톡 등)</label>
        <label><input type="radio" name="business_type" value="offline" <?= old('business_type') === 'offline' ? 'checked' : '' ?>> 오프라인 매장</label>
        <label><input type="radio" name="business_type" value="both" <?= old('business_type', 'both') === 'both' ? 'checked' : '' ?>> 둘 다</label>
      </div>
    </div>
    <div class="field"><label>주소</label><input class="inp" type="text" name="address" value="<?= esc(old('address')) ?>"></div>
  </div>

  <div class="card mt16">
    <h3>사업자 인증 자료 <span class="muted small" style="font-weight:400">(페이스북 페이지 또는 자료 중 하나 이상)</span></h3>
    <div class="field"><label>페이스북 페이지 / 틱톡샵 주소</label><input class="inp" type="text" name="fb_page" value="<?= esc(old('fb_page')) ?>" placeholder="https://facebook.com/yourshop"></div>
    <div class="field"><label>인증 자료 업로드</label><input type="file" name="docs[]" multiple accept="image/*,.pdf" data-list="#docList"><ul id="docList" class="small muted mt8"></ul>
      <div class="help">매장 간판·진열 사진, 영업허가증(YCDC/MCDC), 회사등록증(DICA), 판매 스크린샷 등. JPG/PNG/PDF, 파일당 5MB 이하.</div></div>
    <div class="box-note">담당자가 제출 자료를 직접 확인한 뒤 <b><?= esc(mm_setting('verify_sla_text')) ?></b> 승인합니다. 승인 전에는 상품은 볼 수 있지만 도매가와 주문 신청은 제한됩니다.</div>
  </div>

  <div class="card mt16">
    <h3>추천 코드 <span class="muted small" style="font-weight:400">(있으면 입력)</span></h3>
    <div class="field"><input class="inp" type="text" name="ref" value="<?= esc(old('ref', $ref)) ?>" placeholder="예: AB12CD34" style="text-transform:uppercase"><div class="help">소개해 준 회원의 추천 코드입니다. 내가 승인되면 소개한 회원에게 포인트가 적립됩니다.</div></div>
  </div>

  <button class="btn btn-g btn-block mt16" style="height:50px;font-size:16px" type="submit">가입하고 인증 신청하기</button>
  <div class="auth-sub">이미 회원이신가요? <a href="<?= site_url('login') ?>">로그인</a></div>
</form>
