# 🏪 MINI SHOP - HƯỚNG DẪN SỬ DỤNG

## 📋 Tổng quan hệ thống

Mini Shop là hệ thống e-commerce hoàn chỉnh với 3 vai trò người dùng:
- **Customer (Khách hàng)**: Mua sắm, quản lý giỏ hàng, theo dõi đơn hàng
- **Employee (Nhân viên)**: Quản lý đơn hàng, khách hàng, sản phẩm được phân công
- **Admin (Quản trị viên)**: Quản lý toàn bộ hệ thống

## 🚀 Cài đặt và chạy hệ thống

### 1. Yêu cầu hệ thống
- XAMPP (Apache + MySQL + PHP)
- PHP 7.4+ 
- MySQL 5.7+

### 2. Cài đặt database
```sql
-- Chạy script tạo database và tables
-- Import file classicmodels.sql vào MySQL

-- Thêm tài khoản admin
SOURCE add_admin_account.sql;

-- Thêm tài khoản employee  
SOURCE add_employee_account.sql;
```

### 3. Cấu hình
- Cập nhật thông tin database trong `config/database.php`
- Đảm bảo XAMPP đang chạy Apache và MySQL

## 👥 Tài khoản test

### Admin Account
- **Username**: `admin`
- **Password**: `password123`
- **Quyền**: Quản lý toàn bộ hệ thống

### Employee Account  
- **Username**: `employee`
- **Password**: `employee123`
- **Quyền**: Quản lý đơn hàng và khách hàng được phân công

### Customer Account
- Đăng ký tài khoản mới tại trang đăng ký
- Hoặc sử dụng tài khoản có sẵn trong database

## 🎯 Hướng dẫn sử dụng

### 1. Trang chủ (index.php)
- Hiển thị 3 vai trò: Customer, Employee, Admin
- Click vào vai trò để chuyển đến trang tương ứng
- Customer có thể truy cập trực tiếp, Employee và Admin cần đăng nhập

### 2. Customer Features
- **Trang chủ**: Xem banner, sản phẩm nổi bật, khuyến mãi
- **Sản phẩm**: Duyệt và tìm kiếm sản phẩm
- **Giỏ hàng**: Thêm/xóa sản phẩm, cập nhật số lượng
- **Đặt hàng**: Checkout và thanh toán
- **Theo dõi đơn hàng**: Xem trạng thái đơn hàng
- **Hồ sơ**: Cập nhật thông tin cá nhân

### 3. Employee Features
- **Dashboard**: Thống kê đơn hàng và khách hàng được phân công
- **Đơn hàng**: Quản lý đơn hàng của khách hàng được phân công
- **Khách hàng**: Xem danh sách khách hàng được phân công
- **Sản phẩm**: Cập nhật tồn kho và giá sản phẩm
- **Thanh toán**: Xem lịch sử thanh toán

### 4. Admin Features
- **Dashboard**: Thống kê tổng quan hệ thống
- **Sản phẩm**: Quản lý toàn bộ sản phẩm
- **Đơn hàng**: Xem tất cả đơn hàng
- **Nhân viên**: Thêm/sửa/xóa nhân viên
- **Khách hàng**: Quản lý khách hàng
- **Văn phòng**: Quản lý chi nhánh
- **Banner**: Quản lý banner trang chủ
- **Khuyến mãi**: Tạo và quản lý chương trình khuyến mãi
- **Báo cáo**: Thống kê doanh thu và hiệu suất

## 🔧 Tính năng chính

### Customer (Front-end)
✅ Trang chủ với banner và sản phẩm nổi bật  
✅ Danh mục sản phẩm với bộ lọc  
✅ Tìm kiếm sản phẩm  
✅ Giỏ hàng và checkout  
✅ Đặt hàng và thanh toán  
✅ Theo dõi đơn hàng  
✅ Đánh giá sản phẩm  
✅ Hồ sơ khách hàng  

### Employee (Back-office)
✅ Dashboard với thống kê cá nhân  
✅ Quản lý đơn hàng được phân công  
✅ Quản lý khách hàng được phân công  
✅ Cập nhật tồn kho sản phẩm  
✅ Quản lý thanh toán  
✅ Cập nhật trạng thái đơn hàng  

### Admin (System Management)
✅ Dashboard tổng quan hệ thống  
✅ Quản lý nhân viên  
✅ Quản lý văn phòng chi nhánh  
✅ Báo cáo và thống kê  
✅ Quản lý banner và khuyến mãi  
✅ Cấu hình hệ thống  

## 🎨 Giao diện

- **Responsive Design**: Tương thích với mọi thiết bị
- **Bootstrap 5**: Framework CSS hiện đại
- **Font Awesome**: Icon đẹp mắt
- **Gradient Background**: Thiết kế hiện đại
- **Sidebar Navigation**: Điều hướng dễ dàng

## 🔐 Bảo mật

- **Password Hashing**: Mật khẩu được mã hóa bằng PHP password_hash()
- **Session Management**: Quản lý phiên đăng nhập an toàn
- **Role-based Access**: Phân quyền theo vai trò
- **SQL Injection Protection**: Sử dụng prepared statements
- **XSS Protection**: Escape HTML output

## 📱 Responsive Design

- **Mobile First**: Tối ưu cho thiết bị di động
- **Tablet Support**: Giao diện thân thiện với tablet
- **Desktop Optimized**: Trải nghiệm tốt trên desktop

## 🚀 Performance

- **Database Optimization**: Query được tối ưu
- **Caching**: Session và cart caching
- **Lazy Loading**: Tải hình ảnh theo nhu cầu
- **Minified Assets**: CSS và JS được tối ưu

## 📞 Hỗ trợ

Nếu gặp vấn đề, vui lòng kiểm tra:
1. XAMPP đang chạy Apache và MySQL
2. Database đã được import đúng
3. File cấu hình database chính xác
4. Quyền truy cập thư mục đúng

---

**Mini Shop** - Hệ thống e-commerce hoàn chỉnh với đầy đủ tính năng cho Customer, Employee và Admin! 🛒✨
