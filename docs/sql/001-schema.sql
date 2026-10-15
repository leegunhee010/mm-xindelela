-- ============================================================
-- mm.xindelela.com 스키마 (MySQL 5.6, utf8mb4)
-- app/Database/Migrations/2026-10-15-000001_CreateCoreTables.php 와 동일.
-- 서버에서는 spark migrate 를 쓸 수 없어 scripts/apply-sql.php 가 배포마다 적용한다.
-- 여러 번 실행해도 안전 (IF NOT EXISTS).
-- ============================================================

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role` VARCHAR(20) NOT NULL DEFAULT 'buyer',
  `email` VARCHAR(190) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(50) NULL,
  `viber` VARCHAR(50) NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `company_name` VARCHAR(190) NULL,
  `business_type` VARCHAR(50) NULL,
  `fb_page` VARCHAR(255) NULL,
  `address` VARCHAR(255) NULL,
  `city` VARCHAR(100) NULL,
  `doc_files` TEXT NULL,
  `reject_reason` TEXT NULL,
  `referral_code` VARCHAR(20) NULL,
  `referred_by` INT(11) UNSIGNED NULL,
  `points_balance` INT(11) NOT NULL DEFAULT 0,
  `first_order_at` DATETIME NULL,
  `approved_at` DATETIME NULL,
  `last_login_at` DATETIME NULL,
  `admin_note` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email` (`email`),
  KEY `users_referral_code` (`referral_code`),
  KEY `users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `company_name` VARCHAR(190) NOT NULL,
  `brand` VARCHAR(190) NULL,
  `intro` TEXT NULL,
  `logo` VARCHAR(255) NULL,
  `contact_name` VARCHAR(100) NULL,
  `phone` VARCHAR(50) NULL,
  `address` VARCHAR(255) NULL,
  `biz_no` VARCHAR(50) NULL,
  `commission_rate` DECIMAL(5,2) NOT NULL DEFAULT 0,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `suppliers_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` INT(11) UNSIGNED NULL,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `sort` INT(11) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `categories_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_id` INT(11) UNSIGNED NOT NULL,
  `category_id` INT(11) UNSIGNED NULL,
  `code` VARCHAR(50) NULL,
  `name` VARCHAR(255) NOT NULL,
  `summary` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `thumb` VARCHAR(255) NULL,
  `images` TEXT NULL,
  `price` INT(11) NOT NULL DEFAULT 0,
  `retail_price` INT(11) NULL,
  `tiers` TEXT NULL,
  `moq` INT(11) NOT NULL DEFAULT 1,
  `unit` VARCHAR(20) NOT NULL DEFAULT '개',
  `stock` INT(11) NOT NULL DEFAULT 0,
  `origin` VARCHAR(100) NOT NULL DEFAULT '한국',
  `brand` VARCHAR(100) NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `reject_reason` TEXT NULL,
  `is_best` TINYINT(1) NOT NULL DEFAULT 0,
  `views` INT(11) NOT NULL DEFAULT 0,
  `order_count` INT(11) NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `products_supplier_id` (`supplier_id`),
  KEY `products_category_id` (`category_id`),
  KEY `products_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `carts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `product_id` INT(11) UNSIGNED NOT NULL,
  `qty` INT(11) NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `carts_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_no` VARCHAR(30) NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'requested',
  `payment_method` VARCHAR(20) NOT NULL DEFAULT 'kbzpay',
  `subtotal` INT(11) NOT NULL DEFAULT 0,
  `shipping_fee` INT(11) NOT NULL DEFAULT 0,
  `points_used` INT(11) NOT NULL DEFAULT 0,
  `total` INT(11) NOT NULL DEFAULT 0,
  `ship_name` VARCHAR(100) NULL,
  `ship_phone` VARCHAR(50) NULL,
  `ship_city` VARCHAR(100) NULL,
  `ship_address` VARCHAR(255) NULL,
  `memo` TEXT NULL,
  `admin_memo` TEXT NULL,
  `payment_proof` VARCHAR(255) NULL,
  `payment_note` VARCHAR(255) NULL,
  `tracking` VARCHAR(100) NULL,
  `quoted_at` DATETIME NULL,
  `proof_at` DATETIME NULL,
  `paid_at` DATETIME NULL,
  `shipped_at` DATETIME NULL,
  `delivered_at` DATETIME NULL,
  `cancelled_at` DATETIME NULL,
  `reward_granted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_no` (`order_no`),
  KEY `orders_user_id` (`user_id`),
  KEY `orders_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) UNSIGNED NOT NULL,
  `product_id` INT(11) UNSIGNED NOT NULL,
  `supplier_id` INT(11) UNSIGNED NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `thumb` VARCHAR(255) NULL,
  `unit_price` INT(11) NOT NULL DEFAULT 0,
  `qty` INT(11) NOT NULL DEFAULT 1,
  `line_total` INT(11) NOT NULL DEFAULT 0,
  `item_status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id` (`order_id`),
  KEY `order_items_supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `point_logs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `type` VARCHAR(30) NOT NULL,
  `amount` INT(11) NOT NULL DEFAULT 0,
  `balance_after` INT(11) NOT NULL DEFAULT 0,
  `ref_type` VARCHAR(20) NULL,
  `ref_id` INT(11) UNSIGNED NULL,
  `memo` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `point_logs_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `notices` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `body` TEXT NULL,
  `is_pinned` TINYINT(1) NOT NULL DEFAULT 0,
  `views` INT(11) NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `banners` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NULL,
  `subtitle` VARCHAR(255) NULL,
  `image` VARCHAR(255) NULL,
  `link` VARCHAR(255) NULL,
  `bg` VARCHAR(20) NULL,
  `sort` INT(11) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `skey` VARCHAR(100) NOT NULL,
  `svalue` TEXT NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_skey` (`skey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `product_id` INT(11) UNSIGNED NULL,
  `body` TEXT NOT NULL,
  `answer` TEXT NULL,
  `answered_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `inquiries_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- CI4 migrations 테이블: spark migrate 를 나중에 쓰더라도 중복 실행되지 않도록 기록
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` VARCHAR(255) NOT NULL,
  `class` VARCHAR(255) NOT NULL,
  `group` VARCHAR(255) NOT NULL,
  `namespace` VARCHAR(255) NOT NULL,
  `time` INT(11) NOT NULL,
  `batch` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
SELECT '2026-10-15-000001', 'App\\Database\\Migrations\\CreateCoreTables', 'default', 'App', UNIX_TIMESTAMP(), 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `version` = '2026-10-15-000001');

SELECT COUNT(*) AS tables_ok FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name IN ('users','suppliers','categories','products','carts','orders','order_items','point_logs','notices','banners','settings','inquiries');
