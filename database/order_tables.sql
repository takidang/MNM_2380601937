-- =====================================================
-- BỔ SUNG BẢNG ORDER VÀ ORDER_DETAIL
-- Chạy sau khi đã có database my_store với bảng product, category
-- =====================================================

USE my_store;

-- Bảng đơn hàng (order là từ khóa SQL, phải bọc backtick)
CREATE TABLE IF NOT EXISTS `order` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_email VARCHAR(100) DEFAULT NULL,
    customer_address VARCHAR(255) NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    status ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng chi tiết đơn hàng
CREATE TABLE IF NOT EXISTS order_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES `order`(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE RESTRICT
);

-- Index để tăng tốc truy vấn
CREATE INDEX idx_order_status ON `order`(status);
CREATE INDEX idx_order_created ON `order`(created_at);
CREATE INDEX idx_orderdetail_order ON order_detail(order_id);
CREATE INDEX idx_orderdetail_product ON order_detail(product_id);

-- =====================================================
-- DỮ LIỆU MẪU
-- =====================================================

INSERT INTO `order` (customer_name, customer_phone, customer_email, customer_address, total_amount, status, note) VALUES
('Nguyễn Văn A', '0901234567', 'vana@gmail.com', '123 Lê Lợi, Q1, TP.HCM', 35990000, 'completed', 'Giao giờ hành chính'),
('Trần Thị B', '0912345678', 'thib@gmail.com', '456 Trần Hưng Đạo, Q5, TP.HCM', 27890000, 'shipping', NULL),
('Lê Văn C', '0987654321', NULL, '789 CMT8, Q3, TP.HCM', 1500000, 'pending', 'Gọi trước khi giao');

-- Chi tiết đơn hàng (giả sử product_id 1, 2, 3 đã tồn tại)
INSERT INTO order_detail (order_id, product_id, quantity, price, subtotal) VALUES
(1, 1, 1, 25990000, 25990000),
(1, 4, 2, 5000000, 10000000),
(2, 2, 1, 27890000, 27890000),
(3, 5, 3, 500000, 1500000);
