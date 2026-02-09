-- Remaining migration after base DSA SQL is already executed.
-- Adds:
-- 1) granular DSA permission tables
-- 2) default DSA permission seed
-- 3) backfill of permissions for existing DSA users
-- 4) DSA request/approval workflow table

CREATE TABLE IF NOT EXISTS `dsa_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `perm_key` varchar(80) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_dsa_perm_key` (`perm_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `dsa_user_permissions` (
  `dsa_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`dsa_id`,`permission_id`),
  KEY `idx_dsa_user_permission_perm` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `dsa_permissions` (`perm_key`, `description`)
SELECT 'dsa_dashboard_view', 'View DSA dashboard'
WHERE NOT EXISTS (SELECT 1 FROM dsa_permissions WHERE perm_key = 'dsa_dashboard_view');

INSERT INTO `dsa_permissions` (`perm_key`, `description`)
SELECT 'dsa_profile_manage', 'View and update DSA profile'
WHERE NOT EXISTS (SELECT 1 FROM dsa_permissions WHERE perm_key = 'dsa_profile_manage');

INSERT INTO `dsa_permissions` (`perm_key`, `description`)
SELECT 'dsa_lead_view', 'View own submitted leads'
WHERE NOT EXISTS (SELECT 1 FROM dsa_permissions WHERE perm_key = 'dsa_lead_view');

INSERT INTO `dsa_permissions` (`perm_key`, `description`)
SELECT 'dsa_lead_create', 'Create new lead/application'
WHERE NOT EXISTS (SELECT 1 FROM dsa_permissions WHERE perm_key = 'dsa_lead_create');

INSERT IGNORE INTO `dsa_user_permissions` (`dsa_id`, `permission_id`)
SELECT d.id, p.id
FROM dsa d
JOIN dsa_permissions p;

CREATE TABLE IF NOT EXISTS `dsa_requests` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `firm_name` varchar(255) NOT NULL,
  `pan_number` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pin_code` varchar(10) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `dsa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_dsa_requests_customer` (`customer_id`),
  KEY `idx_dsa_requests_status` (`status`),
  KEY `idx_dsa_requests_reviewed_by` (`reviewed_by`),
  KEY `idx_dsa_requests_dsa` (`dsa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

