<div class="two">
<div>
<h3 style="margin-bottom:10px">추천으로 가입한 회원</h3>
<table class="tbl"><thead><tr><th class="l">가입 회원</th><th>가입일</th><th>인증</th><th>첫 주문</th><th class="l">추천인</th></tr></thead><tbody>
<?php if (! $rows): ?><tr><td colspan="5" style="padding:40px">추천 가입이 없습니다.</td></tr><?php endif ?>
<?php foreach ($rows as $r): ?><tr><td class="l"><a href="<?= site_url('admin/buyers/' . $r['id']) ?>"><?= esc($r['company_name']) ?></a></td><td><?= mm_dt($r['created_at'], 'Y.m.d') ?></td><td><span class="st st-<?= esc($r['status']) ?>"><?= mm_status_label($r['status']) ?></span></td><td><?= $r['first_order_at'] ? mm_dt($r['first_order_at'], 'Y.m.d') : '-' ?></td><td class="l"><?php if ($r['referrer']): ?><a href="<?= site_url('admin/buyers/' . $r['referrer']['id']) ?>"><?= esc($r['referrer']['company_name']) ?></a> <span class="muted"><?= esc($r['referrer']['referral_code']) ?></span><?php endif ?></td></tr><?php endforeach ?>
</tbody></table>
<?= $pager->links('p', 'mm_pager') ?>
</div>
<div class="act"><h3>추천 상위 회원</h3>
<?php if (! $top): ?><div class="muted">없음</div><?php else: ?><table class="tbl"><thead><tr><th class="l">회원</th><th>소개 수</th></tr></thead><tbody><?php foreach ($top as $t): ?><tr><td class="l"><?= esc($t['user']['company_name'] ?? '#' . $t['referred_by']) ?></td><td><b><?= $t['cnt'] ?></b></td></tr><?php endforeach ?></tbody></table><?php endif ?></div>
</div>
