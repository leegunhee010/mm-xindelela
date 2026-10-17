<?php /* 사업자 인증 입력 부품. $sel = 선택된 유형 배열, $link = 판매 채널 링크 */
$types = mm_verify_types(); $sel = $sel ?? []; $link = $link ?? ''; ?>
<div class="vrule">
  <p class="vrule-t">아래 세 가지 중 <b>하나만</b> 증빙하면 됩니다</p>
  <p class="vrule-d">사진이나 휴대폰 캡처도 괜찮습니다. 해당하는 것을 고르고 자료를 올려 주세요. 여러 개 골라도 됩니다.</p>
</div>
<div class="vtypes">
  <?php foreach ($types as $k => $t): ?>
  <label class="vtype">
    <input type="checkbox" name="verify_types[]" value="<?= $k ?>" <?= in_array($k, $sel, true) ? 'checked' : '' ?>>
    <span class="vtype-box">
      <b><?= $t['label'] ?></b>
      <span class="vtype-desc"><?= $t['desc'] ?></span>
      <ul><?php foreach ($t['docs'] as $d): ?><li><?= $d ?></li><?php endforeach ?></ul>
    </span>
  </label>
  <?php endforeach ?>
</div>
<label class="block mt-[18px]"><span class="label1 text-gray3 mb-[6px] block">판매 채널 링크 <span class="text-gray4">(페이스북 페이지·틱톡·인스타 등, 있으면)</span></span><div class="input-item"><input class="h-[44px] w-full px-3 bg-white body3 text-black placeholder:text-gray4 focus:outline-none border-gray6 focus:border-black border rounded-[8px]" type="text" name="fb_page" value="<?= esc($link) ?>" placeholder="https://facebook.com/yourshop"></div></label>
<div class="mt-[14px]"><span class="label1 text-gray3 mb-[6px] block">사진·캡처 올리기 <span class="text-gray4">(여러 장 가능, JPG/PNG/PDF 5MB)</span></span>
  <label class="flex h-[44px] cursor-pointer items-center justify-between rounded-[8px] border border-dashed border-gray5 px-3 body3 text-gray3"><span>매장 사진 · 판매 글 캡처 · 거래처 대화 캡처</span><span class="rounded-full bg-black px-[12px] py-[6px] label1 font-bold text-white">파일 선택</span><input type="file" name="docs[]" multiple accept="image/*,.pdf" data-list="#docList" class="hidden"></label>
  <ul id="docList" class="mt-[8px] label1 text-gray3"></ul></div>
<p class="vrule-note">담당자가 직접 확인한 뒤 <b><?= esc(mm_setting('verify_sla_text')) ?></b> 승인합니다. 자료가 부족하면 이유를 알려 드리고 다시 올려 달라고 요청합니다.</p>
