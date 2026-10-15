<div class="kpi" style="grid-template-columns:repeat(3,1fr)">
  <div>총 지급<b><?= number_format($sum) ?> P</b></div><div>총 사용·차감<b><?= number_format($used) ?> P</b></div><div>회원 보유 잔액 합계<b><?= number_format($bal) ?> P</b></div>
</div>
<div class="tabs">
  <a href="<?= site_url('admin/points') ?>" class="<?= $type === '' ? 'on' : '' ?>">전체</a>
  <?php foreach (['referral_signup', 'referral_first_order', 'signup_bonus', 'order_use', 'order_refund', 'order_reward', 'admin_adjust'] as $t): ?><a href="<?= site_url('admin/points?type=' . $t) ?>" class="<?= $type === $t ? 'on' : '' ?>"><?= mm_point_type_label($t) ?></a><?php endforeach ?>
</div>
<table class="tbl"><thead><tr><th>일시</th><th class="l">회원</th><th>유형</th><th class="l">메모</th><th>증감</th><th>잔액</th></tr></thead><tbody>
<?php if (! $rows): ?><tr><td colspan="6" style="padding:40px">내역이 없습니다.</td></tr><?php endif ?>
<?php foreach ($rows as $l): ?><tr><td><?= mm_dt($l['created_at']) ?></td><td class="l"><a href="<?= site_url('admin/buyers/' . $l['user_id']) ?>"><?= esc($l['user']['company_name'] ?? '#' . $l['user_id']) ?></a></td><td><?= mm_point_type_label($l['type']) ?></td><td class="l"><?= esc($l['memo']) ?></td><td class="<?= $l['amount'] >= 0 ? 'pt-plus' : 'pt-minus' ?>"><?= $l['amount'] >= 0 ? '+' : '' ?><?= number_format((int) $l['amount']) ?></td><td><?= number_format((int) $l['balance_after']) ?></td></tr><?php endforeach ?>
</tbody></table>
<?= $pager->links('p', 'mm_pager') ?>
