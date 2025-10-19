-- =============================================
-- MINI SHOP - TÀI KHOẢN TEST & DỮ LIỆU MẪU
-- =============================================
-- Sử dụng INSERT IGNORE để tránh lỗi duplicate entry
-- Script này có thể chạy nhiều lần an toàn

-- XÓA DỮ LIỆU CŨ (Tùy chọn - uncomment nếu muốn reset)
-- DELETE FROM reviews;
-- DELETE FROM product_images WHERE imageID > 0;
-- DELETE FROM banners WHERE bannerID > 0;
-- DELETE FROM promotions WHERE promotionID > 0;
-- DELETE FROM users WHERE userID > 1000;

-- 1. TÀI KHOẢN TEST
-- Password cho tất cả: 123456

-- Admin account
INSERT IGNORE INTO users (username, password, email, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@minishop.com', 'admin');

-- Employee accounts (yêu cầu đăng nhập)
INSERT IGNORE INTO users (username, password, email, role) VALUES 
('employee1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee1@minishop.com', 'employee'),
('nhanvien', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'nhanvien@minishop.com', 'employee');

-- Customer accounts
INSERT IGNORE INTO users (username, password, email, role) VALUES 
('customer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer1@example.com', 'customer'),
('customer2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer2@example.com', 'customer'),
('khachhang', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'khach@test.com', 'customer');

-- 2. BANNERS
INSERT IGNORE INTO banners (title, image_url, link_url, is_active) VALUES
('🎉 Sale Lớn Mùa Hè - Giảm 50%', 'https://images.unsplash.com/photo-1449034446853-66c86144b0ad?w=1200&h=400&fit=crop', 'customer/products.php', 1),
('🚗 Bộ Sưu Tập Xe Cổ Điển Mới Nhất 2025', 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1200&h=400&fit=crop', 'customer/products.php?line=Classic%20Cars', 1),
('✨ Miễn Phí Vận Chuyển Toàn Quốc', 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=1200&h=400&fit=crop', 'customer/products.php', 1);

-- 3. KHUYẾN MÃI
INSERT IGNORE INTO promotions (promo_name, description, discount_percent, start_date, end_date, is_active) VALUES
('🔥 Sale Mùa Hè', 'Giảm giá đến 25% cho tất cả sản phẩm Classic Cars', 25.00, '2025-01-01', '2025-12-31', 1),
('🎁 Khuyến Mãi Năm Mới 2025', 'Giảm 15% mừng năm mới cho toàn bộ cửa hàng', 15.00, '2025-01-01', '2025-02-28', 1),
('⚡ Flash Sale Cuối Tuần', 'Giảm 30% các sản phẩm Motorcycles', 30.00, '2025-01-01', '2025-06-30', 1);

-- 4. HÌNH ẢNH SẢN PHẨM (Lấy 20 sản phẩm đầu tiên)
-- Chỉ thêm nếu sản phẩm chưa có ảnh chính
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT 
    productCode,
    CONCAT('https://images.unsplash.com/photo-', 
           FLOOR(1400000000000 + RAND() * 100000000000), 
           '?w=400&h=300&fit=crop'),
    1
FROM products 
WHERE productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1)
LIMIT 20;

-- Thêm ảnh phụ cho một số sản phẩm
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT 
    productCode,
    CONCAT('https://images.unsplash.com/photo-', 
           FLOOR(1400000000000 + RAND() * 100000000000), 
           '?w=400&h=300&fit=crop'),
    0
FROM products 
WHERE productCode IN (SELECT DISTINCT productCode FROM product_images WHERE is_main = 1)
LIMIT 10;

-- 5. REVIEWS MẪU
-- Lấy userID của customer1 động
INSERT IGNORE INTO reviews (productCode, userID, rating, comment, created_at) 
SELECT 
    p.productCode,
    (SELECT userID FROM users WHERE username = 'customer1' LIMIT 1),
    FLOOR(3 + RAND() * 3),
    CASE FLOOR(RAND() * 5)
        WHEN 0 THEN 'Sản phẩm tuyệt vời! Chất lượng rất tốt.'
        WHEN 1 THEN 'Đẹp và chính xác như mô tả. Rất hài lòng!'
        WHEN 2 THEN 'Giao hàng nhanh, đóng gói cẩn thận.'
        WHEN 3 THEN 'Mô hình chi tiết, đáng giá đồng tiền.'
        ELSE 'Sẽ mua thêm sản phẩm khác!'
    END,
    DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 30) DAY)
FROM products 
WHERE productCode NOT IN (SELECT DISTINCT productCode FROM reviews)
LIMIT 15;

-- =============================================
-- THÔNG TIN ĐĂNG NHẬP
-- =============================================

/*

🔐 TÀI KHOẢN TEST - MẬT KHẨU TẤT CẢ: 123456

1. ADMIN (Quyền cao nhất)
   Username: admin
   Password: 123456
   Email: admin@minishop.com
   Truy cập: admin/dashboard.php
   
2. EMPLOYEE (Nhân viên - yêu cầu đăng nhập)
   Username: employee1
   Password: 123456
   Email: employee1@minishop.com
   Truy cập: employee/dashboard.php
   
   Username: nhanvien
   Password: 123456
   Email: nhanvien@minishop.com
   Truy cập: employee/dashboard.php
   
3. KHÁCH HÀNG
   Username: customer1
   Password: 123456
   Email: customer1@example.com
   Truy cập: customer/home.php
   
   Username: customer2
   Password: 123456
   Email: customer2@example.com
   
   Username: khachhang
   Password: 123456
   Email: khach@test.com

📝 HƯỚNG DẪN:
1. Mở phpMyAdmin: http://localhost/phpmyadmin
2. Chọn database: classicmodels
3. Vào tab SQL
4. Copy & paste TOÀN BỘ script này
5. Click "Go" để chạy

⚠️ LƯU Ý:
- Script sử dụng INSERT IGNORE nên có thể chạy NHIỀU LẦN an toàn
- Không gây lỗi duplicate entry
- Tự động kiểm tra dữ liệu đã tồn tại

🚀 TRUY CẬP:
- Website: http://localhost/mini_shop/
- Đăng nhập: http://localhost/mini_shop/login.php
- Đăng ký: http://localhost/mini_shop/register.php

✅ SAU KHI CHẠY SCRIPT:
- Đã có 6 tài khoản test:
  • 1 admin (quản trị viên)
  • 2 employee (nhân viên) - YÊU CẦU ĐĂNG NHẬP
  • 3 customer (khách hàng)
- Đã có 3 banners trên trang chủ
- Đã có 3 khuyến mãi
- Đã có hình ảnh cho 20 sản phẩm
- Đã có 15 reviews mẫu

🔑 PHÂN QUYỀN:
- Admin: Toàn quyền quản trị (admin/*)
- Employee: Quản lý đơn hàng, sản phẩm, khách hàng (employee/*)
- Customer: Mua hàng, đánh giá sản phẩm (customer/*)

💡 MẸO:
Nếu muốn XÓA HẾT dữ liệu cũ và thêm mới hoàn toàn,
uncomment (bỏ dấu --) các dòng DELETE ở đầu script.

*/
