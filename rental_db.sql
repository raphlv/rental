-- MySQL Dump
-- Database: rental_db
-- ------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `rental_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rental_db`;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('PS2','PS3','PS4','Nintendo Switch','TV 32 Inch') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ada','disewa','maintenance') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ada',
  `price_per_hour` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
INSERT INTO `units` (`id`, `name`, `type`, `status`, `price_per_hour`, `created_at`, `updated_at`) VALUES
(1,'PS2 - Unit 01','PS2','ada',3000.00,NOW(),NOW()),
(2,'PS2 - Unit 02','PS2','ada',3000.00,NOW(),NOW()),
(3,'PS3 - Unit 01','PS3','ada',5000.00,NOW(),NOW()),
(4,'PS3 - Unit 02','PS3','ada',5000.00,NOW(),NOW()),
(5,'PS3 - Unit 03','PS3','ada',5000.00,NOW(),NOW()),
(6,'PS4 - Unit 01','PS4','ada',8000.00,NOW(),NOW()),
(7,'PS4 - Unit 02','PS4','ada',8000.00,NOW(),NOW()),
(8,'PS4 - Unit 03','PS4','ada',8000.00,NOW(),NOW()),
(9,'Switch - Unit 01','Nintendo Switch','ada',10000.00,NOW(),NOW()),
(10,'Switch - Unit 02','Nintendo Switch','ada',10000.00,NOW(),NOW()),
(11,'TV 32\" - Unit 01','TV 32 Inch','ada',4000.00,NOW(),NOW()),
(12,'TV 32\" - Unit 02','TV 32 Inch','ada',4000.00,NOW(),NOW());
UNLOCK TABLES;

--
-- Table structure for table `rentals`
--

DROP TABLE IF EXISTS `rentals`;
CREATE TABLE `rentals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `unit_id` bigint(20) unsigned NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `payment_method` enum('Cash','Transfer','QRIS') COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rentals_unit_id_foreign` (`unit_id`),
  CONSTRAINT `rentals_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
