<?php $docs = mm_json($user['doc_files']); ?>
<div class="flex gap-[60px] items-start">
<?= view('mypage/_side') ?>
<div class="flex-1 min-w-0">
  <div class="contents-title-area depth-1 title3 font-bold !pt-0 mb-[20px]"><div class="title-area !pt-0"><h3 class="title">사업자 인증</h3></div></div>
  <div class="vstat">
    <h2>현재 상태: <span class="st st-<?= esc($user['status']) ?>" style="font-size:13px"><?= mm_status_label($user['status']) ?></span></h2>
    <?php if ($user['status'] === 'pending'): ?><p>제출 자료를 담당자가 직접 확인하고 있습니다. <?= esc(mm_setting('verify_sla_text')) ?> 승인됩니다. 승인되면 도매가와 주문 신청이 열립니다.</p>
    <?php elseif ($user['status'] === 'approved'): ?><p>인증이 완료되었습니다. 승인일 <?= mm_dt($user['approved_at']) ?></p>
    <?php elseif ($user['status'] === 'rejected'): ?><p style="color:var(--price)"><b>반려 사유:</b> <?= nl2br(esc($user['reject_reason'] ?: '-')) ?></p><p>아래에서 자료를 보완해 다시 제출해 주세요.</p>
    <?php else: ?><p>고객센터로 문의해 주세요.</p><?php endif ?>
  </div>
  <table class="kv">
    <tr><th>상호</th><td><?= esc($user['company_name']) ?></td></tr>
    <tr><th>인증 유형</th><td><?= mm_verify_labels($user['verify_types']) ?> · <?= esc($user['city']) ?></td></tr>
    <tr><th>판매 채널 링크</th><td><?php if ($user['fb_page']): ?><a href="<?= esc($user['fb_page']) ?>" target="_blank" style="color:var(--navy);text-decoration:underline"><?= esc($user['fb_page']) ?></a><?php else: ?>-<?php endif ?></td></tr>
    <tr><th>제출 자료</th><td><?php if (! $docs): ?>-<?php else: ?><ul class="doclist"><?php foreach ($docs as $d): ?><li><a href="<?= base_url($d['path']) ?>" target="_blank"><?= esc($d['name']) ?></a></li><?php endforeach ?></ul><?php endif ?></td></tr>
  </table>
  <?php if ($user['status'] !== 'approved' && $user['status'] !== 'suspended'): ?>
  <div class="card mt24">
    <h3>자료 추가·재제출</h3>
    <form method="post" action="<?= site_url('mypage/verify') ?>" enctype="multipart/form-data"><?= csrf_field() ?>
      <?= view('_verify_fields', ['sel' => array_filter(explode(',', (string) $user['verify_types'])), 'link' => (string) $user['fb_page']]) ?>
      <p class="vrule-note" style="margin-top:6px">이미 올린 자료는 그대로 남고, 새 자료가 추가됩니다.</p>
      <button class="mt-[18px] inline-flex items-center text-white bg-primary min-h-[44px] px-[20px] py-[12px] body3 !leading-none rounded-full font-bold" type="submit">다시 제출하기</button>
    </form>
  </div>
  <?php endif ?>
</div>
</div>
