-- 주문 배송지 좌표 (구글맵 주소 검색으로 고른 위치). 여러 번 실행해도 안전.
-- app/Database/Migrations/2026-10-17-000001_AddOrderGeo.php 와 동일.
SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `orders` ADD COLUMN `ship_lat` DECIMAL(10,7) NULL AFTER `ship_address`, ADD COLUMN `ship_lng` DECIMAL(10,7) NULL AFTER `ship_lat`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'ship_lat');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
