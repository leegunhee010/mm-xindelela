-- 출고 상세 정보(JSON). 여러 번 실행해도 안전.
SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `orders` ADD COLUMN `ship_info` TEXT NULL AFTER `tracking`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'ship_info');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
