# mm.xindelela.com 배포 안내 (1Panel)

messeze.com · makenov.com 과 같은 방식입니다. main 브랜치 푸시 → GitHub Actions → SSH 로 서버 `git reset` → 스키마 적용 → 헬스체크.

## 1. 서버 준비 (1회)

1. 1Panel → 웹사이트 → PHP 사이트 생성
   - 도메인 `mm.xindelela.com`, PHP 8.2 런타임 (확장: intl, mbstring, mysqli, gd, curl)
   - 사이트 디렉터리 기본값이면 `/opt/1panel/www/sites/mm.xindelela.com/index` (다르면 `.github/workflows/deploy.yml` 의 `SITE_DIR` 수정)
   - **웹루트는 `index/public`** 으로 지정 (CI4 구조)
2. 사이트 디렉터리를 이 레포의 clone 으로 교체
   ```bash
   cd /opt/1panel/www/sites/mm.xindelela.com
   rm -rf index && git clone git@github.com:leegunhee010/mm-xindelela.git index
   ```
3. MySQL 5.6 데이터베이스 생성: `mm_xindelela`, utf8mb4_unicode_ci, 전용 계정
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
7. 첫 스키마 적용 (Actions 가 매 배포마다 다시 돌리므로 수동 1회는 선택)
   ```bash
   php scripts/apply-sql.php docs/sql
   ```
   phpMyAdmin 에서 `docs/sql/001-schema.sql` → `002-seed.sql` 순서로 붙여넣어도 됩니다.

## 2. GitHub Actions 비밀값

레포 Settings → Secrets and variables → Actions
- `SERVER_HOST` 서버 IP/호스트
- `SERVER_KEY` deploy 사용자 SSH 개인키 (서버 `~deploy/.ssh/authorized_keys` 에 공개키 등록, 포트 2218)

## 3. 배포 확인

```bash
curl -s https://mm.xindelela.com/deploy-check
# {"ok":true,"marker":1,"env":"production",...}  ← ok 가 false 면 DB 연결 실패
```
Actions 가 초록이어도 `marker` 값으로 대조합니다 (`app/Controllers/DeployCheck.php` 의 MARKER 를 올려 푸시).

## 4. 배포 직후 할 일

1. `/admin/login` → admin@xindelela.com / admin1234 로그인 → 마이페이지 아닌 **설정** 에서 KBZPay 계좌·Viber·이메일 입력
2. 관리자 비밀번호 변경 (현재는 DB 직접 수정: `UPDATE users SET password_hash='<password_hash()>' WHERE email='admin@xindelela.com'`) — 관리자 비번 변경 화면은 다음 작업
3. 데모 공급사(supplier@demo.kr)·데모 상품 12개 삭제 또는 숨김
4. 구글·네이버 색인 차단은 이미 `noindex` 메타로 적용됨 (폐쇄몰)

## 5. 스키마 변경 규칙

- 로컬: `app/Database/Migrations/` 에 마이그레이션 추가 + `php spark migrate`
- 서버: 같은 변경을 `docs/sql/00N-*.sql` 로 작성 (`IF NOT EXISTS` / `WHERE NOT EXISTS` 로 멱등하게). 배포 시 자동 적용.
