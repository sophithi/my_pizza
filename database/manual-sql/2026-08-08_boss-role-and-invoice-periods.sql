-- ============================================================
-- Manual update for LIVE server phpMyAdmin
-- Mirrors these Laravel migrations run locally on 2026-08-08:
--   2026_08_08_000000_add_boss_role_to_users_table
--   2026_08_08_010000_create_invoice_periods_table
--   2026_08_08_010100_add_invoice_period_id_to_invoices_table
--
-- BEFORE RUNNING: export/backup the live database (phpMyAdmin ->
-- Export -> Quick -> SQL). Run this whole script in one go from the
-- SQL tab. Run it once only.
-- ============================================================

-- 1) Add the "Boss" role (same permissions as Manager going forward
--    in the app code) to the users.role enum.
ALTER TABLE `users`
  MODIFY COLUMN `role` ENUM('admin','manager','boss','staff','staff_inventory') NULL DEFAULT 'staff';


-- 2) New table: invoice_periods. A NULL closed_at row is the
--    currently active period; invoice numbering restarts from 1
--    within each period.
CREATE TABLE `invoice_periods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `closed_at` timestamp NULL DEFAULT NULL,
  `closed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_periods_closed_by_foreign` (`closed_by`),
  CONSTRAINT `invoice_periods_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed the initial (currently active) period so existing invoices
-- have something to attach to.
INSERT INTO `invoice_periods` (`created_at`, `updated_at`) VALUES (NOW(), NOW());
SET @initial_period_id = LAST_INSERT_ID();


-- 3) Attach every invoice to a period, and scope the invoice_number
--    uniqueness per period instead of globally (so numbering can
--    restart at INV-000001 after closing a period without colliding
--    with old invoice numbers).
ALTER TABLE `invoices`
  ADD COLUMN `invoice_period_id` bigint(20) unsigned DEFAULT NULL AFTER `invoice_number`;

-- Backfill every existing invoice (including soft-deleted / trashed
-- ones) onto the initial period.
UPDATE `invoices` SET `invoice_period_id` = @initial_period_id;

-- Drop the old global-unique index and replace it with a composite
-- one, adding the FK in the same statement so it reuses that index
-- instead of creating a redundant one.
ALTER TABLE `invoices` DROP INDEX `invoices_invoice_number_unique`;
ALTER TABLE `invoices`
  ADD UNIQUE KEY `invoices_invoice_period_id_invoice_number_unique` (`invoice_period_id`,`invoice_number`),
  ADD CONSTRAINT `invoices_invoice_period_id_foreign` FOREIGN KEY (`invoice_period_id`) REFERENCES `invoice_periods` (`id`) ON DELETE SET NULL;


-- 4) Tell Laravel these 3 migrations already ran, so a future
--    `php artisan migrate` on the live server won't try to redo them.
SET @next_batch = (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);
INSERT INTO `migrations` (`migration`, `batch`) VALUES
  ('2026_08_08_000000_add_boss_role_to_users_table', @next_batch),
  ('2026_08_08_010000_create_invoice_periods_table', @next_batch),
  ('2026_08_08_010100_add_invoice_period_id_to_invoices_table', @next_batch);


-- ============================================================
-- ROLLBACK (only run this if something goes wrong right after
-- applying the script above — undoes everything in reverse order)
-- ============================================================
-- ALTER TABLE `invoices` DROP FOREIGN KEY `invoices_invoice_period_id_foreign`;
-- ALTER TABLE `invoices` DROP INDEX `invoices_invoice_period_id_invoice_number_unique`;
-- ALTER TABLE `invoices` ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`);
-- ALTER TABLE `invoices` DROP COLUMN `invoice_period_id`;
-- DROP TABLE `invoice_periods`;
-- ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin','manager','staff','staff_inventory') NULL DEFAULT 'staff';
-- DELETE FROM `migrations` WHERE `migration` IN (
--   '2026_08_08_000000_add_boss_role_to_users_table',
--   '2026_08_08_010000_create_invoice_periods_table',
--   '2026_08_08_010100_add_invoice_period_id_to_invoices_table'
-- );
