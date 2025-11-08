# 🛒 Mini Shop - E-commerce System
##📋 Mục lục
- [Tính năng](#-tính-năng)
- [Công nghệ sử dụng](#-công-nghệ-sử-dụng)
- [Cài đặt](#-cài-đặt)
- [Cấu trúc dự án](#-cấu-trúc-dự-án)
- [Hướng dẫn sử dụng](#-hướng-dẫn-sử-dụng)
- [Tài khoản mặc định](#-tài-khoản-mặc-định)

---

## ✨ Tính năng

### 👥 Cho Khách hàng (Customer)
- ✅ **Trang chủ**: Banner carousel, sản phẩm nổi bật, khuyến mãi
- ✅ **Danh mục sản phẩm**: Tìm kiếm, lọc theo giá/nhà cung cấp/tồn kho
- ✅ **Chi tiết sản phẩm**: Hình ảnh, mô tả, đánh giá (reviews)
- ✅ **Giỏ hàng**: Thêm/xóa/cập nhật sản phẩm (AJAX)
- ✅ **Đặt hàng**: Checkout với thông tin khách hàng đầy đủ
- ✅ **Thanh toán**: Nhiều phương thức (COD, Bank Transfer, Card, E-Wallet)
- ✅ **Theo dõi đơn hàng**: Xem lịch sử, trạng thái, hủy đơn
- ✅ **Hồ sơ cá nhân**: Cập nhật thông tin, đổi mật khẩu

### 🧑‍💼 Cho Nhân viên (Employee) - *Đang phát triển*
- ⏳ Quản lý đơn hàng được phân công
- ⏳ Cập nhật trạng thái đơn hàng
- ⏳ Quản lý khách hàng
- ⏳ Cập nhật sản phẩm & tồn kho

### 🏢 Cho Admin
- ✅ **Dashboard**: Thống kê tổng quan hệ thống
- ✅ **Quản lý sản phẩm**: CRUD sản phẩm, lọc, phân trang
- ✅ **Quản lý đơn hàng**: Xem, cập nhật trạng thái, chi tiết đơn
- ✅ **Quản lý khách hàng**: Xem danh sách, thống kê mua hàng
- ✅ **Quản lý nhân viên**: CRUD nhân viên, phân quyền, reportsTo
- ✅ **Quản lý văn phòng**: CRUD văn phòng chi nhánh
- ✅ **Quản lý Banner**: Thêm/sửa/xóa banner trang chủ
- ✅ **Quản lý Khuyến mãi**: Tạo và quản lý chương trình khuyến mãi
- ✅ **Báo cáo & Thống kê**: 
  - Doanh thu theo thời gian (ngày/tháng/quý/năm)
  - Top sản phẩm bán chạy
  - Hiệu suất nhân viên kinh doanh
  - Cảnh báo tồn kho thấp
  - Biểu đồ doanh thu & dòng sản phẩm (Chart.js)

---
## 🛠 Công nghệ sử dụng
- **Backend**: PHP 8.x (Vanilla PHP - không framework)
- **Database**: MySQL 8.x
- **Frontend**: 
  - HTML5, CSS3
  - Bootstrap 5.3
  - JavaScript (ES6+)
  - Font Awesome 6.4
- **AJAX**: Fetch API
- **Security**: 
  - Password hashing (bcrypt)
  - Prepared statements (SQL injection prevention)
  - Session management
  - CSRF protection
---
## 📦 Cài đặt

### Yêu cầu hệ thống
- XAMPP (hoặc LAMP/WAMP)
- PHP >= 8.0
- MySQL >= 8.0
- Web browser hiện đại

### Các bước cài đặt

1. **Clone hoặc copy project vào thư mục XAMPP**
   ```bash
   # Windows
   C:\xampp\htdocs\mini_shop\
   
   # Linux/Mac
   /opt/lampp/htdocs/mini_shop/
   ```

2. **Cấu hình database**
   - Mở XAMPP Control Panel
   - Start **Apache** và **MySQL**
   - Database `classicmodels` đã có sẵn với các bảng:
     - `products`, `productlines`, `product_images`
     - `customers`, `orders`, `orderdetails`, `payments`
     - `users`, `cart`, `reviews`, `banners`, `promotions`
     - `employees`, `offices`

3. **Cấu hình kết nối database**
   
3. **Cấu hình Database**

   a. Copy file `.env.example` thành `.env`:
   ```bash
   cp .env.example .env
   ```
   
   b. Mở file `.env` và cập nhật thông tin database của bạn:
   ```env
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=your_password_here
   DB_NAME=classicmodels
   ```
4. **Truy cập website**
   ```
   http://localhost/mini_shop/
   ```
---

## 📁 Cấu trúc dự án

```
mini_shop/
│
├── config/
│   ├── database.php        # Kết nối database
│   └── session.php         # Quản lý session & authentication
│
├── includes/
│   ├── header.php          # Header (navigation)
│   └── footer.php          # Footer
│
├── assets/
│   ├── css/
│   │   └── style.css       # Custom CSS
│   ├── js/
│   │   └── main.js         # JavaScript (AJAX)
│   └── images/             # Hình ảnh sản phẩm
│
├── customer/               # Frontend cho khách hàng
│   ├── home.php            # Trang chủ
│   ├── products.php        # Danh sách sản phẩm
│   ├── product_detail.php  # Chi tiết sản phẩm
│   ├── search.php          # Tìm kiếm
│   ├── cart.php            # Giỏ hàng
│   ├── checkout.php        # Thanh toán
│   ├── payment.php         # Xác nhận thanh toán
│   ├── orders.php          # Danh sách đơn hàng
│   ├── order_detail.php    # Chi tiết đơn hàng
│   ├── profile.php         # Hồ sơ cá nhân
│   └── ajax/               # AJAX endpoints
│       ├── add_to_cart.php
│       ├── update_cart.php
│       ├── remove_from_cart.php
│       ├── submit_review.php
│       └── cancel_order.php
│
├── admin/                  # Admin panel (đang phát triển)
│
├── employee/               # Employee dashboard (đang phát triển)
│
├── login.php               # Đăng nhập
├── register.php            # Đăng ký
├── logout.php              # Đăng xuất
├── index.php               # Redirect to home
│
├── add.php                 # (Legacy) Thêm sản phẩm
├── edit.php                # (Legacy) Sửa sản phẩm
├── delete.php              # (Legacy) Xóa sản phẩm
└── db.php                  # (Legacy) Database connection
```
---

## 🚀 Hướng dẫn sử dụng

### Đối với Khách hàng

1. **Đăng ký tài khoản mới**
   - Truy cập: `http://localhost/mini_shop/register.php`
   - Điền thông tin: username, email, password
   - Click "Đăng ký"

2. **Đăng nhập**
   - Truy cập: `http://localhost/mini_shop/login.php`
   - Nhập username & password
   - Hoặc duyệt web không cần đăng nhập (chỉ không thể mua hàng)

3. **Mua sắm**
   - Xem sản phẩm trên trang chủ
   - Tìm kiếm hoặc lọc sản phẩm
   - Click "Thêm vào giỏ hàng"
   - Vào giỏ hàng → Thanh toán
   - Điền thông tin giao hàng
   - Chọn phương thức thanh toán
   - Xác nhận đơn hàng

4. **Theo dõi đơn hàng**
   - Menu → "Đơn hàng"
   - Xem trạng thái: Processing, Shipped, Delivered
   - Hủy đơn (nếu còn Processing)

5. **Đánh giá sản phẩm**
   - Vào chi tiết sản phẩm
   - Tab "Đánh giá"
   - Chọn số sao & viết nhận xét

---

## 📊 Database Schema

### Bảng chính

#### `users` - Tài khoản người dùng
```sql
userID (PK), username, password, email, role, created_at
```

#### `products` - Sản phẩm
```sql
productCode (PK), productName, productLine, productScale,
productVendor, productDescription, quantityInStock, 
buyPrice, MSRP
```

#### `cart` - Giỏ hàng (session-based)
```sql
cartID (PK), sessionID, productCode (FK), quantity, added_at
```

#### `orders` - Đơn hàng
```sql
orderNumber (PK), orderDate, requiredDate, shippedDate,
status, comments, customerNumber (FK)
```

#### `orderdetails` - Chi tiết đơn hàng
```sql
orderNumber (FK), productCode (FK), quantityOrdered,
priceEach, orderLineNumber
```

#### `payments` - Thanh toán
```sql
customerNumber (FK), checkNumber (PK), paymentDate, amount
```

#### `reviews` - Đánh giá sản phẩm
```sql
reviewID (PK), productCode (FK), userID (FK), 
rating, comment, created_at
```

---

## 🎨 Giao diện

- **Responsive**: Hoạt động tốt trên mobile, tablet, desktop
- **Modern UI**: Bootstrap 5 với custom CSS
- **Icons**: Font Awesome 6.4
- **Animations**: Smooth transitions & hover effects
- **Toast Notifications**: Real-time feedback

---

## 🔐 Bảo mật

- ✅ Password hashing với `password_hash()` (bcrypt)
- ✅ Prepared statements chống SQL injection
- ✅ Session timeout (30 phút)
- ✅ Input validation & sanitization
- ✅ Role-based access control
- ✅ HTTPS ready
---

## 📝 TODO

- [ ] Hoàn thiện Employee dashboard
- [ ] Hoàn thiện Admin panel
- [ ] Email notifications
- [ ] Payment gateway integration
- [ ] Export reports (PDF/Excel)
- [ ] Multi-language support
- [ ] REST API

---

## 👨‍💻 Phát triển

Để phát triển thêm tính năng:

1. Tạo file PHP mới trong folder tương ứng
2. Include `config/database.php` và `config/session.php`
3. Include `includes/header.php` và `includes/footer.php`
4. Sử dụng prepared statements cho queries
5. Test trên local trước khi deploy

---

## 📞 Liên hệ

- **Email**: support@minishop.com
- **Website**: http://localhost/mini_shop/
---
**Developed with ❤️ using PHP & MySQL**
