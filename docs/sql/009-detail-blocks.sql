-- 상품 상세 블록(JSON). 여러 번 실행해도 안전.
SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `products` ADD COLUMN `detail` MEDIUMTEXT NULL AFTER `description`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'detail');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
