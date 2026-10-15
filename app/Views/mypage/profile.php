<div class="flex gap-[60px] items-start">
<?= view('mypage/_side') ?>
<div class="flex-1 min-w-0">
  <div class="contents-title-area depth-1 title3 font-bold !pt-0 mb-[20px]"><div class="title-area !pt-0"><h3 class="title">회원 정보</h3></div></div>
  <div class="card">
    <h3>기본 정보</h3>
    <form method="post" action="<?= site_url('mypage/profile') ?>"><?= csrf_field() ?>
      <div class="g2">
        <div class="field"><label>이메일</label><input class="h-[44px] w-full px-3 bg-white body3 text-black border border-gray6 rounded-[8px] focus:outline-none focus:border-black" value="<?= esc($user['email']) ?>" disabled></div>
        <div class="field"><label>상호</label><input class="h-[44px] w-full px-3 bg-white body3 text-black border border-gray6 rounded-[8px] focus:outline-none focus:border-black" value="<?= esc($user['company_name']) ?>" disabled><div class="help">상호 변경은 고객센터로 요청해 주세요.</div></div>
        <div class="field"><label>담당자 이름</label><input class="h-[44px] w-full px-3 bg-white body3 text-black border border-gray6 rounded-[8px] focus:outline-none focus:border-black" name="name" value="<?= esc($user['name']) ?>"></div>
        <div class="field"><label>전화번호</label><input class="h-[44px] w-full px-3 bg-white body3 text-black border border-gray6 rounded-[8px] focus:outline-none focus:border-black" name="phone" value="<?= esc($user['phone']) ?>"></div>
        <div class="field"><label>Viber</label><input class="h-[44px] w-full px-3 bg-white body3 text-black border border-gray6 rounded-[8px] focus:outline-none focus:border-black" name="viber" value="<?= esc($user['viber']) ?>"></div>
        <div class="field"><label>도시</label><input class="h-[44px] w-full px-3 bg-white body3 text-black border border-gray6 rounded-[8px] focus:outline-none focus:border-black" name="city" value="<?= esc($user['city']) ?>"></div>
      </div>
      <div class="field"><label>주소</label><input class="h-[44px] w-full px-3 bg-white body3 text-black border border-gray6 rounded-[8px] focus:outline-none focus:border-black" name="address" value="<?= esc($user['address']) ?>"></div>
      <button class="inline-flex items-center text-white bg-primary min-h-[44px] px-[20px] py-[12px] body3 !leading-none rounded-full font-bold" type="submit">저장</button>
    </form>
  </div>
  <div class="card mt16">
    <h3>비밀번호 변경</h3>
    <form method="post" action="<?= site_url('mypage/password') ?>"><?= csrf_field() ?>
      <div class="g2">
        <div class="field"><label>현재 비밀번호</label><input class="h-[44px] w-full px-3 bg-white body3 text-black border border-gray6 rounded-[8px] focus:outline-none focus:border-black" type="password" name="current" required></div>
        <div></div>
        <div class="field"><label>새 비밀번호</label><input class="h-[44px] w-full px-3 bg-white body3 text-black border border-gray6 rounded-[8px] focus:outline-none focus:border-black" type="password" name="password" required></div>
        <div class="field"><label>새 비밀번호 확인</label><input class="h-[44px] w-full px-3 bg-white body3 text-black border border-gray6 rounded-[8px] focus:outline-none focus:border-black" type="password" name="password2" required></div>
      </div>
      <button class="inline-flex items-center text-black bg-white border border-gray6 min-h-[44px] px-[20px] py-[12px] body3 !leading-none rounded-full font-bold" type="submit">비밀번호 변경</button>
    </form>
  </div>
</div>
</div>
