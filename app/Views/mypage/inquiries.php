<div class="flex gap-[60px] items-start">
<?= view('mypage/_side') ?>
<div class="flex-1 min-w-0">
  <div class="contents-title-area depth-1 title3 font-bold !pt-0 mb-[20px]"><div class="title-area !pt-0"><h3 class="title">문의 내역</h3></div></div>
  <div class="card" style="margin-bottom:16px"><h3>새 문의</h3>
    <form method="post" action="<?= site_url('inquiry') ?>"><?= csrf_field() ?><div class="field"><textarea class="w-full px-3 py-[10px] bg-white body3 text-black border border-gray6 rounded-[8px] focus:outline-none focus:border-black h-[110px]" name="body" style="height:80px" placeholder="상품·주문·배송 등 궁금한 점을 적어 주세요."></textarea></div><button class="inline-flex items-center text-white bg-primary min-h-[36px] px-[16px] py-[8px] label1 !leading-none rounded-full font-bold" type="submit">문의 보내기</button></form></div>
  <?php if (! $rows): ?><div class="empty">문의 내역이 없습니다.</div><?php endif ?>
  <?php foreach ($rows as $r): ?>
  <div class="card" style="margin-bottom:10px">
    <div class="small muted"><?= mm_dt($r['created_at']) ?><?php if ($r['product']): ?> · <a href="<?= site_url('products/' . $r['product']['id']) ?>" style="color:var(--navy)"><?= esc($r['product']['name']) ?></a><?php endif ?> · <?= $r['answer'] ? '<span class="st st-approved">답변 완료</span>' : '<span class="st st-pending">답변 대기</span>' ?></div>
    <p class="mt8"><?= nl2br(esc($r['body'])) ?></p>
    <?php if ($r['answer']): ?><div class="box-note mt8" style="color:var(--ink)"><b>답변</b> <span class="small muted"><?= mm_dt($r['answered_at']) ?></span><br><?= nl2br(esc($r['answer'])) ?></div><?php endif ?>
  </div>
  <?php endforeach ?>
</div>
</div>
