<p class="mb16"><a href="<?= site_url('admin/queues') ?>" class="muted">‹ 줄서기 목록</a></p>
<form method="post" action="<?= site_url('admin/queues/texts') ?>" class="card"><?= csrf_field() ?>
  <h3>줄서기 문구</h3>
  <p class="muted small" style="margin:-4px 0 16px">회색 글씨가 지금 쓰는 기본 문구입니다. 바꾸고 싶은 칸만 적으세요. 비우면 기본 문구로 돌아갑니다. <b>%s</b> 는 지우지 마세요(숫자·상품명이 들어가는 자리).</p>
  <table class="tbl qtxt"><tbody>
  <?php foreach ($keys as $k => $label): $long = str_starts_with($k, 'help_') || str_ends_with($k, '_body'); ?>
    <tr>
      <th class="l"><?= esc($label) ?></th>
      <td class="l"><?php if ($long): ?><textarea class="ta" name="t_<?= $k ?>" rows="2" placeholder="<?= esc($def[$k]) ?>"><?= esc($over[$k] ?? '') ?></textarea><?php else: ?><input class="inp" name="t_<?= $k ?>" value="<?= esc($over[$k] ?? '') ?>" placeholder="<?= esc($def[$k]) ?>"><?php endif ?></td>
    </tr>
  <?php endforeach ?>
  </tbody></table>
  <button class="btn btn-g mt8" type="submit" style="height:42px;padding:0 28px">저장</button>
</form>
