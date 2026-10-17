-- 관리자 담당자 계정 + 작업 기록. 여러 번 실행해도 안전.
SET @s = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `users` ADD COLUMN `admin_level` VARCHAR(20) NULL AFTER `role`',
  'SELECT 1')
  FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'admin_level');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
UPDATE `users` SET `admin_level` = 'owner' WHERE `role` = 'admin' AND (`admin_level` IS NULL OR `admin_level` = '');

CREATE TABLE IF NOT EXISTS `admin_logs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` INT(11) UNSIGNED NOT NULL,
  `admin_name` VARCHAR(100) NULL,
  `action` VARCHAR(60) NOT NULL,
  `target_type` VARCHAR(30) NULL,
  `target_id` INT(11) UNSIGNED NULL,
  `summary` VARCHAR(500) NULL,
  `ip` VARCHAR(45) NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `admin_logs_admin_id` (`admin_id`),
  KEY `admin_logs_target` (`target_type`, `target_id`),
  KEY `admin_logs_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
