<?php $e = $edit; ?>
<div class="two" style="grid-template-columns:1fr 340px">
<div class="card stf">
  <div class="stf-row stf-head"><span>담당자</span><span>권한</span><span>상태</span><span>이번 달 작업</span><span>마지막 작업</span><span></span></div>
  <?php foreach ($rows as $r): ?>
  <div class="stf-row">
    <span><b><?= esc($r['name']) ?></b><br><small class="muted"><?= esc($r['email']) ?></small></span>
    <span><span class="st <?= $r['admin_level'] === 'owner' ? 'st-approved' : 'st-pending' ?>"><?= $r['admin_level'] === 'owner' ? '대표 관리자' : '담당자' ?></span></span>
    <span><span class="st st-<?= $r['status'] === 'suspended' ? 'suspended' : 'active' ?>"><?= $r['status'] === 'suspended' ? '정지' : '사용' ?></span></span>
    <span><a href="<?= site_url('admin/logs?admin=' . $r['id']) ?>" style="text-decoration:underline"><?= number_format($r['month']) ?>건</a></span>
    <span class="small"><?= $r['last'] ? esc(mb_strimwidth($r['last']['summary'], 0, 34, '…')) . '<br><span class="muted">' . mm_dt($r['last']['created_at'], 'm.d H:i') . '</span>' : '<span class="muted">-</span>' ?></span>
    <span><a class="btn btn-sm stf-btn" href="?edit=<?= $r['id'] ?>">수정</a></span>
  </div>
  <?php endforeach ?>
</div>
<div class="act" style="position:sticky;top:20px">
  <h3><?= $e ? '담당자 수정' : '담당자 추가' ?></h3>
  <form method="post" action="<?= site_url('admin/staff/save') ?>"><?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $e['id'] ?? '' ?>">
    <div class="field"><label>이름 <i>*</i></label><input class="inp" name="name" value="<?= esc(old('name', $e['name'] ?? '')) ?>" required></div>
    <div class="field"><label>로그인 이메일 <i>*</i></label><input class="inp" type="email" name="email" value="<?= esc(old('email', $e['email'] ?? '')) ?>" required></div>
    <div class="field"><label>연락처</label><input class="inp" name="phone" value="<?= esc(old('phone', $e['phone'] ?? '')) ?>"></div>
    <div class="field"><label>비밀번호 <?= $e ? '<span class="muted">(바꿀 때만 입력)</span>' : '<i>*</i>' ?></label><input class="inp" type="password" name="password" autocomplete="new-password" placeholder="8자 이상"></div>
    <div class="field"><label>권한</label><div class="stf-seg">
      <label><input type="radio" name="admin_level" value="staff" <?= ($e['admin_level'] ?? 'staff') !== 'owner' ? 'checked' : '' ?>><span>담당자</span></label>
      <label><input type="radio" name="admin_level" value="owner" <?= ($e['admin_level'] ?? '') === 'owner' ? 'checked' : '' ?>><span>대표 관리자</span></label>
    </div><div class="help">대표 관리자만 담당자 계정을 만들고 고칠 수 있습니다. 나머지 기능은 같습니다.</div></div>
    <div class="field"><label>상태</label><div class="stf-seg">
      <label><input type="radio" name="status" value="approved" <?= ($e['status'] ?? 'approved') !== 'suspended' ? 'checked' : '' ?>><span>사용</span></label>
      <label><input type="radio" name="status" value="suspended" <?= ($e['status'] ?? '') === 'suspended' ? 'checked' : '' ?>><span>정지(로그인 차단)</span></label>
    </div></div>
    <button class="btn btn-g btn-block" type="submit" style="height:42px"><?= $e ? '저장' : '담당자 추가' ?></button>
    <?php if ($e): ?><a class="btn btn-block mt8" href="<?= site_url('admin/staff') ?>">새로 추가하기</a><?php endif ?>
  </form>
</div>
</div>
