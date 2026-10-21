<?php /* 상품 상세: 줄서기 박스 (입고 예정 상품). $p, $approved, $user */
$qN   = mm_queue_counts()[(int) $p['id']] ?? 0;
$mine = mm_my_queue((int) $p['id']);
$unit = esc($p['unit']);
$moq  = (int) $p['moq'];
?>
<div class="qbox">
  <div class="qbox-h">
    <span class="q-badge"><?= esc(mm_q('badge')) ?></span>
    <b class="qbox-n"><?= $qN ? esc(mm_q('count', number_format($qN))) : esc(mm_q('count_zero')) ?></b>
    <details class="qhelp">
      <summary><?= esc(mm_q('help_title')) ?> <i aria-hidden="true">?</i></summary>
      <div class="qhelp-pop">
        <b><?= esc(mm_q('help_title')) ?></b>
        <ul><li><?= esc(mm_q('help_1')) ?></li><li><?= esc(mm_q('help_2')) ?></li><li><?= esc(mm_q('help_3')) ?></li></ul>
      </div>
    </details>
  </div>

  <?php if ($mine): ?>
    <div class="qbox-me">
      <p class="qbox-me-t"><?= esc(mm_q('joined_title')) ?></p>
      <p class="qbox-me-d"><?= esc(mm_q('joined_desc', number_format((int) $mine['qty']) . $p['unit'])) ?></p>
    </div>
    <form class="qbox-row" method="post" action="<?= site_url('products/' . $p['id'] . '/queue') ?>"><?= csrf_field() ?>
      <label class="qbox-qty"><span><?= esc(mm_q('qty_label')) ?></span><input type="number" name="qty" min="<?= $moq ?>" step="1" value="<?= (int) $mine['qty'] ?>" inputmode="numeric"><em><?= $unit ?></em></label>
      <button type="submit" class="qbox-btn is-line"><?= esc(mm_q('btn_update')) ?></button>
    </form>
    <form method="post" action="<?= site_url('products/' . $p['id'] . '/queue/cancel') ?>" onsubmit="return confirm('<?= esc(mm_q('btn_cancel')) ?> 할까요?')"><?= csrf_field() ?><button type="submit" class="qbox-cancel"><?= esc(mm_q('btn_cancel')) ?></button></form>

  <?php elseif ($approved): ?>
    <form method="post" action="<?= site_url('products/' . $p['id'] . '/queue') ?>" id="qJoin"><?= csrf_field() ?>
      <label class="qbox-qty"><span><?= esc(mm_q('qty_label')) ?></span><input type="number" name="qty" min="<?= $moq ?>" step="1" value="<?= $moq ?>" inputmode="numeric"><em><?= $unit ?> (최소 <?= number_format($moq) ?><?= $unit ?>)</em></label>
      <label class="qbox-agree"><input type="checkbox" name="agree" value="1" required><span><?= esc(mm_q('agree_short')) ?></span></label>
      <button type="submit" class="qbox-btn"><?= esc(mm_q('btn')) ?></button>
    </form>

  <?php else: ?>
    <p class="qbox-lock"><?= esc(mm_q('need_verify')) ?> <?= esc(mm_setting('verify_sla_text')) ?> 끝납니다.</p>
    <div class="qbox-btns">
      <?php if ($user && $user['role'] === 'buyer'): ?>
        <a class="qbox-btn" href="<?= site_url('mypage/verify') ?>">인증 상태 보기</a>
      <?php else: ?>
        <a class="qbox-btn" href="<?= site_url('register') ?>">회원가입하고 인증하기</a><a class="qbox-btn is-line" href="<?= site_url('login') ?>">로그인</a>
      <?php endif ?>
    </div>
  <?php endif ?>
</div>
