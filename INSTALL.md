# 🚀 Hướng dẫn cài đặt nhanh Mini Shop

## Bước 1: Khởi động XAMPP

1. Mở **XAMPP Control Panel**
2. Click **Start** cho:
   - ✅ **Apache**
   - ✅ **MySQL**

## Bước 2: Kiểm tra Database

Database `classicmodels` đã có sẵn với các bảng:
- ✅ products, productlines, product_images
- ✅ customers, orders, orderdetails, payments
- ✅ users, cart, reviews
- ✅ banners, promotions
- ✅ employees, offices

## Bước 3: Tạo tài khoản Admin (Nếu chưa có)

Mở **phpMyAdmin**: http://localhost/phpmyadmin

Chạy SQL này để tạo tài khoản admin:

```sql
-- Tạo user admin (password: admin123)
INSERT INTO users (username, password, email, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@minishop.com', 'admin');

-- Tạo user customer (password: customer123)
INSERT INTO users (username, password, email, role) 
VALUES ('customer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer@example.com', 'customer');
```

## Bước 4: Thêm dữ liệu mẫu (Tùy chọn)

### Thêm Banner

```sql
INSERT INTO banners (title, image_url, link_url, is_active) VALUES
('Sale 50% - Summer Collection', 'https://via.placeholder.com/1200x400/667eea/ffffff?text=Summer+Sale+50%25', 'customer/products.php', 1),
('New Arrivals 2025', 'https://via.placeholder.com/1200x400/764ba2/ffffff?text=New+Arrivals', 'customer/products.php', 1);
```

### Thêm Khuyến mãi

```sql
INSERT INTO promotions (promo_name, description, discount_percent, start_date, end_date, is_active) VALUES
('Summer Sale', 'Giảm giá mùa hè cho tất cả sản phẩm', 25.00, '2025-01-01', '2025-12-31', 1),
('New Year 2025', 'Chào mừng năm mới', 15.00, '2025-01-01', '2025-02-28', 1);
```

### Thêm hình ảnh sản phẩm

```sql
-- Thêm hình cho sản phẩm đầu tiên (lấy productCode từ bảng products)
INSERT INTO product_images (productCode, image_url, is_main) 
SELECT productCode, 'https://via.placeholder.com/400x300/3498db/ffffff?text=Classic+Car', 1 
FROM products LIMIT 10;
```

## Bước 5: Truy cập Website

Mở trình duyệt và vào:

```
http://localhost/mini_shop/
```

## Bước 6: Đăng nhập

### Tài khoản Admin
- **Username**: `admin`
- **Password**: `admin123`

### Tài khoản Customer
- **Username**: `customer1`
- **Password**: `customer123`

Hoặc đăng ký tài khoản mới tại: http://localhost/mini_shop/register.php

---

## ✅ Kiểm tra hoạt động

1. ✅ Trang chủ hiển thị sản phẩm
2. ✅ Có thể tìm kiếm sản phẩm
3. ✅ Thêm sản phẩm vào giỏ hàng
4. ✅ Đăng nhập/Đăng ký hoạt động
5. ✅ Checkout và thanh toán

---

## 🐛 Lỗi thường gặp

### Lỗi: "Connection failed"
**Giải pháp**: 
- Kiểm tra MySQL đã start chưa
- Mở `config/database.php` và sửa password

### Lỗi: "Cannot find database"
**Giải pháp**: 
- Database `classicmodels` đã tồn tại
- Kiểm tra trong phpMyAdmin

### Lỗi: "Headers already sent"
**Giải pháp**: 
- Xóa dòng trống trước `<?php` trong file PHP

### Giỏ hàng không hoạt động
**Giải pháp**: 
- Kiểm tra session đã được start
- Clear browser cookies

---

## 📞 Cần trợ giúp?

Đọc file **README.md** để biết thêm chi tiết!

**Chúc bạn sử dụng thành công! 🎉**
