# -*- coding: utf-8 -*-
"""데모 상품·배너에 임시 스톡 사진(public/assets/img/demo/pN.jpg, banner_N.jpg) 연결: 로컬 SQLite + 시더 + MySQL 시드 SQL."""
import io, re, sqlite3, os, sys
sys.stdout.reconfigure(encoding='utf-8')
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
os.chdir(ROOT)

# 1) 로컬 SQLite
db = sqlite3.connect('writable/mm.db')
for i in range(1, 13):
    db.execute("update products set thumb=?, images=? where code=?", (f'assets/img/demo/p{i}.jpg', f'["assets/img/demo/p{i}.jpg"]', f'DEMO-{i:03d}'))
for n in (1, 2, 3):
    db.execute("update banners set image=? where sort=?", (f'assets/img/demo/banner_{n}.jpg', n))
db.commit()
print('sqlite:', db.execute("select thumb from products where code='DEMO-001'").fetchone(), db.execute("select count(*) from banners where image is not null").fetchone())

# 2) DemoSeeder: 배너 image 필드
p = 'app/Database/Seeds/DemoSeeder.php'; s = io.open(p, encoding='utf-8').read()
if "'image' =>" not in s:
    n = [0]
    def addimg(m):
        n[0] += 1
        return m.group(0) + f" 'image' => 'assets/img/demo/banner_{n[0]}.jpg',"
    s = re.sub(r"\['title' => \"[^\"]*\", 'subtitle' => '[^']*',", addimg, s)
    io.open(p, 'w', encoding='utf-8').write(s)
print('seeder image fields:', s.count("'image' =>"))

# 3) MySQL 시드 SQL
p = 'docs/sql/002-seed.sql'; s = io.open(p, encoding='utf-8').read()
s = re.sub(r"assets/img/demo/p(\d+)\.svg", r"assets/img/demo/p\1.jpg", s)
cnt = [0]
def fixb(m):
    cnt[0] += 1
    row = m.group(0)
    if '`image`' in row:
        return row
    row = row.replace("(`title`,`subtitle`,`link`", "(`title`,`subtitle`,`image`,`link`")
    head, vals = row.split("SELECT ", 1)
    out, i, q = '', 0, 0
    while i < len(vals):
        c = vals[i]
        if c == "'":
            j = i + 1
            while j < len(vals):
                if vals[j] == '\\':
                    j += 2; continue
                if vals[j] == "'":
                    break
                j += 1
            out += vals[i:j + 1]; i = j + 1; q += 1
            if q == 2:
                out += f",'assets/img/demo/banner_{cnt[0]}.jpg'"
            continue
        out += c; i += 1
    return head + "SELECT " + out
s = re.sub(r"INSERT INTO `banners` [^\n]*", fixb, s)
io.open(p, 'w', encoding='utf-8').write(s)
print('seed banners patched:', cnt[0], '| jpg refs:', len(re.findall(r'demo/p\d+\.jpg', s)))
print([l for l in s.split('\n') if 'INSERT INTO `banners`' in l][0][:200])
