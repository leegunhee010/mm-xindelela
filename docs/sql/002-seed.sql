-- ============================================================
-- 초기 데이터: 관리자 계정·기본 설정 (여러 번 실행해도 안전, 이미 있으면 건너뜀)
-- 실제 상품·공급사·카테고리·배너·공지는 015-initial-data.sql (데모 데이터는 넣지 않음)
-- 초기 관리자 admin@xindelela.com / admin1234 → 운영 서버는 첫 로그인 때 비밀번호 변경 화면으로 강제 이동
-- ============================================================

INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'site_name','XINDELELA 미얀마 도매몰',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='site_name');
INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'currency_symbol','Ks',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='currency_symbol');
INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'currency_position','after',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='currency_position');
INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'points_signup_bonus','4000',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='points_signup_bonus');
INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'points_reward_rate','0',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='points_reward_rate');
INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'points_use_max_pct','30',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='points_use_max_pct');
INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'points_min_use','1000',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='points_min_use');
INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'kbzpay_account','KBZPay 09891257296 (PARK CHANG MIN)',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='kbzpay_account');
INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'cod_enabled','0',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='cod_enabled');
INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'verify_sla_text','영업일 기준 1일 이내 검수',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='verify_sla_text');
INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'cs_phone','+959 779 076 612',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='cs_phone');
INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'cs_viber','+959 779 076 612',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='cs_viber');
INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'cs_email','mm@xindelela.com',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='cs_email');
INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'topbar_text','신규 가입 비즈니스 인증은 영업일 기준 1일 이내 완료됩니다.',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='topbar_text');

INSERT INTO `users` (`role`,`email`,`password_hash`,`name`,`phone`,`status`,`company_name`,`approved_at`,`created_at`,`updated_at`) SELECT 'admin','admin@xindelela.com','$2y$10$ksUEZFL7Vx3kwFinEmBN0uWYfp4fHKjTqmArJcc2RGv0jYak4kvtG','관리자',NULL,'approved',NULL,NOW(),NOW(),NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `email`='admin@xindelela.com');

SELECT (SELECT COUNT(*) FROM `users`) AS users, (SELECT COUNT(*) FROM `products`) AS products, (SELECT COUNT(*) FROM `categories`) AS categories, (SELECT COUNT(*) FROM `settings`) AS settings;
