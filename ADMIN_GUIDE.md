# 🏢 ADMIN PANEL - HƯỚNG DẪN SỬ DỤNG

## 📋 Tổng quan

Admin Panel được thiết kế dành cho **Quản trị viên hệ thống** với đầy đủ quyền quản lý toàn bộ website Mini Shop.

---

## 🔐 Đăng nhập Admin

**URL**: `http://localhost/mini_shop/login.php`

**Tài khoản Admin mặc định:**
```
Username: admin
Password: password123
```

Sau khi đăng nhập thành công, bạn sẽ được chuyển đến **Admin Dashboard**.

---

## 📊 CÁC CHỨC NĂNG ADMIN

### 1. 🏠 DASHBOARD
**URL**: `admin/dashboard.php`

**Tính năng:**
- ✅ Thống kê tổng quan:
  - Tổng sản phẩm
  - Tổng đơn hàng
  - Tổng doanh thu (USD)
  - Tổng khách hàng
  
- ✅ Cảnh báo:
  - Đơn hàng đang chờ xử lý
  - Sản phẩm sắp hết hàng (< 10)
  
- ✅ Bảng thông tin:
  - 5 đơn hàng gần đây nhất
  - Top 5 sản phẩm bán chạy

---

### 2. 📦 QUẢN LÝ SẢN PHẨM
**URL**: `admin/products.php`

**Tính năng:**
- ✅ **Xem danh sách sản phẩm** với phân trang (20/trang)
- ✅ **Tìm kiếm** theo tên hoặc mô tả
- ✅ **Lọc** theo:
  - Dòng sản phẩm (Product Line)
  - Nhà cung cấp (Vendor)
  - Sản phẩm sắp hết hàng
  
- ✅ **Thêm sản phẩm mới:**
  - Mã sản phẩm (Product Code) - unique
  - Tên sản phẩm
  - Dòng sản phẩm (dropdown)
  - Nhà cung cấp
  - Tỷ lệ (Scale)
  - Số lượng tồn kho
  - Giá vốn (Buy Price)
  - Giá bán (MSRP)
  - Mô tả
  
- ✅ **Sửa sản phẩm:**
  - Cập nhật tất cả thông tin (trừ Product Code)
  - Modal form tiện lợi
  
- ✅ **Xóa sản phẩm:**
  - Xác nhận trước khi xóa
  - Không xóa được nếu sản phẩm có trong đơn hàng

**Hiển thị:**
- Badge màu đỏ nếu tồn kho < 10
- Badge màu xanh nếu tồn kho >= 10

---

### 3. 📋 QUẢN LÝ ĐỐN HÀNG
**URL**: `admin/orders.php`

**Tính năng:**
- ✅ **Xem danh sách đơn hàng** với phân trang
- ✅ **Lọc theo trạng thái:**
  - Processing (Đang xử lý) - Badge vàng
  - Shipped (Đang giao) - Badge xanh dương
  - Delivered (Đã giao) - Badge xanh lá
  - Cancelled (Đã hủy) - Badge đỏ
  
- ✅ **Tìm kiếm:**
  - Theo mã đơn hàng
  - Theo tên khách hàng
  - Theo người liên hệ
  
- ✅ **Lọc theo ngày:**
  - Từ ngày - Đến ngày
  
- ✅ **Xem chi tiết đơn hàng:**
  - Thông tin khách hàng đầy đủ
  - Danh sách sản phẩm trong đơn
  - Tổng tiền
  - Ngày đặt, ngày yêu cầu, ngày giao
  - Trạng thái và ghi chú
  
- ✅ **Cập nhật trạng thái đơn hàng:**
  - Chọn trạng thái mới
  - Thêm ghi chú
  - Cập nhật thời gian giao hàng

**Thống kê đầu trang:**
- Số lượng đơn theo từng trạng thái
- Tổng số đơn hàng

---

### 4. 👥 QUẢN LÝ KHÁCH HÀNG
**URL**: `admin/customers.php`

**Tính năng:**
- ✅ **Thống kê tổng quan:**
  - Tổng số khách hàng
  - Khách hàng hoạt động (6 tháng gần đây)
  - Tổng đơn hàng
  - Tổng doanh thu
  
- ✅ **Danh sách khách hàng:**
  - Tên công ty
  - Người liên hệ
  - Số điện thoại
  - Địa chỉ (city, country)
  - Sales Rep phụ trách
  - Số đơn hàng đã mua
  - Tổng chi tiêu
  - Ngày đặt hàng cuối cùng
  
- ✅ **Lọc & Tìm kiếm:**
  - Tìm theo tên, liên hệ, SĐT
  - Lọc theo quốc gia
  
- ✅ **Phân trang:** 20 khách hàng/trang

---

### 5. 👨‍💼 QUẢN LÝ NHÂN VIÊN
**URL**: `admin/employees.php`

**Tính năng:**
- ✅ **Xem danh sách nhân viên:**
  - ID nhân viên
  - Họ tên
  - Email (click để gửi mail)
  - Chức vụ (Job Title) với badge màu:
    - President: Đỏ
    - Manager: Vàng
    - VP: Xanh dương
    - Khác: Xanh lá
  - Văn phòng làm việc
  - Báo cáo cho (Manager)
  
- ✅ **Thêm nhân viên mới:**
  - Họ, Tên
  - Email
  - Extension (số nội bộ)
  - Chức vụ (dropdown): Sales Rep, Sales Manager, VP Sales, VP Marketing, President
  - Văn phòng (dropdown)
  - Báo cáo cho (dropdown danh sách nhân viên)
  
- ✅ **Sửa nhân viên:**
  - Cập nhật tất cả thông tin (trừ Employee Number)
  
- ✅ **Xóa nhân viên:**
  - Không xóa được nếu:
    - Nhân viên đang quản lý nhân viên khác
    - Nhân viên là Sales Rep của khách hàng
  
- ✅ **Lọc:**
  - Theo văn phòng
  - Theo chức vụ
  - Tìm kiếm theo tên, email

---

### 6. 🏢 QUẢN LÝ VĂN PHÒNG
**URL**: `admin/offices.php`

**Tính năng:**
- ✅ **Hiển thị dạng Card Grid:**
  - Tên thành phố
  - Mã văn phòng (Office Code)
  - Địa chỉ đầy đủ
  - Số điện thoại
  - Khu vực (Territory)
  - Số lượng nhân viên
  - Link xem danh sách nhân viên
  
- ✅ **Thêm văn phòng mới:**
  - Mã văn phòng (unique)
  - Thành phố
  - Quốc gia
  - Điện thoại
  - Địa chỉ 1, 2
  - Tỉnh/Bang (State)
  - Mã bưu điện
  - Khu vực (Territory: APAC, EMEA, NA, JAPAN)
  
- ✅ **Sửa văn phòng:**
  - Cập nhật tất cả thông tin (trừ Office Code)
  
- ✅ **Xóa văn phòng:**
  - Không xóa được nếu có nhân viên đang làm việc tại văn phòng

---

### 7. 🖼️ QUẢN LÝ BANNER
**URL**: `admin/banners.php`

**Tính năng:**
- ✅ **Hiển thị dạng Card Grid:**
  - Hình ảnh banner
  - Tiêu đề
  - Mô tả
  - Link liên kết
  - Trạng thái (Đang hiển thị / Đã ẩn)
  
- ✅ **Thêm banner mới:**
  - Tiêu đề
  - Mô tả
  - URL hình ảnh (link online)
  - Link liên kết (khi click banner)
  - Checkbox: Hiển thị ngay
  
- ✅ **Sửa banner:**
  - Cập nhật tất cả thông tin
  
- ✅ **Bật/Tắt hiển thị:**
  - Toggle nhanh bằng 1 click
  
- ✅ **Xóa banner:**
  - Xác nhận trước khi xóa

**Lưu ý:** Banner sẽ hiển thị trên trang chủ customer ở dạng Carousel.

---

### 8. 🎁 QUẢN LÝ KHUYẾN MÃI
**URL**: `admin/promotions.php`

**Tính năng:**
- ✅ **Xem danh sách khuyến mãi:**
  - Tiêu đề
  - Mô tả
  - % Giảm giá (Badge đỏ)
  - Ngày bắt đầu
  - Ngày kết thúc
  - Trạng thái tự động:
    - Đã tắt (nếu is_active = 0)
    - Sắp diễn ra (nếu chưa đến ngày bắt đầu)
    - Đang hoạt động (trong khoảng thời gian)
    - Đã hết hạn (sau ngày kết thúc)
  
- ✅ **Thêm khuyến mãi mới:**
  - Tiêu đề
  - Mô tả
  - % Giảm giá (0-100)
  - Ngày bắt đầu
  - Ngày kết thúc
  - Checkbox: Kích hoạt ngay
  
- ✅ **Sửa khuyến mãi:**
  - Cập nhật tất cả thông tin
  
- ✅ **Bật/Tắt khuyến mãi:**
  - Toggle nhanh
  
- ✅ **Xóa khuyến mãi:**
  - Xác nhận trước khi xóa

---

### 9. 📊 BÁO CÁO & THỐNG KÊ
**URL**: `admin/reports.php`

**Tính năng:**

#### 📈 Bộ lọc báo cáo:
- Từ ngày - Đến ngày
- Chu kỳ: Ngày / Tháng / Quý / Năm

#### 📊 Thống kê tổng quan (4 card):
1. **Tổng doanh thu** (USD)
2. **Tổng đơn hàng**
3. **Sản phẩm đã bán**
4. **Giá trị trung bình/đơn** (USD)

#### 📈 Biểu đồ doanh thu theo thời gian:
- Biểu đồ Line Chart (Chart.js)
- Hiển thị theo chu kỳ đã chọn
- Dữ liệu từ bảng `payments`

#### 🏆 Top 10 Sản phẩm bán chạy:
- Tên sản phẩm
- Dòng sản phẩm
- Số lượng đã bán
- Tổng doanh thu

#### ⚠️ Cảnh báo tồn kho thấp:
- 10 sản phẩm có tồn kho < 20
- Badge đỏ nếu < 10
- Badge vàng nếu 10-19

#### 👥 Hiệu suất nhân viên kinh doanh:
- Tên nhân viên
- Chức vụ
- Số khách hàng quản lý
- Số đơn hàng
- Tổng doanh số (USD)

#### 📦 Doanh thu theo dòng sản phẩm:
- Biểu đồ Doughnut Chart
- % doanh thu từng dòng sản phẩm
- Màu sắc phân biệt

---

## 🎨 GIAO DIỆN ADMIN

### Sidebar Menu (Gradient tím):
```
🏠 Dashboard

📦 QUẢN LÝ BÁN HÀNG
├── Sản phẩm
├── Đơn hàng
└── Khách hàng

🏢 QUẢN LÝ HỆ THỐNG
├── Nhân viên
└── Văn phòng

📢 MARKETING
├── Banners
└── Khuyến mãi

📊 BÁO CÁO
└── Thống kê & Báo cáo

─────────────
🏪 Về trang chủ
🚪 Đăng xuất
```

### Đặc điểm giao diện:
- ✨ **Sidebar cố định** bên trái với gradient tím
- ✨ **Bootstrap 5** responsive design
- ✨ **Font Awesome icons** đầy đủ
- ✨ **Màu sắc phân biệt:**
  - Primary (Xanh dương): Actions chính
  - Success (Xanh lá): Trạng thái thành công
  - Warning (Vàng): Cảnh báo, đang xử lý
  - Danger (Đỏ): Lỗi, xóa, hủy
  - Info (Xanh nhạt): Thông tin bổ sung
- ✨ **Modal forms** cho thêm/sửa
- ✨ **Alert messages** tự động ẩn sau 5s
- ✨ **Pagination** cho tất cả danh sách dài
- ✨ **Badges** hiển thị trạng thái trực quan

---

## 🔒 BẢO MẬT

1. **Xác thực:**
   - Tất cả trang admin yêu cầu đăng nhập
   - Kiểm tra role = 'admin'
   - Session timeout 30 phút

2. **Phân quyền:**
   - Function `requireAdmin()` kiểm tra quyền
   - Redirect về trang chủ nếu không phải admin

3. **Database:**
   - Sử dụng **Prepared Statements** cho tất cả queries
   - Escape output với `htmlspecialchars()`
   - Validate input trước khi xử lý

4. **CSRF Protection:**
   - POST forms từ cùng domain
   - Session-based authentication

---

## 📱 RESPONSIVE

Admin Panel hoàn toàn responsive:
- ✅ Desktop (> 1200px): Sidebar cố định, bảng full width
- ✅ Tablet (768-1199px): Sidebar thu gọn, layout 2 cột
- ✅ Mobile (< 768px): Sidebar ẩn (toggle button), layout 1 cột

---

## 🚀 PERFORMANCE

- ✅ **Pagination:** Giới hạn 20 records/trang
- ✅ **Lazy Loading:** Chỉ load dữ liệu khi cần
- ✅ **Optimized Queries:** JOIN thay vì N+1 queries
- ✅ **Caching:** Session cache cho user info
- ✅ **CDN:** Bootstrap, Font Awesome, Chart.js từ CDN

---

## 🐛 TROUBLESHOOTING

### Không vào được Admin Panel:
1. Kiểm tra đã đăng nhập với tài khoản admin chưa
2. Kiểm tra bảng `users` → role phải là 'admin'
3. Xóa cookies và đăng nhập lại

### Lỗi "Access Denied":
- Chỉ user có role = 'admin' mới truy cập được
- Đăng xuất và đăng nhập lại với tài khoản admin

### Không thêm được sản phẩm/nhân viên:
- Kiểm tra các trường required
- Product Code / Employee Number phải unique
- Office Code phải tồn tại trong bảng offices

### Biểu đồ không hiển thị:
- Kiểm tra Chart.js CDN đã load chưa
- Xem Console (F12) có lỗi JavaScript không
- Đảm bảo có dữ liệu trong khoảng thời gian đã chọn

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề, kiểm tra:
1. **PHP Error Log:** `C:\xampp\apache\logs\error.log`
2. **MySQL Log:** `C:\xampp\mysql\data\*.err`
3. **Browser Console:** F12 → Console tab
4. **Network Tab:** F12 → Network (kiểm tra AJAX requests)

---

**Chúc bạn quản lý hiệu quả!** 🎉
