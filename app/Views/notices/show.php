<div class="crumb"><a href="<?= site_url('/') ?>">홈</a><a href="<?= site_url('notices') ?>">공지사항</a><?= esc($n['title']) ?></div>
<div style="border-top:2px solid var(--ink);padding:18px 10px;border-bottom:1px solid var(--line)">
  <h1 style="font-size:20px"><?= esc($n['title']) ?></h1>
  <div class="small muted mt8"><?= mm_dt($n['created_at']) ?> · 조회 <?= number_format((int) $n['views']) ?></div>
</div>
<div class="pdesc"><?= $n['body'] ?></div>
<a class="btn" href="<?= site_url('notices') ?>">목록</a>
