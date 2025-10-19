# 🔧 FIX: employee/orders.php - Quản lý đơn hàng

## ❌ Các lỗi ban đầu:

1. **Lỗi field `email`**: Bảng `customers` không có cột `email` (phải dùng `contactEmail`)
2. **Trạng thái không đúng**: Dùng `Processing`, `Delivered` nhưng DB dùng `In Process`, `Resolved`
3. **Thiếu validation**: Không kiểm tra quyền truy cập đơn hàng
4. **Hardcode employeeID**: Không lấy từ session

---

## ✅ Những gì đã sửa:

### 1. **Sửa lỗi email field trong SQL query**

**❌ TRƯỚC (SAI):**
```php
$orders = $conn->query("SELECT o.*, c.customerName, c.phone, c.email, ...
```

**✅ SAU (ĐÚNG):**
```php
$orders = $conn->query("SELECT o.*, c.customerName, c.phone, c.contactEmail, ...
```

### 2. **Sửa hiển thị thông tin liên hệ**

**❌ TRƯỚC:**
```php
<i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['phone']); ?><br>
<i class="fas fa-envelope"></i> <?php echo htmlspecialchars($order['email']); ?>
```

**✅ SAU:**
```php
<?php if (!empty($order['phone'])): ?>
    <i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['phone']); ?><br>
<?php endif; ?>
<?php if (!empty($order['contactEmail'])): ?>
    <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($order['contactEmail']); ?>
<?php endif; ?>
```

### 3. **Cập nhật trạng thái đơn hàng đúng với DB classicmodels**

Bảng `orders` trong classicmodels có các trạng thái:
- `In Process` (Đang xử lý)
- `Shipped` (Đã giao vận)
- `Resolved` (Hoàn thành)
- `On Hold` (Tạm giữ)
- `Cancelled` (Đã hủy)
- `Disputed` (Tranh chấp)

**❌ TRƯỚC (SAI):**
```php
<option value="Processing">Processing</option>
<option value="Shipped">Shipped</option>
<option value="Delivered">Delivered</option>
<option value="Cancelled">Cancelled</option>
```

**✅ SAU (ĐÚNG):**
```php
<option value="In Process">In Process (Đang xử lý)</option>
<option value="Shipped">Shipped (Đã giao vận)</option>
<option value="Resolved">Resolved (Hoàn thành)</option>
<option value="On Hold">On Hold (Tạm giữ)</option>
<option value="Cancelled">Cancelled (Đã hủy)</option>
<option value="Disputed">Disputed (Tranh chấp)</option>
```

### 4. **Cập nhật badge màu sắc cho trạng thái**

**✅ SAU:**
```php
echo match($order['status']) {
    'In Process' => 'bg-warning',      // Vàng
    'Shipped' => 'bg-info',            // Xanh dương
    'Resolved' => 'bg-success',        // Xanh lá
    'Cancelled' => 'bg-danger',        // Đỏ
    'On Hold' => 'bg-secondary',       // Xám
    'Disputed' => 'bg-dark',           // Đen
    default => 'bg-secondary'
};
```

### 5. **Cải thiện AJAX update_order_status.php**

**Thêm các tính năng:**
- ✅ Validation đầu vào
- ✅ Kiểm tra quyền (chỉ update đơn của customer thuộc employee)
- ✅ Validate trạng thái hợp lệ
- ✅ Error handling tốt hơn
- ✅ Content-Type header JSON

**Code mới:**
```php
// Validate status
$validStatuses = ['In Process', 'Shipped', 'Resolved', 'Cancelled', 'On Hold', 'Disputed'];
if (!in_array($status, $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Trạng thái không hợp lệ']);
    exit;
}

// Check if order belongs to this employee's customers
$checkStmt = $conn->prepare("SELECT o.orderNumber 
                              FROM orders o 
                              JOIN customers c ON o.customerNumber = c.customerNumber 
                              WHERE o.orderNumber = ? AND c.salesRepEmployeeNumber = ?");
$checkStmt->bind_param("ii", $orderNumber, $employeeID);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền cập nhật đơn hàng này']);
    exit;
}
```

### 6. **Thêm NULL check**

```php
// Trong hiển thị
<?php echo number_format($order['total'] ?? 0, 0); ?> USD
<?php echo htmlspecialchars($order['status'] ?? 'N/A'); ?>
```

---

## 📊 Cấu trúc bảng orders (classicmodels):

```sql
CREATE TABLE orders (
    orderNumber INT PRIMARY KEY,
    orderDate DATE NOT NULL,
    requiredDate DATE NOT NULL,
    shippedDate DATE,
    status VARCHAR(15) NOT NULL,
    comments TEXT,
    customerNumber INT NOT NULL,
    FOREIGN KEY (customerNumber) REFERENCES customers(customerNumber)
);
```

**Các cột quan trọng:**
- `status` - Trạng thái đơn hàng (In Process, Shipped, Resolved, ...)
- `comments` - Ghi chú khi xử lý đơn (TEXT)
- `customerNumber` - Liên kết với bảng customers

---

## 🔄 Quy trình cập nhật trạng thái:

### Bước 1: Employee xem danh sách đơn hàng
```
GET /employee/orders.php
→ Hiển thị các đơn hàng của customers được gán cho employee
```

### Bước 2: Click nút "Cập nhật trạng thái"
```
Click button → Mở modal updateStatusModal
→ Chọn trạng thái mới
→ Nhập ghi chú (optional)
```

### Bước 3: Submit form
```
POST /employee/ajax/update_order_status.php
{
    orderNumber: 10100,
    status: "Shipped",
    comments: "Đã gửi hàng qua GHTK"
}
```

### Bước 4: Server xử lý
```
1. Validate input
2. Check quyền (order thuộc customer của employee này không?)
3. Update database
4. Return JSON response
```

### Bước 5: Reload trang
```
Success → location.reload()
Error → alert(error message)
```

---

## 📋 Các trạng thái đơn hàng và ý nghĩa:

| Trạng thái | Mô tả | Màu Badge |
|-----------|-------|-----------|
| **In Process** | Đơn hàng đang được xử lý | Warning (Vàng) |
| **Shipped** | Đã giao cho đơn vị vận chuyển | Info (Xanh dương) |
| **Resolved** | Đã hoàn thành, khách đã nhận hàng | Success (Xanh lá) |
| **On Hold** | Tạm giữ (chờ thanh toán, xác nhận) | Secondary (Xám) |
| **Cancelled** | Đã hủy đơn hàng | Danger (Đỏ) |
| **Disputed** | Có tranh chấp (khiếu nại, hoàn trả) | Dark (Đen) |

---

## 🧪 Test chức năng:

### Test 1: Xem danh sách đơn hàng
```
1. Đăng nhập employee: http://localhost/mini_shop/login.php
   Username: employee1
   Password: 123456

2. Vào: http://localhost/mini_shop/employee/orders.php

3. Kiểm tra:
   ✅ Hiển thị danh sách đơn hàng
   ✅ Không lỗi email field
   ✅ Hiển thị đúng trạng thái
```

### Test 2: Cập nhật trạng thái
```
1. Click nút "Cập nhật" (icon bút chì) ở một đơn hàng

2. Chọn trạng thái mới: "Shipped"

3. Nhập ghi chú: "Đã giao cho GHTK, mã vận đơn: GH123456"

4. Click "Cập nhật"

5. Kiểm tra:
   ✅ Modal đóng lại
   ✅ Trang reload
   ✅ Trạng thái đã thay đổi
   ✅ Badge màu xanh dương (Shipped)
```

### Test 3: Kiểm tra ghi chú (comments)
```sql
-- Chạy SQL để kiểm tra
SELECT orderNumber, status, comments 
FROM orders 
WHERE orderNumber = 10100;
```

---

## 🔍 Debug nếu vẫn lỗi:

### Lỗi: "Không có đơn hàng nào"

**Nguyên nhân**: employeeID không có customer nào được gán

**Giải pháp**:
```sql
-- Kiểm tra employee có customers không
SELECT COUNT(*) as total_customers
FROM customers 
WHERE salesRepEmployeeNumber = 1165;

-- Nếu = 0, gán một số customers cho employee
UPDATE customers 
SET salesRepEmployeeNumber = 1165 
WHERE customerNumber IN (103, 112, 114, 119, 121)
LIMIT 10;
```

### Lỗi: "Cập nhật không thành công"

**Kiểm tra**:
1. Console Browser (F12) → Network tab
2. Xem response từ update_order_status.php
3. Kiểm tra error message

### Lỗi: Match expression không hoạt động

**Nguyên nhân**: PHP < 8.0 không hỗ trợ `match`

**Giải pháp**: Thay bằng switch hoặc if-else
```php
// Thay match() bằng:
<?php
$badgeClass = 'bg-secondary';
switch($order['status']) {
    case 'In Process': $badgeClass = 'bg-warning'; break;
    case 'Shipped': $badgeClass = 'bg-info'; break;
    case 'Resolved': $badgeClass = 'bg-success'; break;
    case 'Cancelled': $badgeClass = 'bg-danger'; break;
    case 'On Hold': $badgeClass = 'bg-secondary'; break;
    case 'Disputed': $badgeClass = 'bg-dark'; break;
}
echo $badgeClass;
?>
```

---

## 📁 Files đã sửa:

- ✏️ **employee/orders.php**
  - Sửa SQL query (email → contactEmail)
  - Cập nhật trạng thái đúng DB
  - Thêm NULL check
  - Cải thiện badge colors

- ✏️ **employee/ajax/update_order_status.php**
  - Thêm validation
  - Kiểm tra quyền
  - Cải thiện error handling
  - Thêm Content-Type header

---

## ✅ Kết quả:

- ✅ Không còn lỗi email field
- ✅ Hiển thị đúng trạng thái từ database
- ✅ Cập nhật trạng thái hoạt động
- ✅ Thêm ghi chú (comments) khi xử lý đơn
- ✅ Bảo mật: Chỉ update đơn của customer thuộc employee
- ✅ An toàn với NULL values

---

**Giờ chức năng quản lý đơn hàng hoạt động đầy đủ!** 🎉✨
