<?php /* 배송지 주소 검색 (구글맵 Places API New). 필요 id: shipAddr addrSug shipLat shipLng shipCity addrMap */ ?>
<?php if ($mapsKey): ?>
<script>
(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({key:<?= json_encode($mapsKey) ?>,v:"weekly"});
(function () {
  var inp = document.getElementById('shipAddr'), ul = document.getElementById('addrSug');
  var lat = document.getElementById('shipLat'), lng = document.getElementById('shipLng'), city = document.getElementById('shipCity'), link = document.getElementById('addrMap');
  var lib = null, token = null, timer = null, items = [], cur = -1;
  function showLink() { if (lat.value && lng.value) { link.href = 'https://www.google.com/maps?q=' + lat.value + ',' + lng.value; link.style.display = 'inline-block'; } else { link.style.display = 'none'; } }
  function close() { ul.hidden = true; ul.innerHTML = ''; items = []; cur = -1; }
  function mark() { [].forEach.call(ul.children, function (li, i) { li.classList.toggle('on', i === cur); }); }
  async function search(q) {
    if (!lib) { lib = await google.maps.importLibrary('places'); }
    if (!token) { token = new lib.AutocompleteSessionToken(); }
    var res = await lib.AutocompleteSuggestion.fetchAutocompleteSuggestions({ input: q, sessionToken: token, includedRegionCodes: ['mm'], language: 'en' });
    if (inp.value.trim() !== q) return;
    items = (res.suggestions || []).map(function (s) { return s.placePrediction; }).filter(Boolean);
    if (!items.length) { close(); return; }
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
    var addr = (place.formattedAddress || pp.text.text).replace(/,\s*Myanmar( \(Burma\))?$/, '');
    var name = place.displayName || '';
    inp.value = name && addr.indexOf(name) !== 0 ? name + ', ' + addr : addr;
    var comp = place.addressComponents || [], c = '';
    ['locality', 'administrative_area_level_2', 'administrative_area_level_1'].some(function (t) { var f = comp.find(function (x) { return x.types.indexOf(t) > -1; }); if (f) { c = f.longText; return true; } });
    if (c && !city.value.trim()) city.value = c.replace(/ (Region|City)$/, '');
    if (place.location) { lat.value = place.location.lat().toFixed(7); lng.value = place.location.lng().toFixed(7); }
    showLink();
  }
  inp.addEventListener('input', function () {
    lat.value = ''; lng.value = ''; showLink();
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
})();
</script>
<?php endif ?>
