<?php $docs = mm_json($user['doc_files']); ?>
<div class="my">
<?= view('mypage/_side') ?>
<div class="main">
  <h1>사업자 인증</h1>
  <div class="vstat">
    <h2>현재 상태: <span class="st st-<?= esc($user['status']) ?>" style="font-size:13px"><?= mm_status_label($user['status']) ?></span></h2>
    <?php if ($user['status'] === 'pending'): ?><p>제출 자료를 담당자가 직접 확인하고 있습니다. <?= esc(mm_setting('verify_sla_text')) ?> 승인됩니다. 승인되면 도매가와 주문 신청이 열립니다.</p>
    <?php elseif ($user['status'] === 'approved'): ?><p>인증이 완료되었습니다. 승인일 <?= mm_dt($user['approved_at']) ?></p>
    <?php elseif ($user['status'] === 'rejected'): ?><p style="color:var(--price)"><b>반려 사유:</b> <?= nl2br(esc($user['reject_reason'] ?: '-')) ?></p><p>아래에서 자료를 보완해 다시 제출해 주세요.</p>
    <?php else: ?><p>고객센터로 문의해 주세요.</p><?php endif ?>
  </div>
  <table class="kv">
    <tr><th>상호</th><td><?= esc($user['company_name']) ?></td></tr>
    <tr><th>판매 형태</th><td><?= ['online' => '온라인', 'offline' => '오프라인 매장', 'both' => '온·오프라인'][$user['business_type']] ?? '-' ?> · <?= esc($user['city']) ?></td></tr>
    <tr><th>페이스북 페이지</th><td><?php if ($user['fb_page']): ?><a href="<?= esc($user['fb_page']) ?>" target="_blank" style="color:var(--navy);text-decoration:underline"><?= esc($user['fb_page']) ?></a><?php else: ?>-<?php endif ?></td></tr>
    <tr><th>제출 자료</th><td><?php if (! $docs): ?>-<?php else: ?><ul class="doclist"><?php foreach ($docs as $d): ?><li><a href="<?= base_url($d['path']) ?>" target="_blank"><?= esc($d['name']) ?></a></li><?php endforeach ?></ul><?php endif ?></td></tr>
  </table>
  <?php if ($user['status'] !== 'approved' && $user['status'] !== 'suspended'): ?>
  <div class="card mt24">
    <h3>자료 추가·재제출</h3>
    <form method="post" action="<?= site_url('mypage/verify') ?>" enctype="multipart/form-data"><?= csrf_field() ?>
      <div class="field"><label>페이스북 페이지 / 틱톡샵 주소</label><input class="inp" name="fb_page" value="<?= esc($user['fb_page']) ?>"></div>
      <div class="field"><label>자료 추가 업로드</label><input type="file" name="docs[]" multiple accept="image/*,.pdf" data-list="#docList"><ul id="docList" class="small muted mt8"></ul><div class="help">매장 사진, 영업허가증, 판매 스크린샷 등. 기존 자료는 유지되고 새 자료가 추가됩니다.</div></div>
      <button class="btn btn-g" type="submit">다시 제출하기</button>
    </form>
  </div>
  <?php endif ?>
</div>
</div>
