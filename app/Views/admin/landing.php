<?php $ph = static fn (string $k) => esc($d[$k]); ?>
<div class="lang-tabs"><a href="<?= site_url('admin/landing') ?>" class="<?= $lang === 'ko' ? 'on' : '' ?>">한국어 화면</a><a href="<?= site_url('admin/landing?lang=my') ?>" class="<?= $lang === 'my' ? 'on' : '' ?>">미얀마어 화면</a></div>
<?php if ($lang === 'my'): ?><p class="muted small" style="margin:0 0 12px">미얀마어 화면(기본 언어)에 나오는 문구입니다. 비워 두면 기본 미얀마어 문구가 나옵니다. 사진은 한국어 화면과 같이 씁니다.</p><?php endif ?>
<form method="post" action="<?= site_url('admin/landing/save') ?>" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="lang" value="<?= esc($lang) ?>">
<div class="two">
<div>
  <div class="card">
    <h3>첫 화면 문구</h3>
    <p class="muted small" style="margin:-4px 0 14px">비워 두면 기본 문구(회색 글씨)가 나옵니다. <b>{가입포인트} {인증기간} {사용한도} {통화}</b> 를 쓰면 설정값으로 자동으로 바뀝니다.</p>
    <div class="field"><label>작은 꼬리표</label><input class="inp" name="tag" value="<?= esc($w['tag']) ?>" placeholder="<?= $ph('tag') ?>"></div>
    <div class="g2">
      <div class="field"><label>큰 제목 첫 줄</label><input class="inp" name="title1" value="<?= esc($w['title1']) ?>" placeholder="<?= $ph('title1') ?>"></div>
      <div class="field"><label>큰 제목 둘째 줄</label><input class="inp" name="title2" value="<?= esc($w['title2']) ?>" placeholder="<?= $ph('title2') ?>"></div>
    </div>
    <div class="field"><label>제목 아래 한 줄</label><input class="inp" name="sub" value="<?= esc($w['sub']) ?>" placeholder="<?= $ph('sub') ?>"></div>
  </div>

  <div class="card"<?= $lang === 'my' ? ' hidden' : '' ?>>
    <h3>사진 <span class="muted small" style="font-weight:400">첫 화면·거래 흐름·맨 아래 띠에 돌아가며 씁니다 (최대 12장, 8장 이상 권장)</span></h3>
    <div class="lpics">
      <?php foreach (array_values($w['pics']) as $i => $p): ?>
      <div class="lpic">
        <img src="<?= base_url($p) ?>" alt="">
        <input type="hidden" name="pic[<?= $i ?>]" value="<?= esc($p) ?>">
        <div class="lpic-b"><input class="inp" type="number" name="pic_order[<?= $i ?>]" value="<?= $i + 1 ?>" title="순서"><label><input type="checkbox" name="pic_del[]" value="<?= esc($p) ?>"> 삭제</label></div>
      </div>
      <?php endforeach ?>
    </div>
    <div class="field mt8"><label>사진 추가 (여러 장 선택 가능, 장당 5MB, 정사각형에 가까운 상품 사진)</label><input type="file" name="new_pics[]" accept="image/*" multiple></div>
    <label class="small"><input type="checkbox" name="pics_reset" value="1"> 기본 사진으로 되돌리기</label>
    <?php if (str_contains(implode(',', $w['pics']), 'assets/img/demo/')): ?><p class="muted small mt8">지금은 임시 데모 사진입니다. 실제 상품 사진으로 바꿔 주세요.</p><?php endif ?>
  </div>

  <div class="card">
    <h3>자주 묻는 질문</h3>
    <p class="muted small" style="margin:-4px 0 14px">질문과 답을 둘 다 비우면 그 항목은 빠집니다. 순서는 위에서부터.</p>
    <?php $faq = $w['faq']; for ($i = 0; $i < count($faq) + 3; $i++): $f = $faq[$i] ?? ['q' => '', 'a' => '']; ?>
    <div class="lfaq">
      <span class="lfaq-n"><?= $i + 1 ?></span>
      <div><input class="inp" name="faq_q[]" value="<?= esc($f['q']) ?>" placeholder="질문<?= $i >= count($faq) ? ' (새 항목)' : '' ?>"><textarea class="ta" name="faq_a[]" rows="2" placeholder="답"><?= esc($f['a']) ?></textarea></div>
    </div>
    <?php endfor ?>
  </div>

  <div class="card">
    <h3>맨 아래 문구</h3>
    <div class="g2">
      <div class="field"><label>첫 줄</label><input class="inp" name="band1" value="<?= esc($w['band1']) ?>" placeholder="<?= $ph('band1') ?>"></div>
      <div class="field"><label>둘째 줄 (굵게)</label><input class="inp" name="band2" value="<?= esc($w['band2']) ?>" placeholder="<?= $ph('band2') ?>"></div>
    </div>
  </div>
</div>
<div>
  <div class="act" style="position:sticky;top:20px">
    <h3>저장</h3>
    <button class="btn btn-g btn-block" type="submit" style="height:42px">저장하기</button>
    <a class="btn btn-block mt8" href="<?= site_url('welcome') ?>" target="_blank">랜딩 미리보기</a>
    <p class="muted small mt8">가입 포인트·인증 기간·Viber 번호는 <a href="<?= site_url('admin/settings') ?>" style="text-decoration:underline">설정</a> 값을 그대로 씁니다.</p>
    <p class="muted small mt8">광고·영업 링크: <b><?= esc(site_url('welcome')) ?></b></p>
  </div>
</div>
</div>
</form>
