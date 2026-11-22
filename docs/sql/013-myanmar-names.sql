-- 미얀마어 상품명·공급사명. 여러 번 실행해도 안전.
SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `products` ADD COLUMN `name_my` VARCHAR(255) NULL AFTER `name`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'name_my');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `suppliers` ADD COLUMN `name_my` VARCHAR(190) NULL AFTER `company_name`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'suppliers' AND COLUMN_NAME = 'name_my');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
