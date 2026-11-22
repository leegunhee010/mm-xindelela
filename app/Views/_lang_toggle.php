<?php /* 언어 전환 토글 (붙은 2칸 세그먼트). 미얀마어 기본. */ $L = mm_lang(); ?>
<span class="lang-seg" role="group" aria-label="Language"><a href="<?= site_url('lang/my') ?>" class="<?= $L === 'my' ? 'on' : '' ?>" lang="my" hreflang="my">မြန်မာ</a><a href="<?= site_url('lang/ko') ?>" class="<?= $L === 'ko' ? 'on' : '' ?>" lang="ko" hreflang="ko">한국어</a></span>
