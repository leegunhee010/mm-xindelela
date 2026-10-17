/* mm.xindelela.com 공용 스크립트 (의존성 없음) */
(function () {
  // 히어로 (Swiper, 아이스크림몰 corner-main-visual 동일 옵션)
  if (window.Swiper && document.getElementById('heroSwiper')) {
    var hs = new Swiper('#heroSwiper', {
      slidesPerView: 'auto', centeredSlides: true, spaceBetween: 20, loop: document.querySelectorAll('#heroSwiper .swiper-slide').length > 2,
      autoplay: { delay: 5000, disableOnInteraction: false },
      pagination: { el: '#heroPg', type: 'fraction' },
      navigation: { nextEl: '#heroNext', prevEl: '#heroPrev' }
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
    q.querySelector('.minus').onclick = function () { inp.value = parseInt(inp.value, 10) - moq; calc(); };
    q.querySelector('.plus').onclick = function () { inp.value = parseInt(inp.value, 10) + moq; calc(); };
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
