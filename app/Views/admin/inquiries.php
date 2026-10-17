<?php
$tabs = ['wait' => '답변 대기', 'done' => '답변 완료', 'all' => '전체'];
$qs   = static fn (array $over = []) => '?' . http_build_query(array_filter(array_merge(['st' => $st, 'q' => $q], $over), static fn ($v) => $v !== '' && $v !== null));
$csV  = (string) mm_setting('cs_viber', '');
$tpls = [
    '문의 감사합니다. 확인 후 바로 다시 안내드리겠습니다.',
    '자세한 내용은 고객센터 Viber ' . $csV . ' 로 연락 주시면 빠르게 도와드리겠습니다.',
    '주문 내역에서 진행 상태를 확인하실 수 있습니다.',
];
?>
<div class="iq-top">
  <div class="iq-seg">
    <?php foreach ($tabs as $k => $label): ?><a href="<?= $qs(['st' => $k, 'id' => null, 'page_p' => null]) ?>" class="<?= $st === $k ? 'on' : '' ?>"><?= $label ?><b><?= number_format($counts[$k]) ?></b></a><?php endforeach ?>
  </div>
  <form class="iq-srch" method="get"><input type="hidden" name="st" value="<?= esc($st) ?>"><input name="q" value="<?= esc($q) ?>" placeholder="상호·전화·문의 내용 검색"><button type="submit" class="btn btn-sm">검색</button></form>
</div>

<div class="iq">
  <div class="iq-list">
    <?php if (! $rows): ?><div class="iq-empty"><?= $st === 'wait' ? '답변을 기다리는 문의가 없습니다.' : '문의가 없습니다.' ?></div><?php endif ?>
    <?php foreach ($rows as $r): $on = $sel && (int) $sel['id'] === (int) $r['id']; ?>
    <a class="iq-item <?= $on ? 'on' : '' ?> <?= $r['answer'] ? 'is-done' : '' ?>" href="<?= $qs(['id' => $r['id'], 'page_p' => service('request')->getGet('page_p')]) ?>">
      <div class="iq-item-h"><b><?= esc($r['user']['company_name'] ?? '#' . $r['user_id']) ?></b><span><?= mm_ago($r['created_at']) ?></span></div>
      <p><?= esc(mb_strimwidth(preg_replace('/\s+/', ' ', $r['body']), 0, 70, '…')) ?></p>
      <div class="iq-item-f">
        <?= $r['answer'] ? '<i class="iq-dot done">답변 완료</i>' : '<i class="iq-dot">답변 대기</i>' ?>
        <?php if ($r['product']): ?><em><?= esc($r['product']['name']) ?></em><?php endif ?>
      </div>
    </a>
    <?php endforeach ?>
    <div class="iq-pg"><?= $pager->links('p', 'mm_pager') ?></div>
  </div>

  <div class="iq-view">
    <?php if (! $sel): ?>
      <div class="iq-empty">왼쪽에서 문의를 선택하세요.</div>
    <?php else: $u = $sel['user']; $p = $sel['product']; ?>
      <div class="iq-who">
        <div>
          <a class="iq-who-n" href="<?= site_url('admin/buyers/' . $sel['user_id']) ?>"><?= esc($u['company_name'] ?? '#' . $sel['user_id']) ?></a>
          <?php if ($u): ?><span class="st st-<?= esc($u['status']) ?>"><?= mm_verify_status_label($u['status']) ?></span><?php endif ?>
          <p><?= esc($u['name'] ?? '') ?><?= ! empty($u['city']) ? ' · ' . esc($u['city']) : '' ?></p>
        </div>
        <div class="iq-who-c">
          <?php if (! empty($u['phone'])): ?><a href="tel:<?= esc(preg_replace('/[^0-9+]/', '', $u['phone'])) ?>">전화 <b><?= esc($u['phone']) ?></b></a><?php endif ?>
          <?php if (! empty($u['viber'])): ?><a href="viber://chat?number=<?= rawurlencode(preg_replace('/[^0-9+]/', '', $u['viber'])) ?>">Viber <b><?= esc($u['viber']) ?></b></a><?php endif ?>
          <a href="<?= site_url('admin/buyers/' . $sel['user_id']) ?>">회원 상세 →</a>
        </div>
      </div>

      <?php if ($p): ?>
      <a class="iq-prod" href="<?= site_url('admin/products/' . $p['id']) ?>">
        <img src="<?= mm_thumb($p['thumb'] ?? null) ?>" alt="">
        <div><span>문의 상품</span><b><?= esc($p['name']) ?></b></div><em>상품 보기 →</em>
      </a>
      <?php endif ?>

      <div class="iq-thread">
        <div class="iq-q">
          <div class="iq-meta">질문 · <?= mm_dt($sel['created_at']) ?></div>
          <div class="iq-bub"><?= nl2br(esc($sel['body'])) ?></div>
        </div>
        <?php if ($sel['answer']): ?>
        <div class="iq-a">
          <div class="iq-meta">답변 · <?= mm_dt($sel['answered_at']) ?></div>
          <div class="iq-bub"><?= nl2br(esc($sel['answer'])) ?></div>
          <div><button type="button" class="iq-edit" onclick="document.getElementById('iqForm').hidden=false;this.remove();document.getElementById('iqAns').focus()">답변 수정하기</button></div>
        </div>
        <?php endif ?>
      </div>

      <form class="iq-form" method="post" action="<?= site_url('admin/inquiries/' . $sel['id'] . '/answer') ?>" id="iqForm"<?= $sel['answer'] ? ' hidden' : '' ?>><?= csrf_field() ?>
        <input type="hidden" name="st" value="<?= esc($st) ?>">
        <div class="iq-form-h"><b><?= $sel['answer'] ? '답변 수정' : '답변 쓰기' ?></b><span>회원 마이페이지 &gt; 문의 내역에 표시됩니다</span></div>
        <div class="iq-tpl"><?php foreach ($tpls as $t): ?><button type="button" data-t="<?= esc($t) ?>"><?= esc(mb_strimwidth($t, 0, 28, '…')) ?></button><?php endforeach ?></div>
        <textarea name="answer" id="iqAns" placeholder="답변을 입력하세요 (Ctrl+Enter 저장)"><?= esc($sel['answer']) ?></textarea>
        <div class="iq-btns">
          <?php if (! $sel['answer'] && $counts['wait'] > 1): ?><button class="btn" type="submit" name="next" value="1">저장하고 다음 문의</button><?php endif ?>
          <button class="btn btn-g" type="submit"><?= $sel['answer'] ? '답변 수정' : '답변 저장' ?></button>
        </div>
      </form>

      <?php if ($others): ?>
      <div class="iq-hist">
        <h4>이 회원의 다른 문의</h4>
        <?php foreach ($others as $o): ?>
        <a href="<?= $qs(['st' => 'all', 'id' => $o['id'], 'page_p' => null]) ?>"><span><?= mm_dt($o['created_at'], 'm.d') ?></span><p><?= esc(mb_strimwidth(preg_replace('/\s+/', ' ', $o['body']), 0, 60, '…')) ?></p><?= $o['answer'] ? '<i class="iq-dot done">답변 완료</i>' : '<i class="iq-dot">답변 대기</i>' ?></a>
        <?php endforeach ?>
      </div>
      <?php endif ?>
    <?php endif ?>
  </div>
</div>
<script>
(function () {
  var ta = document.getElementById('iqAns');
  if (!ta) return;
  document.querySelectorAll('.iq-tpl button').forEach(function (b) {
    b.onclick = function () { ta.value = (ta.value.trim() ? ta.value.trim() + '\n' : '') + b.dataset.t; ta.focus(); };
  });
  ta.addEventListener('keydown', function (e) { if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) document.getElementById('iqForm').requestSubmit(); });
})();
</script>
