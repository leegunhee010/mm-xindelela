<form method="post" action="<?= site_url('admin/suppliers/store') ?>" enctype="multipart/form-data" class="two" style="grid-template-columns:1fr 320px"><?= csrf_field() ?>
<input type="hidden" name="id" value="<?= $s['id'] ?? '' ?>">
<div>
  <div class="card"><h3>회사 정보</h3>
    <div class="g2">
      <div class="field"><label>회사명 <i>*</i></label><input class="inp" name="company_name" value="<?= esc(old('company_name', $s['company_name'] ?? '')) ?>" required></div>
      <div class="field"><label>브랜드</label><input class="inp" name="brand" value="<?= esc(old('brand', $s['brand'] ?? '')) ?>"></div>
      <div class="field"><label>담당자</label><input class="inp" name="contact_name" value="<?= esc(old('contact_name', $s['contact_name'] ?? '')) ?>"></div>
      <div class="field"><label>연락처</label><input class="inp" name="phone" value="<?= esc(old('phone', $s['phone'] ?? '')) ?>"></div>
      <div class="field"><label>사업자등록번호</label><input class="inp" name="biz_no" value="<?= esc(old('biz_no', $s['biz_no'] ?? '')) ?>"></div>
      <div class="field"><label>수수료율 (%)</label><input class="inp" type="number" step="0.1" name="commission_rate" value="<?= esc(old('commission_rate', $s['commission_rate'] ?? 0)) ?>"></div>
    </div>
    <div class="field"><label>주소</label><input class="inp" name="address" value="<?= esc(old('address', $s['address'] ?? '')) ?>"></div>
    <div class="field"><label>홈페이지·쇼핑몰 주소</label><input class="inp" name="website" value="<?= esc(old('website', $s['website'] ?? '')) ?>" placeholder="https://"></div>
    <div class="field"><label>회사·상품 소개 <span class="muted">(사이트 공급사 페이지에 표시)</span></label><textarea class="ta" name="intro"><?= esc(old('intro', $s['intro'] ?? '')) ?></textarea></div>
  </div>
  <div class="card"><h3>자료 <span class="muted small" style="font-weight:400">회사소개서 · 카탈로그 · 사업자등록증 등 (내부 보관용)</span></h3>
    <?php if ($docs): ?><ul class="doclist" style="margin-bottom:10px"><?php foreach ($docs as $d): ?><li><label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="keep_docs[]" value="<?= esc($d['path']) ?>" checked><a href="<?= base_url($d['path']) ?>" target="_blank"><?= esc($d['name']) ?></a></label></li><?php endforeach ?></ul><div class="help" style="margin-bottom:10px">체크를 해제하고 저장하면 목록에서 빠집니다.</div><?php endif ?>
    <input type="file" name="docs[]" multiple accept="image/*,.pdf" data-list="#supDocList"><ul id="supDocList" class="small muted mt8"></ul><div class="help">PDF·이미지, 여러 개 가능 (파일당 10MB)</div>
  </div>
</div>
<div>
  <div class="act" style="position:sticky;top:20px">
    <h3>저장</h3>
    <button class="btn btn-g btn-block" type="submit" style="height:42px"><?= $s ? '저장' : '공급사 추가' ?></button>
    <a class="btn btn-block mt8" href="<?= site_url($s ? 'admin/suppliers/' . $s['id'] : 'admin/suppliers') ?>">취소</a>
  </div>
</div>
</form>
