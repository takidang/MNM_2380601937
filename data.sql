-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for my_store
CREATE DATABASE IF NOT EXISTS `my_store` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `my_store`;

-- Dumping structure for table my_store.category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table my_store.category: ~6 rows (approximately)
INSERT INTO `category` (`id`, `name`, `description`) VALUES
	(1, 'Điện thoại', 'Danh mục các loại điện thoại'),
	(2, 'Laptop', 'Danh mục các loại laptop'),
	(3, 'Máy tính bảng', 'Danh mục các loại máy tính bảng'),
	(4, 'Phụ kiện', 'Danh mục phụ kiện điện tử'),
	(5, 'Thiết bị âm thanh', 'Danh mục loa, tai nghe, micro'),
	(6, 'Máy ảnh', 'chụp ảnh');

-- Dumping structure for table my_store.order
CREATE TABLE IF NOT EXISTS `order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_address` varchar(255) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','confirmed','shipping','completed','cancelled') NOT NULL DEFAULT 'pending',
  `note` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_status` (`status`),
  KEY `idx_order_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table my_store.order: ~4 rows (approximately)
INSERT INTO `order` (`id`, `customer_name`, `customer_phone`, `customer_email`, `customer_address`, `total_amount`, `status`, `note`, `created_at`, `updated_at`) VALUES
	(1, 'Nguyễn Văn A', '0901234567', 'vana@gmail.com', '123 Lê Lợi, Q1, TP.HCM', 35990000.00, 'completed', 'Giao giờ hành chính', '2026-05-25 08:53:58', '2026-05-25 08:53:58'),
	(2, 'Trần Thị B', '0912345678', 'thib@gmail.com', '456 Trần Hưng Đạo, Q5, TP.HCM', 27890000.00, 'shipping', NULL, '2026-05-25 08:53:58', '2026-05-25 08:53:58'),
	(3, 'Lê Văn C', '0987654321', NULL, '789 CMT8, Q3, TP.HCM', 1500000.00, 'pending', 'Gọi trước khi giao', '2026-05-25 08:53:58', '2026-05-25 08:53:58'),
	(4, 'tai dang', '0956723457', '', 'quan 9 hcm', 41990000.00, 'pending', '', '2026-05-25 08:57:09', '2026-05-25 08:57:09');

-- Dumping structure for table my_store.order_detail
CREATE TABLE IF NOT EXISTS `order_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_orderdetail_order` (`order_id`),
  KEY `idx_orderdetail_product` (`product_id`),
  CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table my_store.order_detail: ~1 rows (approximately)
INSERT INTO `order_detail` (`id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
	(5, 4, 2, 1, 41990000.00, 41990000.00);

-- Dumping structure for table my_store.product
CREATE TABLE IF NOT EXISTS `product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(15,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table my_store.product: ~3 rows (approximately)
INSERT INTO `product` (`id`, `name`, `description`, `price`, `image`, `category_id`) VALUES
	(1, 'ASUS ROG Strix SCAR 18 G835LX-SA193W (2025)', 'đỉnh', 139000000.00, '1779071686_6a0a7ac6b6195.png', 2),
	(2, 'MacBook Pro 14 M5 - MDE04SA/A | MDE44SA/A', 'lap ngon ', 41990000.00, '1779072431_6a0a7daf39532.png', 2),
	(3, 'Galaxy A57 5G', 'ok', 14000000.00, '1779076214_6a0a8c765eed0.png', 1),
	(4, 'canon EOS R6 Mark II', '', 21312312.00, '1779675544_6a13b1983283c.png', 6);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
