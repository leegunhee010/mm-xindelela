/* mm.xindelela.com 공용 스크립트 (의존성 없음) */
(function () {
  // 히어로 (Swiper, 아이스크림몰 corner-main-visual 동일 옵션)
  if (window.Swiper && document.getElementById('heroSwiper')) {
    // 배너가 적으면 넓은 화면 양옆이 비므로, 6장 이상이 되도록 복제해서 채움(번호는 실제 장수 기준)
    var hw = document.querySelector('#heroSwiper .swiper-wrapper');
    var hOrig = Array.prototype.slice.call(hw.children);
    var hN = hOrig.length;
    if (hN > 1) { while (hw.children.length < 6) hOrig.forEach(function (el) { hw.appendChild(el.cloneNode(true)); }); }
    var hPg = document.getElementById('heroPg');
    function hNum(sw) { if (hPg) hPg.innerHTML = '<span class="swiper-pagination-current">' + (sw.realIndex % hN + 1) + '</span> / <span class="swiper-pagination-total">' + hN + '</span>'; }
    var hs = new Swiper('#heroSwiper', {
      slidesPerView: 'auto', centeredSlides: true, spaceBetween: window.innerWidth <= 1024 ? 10 : 20, loop: hN > 1,
      autoplay: { delay: 5000, disableOnInteraction: false },
      navigation: { nextEl: '#heroNext', prevEl: '#heroPrev' },
      on: { init: hNum, slideChange: hNum }
    });
    var pb = document.getElementById('heroPlay');
    if (pb) pb.addEventListener('click', function () { if (hs.autoplay.running) { hs.autoplay.stop(); pb.classList.add('paused'); } else { hs.autoplay.start(); pb.classList.remove('paused'); } });
  }
  // 수량 스테퍼 + 수량별 단가 합계
  var q = document.querySelector('.qty');
  if (q) {
    var inp = q.querySelector('input[name=qty]');
    var moq = parseInt(inp.dataset.moq || '1', 10);
    var base = parseInt(inp.dataset.price || '0', 10);
    var tiers = [];
    try { tiers = JSON.parse(inp.dataset.tiers || '[]'); } catch (e) {}
    tiers.sort(function (a, b) { return a.min_qty - b.min_qty; });
    var unitEl = document.getElementById('unitPrice'), totEl = document.getElementById('totPrice');
    function fmt(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
    function calc() {
      var v = parseInt(inp.value, 10);
      if (isNaN(v) || v < moq) v = moq;
      inp.value = v;
      var u = base;
      tiers.forEach(function (t) { if (v >= parseInt(t.min_qty, 10) && parseInt(t.price, 10) > 0) u = parseInt(t.price, 10); });
      if (unitEl) unitEl.textContent = fmt(u) + ' ' + (q.dataset.sym || '');
      if (totEl) totEl.textContent = fmt(u * v);
      var on = null;
      document.querySelectorAll('.pd-tier li[data-min]').forEach(function (li) { li.classList.remove('on'); if (v >= parseInt(li.dataset.min, 10)) on = li; });
      if (on) on.classList.add('on');
    }
    q.querySelector('.minus').onclick = function () { inp.value = parseInt(inp.value, 10) - 1; calc(); };
    q.querySelector('.plus').onclick = function () { inp.value = parseInt(inp.value, 10) + 1; calc(); };
    inp.addEventListener('change', calc);
    calc();
  }

  // 상세 탭
  var ptabs = document.querySelectorAll('.ptabs a[data-tab], .ptab-btn');
  ptabs.forEach(function (a) {
    a.addEventListener('click', function (e) {
      e.preventDefault();
      ptabs.forEach(function (x) { x.classList.remove('on'); x.setAttribute('data-state', 'inactive'); });
      a.classList.add('on'); a.setAttribute('data-state', 'active');
      document.querySelectorAll('.ptab').forEach(function (p) { p.classList.toggle('hidden', p.id !== a.dataset.tab); });
    });
  });

  // 모달
  document.querySelectorAll('[data-modal]').forEach(function (b) {
    b.addEventListener('click', function (e) {
      e.preventDefault();
      var m = document.getElementById(b.dataset.modal);
      if (m) m.classList.add('on');
    });
  });
  document.querySelectorAll('.modal').forEach(function (m) {
    m.addEventListener('click', function (e) { if (e.target === m || e.target.classList.contains('close')) m.classList.remove('on'); });
  });

  // 복사 버튼
  document.querySelectorAll('[data-copy]').forEach(function (b) {
    b.addEventListener('click', function () {
      var el = document.querySelector(b.dataset.copy);
      if (!el) return;
      var txt = el.value !== undefined ? el.value : el.textContent;
      navigator.clipboard && navigator.clipboard.writeText(txt).then(function () { b.textContent = '복사됨'; setTimeout(function () { b.textContent = '복사'; }, 1500); });
    });
  });

  // 포인트 사용 전액
  var ptBtn = document.getElementById('ptAll');
  if (ptBtn) ptBtn.onclick = function () { var i = document.getElementById('points'); i.value = i.max; i.dispatchEvent(new Event('input')); };
  var ptInp = document.getElementById('points');
  if (ptInp) ptInp.addEventListener('input', function () {
    var v = parseInt(ptInp.value || '0', 10), mx = parseInt(ptInp.max || '0', 10);
    if (v > mx) v = mx; if (v < 0 || isNaN(v)) v = 0;
    var sub = parseInt(ptInp.dataset.subtotal || '0', 10);
    document.getElementById('ckPoints').textContent = '-' + fmt2(v);
    document.getElementById('ckTotal').textContent = fmt2(sub - v);
  });
  function fmt2(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','); }

  // 파일 선택 표시
  document.querySelectorAll('input[type=file][data-list]').forEach(function (f) {
    f.addEventListener('change', function () {
      var ul = document.querySelector(f.dataset.list); if (!ul) return;
      ul.innerHTML = '';
      Array.prototype.forEach.call(f.files, function (x) { var li = document.createElement('li'); li.textContent = x.name + ' (' + Math.round(x.size / 1024) + 'KB)'; ul.appendChild(li); });
    });
  });
})();

/* 관심상품 하트: 비회원은 쿠키, 회원은 계정에 저장. 같은 상품 하트는 화면 전체가 같이 바뀜 */
(function () {
  var meta = document.querySelector('meta[name=csrf-token]');
  var toastEl = null, toastT = null;
  function toast(html) {
    if (!toastEl) { toastEl = document.createElement('div'); toastEl.className = 'mm-toast'; document.body.appendChild(toastEl); }
    toastEl.innerHTML = html; toastEl.classList.add('show');
    clearTimeout(toastT); toastT = setTimeout(function () { toastEl.classList.remove('show'); }, 2600);
  }
  document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-wish]');
    if (!b) return;
    e.preventDefault(); e.stopPropagation();
    if (b.dataset.busy) return; b.dataset.busy = '1';
    var fd = new FormData(); fd.append('product_id', b.dataset.wish);
    fetch('/wish/toggle', { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': meta ? meta.content : '' } })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        if (!j.ok) { toast(j.error || '잠시 후 다시 시도해 주세요.'); return; }
        document.querySelectorAll('[data-wish="' + b.dataset.wish + '"]').forEach(function (x) { x.classList.toggle('on', j.on); x.setAttribute('aria-pressed', j.on ? 'true' : 'false'); });
        document.querySelectorAll('[data-wish-count]').forEach(function (c) { c.textContent = j.count; c.hidden = !j.count; });
        if (b.hasAttribute('data-wish-remove') && !j.on) { var li = b.closest('li'); if (li) li.remove(); }
        if (j.on) toast(j.guest ? '관심상품에 담았습니다. 가입하면 그대로 옮겨집니다.<a href="/wishlist">보기</a>' : '관심상품에 담았습니다.<a href="/wishlist">보기</a>');
        else toast('관심상품에서 뺐습니다.');
      })
      .catch(function () { toast('잠시 후 다시 시도해 주세요.'); })
      .then(function () { delete b.dataset.busy; });
  });
})();

/* 상품 사진 내려받기: 갤러리 + 상세 이미지를 ZIP 하나로 (JSZip, 상세 화면에서만 로드) */
(function () {
  var btn = document.getElementById('pdPhotos');
  if (!btn) return;
  btn.addEventListener('click', function () {
    if (!window.JSZip) return;
    var urls = JSON.parse(btn.dataset.urls || '[]'), name = (btn.dataset.name || 'photos').replace(/[\/:*?"<>|]+/g, ' ').trim();
    var label = btn.querySelector('span'), orig = label.textContent, zip = new JSZip(), done = 0;
    btn.disabled = true; label.textContent = '준비 중 0 / ' + urls.length;
    Promise.all(urls.map(function (u, i) {
      return fetch(u, { credentials: 'same-origin' }).then(function (r) { if (!r.ok) throw 0; return r.blob(); }).then(function (bl) {
        var ext = (bl.type.split('/')[1] || 'jpg').replace('jpeg', 'jpg').replace('svg+xml', 'svg');
        zip.file(String(i + 1).padStart(2, '0') + '.' + ext, bl);
      }).catch(function () {}).then(function () { done++; label.textContent = '준비 중 ' + done + ' / ' + urls.length; });
    })).then(function () { return zip.generateAsync({ type: 'blob' }); }).then(function (blob) {
      var a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = name + '.zip';
      document.body.appendChild(a); a.click(); a.remove(); setTimeout(function () { URL.revokeObjectURL(a.href); }, 4000);
    }).catch(function () { alert('사진을 묶지 못했습니다. 잠시 후 다시 시도해 주세요.'); }).then(function () { btn.disabled = false; label.textContent = orig; });
  });
})();
