<?php
$pSign  = (int) mm_setting('points_referral_signup', 0);
$pFirst = (int) mm_setting('points_referral_first_order', 0);
$share  = rawurlencode('XINDELELA 미얀마 K-브랜드 도매몰에 가입해 보세요. 추천 코드 ' . $user['referral_code'] . ' ' . $link);
$medal  = [1 => 'gold', 2 => 'silver', 3 => 'bronze'];
?>
<div class="flex gap-[60px] items-start">
<?= view('mypage/_side') ?>
<div class="flex-1 min-w-0">
  <div class="contents-title-area depth-1 title3 font-bold !pt-0 mb-[20px]"><div class="title-area !pt-0"><h3 class="title">친구 소개</h3></div></div>

  <!-- 내 코드 + 내 성적 -->
  <div class="rf-hero">
    <div class="rf-code">
      <span class="rf-lbl">내 추천 코드</span>
      <b id="refCode"><?= esc($user['referral_code']) ?></b>
      <div class="rf-link"><input type="text" id="refLink" value="<?= esc($link) ?>" readonly><button type="button" data-copy="#refLink">링크 복사</button></div>
      <div class="rf-share">
        <a href="viber://forward?text=<?= $share ?>">Viber로 보내기</a>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($link) ?>" target="_blank" rel="noopener">Facebook 공유</a>
        <a href="https://t.me/share/url?url=<?= rawurlencode($link) ?>&text=<?= $share ?>" target="_blank" rel="noopener">Telegram 공유</a>
      </div>
    </div>
    <div class="rf-me">
      <div><span>이번 달 내 순위</span><b><?= $per === 'month' && $me ? $me['rank'] . '<small>위</small>' : ($per === 'all' && $me ? $me['rank'] . '<small>위 (전체)</small>' : '-') ?></b></div>
      <div><span>인증 통과한 친구</span><b><?= $approvedN ?><small>명</small></b></div>
      <div><span>받은 추천 포인트</span><b><?= number_format($earned) ?><small>P</small></b></div>
    </div>
  </div>

  <!-- 받는 방법 -->
  <ol class="rf-steps">
    <li><em>1</em><b>링크·코드로 친구 가입</b><span>가게를 운영하는 친구에게 공유</span></li>
    <li><em>2</em><b>친구 비즈니스 인증 통과</b><span class="rf-pt">+<?= number_format($pSign) ?> P</span></li>
    <li><em>3</em><b>친구 첫 주문 입금 완료</b><span class="rf-pt">+<?= number_format($pFirst) ?> P</span></li>
  </ol>

  <!-- 순위표 -->
  <div class="rf-board">
    <div class="rf-board-h">
      <h4>소개 왕 순위 <span>인증 통과한 친구 수 기준</span></h4>
      <div class="rf-seg"><a href="?p=month" class="<?= $per === 'month' ? 'on' : '' ?>">이번 달</a><a href="?p=all" class="<?= $per === 'all' ? 'on' : '' ?>">전체 기간</a></div>
    </div>
    <?php if (! $board): ?>
      <div class="rf-empty"><b><?= $per === 'month' ? '이번 달 1위 자리가 비어 있어요' : '아직 순위가 없어요' ?></b><span>친구 한 명만 인증을 통과해도 바로 1위입니다.</span></div>
    <?php else: ?>
      <div class="rf-podium">
        <?php foreach ([1, 0, 2] as $pos): $r = $board[$pos] ?? null; ?>
          <div class="rf-pod rf-pod-<?= $pos + 1 ?> <?= $r ? '' : 'is-empty' ?> <?= $r && $r['id'] === (int) $user['id'] ? 'is-me' : '' ?>">
            <?php if ($r): ?>
              <span class="rf-medal <?= $medal[$r['rank']] ?? '' ?>"><?= $r['rank'] ?></span>
              <b class="rf-pod-name"><?= esc($r['name']) ?><?= $r['id'] === (int) $user['id'] ? ' (나)' : '' ?></b>
              <span class="rf-pod-n"><?= $r['n'] ?>명</span>
            <?php else: ?>
              <span class="rf-medal"><?= $pos + 1 ?></span><b class="rf-pod-name">비어 있음</b><span class="rf-pod-n">도전해 보세요</span>
            <?php endif ?>
          </div>
        <?php endforeach ?>
      </div>
      <?php if (count($board) > 3): ?>
      <ol class="rf-list">
        <?php foreach (array_slice($board, 3) as $r): ?><li class="<?= $r['id'] === (int) $user['id'] ? 'is-me' : '' ?>"><em><?= $r['rank'] ?></em><span><?= esc($r['name']) ?><?= $r['id'] === (int) $user['id'] ? ' (나)' : '' ?></span><b><?= $r['n'] ?>명</b></li><?php endforeach ?>
      </ol>
      <?php endif ?>
      <?php if ($me && $me['rank'] > 10): ?><div class="rf-mine"><em><?= $me['rank'] ?></em><span>나</span><b><?= $me['n'] ?>명</b></div><?php endif ?>
      <?php if (! $me): ?><div class="rf-nudge">아직 순위에 없어요. 친구 1명이 인증을 통과하면 순위에 올라갑니다.</div><?php endif ?>
    <?php endif ?>
  </div>

  <!-- 내가 소개한 회원 -->
  <div class="rf-mylist">
    <h4>내가 소개한 친구 <span><?= count($referred) ?>명</span></h4>
    <?php if (! $referred): ?><div class="rf-empty small"><span>아직 소개한 친구가 없습니다. 위 링크를 보내 보세요.</span></div><?php else: ?>
    <ul>
      <?php foreach ($referred as $r): $pd = $paid[$r['id']] ?? []; ?>
      <li>
        <div class="rf-ml-n"><b><?= esc($r['company_name'] ?: $r['name']) ?></b><span><?= mm_dt($r['created_at'], 'Y.m.d') ?> 가입</span></div>
        <div class="rf-ml-s">
          <span class="rf-chip <?= isset($pd['referral_signup']) ? 'on' : '' ?>">인증 <?= isset($pd['referral_signup']) ? '+' . number_format($pd['referral_signup']) . 'P' : ($r['status'] === 'pending' ? '검수 중' : '대기') ?></span>
          <span class="rf-chip <?= isset($pd['referral_first_order']) ? 'on' : '' ?>">첫 주문 <?= isset($pd['referral_first_order']) ? '+' . number_format($pd['referral_first_order']) . 'P' : '대기' ?></span>
        </div>
      </li>
      <?php endforeach ?>
    </ul><?php endif ?>
  </div>
</div>
</div>
