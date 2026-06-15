CREATE TABLE IF NOT EXISTS `banner` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `subtitle` varchar(300) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT 'Xem ngay',
  `button_link` varchar(255) DEFAULT '#products',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dữ liệu mẫu
INSERT INTO `banner` (title, subtitle, button_text, button_link, sort_order, is_active) VALUES
('DEAL SỐC MỖI NGÀY', 'Giảm đến 30% cho tất cả sản phẩm công nghệ', 'Mua ngay', '#products', 1, 1),
('LAPTOP & ĐIỆN THOẠI', 'Cấu hình mạnh – Giá tốt nhất thị trường', 'Khám phá', '#products', 2, 1),
('PHỤ KIỆN GIÁ RẺ', 'Tai nghe, chuột, bàn phím – Giảm 50%', 'Xem ngay', '#products', 3, 1);
