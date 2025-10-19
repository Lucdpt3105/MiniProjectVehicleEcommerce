# 🔧 FIX: employee/customers.php - Lỗi dòng 131

## ❌ Lỗi ban đầu:

**Dòng 131**: Trying to access undefined array key 'email'

### Nguyên nhân:
Bảng `customers` trong database `classicmodels` **KHÔNG CÓ** cột `email`.  
Cột đúng là: `contactEmail` (không phải `email`)

---

## ✅ Những gì đã sửa:

### 1. **Sửa lỗi email field (Dòng 131)**
```php
// ❌ TRƯỚC (SAI):
<i class="fas fa-envelope"></i> <?php echo htmlspecialchars($customer['email']); ?>

// ✅ SAU (ĐÚNG):
<?php if (!empty($customer['contactEmail'])): ?>
    <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($customer['contactEmail']); ?>
<?php endif; ?>
```

### 2. **Thêm kiểm tra NULL cho phone**
```php
// Trước khi hiển thị phone, kiểm tra có dữ liệu không
<?php if (!empty($customer['phone'])): ?>
    <i class="fas fa-phone"></i> <?php echo htmlspecialchars($customer['phone']); ?><br>
<?php endif; ?>
```

### 3. **Cải thiện hiển thị tên khách hàng**
```php
// Xử lý trường hợp contactFirstName hoặc contactLastName NULL
<?php 
$fullName = trim(($customer['contactFirstName'] ?? '') . ' ' . ($customer['contactLastName'] ?? ''));
echo htmlspecialchars($fullName ?: 'N/A'); 
?>
```

### 4. **Cải thiện hiển thị địa chỉ**
```php
// Xử lý trường hợp addressLine1, addressLine2, city, country NULL
<?php 
$address = htmlspecialchars($customer['addressLine1'] ?? '');
if (!empty($customer['addressLine2'])) {
    $address .= '<br>' . htmlspecialchars($customer['addressLine2']);
}
echo $address ?: 'N/A';
?>
```

### 5. **Thêm NULL check cho totalOrders**
```php
// ❌ TRƯỚC:
<?php echo $customer['totalOrders']; ?>

// ✅ SAU:
<?php echo $customer['totalOrders'] ?? 0; ?>
```

---

## 📋 Cấu trúc bảng customers (tham khảo):

Các cột ĐÚNG trong bảng `customers`:
```
- customerNumber
- customerName
- contactFirstName
- contactLastName
- phone
- addressLine1
- addressLine2
- city
- state
- postalCode
- country
- salesRepEmployeeNumber
- creditLimit
- contactEmail  ← CỘT NÀY (KHÔNG PHẢI 'email')
```

---

## 🧪 Test để kiểm tra:

### Bước 1: Đăng nhập employee
```
URL: http://localhost/mini_shop/login.php
Username: employee1 (hoặc nhanvien)
Password: 123456
```

### Bước 2: Vào trang Customers
```
URL: http://localhost/mini_shop/employee/customers.php
hoặc click menu "Khách hàng của tôi"
```

### Bước 3: Kiểm tra
✅ Không còn lỗi dòng 131  
✅ Hiển thị đúng email khách hàng (nếu có)  
✅ Hiển thị đúng phone (nếu có)  
✅ Hiển thị "N/A" nếu thiếu thông tin  

---

## 🔍 Debug thêm (nếu vẫn lỗi):

### Kiểm tra cột trong database:
```sql
-- Chạy SQL này để xem cấu trúc bảng customers
DESCRIBE customers;

-- Hoặc kiểm tra dữ liệu mẫu
SELECT customerNumber, customerName, contactEmail, phone 
FROM customers 
LIMIT 5;
```

### Kiểm tra query có lỗi không:
Thêm debug vào đầu file (sau dòng 27):
```php
if (!$customers) {
    die("Query Error: " . $conn->error);
}

if ($customers->num_rows == 0) {
    echo "<div class='alert alert-warning'>Không có khách hàng nào được gán cho employee này.</div>";
}
```

---

## 💡 Lưu ý quan trọng:

### ⚠️ Vấn đề với employeeID = 1165
File đang hardcode:
```php
$employeeID = 1165;
```

**Vấn đề**: employeeID này có thể không tồn tại trong bảng `employees`.

**Giải pháp**: Nên lấy từ session user hiện tại:
```php
// Thay vì hardcode 1165, dùng:
$employeeID = $_SESSION['userID'] ?? 1;
```

**HOẶC** tạo mapping giữa `users.userID` và `employees.employeeNumber`:
```sql
-- Kiểm tra employee nào tồn tại
SELECT employeeNumber, firstName, lastName, email 
FROM employees 
LIMIT 10;

-- Sau đó update users table để mapping
UPDATE users 
SET employeeNumber = 1165  -- Chọn employeeNumber có thật
WHERE username = 'employee1';
```

---

## 📁 File đã sửa:

- ✏️ **employee/customers.php** - Sửa lỗi dòng 131 + cải thiện NULL handling

---

## 🎯 Kết quả:

✅ **Không còn lỗi "Undefined array key 'email'"**  
✅ **Hiển thị đúng contactEmail**  
✅ **An toàn với NULL values**  
✅ **Không crash khi thiếu dữ liệu**  

---

**Giờ bạn có thể reload trang employee/customers.php và không còn lỗi nữa!** 🎉
