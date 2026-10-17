-- 공급사 홈페이지·제출 자료(회사소개서·카탈로그 등). 여러 번 실행해도 안전.
SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `suppliers` ADD COLUMN `website` VARCHAR(255) NULL AFTER `biz_no`, ADD COLUMN `doc_files` TEXT NULL AFTER `website`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'website');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
