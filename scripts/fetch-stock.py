# -*- coding: utf-8 -*-
"""Openverse(무료 라이선스 이미지 API)에서 데모 상품 사진 후보를 받아 writable/stock/<key>/N.jpg 로 저장.
   python -I scripts/fetch-stock.py            # 후보 4장씩
   라이선스 cc0 / by / by-sa 만. 출처는 writable/stock/credits.json 에 기록."""
import io, json, os, sys, urllib.request, urllib.parse
sys.stdout.reconfigure(encoding='utf-8')
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUT = os.path.join(ROOT, 'writable', 'stock')
ITEMS = {
    'p1': 'toner skincare bottle', 'p2': 'serum dropper', 'p3': 'sheet mask', 'p4': 'sunscreen',
    'p5': 'seaweed snack', 'p6': 'instant noodles cup', 'p7': 'ginseng', 'p8': 'collagen jelly',
    'p9': 'dish soap', 'p10': 'toothbrush', 'p11': 'thermos bottle', 'p12': 'socks pair',
    'b1': 'korean cosmetics', 'b2': 'korean snacks', 'b3': 'warehouse boxes',
}
UA = {'User-Agent': 'mm-xindelela/1.0 (contact: meeneex2@gmail.com)'}
credits = {}
for key, q in ITEMS.items():
    d = os.path.join(OUT, key); os.makedirs(d, exist_ok=True)
    url = 'https://api.openverse.org/v1/images/?' + urllib.parse.urlencode({'q': q, 'license': 'cc0,by,by-sa', 'page_size': 12, 'aspect_ratio': 'square,wide'})
    try:
        data = json.load(urllib.request.urlopen(urllib.request.Request(url, headers=UA), timeout=30))
    except Exception as e:
        print(key, 'API 실패', e); continue
    n = 0
    for r in data.get('results', []):
        if n >= 4: break
        src = r.get('url') or ''
        if not src.lower().split('?')[0].endswith(('.jpg', '.jpeg', '.png')): continue
        try:
            body = urllib.request.urlopen(urllib.request.Request(src, headers=UA), timeout=30).read()
            if len(body) < 20000: continue
            n += 1
            open(os.path.join(d, f'{n}.jpg'), 'wb').write(body)
            credits[f'{key}/{n}'] = {'title': r.get('title'), 'creator': r.get('creator'), 'license': r.get('license'), 'source': r.get('foreign_landing_url'), 'url': src}
        except Exception as e:
            pass
    print(key, q, '->', n)
json.dump(credits, open(os.path.join(OUT, 'credits.json'), 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
# 선택용 시트
h = '<html><body style="margin:0;font:11px sans-serif"><div style="display:grid;grid-template-columns:repeat(8,170px);gap:6px">'
for key in ITEMS:
    for i in range(1, 5):
        p = f'{key}/{i}.jpg'
        if os.path.exists(os.path.join(OUT, p)):
            h += f'<div><img src="{p}" style="width:170px;height:170px;object-fit:cover"><div>{p}</div></div>'
io.open(os.path.join(OUT, '_sheet.html'), 'w', encoding='utf-8').write(h + '</div></body></html>')
print('sheet ok')
