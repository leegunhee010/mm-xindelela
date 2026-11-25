# 사이트 아이콘·공유 이미지 만들기 (파비콘, 홈 화면 추가 아이콘, 앱 아이콘, 공유 미리보기 이미지)
#   python -I scripts/make-icons.py   (mm-xindelela 폴더에서)
# 아이콘 = 브랜드 주황 #FF6200 둥근 사각형 + 흰색 X (로고 XINDELELA 의 첫 글자, 글꼴은 로고와 같은 Pretendard Bold)
from PIL import Image, ImageDraw, ImageFont

ORANGE = (255, 98, 0)
FONT = 'public/assets/is/media/e00e15f44d7b58c0-s.p.woff2'   # Pretendard Bold
OUT = 'public/assets/img/icons/'


def icon(size, radius_ratio=0.22, pad_ratio=0.0):
    S = size * 4
    im = Image.new('RGBA', (S, S), (0, 0, 0, 0))
    d = ImageDraw.Draw(im)
    pad = int(S * pad_ratio)
    d.rounded_rectangle((pad, pad, S - pad - 1, S - pad - 1), int((S - 2 * pad) * radius_ratio), fill=ORANGE)
    f = ImageFont.truetype(FONT, int((S - 2 * pad) * 0.72))
    l, t, r, b = d.textbbox((0, 0), 'X', font=f)
    d.text(((S - (r - l)) / 2 - l, (S - (b - t)) / 2 - t), 'X', font=f, fill='white')
    return im.resize((size, size), Image.LANCZOS)


icon(32).save(OUT + 'favicon-32.png')
icon(16).save(OUT + 'favicon-16.png')
# favicon.ico (16·32·48) → 사이트 루트에
icon(256).save('public/favicon.ico', sizes=[(16, 16), (32, 32), (48, 48)])
# iOS 홈 화면: 배경이 꽉 찬 사각형(둥근 모서리는 iOS가 처리)
ap = Image.new('RGB', (180, 180), ORANGE)
ap.paste(icon(180, radius_ratio=0), (0, 0), icon(180, radius_ratio=0))
ap.save(OUT + 'apple-touch-icon.png')
# 안드로이드·PWA
icon(192).save(OUT + 'icon-192.png')
icon(512).save(OUT + 'icon-512.png')
mk = Image.new('RGB', (512, 512), ORANGE)   # maskable: 안전 영역 안에 X
mk.paste(icon(512, radius_ratio=0, pad_ratio=0.12), (0, 0), icon(512, radius_ratio=0, pad_ratio=0.12))
mk.save(OUT + 'icon-maskable-512.png')

# 공유 미리보기 이미지 1200x630: 흰 바탕 + 로고 글자 + 영문 한 줄 + 상품 배너 사진
W, H = 1200, 630
og = Image.new('RGB', (W, H), 'white')
d = ImageDraw.Draw(og)
fb = ImageFont.truetype(FONT, 72)
x, y = 70, 70
d.text((x, y), 'XINDELELA', font=fb, fill='black')
wx = d.textlength('XINDELELA ', font=fb)
d.text((x + wx, y), 'mall', font=fb, fill=ORANGE)
fs = ImageFont.truetype('C:/Windows/Fonts/segoeuib.ttf', 34)
d.text((x, y + 100), 'Genuine Korean K-Brand products', font=fs, fill=(40, 40, 40))
d.text((x, y + 146), 'at wholesale prices for Myanmar sellers', font=fs, fill=(40, 40, 40))
ph = Image.open('public/assets/img/banners/hero_yopokki.jpg').convert('RGB')
band = ph.resize((W, round(ph.height * W / ph.width)))
bh = H - 300
band = band.crop((0, band.height - bh, W, band.height))
og.paste(band, (0, 300))
og.save('public/assets/img/og-default.jpg', quality=88, optimize=True)
print('ok')
