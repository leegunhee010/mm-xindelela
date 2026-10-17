-- 상품 상세 이미지(JSON 경로 목록). 여러 번 실행해도 안전.
SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `products` ADD COLUMN `detail_images` TEXT NULL AFTER `description`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'detail_images');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
