<?php /* 배송지 주소 검색 (구글맵 Places API New). 필요 id: shipAddr addrSug shipLat shipLng shipCity addrMap */ ?>
<?php if ($mapsKey): ?>
<script>
// 키가 막히면(이 사이트 주소가 키 허용 목록에 없거나 키가 잘못됨) 지도를 숨기고 직접 입력 안내
window.gm_authFailure = function () {
  document.querySelectorAll('.addr-map-wrap, .addr-hint').forEach(function (el) { el.style.display = 'none'; });
  var inp = document.getElementById('shipAddr');
  if (inp && !document.getElementById('addrFail')) {
    var p = document.createElement('p'); p.id = 'addrFail'; p.className = 'addr-fail';
    p.textContent = '지금은 지도 검색을 쓸 수 없습니다. 주소를 직접 입력해 주세요.';
    inp.closest('.addr-search').after(p);
  }
  console.error('[mm] Google Maps key rejected for ' + location.host);
};
(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({key:<?= json_encode($mapsKey) ?>,v:"weekly",language:"en",region:"MM"});
(function () {
  var inp = document.getElementById('shipAddr'), ul = document.getElementById('addrSug');
  var lat = document.getElementById('shipLat'), lng = document.getElementById('shipLng'), city = document.getElementById('shipCity'), link = document.getElementById('addrMap');
  var lib = null, token = null, timer = null, items = [], cur = -1;
  var box = document.getElementById('addrMapBox'), map = null, pin = null, YGN = { lat: 16.8409, lng: 96.1735 };
  function pos() { return lat.value && lng.value ? { lat: parseFloat(lat.value), lng: parseFloat(lng.value) } : null; }
  function setPos(p, zoom) {
    lat.value = p.lat.toFixed(7); lng.value = p.lng.toFixed(7); showLink();
    if (!map) return;
    pin.setPosition(p); pin.setVisible(true);
    if (zoom) { map.setZoom(zoom); }
    map.panTo(p);
  }
  async function initMap() {
    if (map || !box || box.offsetParent === null) return;
    var m = await google.maps.importLibrary('maps'), mk = await google.maps.importLibrary('marker');
    var p = pos();
    map = new m.Map(box, { center: p || YGN, zoom: p ? 17 : 12, mapTypeControl: false, streetViewControl: false, fullscreenControl: false, clickableIcons: false, gestureHandling: 'cooperative' });
    pin = new mk.Marker({ map: map, position: p || YGN, draggable: true, visible: !!p });
    pin.addListener('dragend', function () { var q = pin.getPosition(), p = { lat: q.lat(), lng: q.lng() }; setPos(p); reverse(p); });
    map.addListener('click', function (ev) { var p = { lat: ev.latLng.lat(), lng: ev.latLng.lng() }; setPos(p); reverse(p); });
  }
  var geocoder = null, gseq = 0;
  function cityOf(comp, key) {
    var c = '';
    ['locality', 'administrative_area_level_2', 'administrative_area_level_1'].some(function (t) { var f = (comp || []).find(function (x) { return x.types.indexOf(t) > -1; }); if (f) { c = f[key]; return true; } });
    return c.replace(/ (Region|City)$/, '');
  }
  function clean(a) { return (a || '').replace(/[\u1000-\u109F\uAA60-\uAA7F]+/g, '').replace(/\s*,(\s*,)+/g, ',').replace(/[\s,&]+$/, '').replace(/,\s*Myanmar( \(Burma\))?$/, '').trim(); }
  async function reverse(p) {
    var my = ++gseq;
    // 1) Geocoding API (켜져 있으면)  2) 안 되면 Places API(New) 근처 장소 주소
    try {
      if (geocoder === false) throw 0; if (!geocoder) { var gl = await google.maps.importLibrary('geocoding'); geocoder = new gl.Geocoder(); }
      var r = await geocoder.geocode({ location: p, language: 'en' });
      if (my !== gseq) return;
      var best = (r.results || []).find(function (x) { return x.types.indexOf('plus_code') < 0; });
      if (best) { inp.value = clean(best.formatted_address); var c = cityOf(best.address_components, 'long_name'); if (c) city.value = c; return; }
    } catch (e) { geocoder = false; }
    try {
      var pl = await google.maps.importLibrary('places');
      var res = await pl.Place.searchNearby({ fields: ['displayName', 'formattedAddress', 'addressComponents'], locationRestriction: { center: p, radius: 60 }, maxResultCount: 1, rankPreference: pl.SearchNearbyRankPreference.DISTANCE, language: 'en', region: 'mm' });
      if (my !== gseq || !res.places || !res.places.length) return;
      var pc = res.places[0], addr = clean(pc.formattedAddress || ''), nm = clean(pc.displayName || '');
      inp.value = nm && addr.indexOf(nm) !== 0 ? nm + ', ' + addr : addr;
      var c2 = cityOf(pc.addressComponents, 'longText'); if (c2) city.value = c2;
    } catch (e) { console.warn('reverse', e); }
  }
  window.addEventListener('mm-addr-show', function () { setTimeout(initMap, 0); });
  function showLink() { if (!link) return; if (lat.value && lng.value) { link.href = 'https://www.google.com/maps?q=' + lat.value + ',' + lng.value; link.style.display = 'inline-block'; } else { link.style.display = 'none'; } }
  function close() { ul.hidden = true; ul.innerHTML = ''; items = []; cur = -1; }
  function mark() { [].forEach.call(ul.children, function (li, i) { li.classList.toggle('on', i === cur); }); }
  async function search(q) {
    if (!lib) { lib = await google.maps.importLibrary('places'); }
    if (!token) { token = new lib.AutocompleteSessionToken(); }
    var res = await lib.AutocompleteSuggestion.fetchAutocompleteSuggestions({ input: q, sessionToken: token, includedRegionCodes: ['mm'], language: 'en' });
    if (inp.value.trim() !== q) return;
    items = (res.suggestions || []).map(function (s) { return s.placePrediction; }).filter(Boolean);
    if (!items.length) { ul.innerHTML = '<li class="addr-sug-empty">미얀마 안에서 찾지 못했습니다. 영어로 입력해 보세요 (예: Pyay Rd, Junction City)</li>'; ul.hidden = false; return; }
    ul.innerHTML = items.map(function (pp) {
      var main = pp.mainText ? pp.mainText.text : pp.text.text, sub = pp.secondaryText ? pp.secondaryText.text : '';
      return '<li><b></b><span></span></li>';
    }).join('') + '<li class="addr-sug-by">powered by Google</li>';
    items.forEach(function (pp, i) {
      var li = ul.children[i];
      li.querySelector('b').textContent = pp.mainText ? pp.mainText.text : pp.text.text;
      li.querySelector('span').textContent = pp.secondaryText ? pp.secondaryText.text : '';
      li.addEventListener('mousedown', function (ev) { ev.preventDefault(); pick(i); });
    });
    cur = -1; ul.hidden = false;
  }
  async function pick(i) {
    var pp = items[i]; if (!pp) return;
    close();
    var place = pp.toPlace();
    await place.fetchFields({ fields: ['displayName', 'formattedAddress', 'location', 'addressComponents'] });
    token = null; // 한 번 고르면 세션 종료 (과금 단위)
    var addr = clean(place.formattedAddress || pp.text.text);
    var name = clean(place.displayName || '');
    inp.value = name && addr.indexOf(name) !== 0 ? name + ', ' + addr : addr;
    var comp = place.addressComponents || [], c = '';
    ['locality', 'administrative_area_level_2', 'administrative_area_level_1'].some(function (t) { var f = comp.find(function (x) { return x.types.indexOf(t) > -1; }); if (f) { c = f.longText; return true; } });
    if (c && !city.value.trim()) city.value = c.replace(/ (Region|City)$/, '');
    if (place.location) { await initMap(); setPos({ lat: place.location.lat(), lng: place.location.lng() }, 17); }
    showLink();
  }
  inp.addEventListener('input', function () {
    lat.value = ''; lng.value = ''; showLink(); if (pin) pin.setVisible(false);
    clearTimeout(timer); var q = inp.value.trim();
    if (q.length < 2) { close(); return; }
    timer = setTimeout(function () { search(q).catch(function (e) { console.warn(e); close(); }); }, 250);
  });
  inp.addEventListener('keydown', function (e) {
    if (ul.hidden || !items.length) return;
    if (e.key === 'ArrowDown') { cur = Math.min(cur + 1, items.length - 1); mark(); e.preventDefault(); }
    else if (e.key === 'ArrowUp') { cur = Math.max(cur - 1, 0); mark(); e.preventDefault(); }
    else if (e.key === 'Enter' && cur > -1) { e.preventDefault(); pick(cur); }
    else if (e.key === 'Escape') { close(); }
  });
  inp.addEventListener('blur', function () { setTimeout(close, 150); });
  showLink();
  initMap();
})();
</script>
<?php endif ?>
