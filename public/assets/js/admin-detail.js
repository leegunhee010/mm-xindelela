/* 상품 상세 블록 편집기 (메이크노브 상품 등록 방식 차용)
 * 블록: {type:'p', text} / {type:'img', src} / {type:'img', src, seq:true, w, h} (긴 이미지 조각) / {type:'video', src}
 * 통이미지: 세로/가로 ≥ 2.5 이면 가로를 1200~1600 으로 맞추고 2400px 단위로 잘라 JPEG(0.92)로 올린다.
 * 저장은 폼 submit 때 hidden input(detail_json)에 JSON 으로 넣는다. */
(function () {
  var C = window.MM_DETAIL || {}, blocks = (C.blocks || []).filter(function (b) { return b.type !== 'html'; });
  var legacyHtml = (C.blocks || []).filter(function (b) { return b.type === 'html'; });
  var list = document.getElementById('dtlList'), info = document.getElementById('dtlInfo'), json = document.getElementById('dtlJson');
  if (!list) return;
  var TALL = 2.5, SLICE_H = 2400, MINW = 1200, MAXW = 1600, NORMAL_MAXW = 1600;

  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
  function url(src) { return /^https?:/i.test(src) ? src : C.base + src; }

  /* ---------- 업로드 ---------- */
  function post(blob, name) {
    var fd = new FormData();
    fd.append('file', blob, name);
    fd.append(C.csrfName, C.csrf);
    return fetch(C.upload, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': C.csrf }, credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        if (j.csrf) { C.csrf = j.csrf; document.querySelectorAll('input[name="' + C.csrfName + '"]').forEach(function (i) { i.value = j.csrf; }); }
        if (!j.ok) throw new Error(j.error || '업로드 실패');
        return j.path;
      });
  }
  function load(file) {
    return new Promise(function (res, rej) {
      var img = new Image(); img.onload = function () { res(img); }; img.onerror = function () { rej(new Error(file.name + ': 이미지를 열지 못했습니다')); };
      img.src = URL.createObjectURL(file);
    });
  }
  function toBlob(cv, type, q) { return new Promise(function (r) { cv.toBlob(r, type, q); }); }
  function isGif(f) { return /gif$/i.test(f.type) || /\.gif$/i.test(f.name); }

  /* 보통 이미지: 가로 1600 이하로 줄여 한 장 */
  async function saveOne(file) {
    if (isGif(file)) return [{ type: 'img', src: await post(file, file.name) }];
    var img = await load(file), keepAlpha = /png|webp/i.test(file.type);
    var s = Math.min(1, NORMAL_MAXW / img.width), w = Math.round(img.width * s), h = Math.round(img.height * s);
    var cv = document.createElement('canvas'); cv.width = w; cv.height = h;
    var cx = cv.getContext('2d'); cx.imageSmoothingQuality = 'high';
    if (!keepAlpha) { cx.fillStyle = '#fff'; cx.fillRect(0, 0, w, h); }
    cx.drawImage(img, 0, 0, w, h);
    var blob = await toBlob(cv, keepAlpha ? 'image/png' : 'image/jpeg', 0.86);
    return [{ type: 'img', src: await post(blob, keepAlpha ? 'img.png' : 'img.jpg') }];
  }
  /* 긴 상세 이미지: 잘라서 여러 장 (seq) */
  async function saveDetail(file, onPart) {
    var img = await load(file);
    if (isGif(file) || img.height / img.width < TALL) return saveOne(file);
    var tw = img.width < MINW ? MINW : Math.min(img.width, MAXW), sc = tw / img.width;
    var w = Math.round(img.width * sc), total = Math.round(img.height * sc), n = Math.ceil(total / SLICE_H), out = [];
    for (var i = 0; i < n; i++) {
      onPart && onPart(i + 1, n);
      var dy = i * SLICE_H, dh = Math.min(SLICE_H, total - dy);
      var cv = document.createElement('canvas'); cv.width = w; cv.height = dh;
      var cx = cv.getContext('2d'); cx.imageSmoothingQuality = 'high'; cx.fillStyle = '#fff'; cx.fillRect(0, 0, w, dh);
      cx.drawImage(img, 0, dy / sc, img.width, dh / sc, 0, 0, w, dh);
      var blob = await toBlob(cv, 'image/jpeg', 0.92);
      out.push({ type: 'img', src: await post(blob, 'detail-' + (i + 1) + '.jpg'), seq: true, w: w, h: dh });
      cv.width = cv.height = 0;
      await new Promise(function (r) { setTimeout(r, 0); });
    }
    return out;
  }

  /* ---------- 그리기 ---------- */
  function render() {
    var h = '';
    if (legacyHtml.length) h += '<div class="dtl-blk"><div class="dtl-kind">예전 HTML 설명<small>저장하면 없어집니다</small></div><div class="small muted">예전에 넣은 HTML 설명이 있습니다. 필요한 내용은 문단으로 옮겨 주세요.</div><div class="dtl-btns"><button type="button" class="del" data-legacy>지금 지우기</button></div></div>';
    blocks.forEach(function (b, i) {
      var kind = b.type === 'p' ? '문단' : b.type === 'video' ? '영상' : (b.seq ? '상세 조각' : '이미지'), body;
      if (b.type === 'p') body = '<textarea class="ta" data-i="' + i + '" data-k="text" placeholder="문단 내용">' + esc(b.text) + '</textarea>';
      else if (b.type === 'video') body = '<input class="inp" data-i="' + i + '" data-k="src" value="' + esc(b.src) + '" placeholder="유튜브·비메오 주소">';
      else if (b.src) body = '<img src="' + esc(url(b.src)) + '" alt="">';
      else body = '<label class="dtl-drop" data-drop="' + i + '">이미지를 끌어다 놓거나 눌러서 고르기<input type="file" accept="image/*" hidden data-file="' + i + '"></label>';
      h += '<div class="dtl-blk' + (b.seq ? ' is-seq' : '') + '"><div class="dtl-kind">' + (i + 1) + '. ' + kind + (b.seq && b.w ? '<small>' + b.w + '×' + b.h + '</small>' : '') + '</div><div>' + body + '</div>' +
           '<div class="dtl-btns"><button type="button" data-up="' + i + '"' + (i ? '' : ' disabled') + '>↑ 위로</button><button type="button" data-down="' + i + '"' + (i < blocks.length - 1 ? '' : ' disabled') + '>↓ 아래로</button><button type="button" class="del" data-del="' + i + '">삭제</button></div></div>';
    });
    if (!blocks.length && !legacyHtml.length) h = '<div class="small muted" style="padding:16px;text-align:center;border:1px dashed var(--line);border-radius:8px">아직 상세 내용이 없습니다. 통이미지를 올리거나 문단·이미지를 추가하세요.</div>';
    list.innerHTML = h;
    sync();
  }
  function sync() { json.value = JSON.stringify(blocks); }

  list.addEventListener('input', function (e) {
    var t = e.target; if (t.dataset.i == null) return;
    blocks[+t.dataset.i][t.dataset.k] = t.value; sync();
  });
  list.addEventListener('click', function (e) {
    var t = e.target.closest('button'); if (!t) return;
    if (t.dataset.legacy != null) { legacyHtml = []; render(); return; }
    var i;
    if ((i = t.dataset.up) != null) { i = +i; var x = blocks[i]; blocks[i] = blocks[i - 1]; blocks[i - 1] = x; }
    else if ((i = t.dataset.down) != null) { i = +i; var y = blocks[i]; blocks[i] = blocks[i + 1]; blocks[i + 1] = y; }
    else if ((i = t.dataset.del) != null) { blocks.splice(+i, 1); }
    render();
  });
  async function fillOne(i, file) {
    info.textContent = '이미지 올리는 중…';
    try { var r = await saveOne(file); blocks[i].src = r[0].src; info.textContent = ''; } catch (err) { info.textContent = err.message; }
    render();
  }
  list.addEventListener('change', function (e) { var t = e.target; if (t.dataset.file != null && t.files[0]) fillOne(+t.dataset.file, t.files[0]); });
  list.addEventListener('dragover', function (e) { var d = e.target.closest('[data-drop]'); if (d) { e.preventDefault(); d.classList.add('is-over'); } });
  list.addEventListener('dragleave', function (e) { var d = e.target.closest('[data-drop]'); if (d) d.classList.remove('is-over'); });
  list.addEventListener('drop', function (e) { var d = e.target.closest('[data-drop]'); if (d && e.dataTransfer.files[0]) { e.preventDefault(); fillOne(+d.dataset.drop, e.dataTransfer.files[0]); } });

  document.querySelectorAll('[data-add]').forEach(function (b) {
    b.addEventListener('click', function () {
      var t = b.dataset.add; blocks.push(t === 'p' ? { type: 'p', text: '' } : t === 'video' ? { type: 'video', src: '' } : { type: 'img', src: '' }); render();
      var last = list.lastElementChild; last && last.scrollIntoView({ block: 'nearest' });
    });
  });

  /* 통이미지: 파일 이름 순서(숫자 인식)대로 */
  var bulk = document.getElementById('dtlBulk'), busy = false;
  bulk.addEventListener('change', async function () {
    var files = [].slice.call(bulk.files).sort(function (a, b) { return a.name.localeCompare(b.name, undefined, { numeric: true }); });
    bulk.value = ''; if (!files.length) return;
    busy = true; var added = 0, fail = [];
    for (var f = 0; f < files.length; f++) {
      try {
        var r = await saveDetail(files[f], function (k, n) { info.textContent = '(' + (f + 1) + '/' + files.length + ') ' + files[f].name + (n > 1 ? ' — ' + k + '/' + n + '조각 자르는 중' : ' 올리는 중'); });
        blocks = blocks.concat(r); added += r.length; render();
      } catch (err) { fail.push(files[f].name + ': ' + err.message); }
    }
    busy = false;
    info.textContent = files.length + '개 파일 → 블록 ' + added + '개 추가' + (fail.length ? ' · 실패: ' + fail.join(', ') : '') + '. 저장을 눌러야 반영됩니다.';
  });

  var form = list.closest('form');
  form && form.addEventListener('submit', function (e) {
    if (busy) { e.preventDefault(); alert('이미지를 올리는 중입니다. 끝난 뒤 저장해 주세요.'); return; }
    sync();
  });
  render();
})();
