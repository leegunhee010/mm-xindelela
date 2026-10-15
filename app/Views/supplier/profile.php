<form method="post" action="<?= site_url('supplier/profile') ?>" enctype="multipart/form-data" class="card" style="max-width:760px"><?= csrf_field() ?>
  <h3>공급사 정보 <span class="st st-<?= esc($supplier['status']) ?>"><?= mm_status_label($supplier['status']) ?></span></h3>
  <div class="g2">
    <div class="field"><label>회사명</label><input class="inp" name="company_name" value="<?= esc($supplier['company_name']) ?>" required></div>
    <div class="field"><label>브랜드</label><input class="inp" name="brand" value="<?= esc($supplier['brand']) ?>"></div>
    <div class="field"><label>담당자</label><input class="inp" name="contact_name" value="<?= esc($supplier['contact_name']) ?>"></div>
    <div class="field"><label>연락처</label><input class="inp" name="phone" value="<?= esc($supplier['phone']) ?>"></div>
    <div class="field"><label>사업자등록번호</label><input class="inp" name="biz_no" value="<?= esc($supplier['biz_no']) ?>"></div>
    <div class="field"><label>로고</label><input type="file" name="logo" accept="image/*"><?php if ($supplier['logo']): ?><img src="<?= base_url($supplier['logo']) ?>" alt="" style="height:50px;margin-top:6px"><?php endif ?></div>
  </div>
  <div class="field"><label>주소</label><input class="inp" name="address" value="<?= esc($supplier['address']) ?>"></div>
  <div class="field"><label>소개 (공급사 페이지·상품 상세에 표시)</label><textarea class="ta" name="intro"><?= esc($supplier['intro']) ?></textarea></div>
  <div class="field"><label>수수료율</label><div><?= (float) $supplier['commission_rate'] ?>% <span class="muted small">(관리자가 설정)</span></div></div>
  <button class="btn btn-g" type="submit">저장</button>
</form>
