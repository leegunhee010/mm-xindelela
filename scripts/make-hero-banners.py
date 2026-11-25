# 홈 히어로 배너 사진 만들기 (배너 레이아웃은 그대로, 사진만 상품 연출 사진으로)
#   python -I scripts/make-hero-banners.py   (mm-xindelela 폴더에서)
# 정사각 상품 사진에서 상품이 잘 보이는 가로 띠(1280:416)를 잘라 배너 한 장으로 씀. y = 위에서부터 자르기 시작 비율.
from PIL import Image

W, H = 1600, 520   # 화면 1280x416 의 1.25배
P = 'public/assets/img/products/'
OUT = 'public/assets/img/banners/'

BANNERS = [
    ('hero_yopokki.jpg', P + 'yo1/m1.jpg', 0.35),
    ('hero_sapyeong.jpg', P + 'cu2/m2.jpg', 0.30),
    ('hero_beautyrecipe.jpg', P + 'br5/m8.jpg', 0.10),
]

for name, src, y in BANNERS:
    im = Image.open(src).convert('RGB')
    s = im.size[0]
    bh = round(s * H / W)
    top = min(int(s * y), im.size[1] - bh)
    im.crop((0, top, s, top + bh)).resize((W, H), Image.LANCZOS).save(OUT + name, quality=90, optimize=True)
    print(name)
