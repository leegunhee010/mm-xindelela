-- 미얀마어 배너·공지·카테고리. 여러 번 실행해도 안전.
SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `banners` ADD COLUMN `title_my` TEXT NULL AFTER `title`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'banners' AND COLUMN_NAME = 'title_my');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `banners` ADD COLUMN `subtitle_my` VARCHAR(255) NULL AFTER `subtitle`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'banners' AND COLUMN_NAME = 'subtitle_my');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `notices` ADD COLUMN `title_my` VARCHAR(255) NULL AFTER `title`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notices' AND COLUMN_NAME = 'title_my');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `notices` ADD COLUMN `body_my` MEDIUMTEXT NULL AFTER `body`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notices' AND COLUMN_NAME = 'body_my');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `categories` ADD COLUMN `name_my` VARCHAR(100) NULL AFTER `name`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'categories' AND COLUMN_NAME = 'name_my');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
