# 🔧 FIX: test_accounts.sql - Đã sửa lỗi duplicate entry

## ✅ Những gì đã sửa:

### 1. **Sửa lỗi Duplicate Entry**
- ❌ **Trước**: `INSERT INTO` → Gây lỗi khi chạy lại script
- ✅ **Sau**: `INSERT IGNORE INTO` → Có thể chạy nhiều lần an toàn

### 2. **Thêm tài khoản Employee** (Yêu cầu đăng nhập)
Đã thêm 2 tài khoản nhân viên:
```
Username: employee1
Password: 123456
Email: employee1@minishop.com
Role: employee

Username: nhanvien  
Password: 123456
Email: nhanvien@minishop.com
Role: employee
```

### 3. **Cải thiện logic INSERT**
- ✅ Product images: Chỉ thêm nếu sản phẩm chưa có ảnh chính
- ✅ Reviews: Sử dụng subquery động thay vì hardcode userID
- ✅ Kiểm tra trùng lặp tự động

### 4. **Thêm tùy chọn DELETE**
Nếu muốn xóa hết dữ liệu cũ và thêm lại từ đầu:
```sql
-- Bỏ comment (xóa dấu --) các dòng này:
DELETE FROM reviews;
DELETE FROM product_images WHERE imageID > 0;
DELETE FROM banners WHERE bannerID > 0;
DELETE FROM promotions WHERE promotionID > 0;
DELETE FROM users WHERE userID > 1000;
```

---

## 📋 Danh sách tài khoản sau khi chạy:

### 🔑 ADMIN (1 tài khoản)
```
Username: admin
Password: 123456
Quyền: Toàn quyền quản trị
URL: admin/dashboard.php
```

### 👔 EMPLOYEE (2 tài khoản) - YÊU CẦU ĐĂNG NHẬP
```
Username: employee1 hoặc nhanvien
Password: 123456
Quyền: Quản lý đơn hàng, sản phẩm, khách hàng
URL: employee/dashboard.php
```

### 👤 CUSTOMER (3 tài khoản)
```
Username: customer1, customer2, hoặc khachhang
Password: 123456
Quyền: Mua hàng, đánh giá
URL: customer/home.php
```

---

## 🚀 Cách sử dụng:

### Bước 1: Mở phpMyAdmin
```
http://localhost/phpmyadmin
```

### Bước 2: Chọn database
```
classicmodels
```

### Bước 3: Vào tab SQL
Click vào tab "SQL"

### Bước 4: Copy & Paste
1. Mở file: `test_accounts.sql`
2. Copy TOÀN BỘ nội dung
3. Paste vào ô SQL trong phpMyAdmin

### Bước 5: Chạy
Click nút **"Go"** (hoặc **"Thực hiện"**)

---

## ✅ Kết quả mong đợi:

Sau khi chạy xong, bạn sẽ thấy:
```
✓ Query OK (X rows affected) - Nhiều lần
✓ 0 errors
```

Nếu có dòng:
```
0 rows affected
```
→ Đó là bình thường! Nghĩa là dữ liệu đã tồn tại, script bỏ qua (nhờ INSERT IGNORE)

---

## 🔍 Kiểm tra tài khoản đã được tạo:

Chạy SQL này để kiểm tra:
```sql
SELECT username, email, role FROM users ORDER BY role, username;
```

Kết quả mong đợi:
```
admin       | admin@minishop.com      | admin
employee1   | employee1@minishop.com  | employee
nhanvien    | nhanvien@minishop.com   | employee
customer1   | customer1@example.com   | customer
customer2   | customer2@example.com   | customer
khachhang   | khach@test.com          | customer
```

---

## 🧪 Test đăng nhập:

### Test Admin:
```
1. Vào: http://localhost/mini_shop/login.php
2. Username: admin
3. Password: 123456
4. Sau khi đăng nhập → Tự động redirect đến admin/dashboard.php
```

### Test Employee:
```
1. Vào: http://localhost/mini_shop/login.php
2. Username: employee1 (hoặc nhanvien)
3. Password: 123456
4. Sau khi đăng nhập → Tự động redirect đến employee/dashboard.php
```

### Test Customer:
```
1. Vào: http://localhost/mini_shop/login.php
2. Username: customer1
3. Password: 123456
4. Sau khi đăng nhập → Tự động redirect đến customer/home.php
```

---

## 🐛 Xử lý lỗi:

### Lỗi: "Duplicate entry for key 'PRIMARY'"
**Nguyên nhân**: Đang dùng INSERT thay vì INSERT IGNORE  
**Giải pháp**: Đã fix! File mới dùng INSERT IGNORE

### Lỗi: "Cannot add or update a child row"
**Nguyên nhân**: Foreign key constraint  
**Giải pháp**: 
```sql
SET FOREIGN_KEY_CHECKS = 0;
-- Chạy script
SET FOREIGN_KEY_CHECKS = 1;
```

### Lỗi: "Table 'users' doesn't exist"
**Nguyên nhân**: Database chưa được tạo đầy đủ  
**Giải pháp**: Chạy script tạo database chính trước

### Lỗi: "Unknown column 'role'"
**Nguyên nhân**: Bảng users chưa có cột role  
**Giải pháp**:
```sql
ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'customer';
```

---

## 📊 Dữ liệu sẽ được thêm:

- ✅ **6 tài khoản** (1 admin + 2 employee + 3 customer)
- ✅ **3 banners** cho trang chủ
- ✅ **3 promotions** (khuyến mãi)
- ✅ **Ảnh cho 20 sản phẩm** (ảnh chính)
- ✅ **Ảnh phụ cho 10 sản phẩm**
- ✅ **15 reviews mẫu**

---

## 💡 Lưu ý quan trọng:

### ✅ An toàn khi chạy lại
Script này có thể chạy **NHIỀU LẦN** mà không gây lỗi nhờ `INSERT IGNORE`

### ✅ Không mất dữ liệu cũ
Nếu tài khoản đã tồn tại, script sẽ bỏ qua (không ghi đè)

### ✅ Linh hoạt
Nếu muốn reset hoàn toàn, uncomment các dòng DELETE ở đầu file

---

## 🎯 Phân quyền theo code:

File `config/session.php` đã có các hàm kiểm tra quyền:
- `requireAdmin()` - Chỉ admin mới vào được
- `requireEmployee()` - Admin hoặc Employee mới vào được
- `requireLogin()` - Phải đăng nhập

Employee dashboard (`employee/dashboard.php`) sử dụng:
```php
requireLogin();
requireEmployee(); // Admin + Employee đều vào được
```

Admin dashboard (`admin/dashboard.php`) sử dụng:
```php
requireLogin();
requireAdmin(); // Chỉ Admin mới vào được
```

---

## 🎉 Hoàn thành!

Bây giờ bạn có thể:
1. ✅ Chạy script mà không lo lỗi duplicate entry
2. ✅ Đăng nhập với tài khoản employee (yêu cầu đăng nhập như code)
3. ✅ Test đầy đủ 3 roles: admin, employee, customer
4. ✅ Chạy lại script bao nhiêu lần cũng được

---

**Chúc bạn test thành công! 🚀**
