# 홈 히어로 배너 사진 만들기 (배너 레이아웃은 그대로, 사진만 상품 사진으로)
#   python -I scripts/make-hero-banners.py [--preview]   (mm-xindelela 폴더에서)
# 2560x832 (화면 1280x416 의 2배). 정사각 상품 사진을 오른쪽에 높이 꽉 차게 두고,
# 사진 왼쪽 가장자리의 배경색을 줄마다 이어 붙여 왼쪽으로 배경을 늘린다(글자 자리).
import sys
import random
from PIL import Image, ImageFilter

W, H = 2560, 832
P = 'public/assets/img/products/'
OUT = 'public/assets/img/banners/'

# (파일명, 사진, 오른쪽 여백 비율: 0 이면 오른쪽 끝에 붙임)
BANNERS = [
    ('hero_yopokki.jpg', P + 'yo4/m3.jpg', 0.04),
    ('hero_sapyeong.jpg', P + 'cu2/m3.jpg', 0.04),
    ('hero_beautyrecipe.jpg', P + 'br5/m6.jpg', 0.04),
]
if '--preview' in sys.argv:
    BANNERS = [(f'cand_{i}.jpg', P + s + '.jpg', 0.04) for i, s in enumerate(sys.argv[2:])]


def extend(src, right_pad):
    im = Image.open(src).convert('RGB').resize((H, H), Image.LANCZOS)
    x0 = W - H - int(W * right_pad)
    # 사진 왼쪽 가장자리 띠의 줄별 평균색 → 왼쪽 전체로 늘림
    strip = im.crop((0, 0, 48, H)).resize((1, H), Image.BOX).filter(ImageFilter.GaussianBlur(40))
    edge = strip.resize((x0 + H // 2, H), Image.NEAREST)
    # 줄 경계가 보이지 않게 세로로 살짝 흐림 + 아주 약한 잡음
    edge = edge.filter(ImageFilter.GaussianBlur(6))
    px = edge.load()
    rnd = random.Random(7)
    for y in range(0, H, 2):
        for x in range(0, edge.size[0], 2):
            n = rnd.randint(-3, 3)
            r, g, b = px[x, y]
            c = (max(0, min(255, r + n)), max(0, min(255, g + n)), max(0, min(255, b + n)))
            px[x, y] = c
            if x + 1 < edge.size[0]: px[x + 1, y] = c
            if y + 1 < H: px[x, y + 1] = c
            if x + 1 < edge.size[0] and y + 1 < H: px[x + 1, y + 1] = c
    # 오른쪽 여백도 같은 방법으로 (사진 오른쪽 가장자리)
    canvas = Image.new('RGB', (W, H))
    canvas.paste(edge.resize((x0 + H // 2, H)), (0, 0))
    rs = im.crop((H - 48, 0, H, H)).resize((1, H), Image.BOX).filter(ImageFilter.GaussianBlur(40)).resize((W - x0 - H // 2, H), Image.NEAREST)
    canvas.paste(rs, (x0 + H // 2, 0))
    # 사진 양쪽 가장자리를 부드럽게 섞음
    fade = 120
    mask = Image.new('L', (H, H), 255)
    for i in range(fade):
        v = int(255 * i / fade)
        mask.paste(v, (i, 0, i + 1, H))
        mask.paste(v, (H - 1 - i, 0, H - i, H))
    canvas.paste(im, (x0, 0), mask)
    return canvas


for name, src, pad in BANNERS:
    extend(src, pad).save(OUT + name, quality=90, optimize=True)
    print(name)
