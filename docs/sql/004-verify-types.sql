-- 사업자 인증 유형 (offline,online,network 쉼표 목록). 여러 번 실행해도 안전.
-- app/Database/Migrations/2026-10-17-000002_AddVerifyTypes.php 와 동일.
SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `users` ADD COLUMN `verify_types` VARCHAR(60) NULL AFTER `business_type`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'verify_types');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
-- 기존 회원: 판매 형태(business_type)로 채움
UPDATE `users` SET `verify_types` = CASE `business_type` WHEN 'both' THEN 'offline,online' ELSE `business_type` END
 WHERE `role` = 'buyer' AND (`verify_types` IS NULL OR `verify_types` = '') AND `business_type` IN ('online','offline','both');
