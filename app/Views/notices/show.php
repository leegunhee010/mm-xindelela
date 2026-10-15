<div style="border-top:2px solid var(--ink);padding:18px 10px;border-bottom:1px solid var(--line)">
  <h1 style="font-size:20px"><?= esc($n['title']) ?></h1>
  <div class="small muted mt8"><?= mm_dt($n['created_at']) ?> · 조회 <?= number_format((int) $n['views']) ?></div>
</div>
<div class="pdesc"><?= $n['body'] ?></div>
<a class="inline-flex items-center text-black bg-white border border-gray6 min-h-[44px] px-[20px] py-[12px] body3 !leading-none rounded-full font-bold mt-[24px]" href="<?= site_url('notices') ?>">목록</a>
