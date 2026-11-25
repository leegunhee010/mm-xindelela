# XINDELELA 미얀마 도매몰 운영 서버 배포 안내 (1Panel)

messeze.com · makenov.com 과 같은 방식입니다. main 브랜치 푸시 → GitHub Actions → SSH 로 서버 `git reset` → DB 스키마·초기 데이터 적용 → 헬스체크(실패 시 이전 커밋으로 되돌림).

> 도메인: **mm.xindelela.com** (2026-11-25 확정). 바꾸게 되면 `.github/workflows/deploy.yml` 의 `SITE_DIR`, `SITE_URL` 과 `.env` 의 `app.baseURL` 을 같이 바꿀 것.

## 1. 서버 준비 (1회)

1. 1Panel → 웹사이트 → PHP 사이트 생성
   - 도메인 `mm.xindelela.com`, **PHP 8.2** 런타임 (확장: intl, mbstring, mysqli, gd, curl)
   - 사이트 디렉터리 기본값 `/opt/1panel/www/sites/mm.xindelela.com/index` (다르면 deploy.yml 의 `SITE_DIR` 수정)
   - **웹루트는 `index/public`** (CodeIgniter 4 구조)
   - HTTPS 인증서 발급 (1Panel 의 Let's Encrypt)
2. 사이트 디렉터리를 이 레포의 clone 으로 교체
   ```bash
   cd /opt/1panel/www/sites/mm.xindelela.com
   rm -rf index && git clone git@github.com:leegunhee010/mm-xindelela.git index
   ```
3. MySQL 데이터베이스 생성: `mm_xindelela`, utf8mb4 / utf8mb4_unicode_ci, 전용 계정
4. `.env` 작성 (`.env.example` 복사 후 값 채움)
   ```
   CI_ENVIRONMENT = production
   app.baseURL = 'https://mm.xindelela.com/'
   database.default.hostname = <1Panel MySQL 컨테이너명 또는 127.0.0.1>
   database.default.database = mm_xindelela
   database.default.username = ...
   database.default.password = ...
   ```
   ⚠️ `.env` 는 PHP-FPM 사용자가 읽을 수 있어야 함. 이후 수정은 `sed -i`/`mv` 금지, `cat > .env` 로 같은 inode 유지 (makenov 9/15 장애 사례).
5. 권한: `writable/`, `public/uploads/` 를 PHP 실행 사용자(1panel/www-data) 소유로, 775
6. Nginx: 1Panel PHP 사이트 기본 설정이면 충분. `location /` 는 `try_files $uri $uri/ /index.php?$query_string;`
7. 첫 DB 적용 (Actions 가 매 배포마다 다시 돌리므로 수동 1회는 선택)
   ```bash
   php scripts/apply-sql.php docs/sql
   ```

### DB 파일 (docs/sql, 이름순으로 자동 적용, 여러 번 실행해도 안전)

| 파일 | 내용 |
|---|---|
| 001 ~ 014 | 테이블·칸 (미얀마어 이름·내용 칸 013, 014 포함) |
| 002-seed.sql | 관리자 계정 1개 + 기본 설정 (데모 없음) |
| 015-initial-data.sql | **실제 판매 데이터**: 카테고리 3 · 공급사 10 · 상품 27 · 배너 3 · 공지 2. **처음 한 번만** 들어감 (`settings.initial_data_v1` 표시). 이후 관리자에서 지운 상품이 배포 때 되살아나지 않음 |

데모 회원·데모 주문·데모 상품은 운영 DB 에 들어가지 않습니다.

## 2. GitHub Actions 비밀값

레포 Settings → Secrets and variables → Actions
- `SERVER_HOST` 서버 IP/호스트
- `SERVER_KEY` deploy 사용자 SSH 개인키 (서버 `~deploy/.ssh/authorized_keys` 에 공개키 등록, 포트 2218)

비밀값이 없으면 푸시할 때마다 Actions 가 실패로 표시됩니다(서버엔 영향 없음).

## 3. 배포 확인

```bash
curl -s https://mm.xindelela.com/deploy-check
# {"ok":true,"marker":...,"env":"production",...}  ← ok 가 false 면 DB 연결 실패
```

## 4. 배포 직후 할 일 (순서대로)

1. `https://mm.xindelela.com/admin/login` → `admin@xindelela.com` / `admin1234` 로그인
   → **비밀번호 변경 화면으로 자동 이동**합니다. 10자 이상 새 비밀번호로 바꿔야 다른 화면을 쓸 수 있습니다.
2. 관리자 › **설정**: KBZPay 입금 계좌, 고객센터 Viber·전화·이메일 확인
3. 관리자 › **설정**: 구글맵 키 입력 (주문서 주소 검색용). 구글 클라우드 콘솔에서 키의 허용 웹사이트에 `https://mm.xindelela.com/*` 추가
4. 관리자 › **SEO 설정**: 검색 노출 여부(기본 차단), 사이트 제목·설명. 파비콘·공유 이미지는 기본값이 이미 들어 있음
5. 상품 가격·재고 확인 (재고 0 인 상품은 인증 회원에게 '재고 0개'로 보임)
6. 구매자 화면에서 가입 → 관리자 인증 승인 → 주문 → 입금 확인 요청 → 관리자 입금 확인까지 한 번 시험

## 5. 언어

- 구매자 화면은 **미얀마어가 기본**, 상단 토글로 한국어. 관리자 화면은 항상 한국어.
- 화면 문구 번역: `app/Language/my/ui.php` (한국어 원문 → 미얀마어). 빠진 문구 확인: `php scripts/i18n-extract.php`
- 상품·공급사·배너·공지·카테고리·가입 랜딩은 관리자 화면의 '미얀마어' 칸에서 입력

## 6. 스키마 변경 규칙

- 로컬: `app/Database/Migrations/` 에 마이그레이션 추가 + `php spark migrate`
- 서버: 같은 변경을 `docs/sql/0NN-*.sql` 로 작성 (`IF NOT EXISTS` / `WHERE NOT EXISTS` 로 멱등하게). 배포 시 자동 적용.
- 실서버는 `php spark` 를 쓸 수 없음 (`scripts/apply-sql.php` 머리말 참고)

## 7. 테스트 서버 (Render)

`https://mm-xindelela-test.onrender.com` 은 무료 테스트 서버입니다. 재시작하면 DB 가 데모 상태로 초기화되므로 실제 주문을 받으면 안 됩니다. 운영 서버가 열리면 Render 서비스는 정지하거나 삭제하세요.
