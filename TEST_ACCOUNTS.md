# 🔐 TÀI KHOẢN TEST - MINI SHOP

## 👑 ADMIN ACCOUNTS

### Tài khoản Admin chính
- **Username**: `admin`
- **Password**: `password123`
- **Email**: `admin@minishop.com`
- **Quyền**: Quản lý toàn bộ hệ thống
- **Tính năng**: Dashboard, quản lý nhân viên, báo cáo, cấu hình hệ thống

### Tài khoản Admin backup
- **Username**: `admin2`
- **Password**: `admin123`
- **Email**: `admin2@minishop.com`
- **Quyền**: Quản lý toàn bộ hệ thống

## 👨‍💼 EMPLOYEE ACCOUNTS

### Tài khoản Employee chính
- **Username**: `employee`
- **Password**: `employee123`
- **Email**: `employee@minishop.com`
- **Quyền**: Quản lý đơn hàng và khách hàng được phân công
- **Tính năng**: Dashboard cá nhân, quản lý đơn hàng, cập nhật tồn kho

### Tài khoản Employee backup
- **Username**: `emp2`
- **Password**: `emp123`
- **Email**: `emp2@minishop.com`
- **Quyền**: Quản lý đơn hàng và khách hàng được phân công

## 🛒 CUSTOMER ACCOUNTS

### Tài khoản Customer mẫu (từ database classicmodels)
- **Customer Number**: 103
- **Customer Name**: Atelier graphique
- **Contact**: Schmitt, Carine
- **Phone**: 40.32.2555
- **Email**: contact@ateliergraphique.com

- **Customer Number**: 112
- **Customer Name**: Signal Gift Stores
- **Contact**: King, Jean
- **Phone**: 7025551838
- **Email**: jean.king@signal.com

*Lưu ý: Customer accounts cần được đăng ký mới qua trang register.php*

## 🚀 HƯỚNG DẪN ĐĂNG NHẬP

### 1. Truy cập hệ thống
- Mở trình duyệt và vào: `http://localhost/mini_shop/`
- Chọn vai trò phù hợp

### 2. Đăng nhập Admin
- Click vào "Admin" trên trang chủ
- Hoặc truy cập trực tiếp: `http://localhost/mini_shop/login.php`
- Nhập: `admin` / `password123`

### 3. Đăng nhập Employee
- Click vào "Employee" trên trang chủ
- Hoặc truy cập trực tiếp: `http://localhost/mini_shop/login.php`
- Nhập: `employee` / `employee123`

### 4. Đăng nhập Customer
- Click vào "Customer" trên trang chủ để xem shop
- Hoặc đăng ký tài khoản mới tại: `http://localhost/mini_shop/register.php`

## 🔧 CÀI ĐẶT TÀI KHOẢN

### Chạy script SQL để tạo tài khoản:

```sql
-- Tạo tài khoản Admin
SOURCE add_admin_account.sql;

-- Tạo tài khoản Employee
SOURCE add_employee_account.sql;
```

### Hoặc chạy thủ công:

```sql
-- Admin account
INSERT INTO users (username, email, password, full_name, role, created_at, updated_at) 
VALUES ('admin', 'admin@minishop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', NOW(), NOW());

-- Employee account
INSERT INTO users (username, email, password, full_name, role, created_at, updated_at) 
VALUES ('employee', 'employee@minishop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sales Employee', 'employee', NOW(), NOW());
```

## 📋 KIỂM TRA QUYỀN TRUY CẬP

### Admin có thể truy cập:
- ✅ `/admin/dashboard.php` - Dashboard admin
- ✅ `/admin/employees.php` - Quản lý nhân viên
- ✅ `/admin/products.php` - Quản lý sản phẩm
- ✅ `/admin/orders.php` - Quản lý đơn hàng
- ✅ `/admin/customers.php` - Quản lý khách hàng
- ✅ `/admin/reports.php` - Báo cáo thống kê
- ✅ `/employee/dashboard.php` - Dashboard employee
- ✅ `/customer/home.php` - Trang shop

### Employee có thể truy cập:
- ✅ `/employee/dashboard.php` - Dashboard employee
- ✅ `/employee/orders.php` - Đơn hàng được phân công
- ✅ `/employee/customers.php` - Khách hàng được phân công
- ✅ `/employee/products.php` - Quản lý sản phẩm
- ✅ `/employee/payments.php` - Quản lý thanh toán
- ✅ `/customer/home.php` - Trang shop

### Customer có thể truy cập:
- ✅ `/customer/home.php` - Trang chủ shop
- ✅ `/customer/products.php` - Danh sách sản phẩm
- ✅ `/customer/cart.php` - Giỏ hàng
- ✅ `/customer/orders.php` - Đơn hàng của mình
- ✅ `/customer/profile.php` - Hồ sơ cá nhân

## ⚠️ LƯU Ý QUAN TRỌNG

1. **Mật khẩu**: Tất cả mật khẩu đã được hash bằng `password_hash()` trong PHP
2. **Session**: Hệ thống sử dụng session để quản lý đăng nhập
3. **Timeout**: Session sẽ hết hạn sau 30 phút không hoạt động
4. **Database**: Đảm bảo database `classicmodels` đã được import
5. **XAMPP**: Cần chạy Apache và MySQL trong XAMPP

## 🐛 TROUBLESHOOTING

### Lỗi đăng nhập:
- Kiểm tra username/password chính xác
- Đảm bảo database đã có tài khoản
- Kiểm tra session đã được start

### Lỗi truy cập trang:
- Kiểm tra quyền truy cập theo role
- Đảm bảo đã đăng nhập
- Kiểm tra đường dẫn file đúng

### Lỗi database:
- Kiểm tra XAMPP MySQL đang chạy
- Kiểm tra cấu hình database trong `config/database.php`
- Đảm bảo database `classicmodels` tồn tại

---

**Chúc bạn sử dụng Mini Shop thành công!** 🎉
