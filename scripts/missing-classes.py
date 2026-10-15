# -*- coding: utf-8 -*-
"""이식한 뷰에서 쓴 Tailwind 클래스 중 아이스크림몰 CSS 번들에 없는 것 목록."""
import re, io, glob, sys
sys.stdout.reconfigure(encoding='utf-8')
css = ''.join(io.open(f, encoding='utf-8', errors='replace').read() for f in glob.glob('public/assets/is/css/*.css'))
import glob as _g
views = [f for f in _g.glob('app/Views/**/*.php', recursive=True) if ('admin' not in f and 'supplier' not in f) or f.endswith(('login.php', 'register.php'))]
toks = set()
for v in views:
    s = io.open(v, encoding='utf-8').read()
    for m in re.finditer(r'class="([^"]*)"', s):
        for t in re.split(r'\s+', m.group(1)):
            if t and '<' not in t and '$' not in t and '?' not in t:
                toks.add(t)
def esc(t):
    return re.sub(r"([\[\]:/.!#%&(),'])", lambda m: '\\' + m.group(1), t)
skip = {'ptab', 'ptab-btn', 'mmwrap', 'mmrow', 'qty', 'tiers', 'modal', 'btn-g', 'close', 'field', 'ta', 'small', 'pdesc', 'in', 'blind', 'stp', 'minus', 'plus', 'tot', 'btn', 'sp'}
missing = []
for t in sorted(toks):
    if t in skip:
        continue
    e = '.' + esc(t)
    if any((e + x) in css for x in ('{', ',', ':', ' ', '>', '\\')):
        continue
    missing.append(t)
print(len(toks), 'tokens; missing', len(missing))
print('\n'.join(missing))
