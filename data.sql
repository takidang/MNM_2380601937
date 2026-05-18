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
	(3, 'Galaxy A57 5G', 'ok', 14000000.00, '1779076214_6a0a8c765eed0.png', 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
