<form method="post" action="<?= site_url('admin/settings') ?>" style="max-width:860px"><?= csrf_field() ?>
<?php foreach ($fields as $group => $items): ?>
<div class="card"><h3><?= $group ?></h3>
  <div class="g2">
  <?php foreach ($items as $k => [$label, $help, $type]): ?>
    <div class="field"><label><?= $label ?></label><input class="inp" type="<?= $type ?>" name="<?= $k ?>" value="<?= esc($vals[$k] ?? '') ?>" <?= $type === 'number' ? 'step="any"' : '' ?>><?php if ($help): ?><div class="help"><?= $help ?></div><?php endif ?></div>
  <?php endforeach ?>
  </div>
</div>
<?php endforeach ?>
<button class="btn btn-g" type="submit" style="height:44px;padding:0 30px">설정 저장</button>
</form>
