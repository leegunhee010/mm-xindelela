<div class="flex gap-[60px] items-start">
<?= view('mypage/_side') ?>
<div class="flex-1 min-w-0">
  <div class="contents-title-area depth-1 title3 font-bold !pt-0 mb-[20px]"><div class="title-area !pt-0"><h3 class="title"><?= t('주문 내역') ?></h3></div></div>
  <?php if (! $orders): ?><div class="empty"><?= t('주문 내역이 없습니다.') ?></div><?php endif ?>
  <div class="olist">
  <?php foreach ($orders as $o): ?>
  <div class="ocard">
    <div class="ocard-h">
      <div>
        <div class="ocard-no"><?= esc($o['order_no']) ?><span class="st st-<?= esc($o['status']) ?>"><?= mm_order_status_label($o['status']) ?></span></div>
        <div class="ocard-dt"><?= mm_dt($o['created_at']) ?> <?= t('주문') ?></div>
      </div>
      <div class="ocard-acts">
        <?php if (mm_is_approved()): ?><form method="post" action="<?= site_url('mypage/orders/' . $o['id'] . '/reorder') ?>"><?= csrf_field() ?><button type="submit" class="ocard-re"><?= t('다시 담기') ?></button></form><?php endif ?>
        <a class="ocard-btn" href="<?= site_url('mypage/orders/' . $o['id']) ?>"><?= t('주문 상세 ›') ?></a>
      </div>
    </div>
    <ul class="ocard-items">
      <?php foreach ($o['items'] as $it): ?><li><img src="<?= mm_thumb($it['thumb']) ?>" alt=""><span class="n"><?= esc($it['product_name']) ?></span><span class="q"><?= number_format((int) $it['qty']) ?><?= t('개') ?></span><span class="p"><?= mm_price($it['line_total']) ?></span></li><?php endforeach ?>
    </ul>
    <div class="ocard-f">
      <?php if ((int) $o['points_used'] > 0): ?><span class="ocard-sub"><?= t('상품') ?> <?= mm_price($o['subtotal']) ?> <?= t('· 포인트 -') ?><?= number_format((int) $o['points_used']) ?>P</span><?php endif ?>
      <span class="ocard-tl"><?= t('결제 금액') ?></span><b class="ocard-tot"><?= mm_price($o['total']) ?></b>
    </div>
  </div>
  <?php endforeach ?>
  </div>
  <?= $pager->links('p', 'mm_pager') ?>
</div>
</div>
