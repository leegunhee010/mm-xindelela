-- 줄서기 + 사이트 안 알림. 여러 번 실행해도 안전. (상품 status 'coming' = 입고 예정, 컬럼 변경 없음)
CREATE TABLE IF NOT EXISTS `queues` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `product_id` INT(11) UNSIGNED NOT NULL,
  `qty` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `status` VARCHAR(20) NOT NULL DEFAULT 'waiting',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `queues_user_product` (`user_id`, `product_id`),
  KEY `queues_product_status` (`product_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `type` VARCHAR(30) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `body` VARCHAR(500) NULL,
  `link` VARCHAR(255) NULL,
  `read_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_read` (`user_id`, `read_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
