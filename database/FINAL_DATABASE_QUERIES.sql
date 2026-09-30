-- =========================================================
-- FINAL DATABASE QUERIES
-- Laravel 9 + MySQL POS System
-- Database Design Lab Submission
-- Current schema inspected from project migrations and models
-- =========================================================

-- =========================================================
-- A. DATABASE STRUCTURE
-- =========================================================

CREATE DATABASE IF NOT EXISTS `pos-2026`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `pos-2026`;

-- -------------------------
-- Authentication and system tables
-- -------------------------

CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(100) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_resets` (
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` VARCHAR(255) NOT NULL,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tokenable_type` VARCHAR(255) NOT NULL,
    `tokenable_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `abilities` TEXT NULL,
    `last_used_at` TIMESTAMP NULL DEFAULT NULL,
    `expires_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
    KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------
-- Master data tables
-- -------------------------

CREATE TABLE IF NOT EXISTS `branches` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `code` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(255) NULL,
    `email` VARCHAR(255) NULL,
    `address` TEXT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `branches_code_unique` (`code`),
    KEY `branches_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `customer_groups` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `customer_groups_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_id` BIGINT UNSIGNED NULL,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `categories_slug_unique` (`slug`),
    KEY `categories_status_index` (`status`),
    KEY `categories_parent_id_index` (`parent_id`),
    CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `brands` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `brands_slug_unique` (`slug`),
    KEY `brands_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `units` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `short_name` VARCHAR(255) NOT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `units_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `brand_id` BIGINT UNSIGNED NULL,
    `unit_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `sku` VARCHAR(255) NOT NULL,
    `barcode` VARCHAR(255) NULL,
    `cost_price` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `selling_price` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `minimum_stock` DECIMAL(15,3) NOT NULL DEFAULT 0,
    `description` TEXT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `products_sku_unique` (`sku`),
    UNIQUE KEY `products_barcode_unique` (`barcode`),
    KEY `products_status_index` (`status`),
    KEY `products_category_id_brand_id_unit_id_index` (`category_id`, `brand_id`, `unit_id`),
    KEY `products_brand_id_foreign` (`brand_id`),
    KEY `products_unit_id_foreign` (`unit_id`),
    CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
    CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `customers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_group_id` BIGINT UNSIGNED NULL,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(255) NULL,
    `email` VARCHAR(255) NULL,
    `address` TEXT NULL,
    `credit_limit` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `opening_due` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `customers_customer_group_id_foreign` (`customer_group_id`),
    KEY `customers_phone_index` (`phone`),
    KEY `customers_email_index` (`email`),
    KEY `customers_status_index` (`status`),
    CONSTRAINT `customers_customer_group_id_foreign` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `suppliers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `company_name` VARCHAR(255) NULL,
    `phone` VARCHAR(255) NULL,
    `email` VARCHAR(255) NULL,
    `address` TEXT NULL,
    `opening_due` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `suppliers_company_name_index` (`company_name`),
    KEY `suppliers_phone_index` (`phone`),
    KEY `suppliers_email_index` (`email`),
    KEY `suppliers_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `warehouses` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `code` VARCHAR(255) NOT NULL,
    `address` TEXT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `warehouses_code_unique` (`code`),
    KEY `warehouses_status_index` (`status`),
    KEY `warehouses_branch_id_index` (`branch_id`),
    CONSTRAINT `warehouses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------
-- Inventory tables
-- -------------------------

CREATE TABLE IF NOT EXISTS `inventories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `warehouse_id` BIGINT UNSIGNED NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `quantity` DECIMAL(15,3) NOT NULL DEFAULT 0,
    `reserved_quantity` DECIMAL(15,3) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `inventories_branch_id_warehouse_id_product_id_unique` (`branch_id`, `warehouse_id`, `product_id`),
    KEY `inventories_branch_id_index` (`branch_id`),
    KEY `inventories_product_id_index` (`product_id`),
    KEY `inventories_warehouse_id_foreign` (`warehouse_id`),
    CONSTRAINT `inventories_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `inventories_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `inventories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_movements` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `warehouse_id` BIGINT UNSIGNED NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `quantity` DECIMAL(15,3) NOT NULL,
    `reference_type` VARCHAR(255) NULL,
    `reference_id` BIGINT UNSIGNED NULL,
    `note` TEXT NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `stock_movements_branch_id_index` (`branch_id`),
    KEY `stock_movements_product_id_index` (`product_id`),
    KEY `stock_movements_reference_type_reference_id_index` (`reference_type`, `reference_id`),
    KEY `stock_movements_warehouse_id_foreign` (`warehouse_id`),
    KEY `stock_movements_created_by_foreign` (`created_by`),
    CONSTRAINT `stock_movements_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `stock_movements_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `stock_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_adjustments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `warehouse_id` BIGINT UNSIGNED NULL,
    `adjustment_no` VARCHAR(255) NOT NULL,
    `type` ENUM('increase','decrease') NOT NULL,
    `reason` VARCHAR(255) NULL,
    `note` TEXT NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `status` ENUM('draft','completed','cancelled') NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `stock_adjustments_adjustment_no_unique` (`adjustment_no`),
    KEY `stock_adjustments_branch_id_index` (`branch_id`),
    KEY `stock_adjustments_status_index` (`status`),
    KEY `stock_adjustments_warehouse_id_foreign` (`warehouse_id`),
    KEY `stock_adjustments_created_by_foreign` (`created_by`),
    CONSTRAINT `stock_adjustments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `stock_adjustments_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `stock_adjustments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_adjustment_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `stock_adjustment_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `quantity` DECIMAL(15,3) NOT NULL,
    `unit_cost` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `stock_adjustment_items_product_id_index` (`product_id`),
    KEY `stock_adjustment_items_stock_adjustment_id_foreign` (`stock_adjustment_id`),
    CONSTRAINT `stock_adjustment_items_stock_adjustment_id_foreign` FOREIGN KEY (`stock_adjustment_id`) REFERENCES `stock_adjustments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `stock_adjustment_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_transfers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `transfer_no` VARCHAR(255) NOT NULL,
    `from_branch_id` BIGINT UNSIGNED NOT NULL,
    `to_branch_id` BIGINT UNSIGNED NOT NULL,
    `from_warehouse_id` BIGINT UNSIGNED NULL,
    `to_warehouse_id` BIGINT UNSIGNED NULL,
    `status` ENUM('draft','pending','completed','cancelled') NOT NULL,
    `transfer_date` DATE NOT NULL,
    `note` TEXT NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `stock_transfers_transfer_no_unique` (`transfer_no`),
    KEY `stock_transfers_from_branch_id_to_branch_id_index` (`from_branch_id`, `to_branch_id`),
    KEY `stock_transfers_status_index` (`status`),
    KEY `stock_transfers_transfer_date_index` (`transfer_date`),
    KEY `stock_transfers_to_branch_id_foreign` (`to_branch_id`),
    KEY `stock_transfers_from_warehouse_id_foreign` (`from_warehouse_id`),
    KEY `stock_transfers_to_warehouse_id_foreign` (`to_warehouse_id`),
    KEY `stock_transfers_created_by_foreign` (`created_by`),
    CONSTRAINT `stock_transfers_from_branch_id_foreign` FOREIGN KEY (`from_branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `stock_transfers_to_branch_id_foreign` FOREIGN KEY (`to_branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `stock_transfers_from_warehouse_id_foreign` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `stock_transfers_to_warehouse_id_foreign` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `stock_transfers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_transfer_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `stock_transfer_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `quantity` DECIMAL(15,3) NOT NULL,
    `unit_cost` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `stock_transfer_items_product_id_index` (`product_id`),
    KEY `stock_transfer_items_stock_transfer_id_foreign` (`stock_transfer_id`),
    CONSTRAINT `stock_transfer_items_stock_transfer_id_foreign` FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `stock_transfer_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------
-- Sales and returns tables
-- -------------------------

CREATE TABLE IF NOT EXISTS `sales` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `warehouse_id` BIGINT UNSIGNED NULL,
    `customer_id` BIGINT UNSIGNED NULL,
    `invoice_no` VARCHAR(255) NOT NULL,
    `sale_date` DATETIME NOT NULL,
    `subtotal` DECIMAL(15,2) NOT NULL,
    `discount` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `tax` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `total` DECIMAL(15,2) NOT NULL,
    `paid_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `due_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `status` ENUM('completed','partial','cancelled') NOT NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `sales_invoice_no_unique` (`invoice_no`),
    KEY `sales_branch_id_index` (`branch_id`),
    KEY `sales_customer_id_index` (`customer_id`),
    KEY `sales_sale_date_index` (`sale_date`),
    KEY `sales_status_index` (`status`),
    KEY `sales_warehouse_id_foreign` (`warehouse_id`),
    KEY `sales_created_by_foreign` (`created_by`),
    CONSTRAINT `sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `sales_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `sales_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sale_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sale_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `quantity` DECIMAL(15,3) NOT NULL,
    `unit_price` DECIMAL(15,2) NOT NULL,
    `unit_cost` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `discount` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `tax` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `total` DECIMAL(15,2) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `sale_items_product_id_index` (`product_id`),
    KEY `sale_items_sale_id_foreign` (`sale_id`),
    CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
    CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sale_id` BIGINT UNSIGNED NOT NULL,
    `payment_method` VARCHAR(255) NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `reference_no` VARCHAR(255) NULL,
    `paid_at` DATETIME NOT NULL,
    `received_by` BIGINT UNSIGNED NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `payments_sale_id_index` (`sale_id`),
    KEY `payments_payment_method_index` (`payment_method`),
    KEY `payments_paid_at_index` (`paid_at`),
    KEY `payments_received_by_foreign` (`received_by`),
    CONSTRAINT `payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `payments_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sale_returns` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sale_id` BIGINT UNSIGNED NOT NULL,
    `return_no` VARCHAR(255) NOT NULL,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `return_date` DATETIME NOT NULL,
    `total_amount` DECIMAL(15,2) NOT NULL,
    `reason` TEXT NULL,
    `status` ENUM('completed','cancelled') NOT NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `sale_returns_return_no_unique` (`return_no`),
    KEY `sale_returns_sale_id_index` (`sale_id`),
    KEY `sale_returns_branch_id_index` (`branch_id`),
    KEY `sale_returns_return_date_index` (`return_date`),
    KEY `sale_returns_status_index` (`status`),
    KEY `sale_returns_created_by_foreign` (`created_by`),
    CONSTRAINT `sale_returns_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `sale_returns_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `sale_returns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sale_return_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sale_return_id` BIGINT UNSIGNED NOT NULL,
    `sale_item_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `quantity` DECIMAL(15,3) NOT NULL,
    `unit_price` DECIMAL(15,2) NOT NULL,
    `total` DECIMAL(15,2) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `sale_return_items_sale_item_id_index` (`sale_item_id`),
    KEY `sale_return_items_product_id_index` (`product_id`),
    KEY `sale_return_items_sale_return_id_foreign` (`sale_return_id`),
    CONSTRAINT `sale_return_items_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE,
    CONSTRAINT `sale_return_items_sale_item_id_foreign` FOREIGN KEY (`sale_item_id`) REFERENCES `sale_items` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `sale_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `refund_payments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sale_return_id` BIGINT UNSIGNED NOT NULL,
    `payment_method` VARCHAR(255) NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `reference_no` VARCHAR(255) NULL,
    `refunded_at` DATETIME NOT NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `refund_payments_sale_return_id_refunded_at_index` (`sale_return_id`, `refunded_at`),
    KEY `refund_payments_created_by_foreign` (`created_by`),
    CONSTRAINT `refund_payments_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE,
    CONSTRAINT `refund_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------
-- Purchase tables
-- -------------------------

CREATE TABLE IF NOT EXISTS `purchases` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `supplier_id` BIGINT UNSIGNED NOT NULL,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `warehouse_id` BIGINT UNSIGNED NULL,
    `purchase_no` VARCHAR(255) NOT NULL,
    `purchase_date` DATE NOT NULL,
    `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `discount` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `tax` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `total` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `paid_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `due_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `status` ENUM('draft','received','cancelled') NOT NULL DEFAULT 'draft',
    `note` TEXT NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `purchases_purchase_no_unique` (`purchase_no`),
    KEY `purchases_supplier_id_branch_id_warehouse_id_index` (`supplier_id`, `branch_id`, `warehouse_id`),
    KEY `purchases_purchase_date_status_index` (`purchase_date`, `status`),
    KEY `purchases_branch_id_foreign` (`branch_id`),
    KEY `purchases_warehouse_id_foreign` (`warehouse_id`),
    KEY `purchases_created_by_foreign` (`created_by`),
    CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `purchases_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `purchases_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `purchases_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `purchase_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `purchase_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `quantity` DECIMAL(15,3) NOT NULL,
    `unit_cost` DECIMAL(15,2) NOT NULL,
    `discount` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `tax` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `total` DECIMAL(15,2) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `purchase_items_product_id_index` (`product_id`),
    KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
    CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
    CONSTRAINT `purchase_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `purchase_payments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `purchase_id` BIGINT UNSIGNED NOT NULL,
    `payment_method` VARCHAR(255) NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `reference_no` VARCHAR(255) NULL,
    `paid_at` DATETIME NOT NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `purchase_payments_purchase_id_paid_at_index` (`purchase_id`, `paid_at`),
    KEY `purchase_payments_created_by_foreign` (`created_by`),
    CONSTRAINT `purchase_payments_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `purchase_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------
-- Accounting tables
-- -------------------------

CREATE TABLE IF NOT EXISTS `expense_categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `expense_categories_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `expenses` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `expense_category_id` BIGINT UNSIGNED NOT NULL,
    `expense_no` VARCHAR(255) NOT NULL,
    `expense_date` DATE NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `payment_method` VARCHAR(255) NOT NULL,
    `reference_no` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `expenses_expense_no_unique` (`expense_no`),
    KEY `expenses_expense_date_branch_id_expense_category_id_index` (`expense_date`, `branch_id`, `expense_category_id`),
    KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
    KEY `expenses_created_by_foreign` (`created_by`),
    CONSTRAINT `expenses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------
-- Roles and permissions tables
-- -------------------------

CREATE TABLE IF NOT EXISTS `roles` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `roles_name_unique` (`name`),
    UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `group_name` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `permissions_slug_unique` (`slug`),
    KEY `permissions_group_name_index` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_user` (
    `role_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    UNIQUE KEY `role_user_role_id_user_id_unique` (`role_id`, `user_id`),
    KEY `role_user_user_id_foreign` (`user_id`),
    CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permission_role` (
    `permission_id` BIGINT UNSIGNED NOT NULL,
    `role_id` BIGINT UNSIGNED NOT NULL,
    UNIQUE KEY `permission_role_permission_id_role_id_unique` (`permission_id`, `role_id`),
    KEY `permission_role_role_id_foreign` (`role_id`),
    CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
    CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- B. BASIC CRUD QUERY EXAMPLES
-- =========================================================

-- Branch CRUD
INSERT INTO branches (name, code, phone, email, address, status, created_at, updated_at)
VALUES ('Dhanmondi Branch', 'DHA', '01711000001', 'dhanmondi@example.com', 'Dhanmondi, Dhaka', 1, NOW(), NOW());
SELECT * FROM branches WHERE code = 'DHA';
UPDATE branches SET phone = '01711000009', updated_at = NOW() WHERE code = 'DHA';
DELETE FROM branches WHERE code = 'OLD';

-- Warehouse CRUD
INSERT INTO warehouses (branch_id, name, code, address, status, created_at, updated_at)
VALUES (1, 'Main Warehouse', 'WH-MAIN', 'Back store', 1, NOW(), NOW());
SELECT w.*, b.name AS branch_name FROM warehouses w INNER JOIN branches b ON b.id = w.branch_id;
UPDATE warehouses SET status = 0, updated_at = NOW() WHERE code = 'WH-MAIN';
DELETE FROM warehouses WHERE code = 'WH-OLD';

-- Category, brand, unit CRUD
INSERT INTO categories (parent_id, name, slug, status, created_at, updated_at) VALUES (NULL, 'Snacks', 'snacks', 1, NOW(), NOW());
SELECT * FROM categories WHERE status = 1 ORDER BY name;
UPDATE categories SET name = 'Snack Items', updated_at = NOW() WHERE slug = 'snacks';
DELETE FROM categories WHERE slug = 'old-category';
INSERT INTO brands (name, slug, status, created_at, updated_at) VALUES ('PRAN', 'pran', 1, NOW(), NOW());
SELECT * FROM brands WHERE status = 1;
UPDATE brands SET status = 0, updated_at = NOW() WHERE slug = 'old-brand';
DELETE FROM brands WHERE slug = 'unused-brand';
INSERT INTO units (name, short_name, status, created_at, updated_at) VALUES ('Piece', 'pcs', 1, NOW(), NOW());
SELECT * FROM units ORDER BY name;
UPDATE units SET short_name = 'pc', updated_at = NOW() WHERE short_name = 'pcs';
DELETE FROM units WHERE short_name = 'old';

-- Product CRUD
INSERT INTO products (category_id, brand_id, unit_id, name, sku, barcode, cost_price, selling_price, minimum_stock, description, status, created_at, updated_at)
VALUES (1, 1, 1, 'PRAN Chanachur 300gm', 'SKU-001', '880000001', 55.00, 70.00, 10.000, 'Demo product', 1, NOW(), NOW());
SELECT p.*, c.name AS category_name, b.name AS brand_name, u.short_name FROM products p INNER JOIN categories c ON c.id = p.category_id LEFT JOIN brands b ON b.id = p.brand_id INNER JOIN units u ON u.id = p.unit_id;
UPDATE products SET selling_price = 75.00, updated_at = NOW() WHERE sku = 'SKU-001';
DELETE FROM products WHERE sku = 'SKU-OLD';

-- Customer and supplier CRUD
INSERT INTO customer_groups (name, description, status, created_at, updated_at) VALUES ('Regular', 'Regular customers', 1, NOW(), NOW());
INSERT INTO customers (customer_group_id, name, phone, email, address, credit_limit, opening_due, status, created_at, updated_at)
VALUES (1, 'Rahim Ahmed', '01733000001', 'rahim@example.com', 'Dhaka', 5000.00, 0.00, 1, NOW(), NOW());
SELECT c.*, cg.name AS group_name FROM customers c LEFT JOIN customer_groups cg ON cg.id = c.customer_group_id;
UPDATE customers SET opening_due = 250.00, updated_at = NOW() WHERE phone = '01733000001';
DELETE FROM customers WHERE email = 'old.customer@example.com';
INSERT INTO suppliers (name, company_name, phone, email, address, opening_due, status, created_at, updated_at)
VALUES ('Dhaka Consumer Distributors', 'Dhaka Consumer Distributors', '01822000001', 'supplier@example.com', 'Dhaka', 0.00, 1, NOW(), NOW());
SELECT * FROM suppliers WHERE status = 1;
UPDATE suppliers SET opening_due = 1200.00, updated_at = NOW() WHERE email = 'supplier@example.com';
DELETE FROM suppliers WHERE email = 'old.supplier@example.com';

-- Inventory CRUD
INSERT INTO inventories (branch_id, warehouse_id, product_id, quantity, reserved_quantity, created_at, updated_at)
VALUES (1, 1, 1, 100.000, 0.000, NOW(), NOW());
SELECT * FROM inventories WHERE branch_id = 1 AND product_id = 1;
UPDATE inventories SET quantity = quantity + 10, updated_at = NOW() WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1;
DELETE FROM inventories WHERE quantity = 0 AND reserved_quantity = 0;

-- Purchase CRUD
INSERT INTO purchases (supplier_id, branch_id, warehouse_id, purchase_no, purchase_date, subtotal, discount, tax, total, paid_amount, due_amount, status, note, created_by, created_at, updated_at)
VALUES (1, 1, 1, 'PUR-20260927-0001', CURDATE(), 1000.00, 0.00, 0.00, 1000.00, 0.00, 1000.00, 'draft', 'Demo purchase', 1, NOW(), NOW());
INSERT INTO purchase_items (purchase_id, product_id, quantity, unit_cost, discount, tax, total, created_at, updated_at)
VALUES (1, 1, 10.000, 55.00, 0.00, 0.00, 550.00, NOW(), NOW());
INSERT INTO purchase_payments (purchase_id, payment_method, amount, reference_no, paid_at, created_by, created_at, updated_at)
VALUES (1, 'cash', 500.00, 'PP-001', NOW(), 1, NOW(), NOW());
SELECT p.*, s.name AS supplier_name FROM purchases p INNER JOIN suppliers s ON s.id = p.supplier_id ORDER BY p.purchase_date DESC;
UPDATE purchases SET status = 'received', updated_at = NOW() WHERE purchase_no = 'PUR-20260927-0001';
DELETE FROM purchases WHERE status = 'draft' AND purchase_no = 'PUR-OLD';

-- Sales and payment CRUD
INSERT INTO sales (branch_id, warehouse_id, customer_id, invoice_no, sale_date, subtotal, discount, tax, total, paid_amount, due_amount, status, created_by, created_at, updated_at)
VALUES (1, 1, 1, 'SAL-20260927-0001', NOW(), 500.00, 0.00, 0.00, 500.00, 500.00, 0.00, 'completed', 1, NOW(), NOW());
INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, unit_cost, discount, tax, total, created_at, updated_at)
VALUES (1, 1, 2.000, 70.00, 55.00, 0.00, 0.00, 140.00, NOW(), NOW());
INSERT INTO payments (sale_id, payment_method, amount, reference_no, paid_at, received_by, note, created_at, updated_at)
VALUES (1, 'cash', 500.00, NULL, NOW(), 1, NULL, NOW(), NOW());
SELECT s.*, c.name AS customer_name FROM sales s LEFT JOIN customers c ON c.id = s.customer_id ORDER BY s.sale_date DESC;
UPDATE sales SET status = 'partial', due_amount = 100.00, updated_at = NOW() WHERE invoice_no = 'SAL-20260927-0001';
DELETE FROM sales WHERE status = 'cancelled' AND invoice_no = 'SAL-OLD';

-- Sale return and refund CRUD
INSERT INTO sale_returns (sale_id, return_no, branch_id, return_date, total_amount, reason, status, created_by, created_at, updated_at)
VALUES (1, 'RET-20260927-0001', 1, NOW(), 70.00, 'Damaged item', 'completed', 1, NOW(), NOW());
INSERT INTO sale_return_items (sale_return_id, sale_item_id, product_id, quantity, unit_price, total, created_at, updated_at)
VALUES (1, 1, 1, 1.000, 70.00, 70.00, NOW(), NOW());
INSERT INTO refund_payments (sale_return_id, payment_method, amount, reference_no, refunded_at, created_by, created_at, updated_at)
VALUES (1, 'cash', 70.00, NULL, NOW(), 1, NOW(), NOW());
SELECT sr.*, s.invoice_no FROM sale_returns sr INNER JOIN sales s ON s.id = sr.sale_id;
UPDATE sale_returns SET status = 'cancelled', updated_at = NOW() WHERE return_no = 'RET-OLD';
DELETE FROM sale_returns WHERE status = 'cancelled' AND return_no = 'RET-OLD';

-- Stock movement, adjustment and transfer CRUD
INSERT INTO stock_movements (branch_id, warehouse_id, product_id, type, quantity, reference_type, reference_id, note, created_by, created_at, updated_at)
VALUES (1, 1, 1, 'adjustment_in', 5.000, 'App\\Models\\StockAdjustment', 1, 'Manual adjustment', 1, NOW(), NOW());
INSERT INTO stock_adjustments (branch_id, warehouse_id, adjustment_no, type, reason, note, created_by, status, created_at, updated_at)
VALUES (1, 1, 'ADJ-20260927-0001', 'increase', 'Counting difference', NULL, 1, 'completed', NOW(), NOW());
INSERT INTO stock_adjustment_items (stock_adjustment_id, product_id, quantity, unit_cost, created_at, updated_at)
VALUES (1, 1, 5.000, 55.00, NOW(), NOW());
INSERT INTO stock_transfers (transfer_no, from_branch_id, to_branch_id, from_warehouse_id, to_warehouse_id, status, transfer_date, note, created_by, created_at, updated_at)
VALUES ('TRF-20260927-0001', 1, 2, 1, 2, 'completed', CURDATE(), 'Transfer stock', 1, NOW(), NOW());
INSERT INTO stock_transfer_items (stock_transfer_id, product_id, quantity, unit_cost, created_at, updated_at)
VALUES (1, 1, 3.000, 55.00, NOW(), NOW());
SELECT * FROM stock_movements ORDER BY created_at DESC;
UPDATE stock_adjustments SET status = 'cancelled', updated_at = NOW() WHERE status = 'draft';
DELETE FROM stock_transfer_items WHERE stock_transfer_id = 999;

-- Expense CRUD
INSERT INTO expense_categories (name, status, created_at, updated_at) VALUES ('Shop Rent', 1, NOW(), NOW());
INSERT INTO expenses (branch_id, expense_category_id, expense_no, expense_date, amount, payment_method, reference_no, description, created_by, created_at, updated_at)
VALUES (1, 1, 'EXP-20260927-0001', CURDATE(), 25000.00, 'bank', 'BNK-001', 'Monthly rent', 1, NOW(), NOW());
SELECT e.*, ec.name AS category_name FROM expenses e INNER JOIN expense_categories ec ON ec.id = e.expense_category_id;
UPDATE expenses SET amount = 26000.00, updated_at = NOW() WHERE expense_no = 'EXP-20260927-0001';
DELETE FROM expenses WHERE expense_no = 'EXP-OLD';

-- User, role and permission CRUD
INSERT INTO roles (name, slug, description, created_at, updated_at) VALUES ('Manager', 'manager', 'Operational manager', NOW(), NOW());
INSERT INTO permissions (name, slug, group_name, created_at, updated_at) VALUES ('Sales View', 'sales.view', 'Sales', NOW(), NOW());
INSERT INTO role_user (role_id, user_id) VALUES (1, 1);
INSERT INTO permission_role (permission_id, role_id) VALUES (1, 1);
SELECT u.name, r.name AS role_name FROM users u INNER JOIN role_user ru ON ru.user_id = u.id INNER JOIN roles r ON r.id = ru.role_id;
UPDATE roles SET description = 'Updated role description', updated_at = NOW() WHERE slug = 'manager';
DELETE FROM permission_role WHERE permission_id = 1 AND role_id = 1;

-- System table examples
SELECT * FROM failed_jobs ORDER BY failed_at DESC;
SELECT * FROM personal_access_tokens WHERE tokenable_type = 'App\\Models\\User';
DELETE FROM password_resets WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 DAY);

-- =========================================================
-- C. BUSINESS QUERIES
-- =========================================================

-- Product search by SKU, barcode or name
SELECT id, name, sku, barcode, selling_price
FROM products
WHERE status = 1
  AND (sku = 'BD-RICE-001' OR barcode = '880100000001' OR name LIKE '%Rice%')
ORDER BY name;

-- Current stock by branch and warehouse
SELECT b.name AS branch, w.name AS warehouse, p.name AS product, p.sku,
       i.quantity, i.reserved_quantity, (i.quantity - i.reserved_quantity) AS available_quantity
FROM inventories i
INNER JOIN products p ON p.id = i.product_id
INNER JOIN branches b ON b.id = i.branch_id
LEFT JOIN warehouses w ON w.id = i.warehouse_id
ORDER BY b.name, w.name, p.name;

-- Low stock
SELECT p.name, p.sku, b.name AS branch, w.name AS warehouse, i.quantity, p.minimum_stock
FROM inventories i
INNER JOIN products p ON p.id = i.product_id
INNER JOIN branches b ON b.id = i.branch_id
LEFT JOIN warehouses w ON w.id = i.warehouse_id
WHERE i.quantity > 0 AND i.quantity <= p.minimum_stock;

-- Out of stock
SELECT p.name, p.sku, b.name AS branch, w.name AS warehouse, i.quantity
FROM inventories i
INNER JOIN products p ON p.id = i.product_id
INNER JOIN branches b ON b.id = i.branch_id
LEFT JOIN warehouses w ON w.id = i.warehouse_id
WHERE i.quantity <= 0;

-- Inventory value
SELECT b.name AS branch, SUM(i.quantity * p.cost_price) AS inventory_cost_value,
       SUM(i.quantity * p.selling_price) AS inventory_selling_value
FROM inventories i
INNER JOIN products p ON p.id = i.product_id
INNER JOIN branches b ON b.id = i.branch_id
GROUP BY b.id, b.name;

-- Purchase history and supplier due
SELECT p.purchase_no, p.purchase_date, s.name AS supplier, b.name AS branch, p.total, p.paid_amount, p.due_amount, p.status
FROM purchases p
INNER JOIN suppliers s ON s.id = p.supplier_id
INNER JOIN branches b ON b.id = p.branch_id
ORDER BY p.purchase_date DESC;

SELECT s.id, s.name, s.opening_due + COALESCE(SUM(p.due_amount), 0) AS total_supplier_due
FROM suppliers s
LEFT JOIN purchases p ON p.supplier_id = s.id AND p.status <> 'cancelled'
GROUP BY s.id, s.name, s.opening_due
HAVING total_supplier_due > 0;

-- Customer due
SELECT c.id, c.name, c.opening_due + COALESCE(SUM(s.due_amount), 0) AS total_customer_due
FROM customers c
LEFT JOIN sales s ON s.customer_id = c.id AND s.status <> 'cancelled'
GROUP BY c.id, c.name, c.opening_due
HAVING total_customer_due > 0;

-- Sales history and details
SELECT s.invoice_no, s.sale_date, COALESCE(c.name, 'Walk-in Customer') AS customer, b.name AS branch,
       s.total, s.paid_amount, s.due_amount, s.status, u.name AS cashier
FROM sales s
LEFT JOIN customers c ON c.id = s.customer_id
INNER JOIN branches b ON b.id = s.branch_id
INNER JOIN users u ON u.id = s.created_by
ORDER BY s.sale_date DESC;

SELECT s.invoice_no, p.name AS product, si.quantity, si.unit_price, si.discount, si.tax, si.total
FROM sale_items si
INNER JOIN sales s ON s.id = si.sale_id
INNER JOIN products p ON p.id = si.product_id
WHERE s.invoice_no = 'SAL-20260927-0001';

-- Payment history
SELECT pay.payment_method, pay.amount, pay.paid_at, s.invoice_no, u.name AS received_by
FROM payments pay
INNER JOIN sales s ON s.id = pay.sale_id
LEFT JOIN users u ON u.id = pay.received_by
ORDER BY pay.paid_at DESC;

-- Sale returns
SELECT sr.return_no, sr.return_date, s.invoice_no, sr.total_amount, sr.status
FROM sale_returns sr
INNER JOIN sales s ON s.id = sr.sale_id
ORDER BY sr.return_date DESC;

-- Stock adjustments
SELECT sa.adjustment_no, sa.type, sa.reason, sa.status, b.name AS branch, SUM(sai.quantity) AS total_quantity
FROM stock_adjustments sa
INNER JOIN stock_adjustment_items sai ON sai.stock_adjustment_id = sa.id
INNER JOIN branches b ON b.id = sa.branch_id
GROUP BY sa.id, sa.adjustment_no, sa.type, sa.reason, sa.status, b.name
ORDER BY sa.created_at DESC;

-- Stock transfers
SELECT st.transfer_no, fb.name AS from_branch, tb.name AS to_branch, st.status, st.transfer_date
FROM stock_transfers st
INNER JOIN branches fb ON fb.id = st.from_branch_id
INNER JOIN branches tb ON tb.id = st.to_branch_id
ORDER BY st.transfer_date DESC;

-- Expenses
SELECT e.expense_no, e.expense_date, ec.name AS category, b.name AS branch, e.amount, e.payment_method
FROM expenses e
INNER JOIN expense_categories ec ON ec.id = e.expense_category_id
INNER JOIN branches b ON b.id = e.branch_id
ORDER BY e.expense_date DESC;

-- =========================================================
-- D. SQL CONCEPT QUERIES
-- =========================================================

-- INNER JOIN
SELECT p.name, c.name AS category FROM products p INNER JOIN categories c ON c.id = p.category_id;

-- LEFT JOIN
SELECT p.name, b.name AS brand FROM products p LEFT JOIN brands b ON b.id = p.brand_id;

-- Multiple JOIN
SELECT s.invoice_no, c.name AS customer, b.name AS branch, u.name AS cashier
FROM sales s
LEFT JOIN customers c ON c.id = s.customer_id
INNER JOIN branches b ON b.id = s.branch_id
INNER JOIN users u ON u.id = s.created_by;

-- GROUP BY, SUM, COUNT and ORDER BY
SELECT b.name AS branch, COUNT(s.id) AS sale_count, SUM(s.total) AS total_sales
FROM sales s
INNER JOIN branches b ON b.id = s.branch_id
GROUP BY b.id, b.name
ORDER BY total_sales DESC;

-- HAVING
SELECT customer_id, SUM(due_amount) AS due_total
FROM sales
WHERE customer_id IS NOT NULL
GROUP BY customer_id
HAVING due_total > 0;

-- LIKE
SELECT * FROM products WHERE name LIKE '%Oil%';

-- BETWEEN
SELECT * FROM sales WHERE DATE(sale_date) BETWEEN '2026-09-01' AND '2026-09-30';

-- IN
SELECT * FROM payments WHERE payment_method IN ('cash', 'bkash', 'nagad');

-- AVG, MIN, MAX
SELECT AVG(selling_price) AS average_price, MIN(selling_price) AS minimum_price, MAX(selling_price) AS maximum_price
FROM products;

-- CASE
SELECT p.name, i.quantity, p.minimum_stock,
       CASE
           WHEN i.quantity <= 0 THEN 'Out of Stock'
           WHEN i.quantity <= p.minimum_stock THEN 'Low Stock'
           ELSE 'In Stock'
       END AS stock_status
FROM inventories i
INNER JOIN products p ON p.id = i.product_id;

-- Subquery
SELECT *
FROM products
WHERE id IN (
    SELECT product_id FROM sale_items GROUP BY product_id HAVING SUM(quantity) > 10
);

-- EXISTS
SELECT p.*
FROM products p
WHERE EXISTS (
    SELECT 1 FROM inventories i WHERE i.product_id = p.id AND i.quantity > 0
);

-- =========================================================
-- E. REPORT QUERIES
-- =========================================================

-- Profit/Loss Report
SELECT
    COALESCE((SELECT SUM(total) FROM sales WHERE status <> 'cancelled' AND DATE(sale_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0) AS gross_sales,
    COALESCE((SELECT SUM(total_amount) FROM sale_returns WHERE status = 'completed' AND DATE(return_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0) AS returns,
    COALESCE((SELECT SUM(total) FROM sales WHERE status <> 'cancelled' AND DATE(sale_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0)
      - COALESCE((SELECT SUM(total_amount) FROM sale_returns WHERE status = 'completed' AND DATE(return_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0) AS net_sales,
    COALESCE((SELECT SUM(si.quantity * si.unit_cost) FROM sale_items si INNER JOIN sales s ON s.id = si.sale_id WHERE s.status <> 'cancelled' AND DATE(s.sale_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0) AS cogs,
    (COALESCE((SELECT SUM(total) FROM sales WHERE status <> 'cancelled' AND DATE(sale_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0)
      - COALESCE((SELECT SUM(total_amount) FROM sale_returns WHERE status = 'completed' AND DATE(return_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0)
      - COALESCE((SELECT SUM(si.quantity * si.unit_cost) FROM sale_items si INNER JOIN sales s ON s.id = si.sale_id WHERE s.status <> 'cancelled' AND DATE(s.sale_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0)) AS gross_profit,
    COALESCE((SELECT SUM(amount) FROM expenses WHERE expense_date BETWEEN '2026-09-01' AND '2026-09-30'), 0) AS expenses,
    (COALESCE((SELECT SUM(total) FROM sales WHERE status <> 'cancelled' AND DATE(sale_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0)
      - COALESCE((SELECT SUM(total_amount) FROM sale_returns WHERE status = 'completed' AND DATE(return_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0)
      - COALESCE((SELECT SUM(si.quantity * si.unit_cost) FROM sale_items si INNER JOIN sales s ON s.id = si.sale_id WHERE s.status <> 'cancelled' AND DATE(s.sale_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0)
      - COALESCE((SELECT SUM(amount) FROM expenses WHERE expense_date BETWEEN '2026-09-01' AND '2026-09-30'), 0)) AS net_profit;

-- Purchase & Sale Report
SELECT report_date, SUM(purchase_total) AS purchase_total, SUM(sale_total) AS sale_total
FROM (
    SELECT purchase_date AS report_date, SUM(total) AS purchase_total, 0 AS sale_total FROM purchases WHERE status <> 'cancelled' GROUP BY purchase_date
    UNION ALL
    SELECT DATE(sale_date) AS report_date, 0 AS purchase_total, SUM(total) AS sale_total FROM sales WHERE status <> 'cancelled' GROUP BY DATE(sale_date)
) x
GROUP BY report_date
ORDER BY report_date;

-- Tax Report
SELECT 'sales' AS source, SUM(tax) AS tax_amount FROM sales WHERE status <> 'cancelled'
UNION ALL
SELECT 'purchases' AS source, SUM(tax) AS tax_amount FROM purchases WHERE status <> 'cancelled';

-- Supplier & Customer Report
SELECT 'supplier' AS type, name, phone, email, opening_due FROM suppliers
UNION ALL
SELECT 'customer' AS type, name, phone, email, opening_due FROM customers;

-- Customer Group Report
SELECT cg.name AS group_name, COUNT(c.id) AS customer_count, SUM(c.opening_due) AS opening_due
FROM customer_groups cg
LEFT JOIN customers c ON c.customer_group_id = cg.id
GROUP BY cg.id, cg.name;

-- Stock Report
SELECT b.name AS branch, w.name AS warehouse, p.name AS product, i.quantity, i.reserved_quantity, p.minimum_stock
FROM inventories i
INNER JOIN branches b ON b.id = i.branch_id
LEFT JOIN warehouses w ON w.id = i.warehouse_id
INNER JOIN products p ON p.id = i.product_id;

-- Stock Adjustment Report
SELECT sa.adjustment_no, sa.type, sa.reason, sa.status, p.name AS product, sai.quantity, sai.unit_cost
FROM stock_adjustments sa
INNER JOIN stock_adjustment_items sai ON sai.stock_adjustment_id = sa.id
INNER JOIN products p ON p.id = sai.product_id;

-- Trending Products
SELECT p.id, p.name, p.sku, SUM(si.quantity) AS sold_quantity, SUM(si.total) AS sales_amount
FROM sale_items si
INNER JOIN sales s ON s.id = si.sale_id
INNER JOIN products p ON p.id = si.product_id
WHERE s.status <> 'cancelled'
GROUP BY p.id, p.name, p.sku
ORDER BY sold_quantity DESC;

-- Items Report
SELECT p.name, p.sku, c.name AS category, b.name AS brand, u.short_name AS unit, p.cost_price, p.selling_price
FROM products p
INNER JOIN categories c ON c.id = p.category_id
LEFT JOIN brands b ON b.id = p.brand_id
INNER JOIN units u ON u.id = p.unit_id;

-- Product Purchase Report
SELECT p.name, SUM(pi.quantity) AS purchased_quantity, SUM(pi.total) AS purchase_total
FROM purchase_items pi
INNER JOIN purchases pur ON pur.id = pi.purchase_id
INNER JOIN products p ON p.id = pi.product_id
WHERE pur.status <> 'cancelled'
GROUP BY p.id, p.name;

-- Product Sell Report
SELECT p.name, SUM(si.quantity) AS sold_quantity, SUM(si.total) AS sell_total
FROM sale_items si
INNER JOIN sales s ON s.id = si.sale_id
INNER JOIN products p ON p.id = si.product_id
WHERE s.status <> 'cancelled'
GROUP BY p.id, p.name;

-- Purchase Payment Report
SELECT pp.payment_method, SUM(pp.amount) AS amount, COUNT(*) AS payment_count
FROM purchase_payments pp
GROUP BY pp.payment_method;

-- Sell Payment Report
SELECT p.payment_method, SUM(p.amount) AS amount, COUNT(*) AS payment_count
FROM payments p
GROUP BY p.payment_method;

-- Expense Report
SELECT ec.name AS category, SUM(e.amount) AS total_amount, COUNT(e.id) AS expense_count
FROM expenses e
INNER JOIN expense_categories ec ON ec.id = e.expense_category_id
GROUP BY ec.id, ec.name;

-- Register Report
SELECT DATE(paid_at) AS register_date, payment_method, SUM(amount) AS total_received
FROM payments
GROUP BY DATE(paid_at), payment_method
ORDER BY register_date DESC;

-- Sales Representative Report
SELECT u.name AS representative, COUNT(s.id) AS sale_count, SUM(s.total) AS sales_total
FROM users u
LEFT JOIN sales s ON s.created_by = u.id AND s.status <> 'cancelled'
GROUP BY u.id, u.name
ORDER BY sales_total DESC;

-- Activity Log style report from business movements
SELECT created_at AS activity_time, 'stock_movement' AS activity_type, type AS description, reference_type, reference_id
FROM stock_movements
UNION ALL
SELECT created_at AS activity_time, 'sale' AS activity_type, invoice_no AS description, 'App\\Models\\Sale' AS reference_type, id AS reference_id
FROM sales
UNION ALL
SELECT created_at AS activity_time, 'purchase' AS activity_type, purchase_no AS description, 'App\\Models\\Purchase' AS reference_type, id AS reference_id
FROM purchases
ORDER BY activity_time DESC;

-- =========================================================
-- F. TRANSACTION EXAMPLES
-- =========================================================

-- Completing a sale. If any statement fails, execute ROLLBACK instead of COMMIT.
START TRANSACTION;
SELECT quantity, reserved_quantity
FROM inventories
WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1
FOR UPDATE;
INSERT INTO sales (branch_id, warehouse_id, customer_id, invoice_no, sale_date, subtotal, discount, tax, total, paid_amount, due_amount, status, created_by, created_at, updated_at)
VALUES (1, 1, 1, 'SAL-TRX-0001', NOW(), 140.00, 0.00, 0.00, 140.00, 140.00, 0.00, 'completed', 1, NOW(), NOW());
SET @sale_id = LAST_INSERT_ID();
INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, unit_cost, discount, tax, total, created_at, updated_at)
VALUES (@sale_id, 1, 2.000, 70.00, 55.00, 0.00, 0.00, 140.00, NOW(), NOW());
UPDATE inventories SET quantity = quantity - 2.000, updated_at = NOW()
WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1 AND (quantity - reserved_quantity) >= 2.000;
INSERT INTO stock_movements (branch_id, warehouse_id, product_id, type, quantity, reference_type, reference_id, note, created_by, created_at, updated_at)
VALUES (1, 1, 1, 'sale', -2.000, 'App\\Models\\Sale', @sale_id, NULL, 1, NOW(), NOW());
INSERT INTO payments (sale_id, payment_method, amount, reference_no, paid_at, received_by, note, created_at, updated_at)
VALUES (@sale_id, 'cash', 140.00, NULL, NOW(), 1, NULL, NOW(), NOW());
COMMIT;

-- Receiving a purchase. If stock update fails, execute ROLLBACK instead of COMMIT.
START TRANSACTION;
INSERT INTO purchases (supplier_id, branch_id, warehouse_id, purchase_no, purchase_date, subtotal, discount, tax, total, paid_amount, due_amount, status, note, created_by, created_at, updated_at)
VALUES (1, 1, 1, 'PUR-TRX-0001', CURDATE(), 550.00, 0.00, 0.00, 550.00, 550.00, 0.00, 'received', 'Received purchase', 1, NOW(), NOW());
SET @purchase_id = LAST_INSERT_ID();
INSERT INTO purchase_items (purchase_id, product_id, quantity, unit_cost, discount, tax, total, created_at, updated_at)
VALUES (@purchase_id, 1, 10.000, 55.00, 0.00, 0.00, 550.00, NOW(), NOW());
UPDATE inventories SET quantity = quantity + 10.000, updated_at = NOW()
WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1;
INSERT INTO stock_movements (branch_id, warehouse_id, product_id, type, quantity, reference_type, reference_id, note, created_by, created_at, updated_at)
VALUES (1, 1, 1, 'purchase', 10.000, 'App\\Models\\Purchase', @purchase_id, 'Received purchase', 1, NOW(), NOW());
INSERT INTO purchase_payments (purchase_id, payment_method, amount, reference_no, paid_at, created_by, created_at, updated_at)
VALUES (@purchase_id, 'cash', 550.00, NULL, NOW(), 1, NOW(), NOW());
COMMIT;

-- Stock transfer. If source stock is not enough, execute ROLLBACK instead of COMMIT.
START TRANSACTION;
SELECT quantity, reserved_quantity
FROM inventories
WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1
FOR UPDATE;
INSERT INTO stock_transfers (transfer_no, from_branch_id, to_branch_id, from_warehouse_id, to_warehouse_id, status, transfer_date, note, created_by, created_at, updated_at)
VALUES ('TRF-TRX-0001', 1, 2, 1, 2, 'completed', CURDATE(), 'Branch transfer', 1, NOW(), NOW());
SET @transfer_id = LAST_INSERT_ID();
INSERT INTO stock_transfer_items (stock_transfer_id, product_id, quantity, unit_cost, created_at, updated_at)
VALUES (@transfer_id, 1, 5.000, 55.00, NOW(), NOW());
UPDATE inventories SET quantity = quantity - 5.000, updated_at = NOW()
WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1 AND (quantity - reserved_quantity) >= 5.000;
INSERT INTO inventories (branch_id, warehouse_id, product_id, quantity, reserved_quantity, created_at, updated_at)
VALUES (2, 2, 1, 5.000, 0.000, NOW(), NOW())
ON DUPLICATE KEY UPDATE quantity = quantity + 5.000, updated_at = NOW();
INSERT INTO stock_movements (branch_id, warehouse_id, product_id, type, quantity, reference_type, reference_id, note, created_by, created_at, updated_at)
VALUES (1, 1, 1, 'transfer_out', 5.000, 'App\\Models\\StockTransfer', @transfer_id, 'Branch transfer', 1, NOW(), NOW()),
       (2, 2, 1, 'transfer_in', 5.000, 'App\\Models\\StockTransfer', @transfer_id, 'Branch transfer', 1, NOW(), NOW());
COMMIT;

-- Sale return. If return quantity exceeds sold quantity, execute ROLLBACK instead of COMMIT.
START TRANSACTION;
SELECT si.id, si.sale_id, si.product_id, si.quantity,
       COALESCE(SUM(sri.quantity), 0) AS already_returned
FROM sale_items si
LEFT JOIN sale_return_items sri ON sri.sale_item_id = si.id
WHERE si.id = 1
GROUP BY si.id, si.sale_id, si.product_id, si.quantity
FOR UPDATE;
INSERT INTO sale_returns (sale_id, return_no, branch_id, return_date, total_amount, reason, status, created_by, created_at, updated_at)
VALUES (1, 'RET-TRX-0001', 1, NOW(), 70.00, 'Customer return', 'completed', 1, NOW(), NOW());
SET @return_id = LAST_INSERT_ID();
INSERT INTO sale_return_items (sale_return_id, sale_item_id, product_id, quantity, unit_price, total, created_at, updated_at)
VALUES (@return_id, 1, 1, 1.000, 70.00, 70.00, NOW(), NOW());
UPDATE inventories SET quantity = quantity + 1.000, updated_at = NOW()
WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1;
INSERT INTO stock_movements (branch_id, warehouse_id, product_id, type, quantity, reference_type, reference_id, note, created_by, created_at, updated_at)
VALUES (1, 1, 1, 'sale_return', 1.000, 'App\\Models\\SaleReturn', @return_id, 'Customer return', 1, NOW(), NOW());
INSERT INTO refund_payments (sale_return_id, payment_method, amount, reference_no, refunded_at, created_by, created_at, updated_at)
VALUES (@return_id, 'cash', 70.00, NULL, NOW(), 1, NOW(), NOW());
COMMIT;

-- Use ROLLBACK instead of COMMIT whenever validation fails, stock becomes negative, or a foreign key reference is invalid.
