<?php $coming = $p['status'] === 'coming'; ?>
<p class="mb16"><a href="<?= site_url('admin/queues') ?>" class="muted">‹ 줄서기 목록</a></p>
<div class="two">
<div>
  <div class="card">
    <h3><?= esc($p['name']) ?> <span class="st st-<?= esc($p['status']) ?>"><?= mm_product_status_label($p['status']) ?></span>
      <a class="btn btn-xs" href="<?= site_url('products/' . $p['id']) ?>" target="_blank" style="float:right">구매자 화면</a></h3>
    <div class="rkpi" style="margin:14px 0 18px">
      <div><span>줄 선 회원</span><b><?= number_format($n) ?><small>명</small></b><em>지금 줄 서는 중</em></div>
      <div><span>희망 수량 합계</span><b><?= number_format($qty) ?><small><?= esc($p['unit']) ?></small></b><em>최소 <?= number_format((int) $p['moq']) ?><?= esc($p['unit']) ?> 기준</em></div>
    </div>
    <?php if (! $rows): ?><div class="muted rempty">아직 줄을 선 회원이 없습니다.</div><?php else: ?>
    <table class="tbl"><thead><tr><th>#</th><th class="l">회원</th><th>연락처</th><th>희망 수량</th><th>신청일</th><th>상태</th></tr></thead><tbody>
    <?php foreach ($rows as $i => $r): $u = $r['user']; ?>
      <tr<?= $r['status'] === 'cancelled' ? ' class="muted"' : '' ?>>
        <td><?= $i + 1 ?></td>
        <td class="l"><?php if ($u): ?><a href="<?= site_url('admin/buyers/' . $u['id']) ?>"><b><?= esc($u['company_name'] ?: $u['name']) ?></b></a> <span class="muted small"><?= esc($u['city'] ?? '') ?></span><?php else: ?>(삭제된 회원)<?php endif ?></td>
        <td class="small"><?= esc($u['phone'] ?? '') ?><?php if (! empty($u['viber'])): ?><br>Viber <?= esc($u['viber']) ?><?php endif ?></td>
        <td><b><?= number_format((int) $r['qty']) ?></b><?= esc($p['unit']) ?></td>
        <td class="small"><?= mm_dt($r['created_at'], 'Y.m.d H:i') ?></td>
        <td><?= esc(mm_q('st_' . $r['status'])) ?></td>
      </tr>
    <?php endforeach ?>
    </tbody></table><?php endif ?>
  </div>
</div>
<div>
  <?php if ($coming): ?>
  <div class="act" style="border:2px solid var(--pri)"><h3>입고 처리</h3>
    <p class="small muted" style="margin-bottom:10px">입고되면 판매를 시작하고, 줄 선 회원 <?= $n ?>명에게 동시에 알림을 보냅니다. 도매가·재고를 먼저 확인해 주세요.</p>
    <form method="post" action="<?= site_url('admin/queues/' . $p['id'] . '/arrive') ?>" onsubmit="return confirm('입고 완료로 바꾸고 판매를 시작할까요? 줄 선 회원에게 알림이 갑니다.')"><?= csrf_field() ?><button class="btn btn-g btn-block" type="submit" style="height:42px">입고 완료 · 판매 시작</button></form>
    <a class="btn btn-block mt8" href="<?= site_url('admin/products/' . $p['id'] . '/edit') ?>">도매가·재고 수정</a>
  </div>
  <div class="act"><h3>입고 취소</h3>
    <p class="small muted" style="margin-bottom:10px">통관 불가 등으로 못 들여오면 상품을 숨기고 줄 선 회원에게 알립니다. 우리 쪽 책임은 없습니다.</p>
    <form method="post" action="<?= site_url('admin/queues/' . $p['id'] . '/fail') ?>" onsubmit="return confirm('입고를 취소할까요? 줄 선 회원에게 알림이 가고 줄이 닫힙니다.')"><?= csrf_field() ?>
      <input class="inp" name="reason" placeholder="사유 (선택, 회원에게 표시) 예: 통관 불가">
      <button class="btn btn-red btn-block mt8" type="submit">입고 취소</button></form>
  </div>
  <?php else: ?>
  <div class="act"><h3>줄이 닫혔습니다</h3><p class="small muted">이 상품은 지금 '<?= mm_product_status_label($p['status']) ?>' 상태입니다. 다시 줄서기를 받으려면 상품 수정에서 노출 상태를 '입고 예정'으로 바꾸세요.</p>
    <a class="btn btn-block mt8" href="<?= site_url('admin/products/' . $p['id'] . '/edit') ?>">상품 수정</a></div>
  <?php endif ?>
</div>
</div>
