<div class="my">
<?= view('mypage/_side') ?>
<div class="main">
  <h1>친구 소개</h1>
  <div class="refbox">
    <div class="small muted">내 추천 코드</div>
    <div class="code"><?= esc($user['referral_code']) ?></div>
    <div class="small muted">가입할 때 이 코드를 입력하거나, 아래 링크로 가입하면 자동 입력됩니다.</div>
    <div class="lnk"><input type="text" id="refLink" value="<?= esc($link) ?>" readonly><button class="btn btn-sm" type="button" data-copy="#refLink">복사</button></div>
  </div>
  <div class="refrule">
    <div>소개한 회원이 <b style="font-size:14px">사업자 인증을 통과하면</b><b>+<?= number_format((int) mm_setting('points_referral_signup', 0)) ?> P</b></div>
    <div>소개한 회원이 <b style="font-size:14px">첫 주문 입금을 완료하면</b><b>+<?= number_format((int) mm_setting('points_referral_first_order', 0)) ?> P</b></div>
  </div>
  <div class="sec-h"><h2 style="font-size:15px">내가 소개한 회원 <?= count($referred) ?>명</h2></div>
  <?php if (! $referred): ?><div class="empty">아직 소개한 회원이 없습니다.</div><?php else: ?>
  <table class="tbl"><thead><tr><th>상호</th><th>가입일</th><th>인증</th><th>첫 주문</th></tr></thead><tbody>
  <?php foreach ($referred as $r): ?><tr><td><?= esc($r['company_name'] ?: $r['name']) ?></td><td><?= mm_dt($r['created_at'], 'Y.m.d') ?></td><td><span class="st st-<?= esc($r['status']) ?>"><?= mm_status_label($r['status']) ?></span></td><td><?= $r['first_order_at'] ? mm_dt($r['first_order_at'], 'Y.m.d') : '-' ?></td></tr><?php endforeach ?>
  </tbody></table><?php endif ?>
</div>
</div>
