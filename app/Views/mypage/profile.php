<div class="my">
<?= view('mypage/_side') ?>
<div class="main">
  <h1>회원 정보</h1>
  <div class="card">
    <h3>기본 정보</h3>
    <form method="post" action="<?= site_url('mypage/profile') ?>"><?= csrf_field() ?>
      <div class="g2">
        <div class="field"><label>이메일</label><input class="inp" value="<?= esc($user['email']) ?>" disabled></div>
        <div class="field"><label>상호</label><input class="inp" value="<?= esc($user['company_name']) ?>" disabled><div class="help">상호 변경은 고객센터로 요청해 주세요.</div></div>
        <div class="field"><label>담당자 이름</label><input class="inp" name="name" value="<?= esc($user['name']) ?>"></div>
        <div class="field"><label>전화번호</label><input class="inp" name="phone" value="<?= esc($user['phone']) ?>"></div>
        <div class="field"><label>Viber</label><input class="inp" name="viber" value="<?= esc($user['viber']) ?>"></div>
        <div class="field"><label>도시</label><input class="inp" name="city" value="<?= esc($user['city']) ?>"></div>
      </div>
      <div class="field"><label>주소</label><input class="inp" name="address" value="<?= esc($user['address']) ?>"></div>
      <button class="btn btn-g" type="submit">저장</button>
    </form>
  </div>
  <div class="card mt16">
    <h3>비밀번호 변경</h3>
    <form method="post" action="<?= site_url('mypage/password') ?>"><?= csrf_field() ?>
      <div class="g2">
        <div class="field"><label>현재 비밀번호</label><input class="inp" type="password" name="current" required></div>
        <div></div>
        <div class="field"><label>새 비밀번호</label><input class="inp" type="password" name="password" required></div>
        <div class="field"><label>새 비밀번호 확인</label><input class="inp" type="password" name="password2" required></div>
      </div>
      <button class="btn" type="submit">비밀번호 변경</button>
    </form>
  </div>
</div>
</div>
