<form method="post" action="<?= site_url('admin/products/save') ?>" enctype="multipart/form-data" class="two" style="grid-template-columns:1fr 320px"><?= csrf_field() ?>
<input type="hidden" name="id" value="<?= $p['id'] ?? '' ?>">
<div>
  <div class="card"><h3>기본 정보</h3>
    <div class="field"><label>상품명 <i>*</i></label><input class="inp" name="name" value="<?= esc(old('name', $p['name'] ?? '')) ?>" required></div>
    <div class="g2">
      <div class="field"><label>카테고리</label><select class="sel" name="category_id"><option value="">선택</option><?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= (int) old('category_id', $p['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option><?php endforeach ?></select></div>
      <div class="field"><label>공급사 <i>*</i></label><select class="sel" name="supplier_id" required><option value="">선택</option><?php foreach ($suppliers as $sp): ?><option value="<?= $sp['id'] ?>" <?= (int) old('supplier_id', $p['supplier_id'] ?? (int) service('request')->getGet('supplier')) === (int) $sp['id'] ? 'selected' : '' ?>><?= esc($sp['company_name']) ?></option><?php endforeach ?></select><div class="help"><a href="<?= site_url('admin/suppliers/new') ?>" style="text-decoration:underline">공급사 추가</a></div></div>
      <div class="field"><label>브랜드</label><input class="inp" name="brand" value="<?= esc(old('brand', $p['brand'] ?? '')) ?>"></div>
      <div class="field"><label>상품코드</label><input class="inp" name="code" value="<?= esc(old('code', $p['code'] ?? '')) ?>"></div>
      <div class="field"><label>원산지</label><input class="inp" name="origin" value="<?= esc(old('origin', $p['origin'] ?? '한국')) ?>"></div>
    </div>
    <div class="field"><label>한 줄 요약</label><input class="inp" name="summary" value="<?= esc(old('summary', $p['summary'] ?? '')) ?>" placeholder="목록에 표시되는 짧은 설명"></div>
  </div>
  <div class="card"><h3>가격·수량 <span class="muted small" style="font-weight:400">(통화: <?= esc(mm_setting('currency_symbol', 'Ks')) ?>)</span></h3>
    <div class="g2">
      <div class="field"><label>도매가 (기본 단가) <i>*</i></label><input class="inp" type="number" name="price" value="<?= esc(old('price', $p['price'] ?? '')) ?>" required></div>
      <div class="field"><label>권장 소비자가 <span class="muted">(비회원에게도 표시)</span></label><input class="inp" type="number" name="retail_price" value="<?= esc(old('retail_price', $p['retail_price'] ?? '')) ?>"></div>
      <div class="field"><label>최소주문수량 <i>*</i></label><input class="inp" type="number" name="moq" value="<?= esc(old('moq', $p['moq'] ?? 1)) ?>" required><div class="help">이 수량부터 주문할 수 있고, 그 이상은 1개씩 늘릴 수 있습니다</div></div>
      <div class="field"><label>단위</label><input class="inp" name="unit" value="<?= esc(old('unit', $p['unit'] ?? '개')) ?>"></div>
      <div class="field"><label>재고</label><input class="inp" type="number" name="stock" value="<?= esc(old('stock', $p['stock'] ?? 0)) ?>"></div>
    </div>
    <label class="small" style="font-weight:700">수량별 단가 (많이 사면 싸게)</label>
    <div id="tiers" class="mt8">
      <?php $rows = $tiers ?: []; for ($i = 0; $i < 4; $i++): $t = $rows[$i] ?? ['min_qty' => '', 'price' => '']; ?>
      <div class="tierrow"><input class="inp" type="number" name="tier_qty[]" value="<?= esc($t['min_qty']) ?>" placeholder="이 수량 이상"><input class="inp" type="number" name="tier_price[]" value="<?= esc($t['price']) ?>" placeholder="단가"><span class="small muted">개↑</span></div>
      <?php endfor ?>
    </div>
  </div>
  <div class="card"><h3>이미지</h3>
    <?php if ($images): ?><div class="imgs" style="margin-bottom:10px"><?php foreach ($images as $im): ?><label><img src="<?= mm_thumb($im) ?>" alt=""><input type="checkbox" name="keep_images[]" value="<?= esc($im) ?>" checked title="체크 해제 시 삭제"></label><?php endforeach ?></div><div class="small muted">체크를 풀면 저장 시 삭제됩니다. 첫 번째 이미지가 대표 이미지입니다.</div><?php endif ?>
    <div class="field mt8"><input type="file" name="images[]" multiple accept="image/*" data-list="#imgList"><ul id="imgList" class="small muted mt8"></ul><div class="help">JPG/PNG/WebP, 장당 5MB 이하, 여러 장 선택 가능. 정사각형(600×600 이상) 권장.</div></div>
  </div>
  <?php $blocks = $p ? mm_detail_blocks($p) : []; ?>
  <div class="card"><h3>상세 내용 <span class="muted small" style="font-weight:400">문단·이미지·영상을 쌓아서 만듭니다 · 위에서 아래 순서대로 보입니다</span></h3>
    <div class="dtl-tools">
      <label class="btn btn-g dtl-bulk">통이미지 올리기<input type="file" id="dtlBulk" accept="image/*" multiple hidden></label>
      <button type="button" class="btn" data-add="p">+ 문단</button>
      <button type="button" class="btn" data-add="img">+ 이미지</button>
      <button type="button" class="btn" data-add="video">+ 영상</button>
    </div>
    <div class="help" style="margin-bottom:10px">통이미지: 번역한 상세페이지 이미지를 여러 장 고르면 파일 이름 순서대로 붙습니다. 세로로 긴 이미지는 자동으로 잘라(2400px 단위) 올리고, 사이트에서는 틈 없이 한 장처럼 이어 보입니다.</div>
    <div id="dtlInfo" class="small muted"></div>
    <div id="dtlList" class="dtl-list"></div>
    <input type="hidden" name="detail_json" id="dtlJson">
  </div>
  <script>window.MM_DETAIL = <?= json_encode(['blocks' => $blocks, 'base' => base_url(), 'upload' => site_url('admin/upload/image'), 'csrfName' => csrf_token(), 'csrf' => csrf_hash()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;</script>
  <script src="<?= base_url('assets/js/admin-detail.js?v=1') ?>"></script>
</div>
<div>
  <div class="act" style="position:sticky;top:20px">
    <h3>저장</h3>
    <div class="field"><label>노출 상태</label><select class="sel" name="status">
      <?php foreach (['active' => '판매중 (바로 노출)', 'hidden' => '숨김', 'draft' => '임시 저장'] as $k => $l): ?><option value="<?= $k ?>" <?= ($p['status'] ?? 'active') === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach ?>
    </select></div>
    <button class="btn btn-g btn-block" type="submit" style="height:42px"><?= $p ? '저장' : '상품 등록' ?></button>
    <div class="small muted mt8">도매가는 인증 회원에게만 보입니다.</div>
    <a class="btn btn-block mt8" href="<?= site_url('admin/products') ?>">목록</a>
    <?php if ($p): ?><a class="btn btn-block mt8" href="<?= site_url('products/' . $p['id']) ?>" target="_blank">사이트에서 보기</a><?php endif ?>
  </div>
  <?php if ($p): ?><?= view('admin/_history', ['type' => 'product', 'id' => (int) $p['id']]) ?><?php endif ?>
</div>
</div>
</form>
<?php if ($p): ?>
<form method="post" action="<?= site_url('admin/products/' . $p['id'] . '/delete') ?>" onsubmit="return confirm('이 상품을 삭제할까요?')" style="margin-top:12px"><?= csrf_field() ?><button class="btn btn-sm btn-red" type="submit">상품 삭제</button></form>
<?php endif ?>
