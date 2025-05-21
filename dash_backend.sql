-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 21, 2025 at 11:24 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dash_backend`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `area_id` int NOT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zip_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `street` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `building_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apartment_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `area_id`, `state`, `zip_code`, `street`, `building_number`, `apartment_number`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 5, 'مصر', '32409', 'Hintz Place', '56', '31', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(2, 2, 4, 'مصر', '68080', 'Nedra Square', '13', '36', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(3, 3, 1, 'مصر', '23040', 'Kuphal Cliffs', '89', '26', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(4, 4, 5, 'مصر', '20699', 'Savannah Summit', '65', '46', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(5, 5, 5, 'مصر', '97475', 'Jakob Forest', '82', '16', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

CREATE TABLE `areas` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `areas`
--

INSERT INTO `areas` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'القاهرة', '2025-04-21 18:50:44', '2025-04-21 18:50:44', NULL),
(2, 'الجيزة', '2025-04-21 18:50:44', '2025-04-21 18:50:44', NULL),
(3, 'الإسكندرية', '2025-04-21 18:50:44', '2025-04-21 18:50:44', NULL),
(4, 'المنصورة', '2025-04-21 18:50:44', '2025-04-21 18:50:44', NULL),
(5, 'أسوان', '2025-04-21 18:50:44', '2025-04-21 18:50:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3', 'i:3;', 1747868405),
('laravel_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1747868405;', 1747868405);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `cart_total` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint UNSIGNED NOT NULL,
  `cart_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `variation_id` bigint UNSIGNED DEFAULT NULL,
  `total_amount` double NOT NULL,
  `quantity` int NOT NULL,
  `price` int NOT NULL,
  `variation_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `parent_id` int DEFAULT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `data`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'إلكترونيات', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(2, NULL, 'ملابس', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(3, NULL, 'أثاث', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(4, NULL, 'أطعمة', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(5, NULL, 'كتب', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_value` double NOT NULL,
  `discount_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid_from` timestamp NOT NULL,
  `valid_to` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `name`, `code`, `discount_value`, `discount_type`, `valid_from`, `valid_to`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Coupon', 'code', 50, 'fixed', '2025-04-21 21:43:38', '2025-05-10 20:43:42', '2025-04-21 19:43:47', '2025-04-21 19:43:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_04_18_001134_create_areas_table', 1),
(5, '2025_04_18_001135_create_addresses_table', 1),
(6, '2025_04_18_001139_create_shipping_values_table', 1),
(7, '2025_04_18_001140_create_products_table', 1),
(8, '2025_04_18_001141_create_product_variations_table', 1),
(9, '2025_04_18_001143_create_product_images_table', 1),
(10, '2025_04_18_001144_create_product_reviews_table', 1),
(11, '2025_04_18_001150_create_categories_table', 1),
(12, '2025_04_18_001151_create_product_categories_table', 1),
(13, '2025_04_18_001153_create_coupons_table', 1),
(14, '2025_04_18_001155_create_orders_table', 1),
(15, '2025_04_18_001156_create_order_items_table', 1),
(16, '2025_04_18_001157_create_order_notes_table', 1),
(17, '2025_04_18_001158_create_carts_table', 1),
(18, '2025_04_18_001159_create_cart_items_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `coupon_id` bigint UNSIGNED DEFAULT NULL,
  `address_id` bigint UNSIGNED NOT NULL,
  `total_amount` double NOT NULL,
  `payment_method` enum('cash','credit_card') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pre-pay','pending','completed','cancelled','payed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tracking_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `coupon_id`, `address_id`, `total_amount`, `payment_method`, `status`, `tracking_number`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, NULL, 1, 1026, 'cash', 'payed', NULL, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(2, 1, NULL, 1, 2255, 'cash', 'cancelled', NULL, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(3, 2, NULL, 2, 2159, 'cash', 'completed', NULL, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(4, 2, NULL, 2, 819, 'cash', 'cancelled', NULL, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(5, 2, NULL, 2, 4582, 'credit_card', 'pre-pay', NULL, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(6, 3, NULL, 3, 4161, 'cash', 'pending', NULL, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(7, 3, NULL, 3, 4228, 'credit_card', 'payed', NULL, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(8, 4, NULL, 4, 3011, 'credit_card', 'completed', NULL, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(9, 4, NULL, 4, 2431, 'cash', 'cancelled', NULL, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:46', NULL),
(10, 5, NULL, 5, 4299, 'cash', 'completed', NULL, NULL, '2025-04-21 18:50:46', '2025-04-21 18:50:46', NULL),
(11, 5, NULL, 5, 1234, 'credit_card', 'pending', NULL, NULL, '2025-04-21 18:50:46', '2025-04-21 18:50:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `variation_id` bigint UNSIGNED DEFAULT NULL,
  `total_amount` double NOT NULL,
  `quantity` int NOT NULL,
  `price` double DEFAULT NULL,
  `variation_data` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `variation_id`, `total_amount`, `quantity`, `price`, `variation_data`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 8, 4, 1026, 2, 513, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(2, 2, 5, NULL, 434, 2, 217, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(3, 2, 7, 1, 1821, 3, 607, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(4, 3, 8, 4, 1539, 3, 513, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(5, 3, 7, 2, 620, 1, 620, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(6, 4, 3, NULL, 602, 1, 602, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(7, 4, 5, NULL, 217, 1, 217, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(8, 5, 7, 1, 1214, 2, 607, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(9, 5, 8, 4, 1026, 2, 513, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(10, 5, 7, 2, 1860, 3, 620, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(11, 5, 6, NULL, 482, 1, 482, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(12, 6, 7, 2, 1860, 3, 620, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(13, 6, 3, NULL, 1204, 2, 602, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(14, 6, 1, NULL, 816, 1, 816, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(15, 6, 10, NULL, 281, 1, 281, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(16, 7, 7, 2, 1860, 3, 620, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(17, 7, 3, NULL, 1806, 3, 602, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(18, 7, 10, NULL, 562, 2, 281, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(19, 8, 3, NULL, 602, 1, 602, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(20, 8, 1, NULL, 816, 1, 816, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(21, 8, 2, NULL, 1593, 3, 531, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(22, 9, 6, NULL, 964, 2, 482, NULL, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(23, 9, 5, NULL, 651, 3, 217, NULL, '2025-04-21 18:50:46', '2025-04-21 18:50:46', NULL),
(24, 9, 1, NULL, 816, 1, 816, NULL, '2025-04-21 18:50:46', '2025-04-21 18:50:46', NULL),
(25, 10, 1, NULL, 816, 1, 816, NULL, '2025-04-21 18:50:46', '2025-04-21 18:50:46', NULL),
(26, 10, 7, 3, 1851, 3, 617, NULL, '2025-04-21 18:50:46', '2025-04-21 18:50:46', NULL),
(27, 10, 1, NULL, 1632, 2, 816, NULL, '2025-04-21 18:50:46', '2025-04-21 18:50:46', NULL),
(28, 11, 7, 3, 1234, 2, 617, NULL, '2025-04-21 18:50:46', '2025-04-21 18:50:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_notes`
--

CREATE TABLE `order_notes` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_notes`
--

INSERT INTO `order_notes` (`id`, `order_id`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'تم الشحن', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(2, 8, 'جاري التجهيز', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(3, 11, 'ملاحظة خاصة', '2025-04-21 18:50:46', '2025-04-21 18:50:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `sale_price` double NOT NULL,
  `sold_individually` int DEFAULT NULL,
  `stock_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_qty` int NOT NULL,
  `total_sales` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `slug`, `type`, `sku`, `price`, `sale_price`, `sold_individually`, `stock_status`, `stock_qty`, `total_sales`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'product-1', 'simple', 'SKU-1484', 816, 500, NULL, 'in_stock', 94, 0, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(2, 'product-2', 'simple', 'SKU-1793', 531, 806, NULL, 'out_of_stock', 98, 0, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(3, 'product-3', 'simple', 'SKU-2612', 602, 552, NULL, 'out_of_stock', 7, 0, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(4, 'product-4', 'simple', 'SKU-6655', 345, 250, NULL, 'out_of_stock', 86, 0, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(5, 'product-5', 'simple', 'SKU-8116', 217, 852, NULL, 'out_of_stock', 53, 0, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(6, 'product-6', 'simple', 'SKU-4584', 482, 512, NULL, 'in_stock', 28, 0, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(7, 'product-7', 'variable', 'SKU-1742', 919, 600, NULL, 'out_of_stock', 62, 0, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(8, 'product-8', 'variable', 'SKU-1422', 247, 518, NULL, 'in_stock', 71, 0, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(9, 'product-9', 'variable', 'SKU-1223', 954, 185, NULL, 'in_stock', 30, 0, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(10, 'product-10', 'simple', 'SKU-2419', 281, 566, NULL, 'in_stock', 23, 0, '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `category_id`, `product_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, NULL, NULL, NULL),
(2, 4, 1, NULL, NULL, NULL),
(3, 2, 2, NULL, NULL, NULL),
(4, 4, 2, NULL, NULL, NULL),
(5, 1, 3, NULL, NULL, NULL),
(6, 2, 3, NULL, NULL, NULL),
(7, 4, 3, NULL, NULL, NULL),
(8, 1, 4, NULL, NULL, NULL),
(9, 4, 4, NULL, NULL, NULL),
(10, 5, 4, NULL, NULL, NULL),
(11, 1, 5, NULL, NULL, NULL),
(12, 4, 5, NULL, NULL, NULL),
(13, 5, 5, NULL, NULL, NULL),
(14, 3, 6, NULL, NULL, NULL),
(15, 1, 7, NULL, NULL, NULL),
(16, 2, 7, NULL, NULL, NULL),
(17, 4, 7, NULL, NULL, NULL),
(18, 2, 8, NULL, NULL, NULL),
(19, 1, 9, NULL, NULL, NULL),
(20, 4, 9, NULL, NULL, NULL),
(21, 4, 10, NULL, NULL, NULL),
(22, 5, 10, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint UNSIGNED NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `rating` int NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variations`
--

CREATE TABLE `product_variations` (
  `id` bigint UNSIGNED NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `regular_price` double NOT NULL,
  `sale_price` double NOT NULL,
  `manage_stock` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_qty` int NOT NULL,
  `total_sales` int NOT NULL,
  `backorder_limit` int NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variations`
--

INSERT INTO `product_variations` (`id`, `slug`, `product_id`, `regular_price`, `sale_price`, `manage_stock`, `stock_status`, `stock_qty`, `total_sales`, `backorder_limit`, `sku`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'product-7-variation-1', 7, 896, 607, 'yes', 'out_of_stock', 27, 0, 18, 'SKU-1742-VAR-1', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(2, 'product-7-variation-2', 7, 884, 620, 'yes', 'out_of_stock', 27, 0, 13, 'SKU-1742-VAR-2', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(3, 'product-7-variation-3', 7, 966, 617, 'yes', 'out_of_stock', 6, 0, 13, 'SKU-1742-VAR-3', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(4, 'product-8-variation-1', 8, 278, 513, 'yes', 'in_stock', 47, 0, 20, 'SKU-1422-VAR-1', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL),
(5, 'product-9-variation-1', 9, 981, 186, 'yes', 'in_stock', 40, 0, 18, 'SKU-1223-VAR-1', '2025-04-21 18:50:45', '2025-04-21 18:50:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('cUnO2estFidCbYjV0HQgpbbOyV3tPxffaRGGLCGn', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoibW45ejVaMW5GZVNaY1YxOXp5MDFuaHJPVmhFME1GZkZDcjBTNzFTNiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo2O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkM2tmMXIzTVgxbDhibUh1V1hHYTJNdXNtVTFWbWc3bHgzamQuaGxnZFZDNkpLcmJzUHJjZGUiO30=', 1747869886);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_values`
--

CREATE TABLE `shipping_values` (
  `id` bigint UNSIGNED NOT NULL,
  `area_id` bigint UNSIGNED NOT NULL,
  `value` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping_values`
--

INSERT INTO `shipping_values` (`id`, `area_id`, `value`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 20, '2025-04-21 18:50:44', '2025-04-21 18:50:44', NULL),
(2, 2, 45, '2025-04-21 18:50:44', '2025-04-21 18:50:44', NULL),
(3, 3, 22, '2025-04-21 18:50:44', '2025-04-21 18:50:44', NULL),
(4, 4, 27, '2025-04-21 18:50:44', '2025-04-21 18:50:44', NULL),
(5, 5, 45, '2025-04-21 18:50:44', '2025-04-21 18:50:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `date_of_birth` date DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `gender`, `date_of_birth`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Ernestina Gleichner', 'gilda.hessel@example.com', '2025-04-21 18:50:44', '$2y$12$d/HBN9PaeGSG5HrZH9kcaeTUgRy/q0NqpvfrWiH8zZWpBqqNB4.sS', '212-950-0226', 'female', '1973-03-11', '6MuWMXdih5', '2025-04-21 18:50:44', '2025-04-21 18:50:44'),
(2, 'Willard Marvin', 'ortiz.aleen@example.net', '2025-04-21 18:50:44', '$2y$12$d/HBN9PaeGSG5HrZH9kcaeTUgRy/q0NqpvfrWiH8zZWpBqqNB4.sS', '+1-682-953-1497', 'male', '1984-03-04', 'GAt0eXZrzr', '2025-04-21 18:50:45', '2025-04-21 18:50:45'),
(3, 'Emerson Bogan', 'nboehm@example.com', '2025-04-21 18:50:44', '$2y$12$d/HBN9PaeGSG5HrZH9kcaeTUgRy/q0NqpvfrWiH8zZWpBqqNB4.sS', '+1-734-977-2925', 'male', '2005-06-29', 'ZWGMFIbKUb', '2025-04-21 18:50:45', '2025-04-21 18:50:45'),
(4, 'Darryl Klocko', 'tabitha83@example.net', '2025-04-21 18:50:44', '$2y$12$d/HBN9PaeGSG5HrZH9kcaeTUgRy/q0NqpvfrWiH8zZWpBqqNB4.sS', '1-757-277-1903', 'female', '2019-02-06', 'Ua5QzNWSd9', '2025-04-21 18:50:45', '2025-04-21 18:50:45'),
(5, 'Ms. Princess Jacobson', 'brekke.margaret@example.net', '2025-04-21 18:50:44', '$2y$12$d/HBN9PaeGSG5HrZH9kcaeTUgRy/q0NqpvfrWiH8zZWpBqqNB4.sS', '1-571-277-6309', 'female', '1984-05-04', 'BSItgTMFRC', '2025-04-21 18:50:45', '2025-04-21 18:50:45'),
(6, 'admin', 'admin@admin.com', NULL, '$2y$12$3kf1r3MX1l8bmHuWXGa2MusmU1Vmg7lx3jd.hlgdVC6JKrbsPrcde', NULL, 'other', NULL, NULL, '2025-04-21 18:51:06', '2025-04-21 18:51:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addresses_user_id_foreign` (`user_id`);

--
-- Indexes for table `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carts_user_id_foreign` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_items_cart_id_foreign` (`cart_id`),
  ADD KEY `cart_items_product_id_foreign` (`product_id`),
  ADD KEY `cart_items_variation_id_foreign` (`variation_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_coupon_id_foreign` (`coupon_id`),
  ADD KEY `orders_address_id_foreign` (`address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`),
  ADD KEY `order_items_variation_id_foreign` (`variation_id`);

--
-- Indexes for table `order_notes`
--
ALTER TABLE `order_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_notes_order_id_foreign` (`order_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_categories_category_id_foreign` (`category_id`),
  ADD KEY `product_categories_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_reviews_user_id_foreign` (`user_id`),
  ADD KEY `product_reviews_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_variations`
--
ALTER TABLE `product_variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variations_product_id_foreign` (`product_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shipping_values`
--
ALTER TABLE `shipping_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipping_values_area_id_foreign` (`area_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `areas`
--
ALTER TABLE `areas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `order_notes`
--
ALTER TABLE `order_notes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variations`
--
ALTER TABLE `product_variations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shipping_values`
--
ALTER TABLE `shipping_values`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `product_variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `product_variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_notes`
--
ALTER TABLE `order_notes`
  ADD CONSTRAINT `order_notes_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_categories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variations`
--
ALTER TABLE `product_variations`
  ADD CONSTRAINT `product_variations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping_values`
--
ALTER TABLE `shipping_values`
  ADD CONSTRAINT `shipping_values_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
