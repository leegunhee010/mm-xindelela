<div class="two">
<div>
  <form method="post" action="<?= site_url('admin/notify/send') ?>" class="card" onsubmit="return confirm('알림을 보낼까요? 보낸 알림은 되돌릴 수 없습니다.')"><?= csrf_field() ?>
    <h3>알림 보내기</h3>
    <p class="muted small" style="margin:-4px 0 14px">회원은 사이트 헤더의 종 아이콘과 마이페이지 › 알림에서 봅니다. 입고·할인·공지 안내에 쓰세요.</p>
    <div class="field"><label>받는 회원</label>
      <div class="nto">
        <?php $old = old('to', $product ? 'wish' : 'approved');
        foreach (['approved' => ['인증 회원 전체', $counts['approved']], 'all' => ['가입 회원 전체', $counts['all']], 'pending' => ['인증 전 회원 (검수 중·반려)', $counts['pending']]] as $k => [$l, $n]): ?>
          <label><input type="radio" name="to" value="<?= $k ?>" <?= $old === $k ? 'checked' : '' ?>> <?= $l ?> <span class="muted">(<?= number_format($n) ?>명)</span></label>
        <?php endforeach ?>
        <?php if ($product): ?><label><input type="radio" name="to" value="wish" <?= $old === 'wish' ? 'checked' : '' ?>> '<?= esc($product['name']) ?>' 관심상품에 담은 회원 <span class="muted">(<?= number_format($counts['wish']) ?>명)</span></label><input type="hidden" name="product" value="<?= $product['id'] ?>"><?php endif ?>
        <label><input type="radio" name="to" value="one" <?= $old === 'one' ? 'checked' : '' ?>> 특정 회원 한 명 <input class="inp" name="email" value="<?= esc(old('email')) ?>" placeholder="이메일 또는 추천 코드" style="width:220px;height:32px;margin-left:6px"></label>
      </div>
    </div>
    <div class="field"><label>제목 <i>*</i></label><input class="inp" name="title" maxlength="200" value="<?= esc(old('title')) ?>" placeholder="예: 마스크팩 10매입 재입고되었습니다" required></div>
    <div class="field"><label>내용 (선택)</label><textarea class="ta" name="body" rows="3" maxlength="500" placeholder="예: 이번 주 금요일까지 수량별 단가를 한 단계 낮췄습니다."><?= esc(old('body')) ?></textarea></div>
    <div class="field"><label>누르면 갈 곳 (선택)</label><input class="inp" name="link" value="<?= esc(old('link', $product ? 'products/' . $product['id'] : '')) ?>" placeholder="예: products/12 · notices/3"><div class="help">사이트 안 주소만. 비우면 알림만 보입니다.</div></div>
    <button class="btn btn-g" type="submit" style="height:42px;padding:0 28px">알림 보내기</button>
  </form>
</div>
<div>
  <div class="act"><h3>보낸 알림</h3>
    <?php if (! $sent): ?><p class="muted small">아직 보낸 알림이 없습니다.</p><?php endif ?>
    <?php foreach ($sent as $s): ?>
      <div class="nsent">
        <b><?= esc($s['title']) ?></b>
        <?php if ($s['body']): ?><p><?= esc(mb_strimwidth($s['body'], 0, 80, '…')) ?></p><?php endif ?>
        <span><?= mm_dt($s['at'], 'Y.m.d H:i') ?> · <?= number_format((int) $s['n']) ?>명 · 읽음 <?= number_format((int) $s['r']) ?>명</span>
      </div>
    <?php endforeach ?>
  </div>
</div>
</div>
