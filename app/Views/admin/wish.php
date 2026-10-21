<p class="mb16"><a href="<?= site_url('admin/wishes') ?>" class="muted">‹ 관심상품 현황</a></p>
<div class="card">
  <h3><?= esc($p['name']) ?> <span class="st st-<?= esc($p['status']) ?>"><?= mm_product_status_label($p['status']) ?></span>
    <a class="btn btn-xs" href="<?= site_url('admin/products/' . $p['id'] . '/edit') ?>" style="float:right">상품 수정</a></h3>
  <p class="muted small" style="margin:6px 0 14px">이 상품을 관심상품에 담은 회원 <?= count($rows) ?>명. 입고·할인 소식을 알리고 싶으면 <a href="<?= site_url('admin/notify?product=' . $p['id']) ?>" style="text-decoration:underline">이 회원들에게 알림 보내기</a>.</p>
  <?php if (! $rows): ?><div class="muted rempty">담은 회원이 없습니다.</div><?php else: ?>
  <table class="tbl"><thead><tr><th class="l">회원</th><th>인증</th><th>연락처</th><th>담은 날</th></tr></thead><tbody>
  <?php foreach ($rows as $r): $u = $r['user']; ?>
    <tr>
      <td class="l"><?php if ($u): ?><a href="<?= site_url('admin/buyers/' . $u['id']) ?>"><b><?= esc($u['company_name'] ?: $u['name']) ?></b></a> <span class="muted small"><?= esc($u['city'] ?? '') ?></span><?php else: ?>(삭제된 회원)<?php endif ?></td>
      <td><?php if ($u): ?><span class="st st-<?= esc($u['status']) ?>"><?= mm_verify_status_label($u['status']) ?></span><?php endif ?></td>
      <td class="small"><?= esc($u['phone'] ?? '') ?><?php if (! empty($u['viber'])): ?><br>Viber <?= esc($u['viber']) ?><?php endif ?></td>
      <td class="small"><?= mm_dt($r['created_at'], 'Y.m.d H:i') ?></td>
    </tr>
  <?php endforeach ?>
  </tbody></table><?php endif ?>
</div>
