<div class="flex gap-[60px] items-start">
<?= view('mypage/_side') ?>
<div class="flex-1 min-w-0">
  <div class="contents-title-area depth-1 title3 font-bold !pt-0 mb-[12px]"><div class="title-area !pt-0"><h3 class="title"><?= esc(mm_q('menu')) ?></h3></div></div>
  <p class="qlist-note">줄서기는 주문이나 예약이 아닙니다. 입고되면 알림으로 알려 드리고, 그때 도매가를 확인하고 주문하시면 됩니다.</p>
  <?php if (! $rows): ?>
    <div class="empty">아직 줄을 선 상품이 없습니다. <a href="<?= site_url('products?only=coming') ?>" class="text-primary font-bold"><?= esc(mm_q('badge')) ?> 상품 보기 ›</a></div>
  <?php else: ?>
  <ul class="qlist">
    <?php foreach ($rows as $r): $p = $r['p']; $n = $stats[(int) $p['id']]['n'] ?? 0; ?>
    <li class="qlist-li">
      <a class="qlist-img" href="<?= site_url('products/' . $p['id']) ?>"><img src="<?= mm_thumb($p['thumb']) ?>" alt=""></a>
      <div class="qlist-b">
        <span class="qst qst-<?= esc($r['status']) ?>"><?= esc(mm_q('st_' . $r['status'])) ?></span>
        <a class="qlist-n" href="<?= site_url('products/' . $p['id']) ?>"><?= esc($p['name']) ?></a>
        <p class="qlist-s">희망 수량 <?= number_format((int) $r['qty']) ?><?= esc($p['unit']) ?> · <?= mm_dt($r['created_at'], 'Y.m.d') ?> 신청<?= $r['status'] === 'waiting' && $n ? ' · ' . esc(mm_q('count', number_format($n))) : '' ?></p>
      </div>
      <div class="qlist-act">
        <?php if ($r['status'] === 'waiting'): ?>
          <form method="post" action="<?= site_url('products/' . $p['id'] . '/queue/cancel') ?>" onsubmit="return confirm('<?= esc(mm_q('btn_cancel')) ?> 할까요?')"><?= csrf_field() ?><button type="submit" class="ocard-btn"><?= esc(mm_q('btn_cancel')) ?></button></form>
        <?php elseif ($r['status'] === 'arrived' && $p['status'] === 'active'): ?>
          <a class="ocard-re" href="<?= site_url('products/' . $p['id']) ?>">지금 주문하기</a>
        <?php endif ?>
      </div>
    </li>
    <?php endforeach ?>
  </ul>
  <?php endif ?>
</div>
</div>
