# mm.xindelela.com — 미얀마 K-브랜드 B2B 도매 폐쇄몰

도매꾹 방식의 회원제 도매몰. 상품은 공개, **도매가·주문 신청은 사업자 인증(수동 검수) 회원만**.
사이트 결제 없음: 주문 신청 → 관리자 금액 확정 → KBZPay 선불 송금·캡처 업로드 → 입금 확인 → 공급사 발주·출고.

스택: PHP 8.2 / CodeIgniter 4.7 (system/ 동봉, composer 불필요) / MySQL 5.6 (운영) · SQLite3 (로컬)

## 로컬 실행
```
cp .env.example .env   # 로컬은 이미 .env 가 SQLite 로 설정됨
php spark migrate
php spark db:seed DemoSeeder
php -S localhost:5880 -t public system/rewrite.php
```
데모 계정
- 관리자 `/admin/login` admin@xindelela.com / admin1234
- 공급사 `/supplier/login` supplier@demo.kr / supplier1234
- 구매자(승인) buyer@demo.mm / buyer1234 · 구매자(대기) pending@demo.mm / buyer1234

## 운영 배포 (1Panel / MySQL)
1. `.env` 를 `.env.example` 기준으로 작성 (`CI_ENVIRONMENT=production`, MySQLi, baseURL `https://mm.xindelela.com/`)
2. `php spark migrate && php spark db:seed DemoSeeder` (관리자·설정·카테고리 생성. 데모 공급사·상품은 관리자에서 삭제)
3. 웹루트 = `public/`, `writable/`·`public/uploads/` 쓰기 권한 (www-data)
4. 관리자 로그인 후 **설정**에서 KBZPay 계좌·Viber·포인트 규칙 입력, 관리자 비밀번호 변경

## 구조
- `app/Controllers` 공개(Home·Products·Auth·Cart·Checkout·MyPage·Notices) / `Supplier/*` / `Admin/*`
- `app/Filters` BuyerAuth · SupplierAuth · AdminAuth (역할·승인 상태 검사)
- `app/Libraries/PointService.php` 포인트 원장(모든 증감은 여기), `Uploader.php` 업로드 화이트리스트
- `app/Helpers/mm_helper.php` `mm_user()` `mm_is_approved()` `mm_price()` `mm_unit_price()` `mm_setting()` 라벨 함수
- `app/Views` 레이아웃 `layouts/main`(980px 도매꾹 실측) · `layouts/admin` · `layouts/supplier`
- `public/assets/css/mm.css` 사이트, `admin.css` 관리자·공급사센터

## 상태 흐름
- 구매자 `pending`(검수 대기) → `approved` / `rejected`(사유 표시, 재제출하면 pending) / `suspended`
- 상품 `draft` → `pending`(검수) → `active` / `hidden` / `rejected`
- 주문 `requested` → `quoted`(금액 확정·입금 대기) → `proof`(캡처 업로드) → `paid` → `preparing` → `shipped` → `delivered` / `cancelled`

## 포인트 규칙 (관리자 > 설정)
- 추천 회원 승인 시 추천인 +N, 추천 회원 첫 주문 입금 시 추천인 +N, 가입 보너스, 구매 적립률
- 주문당 사용 한도 = 상품 금액 × %, 최소 사용 포인트. 취소 시 자동 환원. 중복 지급은 `point_logs` 로 차단

## 미정(사장님 확인 대기)
- COD(착불): 설정 `cod_enabled=1` 로 켤 수 있게만 준비, 기본 꺼짐
- 통화 기호(기본 Ks), 배송 주체, 공급사 수수료율 사용처, 승인·반려 알림 채널(현재 사이트 내 표시만)
