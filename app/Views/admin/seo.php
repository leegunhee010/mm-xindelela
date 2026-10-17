<?php
$title = $v['seo_title'] ?? '';
$desc  = $v['seo_description'] ?? '';
$og    = $v['seo_og_image'] ?? '';
$fav   = $v['seo_favicon'] ?? '';
$host  = parse_url(base_url(), PHP_URL_HOST) ?: 'mm.xindelela.com';
?>
<form method="post" action="<?= site_url('admin/seo') ?>" enctype="multipart/form-data" class="two" style="grid-template-columns:1fr 400px"><?= csrf_field() ?>
<div>
  <div class="card"><h3>검색 결과에 보이는 내용</h3>
    <div class="field"><label>사이트 제목 <span class="muted" id="tCnt"></span></label><input class="inp" name="seo_title" id="seoTitle" value="<?= esc(old('seo_title', $title)) ?>" maxlength="120" placeholder="예: XINDELELA 미얀마 K-브랜드 도매몰"><div class="help">홈 화면 제목이 되고, 다른 페이지는 "페이지 이름 | 사이트 제목"으로 붙습니다. 30~60자 권장.</div></div>
    <div class="field"><label>사이트 설명 <span class="muted" id="dCnt"></span></label><textarea class="ta" name="seo_description" id="seoDesc" maxlength="300" style="height:90px" placeholder="예: 한국 정품 K-브랜드를 미얀마 소매상·온라인 셀러에게 박스 단위 도매가로 공급합니다."><?= esc(old('seo_description', $desc)) ?></textarea><div class="help">검색 결과 제목 아래와 공유 카드에 나오는 문장. 80~150자 권장.</div></div>
    <div class="field"><label>키워드 <span class="muted">(쉼표로 구분, 선택)</span></label><input class="inp" name="seo_keywords" value="<?= esc(old('seo_keywords', $v['seo_keywords'] ?? '')) ?>" placeholder="K-beauty wholesale Myanmar, Korean products Yangon"></div>
  </div>

  <div class="card"><h3>공유 이미지 <span class="muted small" style="font-weight:400">페이스북·Viber·카톡에 링크를 보낼 때 뜨는 그림</span></h3>
    <?php if ($og): ?><div class="seo-img"><img src="<?= base_url($og) ?>" alt=""><label class="small"><input type="checkbox" name="del_og" value="1"> 삭제</label></div><?php endif ?>
    <div class="field"><input type="file" name="og_image" accept="image/*" id="ogFile"><div class="help">1200×630 권장, JPG/PNG, 5MB 이하. 새로 올리면 바뀝니다.</div></div>
  </div>

  <div class="card"><h3>파비콘 <span class="muted small" style="font-weight:400">브라우저 탭에 보이는 작은 아이콘</span></h3>
    <?php if ($fav): ?><div class="seo-fav"><img src="<?= base_url($fav) ?>" alt=""><label class="small"><input type="checkbox" name="del_favicon" value="1"> 삭제</label></div><?php endif ?>
    <div class="field"><input type="file" name="favicon" accept=".png,.ico,.svg,.webp" id="favFile"><div class="help">정사각형 PNG 512×512 권장 (ICO·SVG도 가능), 1MB 이하.</div></div>
  </div>

  <div class="card"><h3>검색엔진 노출</h3>
    <label class="seo-sw"><input type="checkbox" name="seo_indexable" value="1" <?= ($v['seo_indexable'] ?? '0') === '1' ? 'checked' : '' ?>> <span>구글 등 검색엔진에 사이트를 노출합니다</span></label>
    <div class="field"><div class="help">끄면 검색 결과에 나오지 않습니다(준비 중일 때). 오픈할 때 켜 주세요. 마이페이지·관리자·주문 화면은 항상 제외됩니다.</div></div>
  </div>
</div>

<div>
  <div class="act" style="position:sticky;top:20px">
    <h3>미리보기</h3>
    <p class="small muted" style="margin:0 0 8px">구글 검색 결과</p>
    <div class="seo-g">
      <div class="seo-g-site"><span class="seo-g-ic"><?php if ($fav): ?><img src="<?= base_url($fav) ?>" alt=""><?php endif ?></span><span><b>XINDELELA</b><br><?= esc($host) ?></span></div>
      <div class="seo-g-t" id="pvT"><?= esc($title ?: '사이트 제목') ?></div>
      <div class="seo-g-d" id="pvD"><?= esc($desc ?: '사이트 설명이 여기에 표시됩니다.') ?></div>
    </div>
    <p class="small muted" style="margin:16px 0 8px">링크 공유 카드</p>
    <div class="seo-c">
      <div class="seo-c-img" id="pvImg"><?php if ($og): ?><img src="<?= base_url($og) ?>" alt=""><?php else: ?><span>공유 이미지 없음</span><?php endif ?></div>
      <div class="seo-c-b"><div class="seo-c-h"><?= esc(strtoupper($host)) ?></div><div class="seo-c-t" id="pvT2"><?= esc($title ?: '사이트 제목') ?></div><div class="seo-c-d" id="pvD2"><?= esc($desc ?: '사이트 설명') ?></div></div>
    </div>
    <button class="btn btn-g btn-block" type="submit" style="height:44px;margin-top:16px">SEO 설정 저장</button>
  </div>
</div>
</form>
<script>
(function () {
  var t = document.getElementById('seoTitle'), d = document.getElementById('seoDesc');
  function upd() {
    var tv = t.value || '사이트 제목', dv = d.value || '사이트 설명이 여기에 표시됩니다.';
    ['pvT', 'pvT2'].forEach(function (id) { document.getElementById(id).textContent = tv; });
    ['pvD', 'pvD2'].forEach(function (id) { document.getElementById(id).textContent = dv; });
    document.getElementById('tCnt').textContent = t.value.length + '자';
    document.getElementById('dCnt').textContent = d.value.length + '자';
  }
  t.addEventListener('input', upd); d.addEventListener('input', upd); upd();
  document.getElementById('ogFile').addEventListener('change', function () {
    var f = this.files[0]; if (!f) return;
    var u = URL.createObjectURL(f); document.getElementById('pvImg').innerHTML = '<img src="' + u + '" alt="">';
  });
})();
</script>
