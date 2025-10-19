# ✅ ĐÃ SỬA LỖI PROMOTIONS.PHP

## 🐛 Vấn đề

Các dòng **96, 97, và 101** trong file `admin/promotions.php` gây ra lỗi:
- **Dòng 96**: `$promo['id']` - Undefined array key 'id'
- **Dòng 97**: `$promo['title']` - Undefined array key 'title'  
- **Dòng 101**: `$promo['discount_percentage']` - Undefined array key 'discount_percentage'

---

## 🔍 Nguyên nhân

**Mismatch giữa tên cột trong database và tên biến trong code!**

### Cấu trúc thực tế trong database:
```sql
+------------------+--------------+
| Field            | Type         |
+------------------+--------------+
| promoID          | int          |  ← Không phải 'id'
| promo_name       | varchar(100) |  ← Không phải 'title'
| description      | text         |
| discount_percent | decimal(5,2) |  ← Không phải 'discount_percentage'
| start_date       | date         |
| end_date         | date         |
| is_active        | tinyint(1)   |
+------------------+--------------+
```

### Code đang sử dụng (SAI):
```php
$promo['id']                   // ❌ Không tồn tại
$promo['title']                // ❌ Không tồn tại
$promo['discount_percentage']  // ❌ Không tồn tại
```

### Code phải sử dụng (ĐÚNG):
```php
$promo['promoID']              // ✅ 
$promo['promo_name']           // ✅
$promo['discount_percent']     // ✅
```

---

## ✅ Giải pháp đã áp dụng

### 1. **Sửa SQL queries**

#### INSERT (Thêm mới):
```php
// Trước (SAI):
INSERT INTO promotions (title, description, discount_percentage, ...) 

// Sau (ĐÚNG):
INSERT INTO promotions (promo_name, description, discount_percent, ...)
```

#### UPDATE (Cập nhật):
```php
// Trước (SAI):
UPDATE promotions SET title = ?, discount_percentage = ? WHERE id = ?

// Sau (ĐÚNG):
UPDATE promotions SET promo_name = ?, discount_percent = ? WHERE promoID = ?
```

#### DELETE (Xóa):
```php
// Trước (SAI):
DELETE FROM promotions WHERE id = ?

// Sau (ĐÚNG):
DELETE FROM promotions WHERE promoID = ?
```

### 2. **Sửa hiển thị trong table**

```php
// Dòng 96 - ID
echo $promo['promoID'];              // ✅ (trước: $promo['id'])

// Dòng 97 - Tên khuyến mãi
echo $promo['promo_name'];           // ✅ (trước: $promo['title'])

// Dòng 98 - Mô tả (thêm null check)
echo $promo['description'] ?? '';    // ✅ (trước: $promo['description'])

// Dòng 101 - Phần trăm giảm giá
echo $promo['discount_percent'];     // ✅ (trước: $promo['discount_percentage'])
```

### 3. **Sửa JavaScript (Edit Modal)**

```javascript
// Trước (SAI):
document.getElementById('edit_id').value = promo.id;
document.getElementById('edit_title').value = promo.title;
document.getElementById('edit_discount_percentage').value = promo.discount_percentage;

// Sau (ĐÚNG):
document.getElementById('edit_id').value = promo.promoID;
document.getElementById('edit_title').value = promo.promo_name;
document.getElementById('edit_discount_percentage').value = promo.discount_percent;
```

---

## 📋 Các thay đổi chi tiết

### File: `admin/promotions.php`

| Dòng | Trước (SAI) | Sau (ĐÚNG) |
|------|-------------|------------|
| 12 | `title, description, discount_percentage` | `promo_name, description, discount_percent` |
| 18 | `SET title = ?, discount_percentage = ? WHERE id = ?` | `SET promo_name = ?, discount_percent = ? WHERE promoID = ?` |
| 24 | `WHERE id = ?` | `WHERE promoID = ?` |
| 29 | `WHERE id = ?` | `WHERE promoID = ?` |
| 96 | `$promo['id']` | `$promo['promoID']` |
| 97 | `$promo['title']` | `$promo['promo_name']` |
| 98 | `$promo['description']` | `$promo['description'] ?? ''` |
| 101 | `$promo['discount_percentage']` | `$promo['discount_percent']` |
| 118 | `value="<?php echo $promo['id']; ?>"` | `value="<?php echo $promo['promoID']; ?>"` |
| 126 | `value="<?php echo $promo['id']; ?>"` | `value="<?php echo $promo['promoID']; ?>"` |
| 263 | `promo.id` | `promo.promoID` |
| 264 | `promo.title` | `promo.promo_name` |
| 266 | `promo.discount_percentage` | `promo.discount_percent` |

---

## 🎯 Kết quả

✅ **Không còn lỗi "Undefined array key"**  
✅ **Có thể thêm khuyến mãi mới**  
✅ **Có thể sửa khuyến mãi**  
✅ **Có thể xóa khuyến mãi**  
✅ **Có thể bật/tắt trạng thái khuyến mãi**  
✅ **Hiển thị danh sách khuyến mãi đúng**

---

## 🧪 Cách kiểm tra

1. **Vào trang Quản lý Khuyến mãi**
   ```
   http://localhost/mini_shop/admin/promotions.php
   ```

2. **Test thêm mới**
   - Click "Thêm Khuyến mãi"
   - Điền thông tin
   - Submit → Phải thành công

3. **Test hiển thị**
   - Kiểm tra danh sách khuyến mãi
   - Các cột phải hiển thị đúng:
     - ID, Tiêu đề, Mô tả, Giảm giá, Ngày, Trạng thái

4. **Test chỉnh sửa**
   - Click nút Edit (màu vàng)
   - Modal phải hiện với data đúng
   - Sửa và save → Phải cập nhật thành công

5. **Test xóa**
   - Click nút Delete (màu đỏ)
   - Confirm → Phải xóa thành công

6. **Test toggle status**
   - Click nút Toggle (màu xanh)
   - Trạng thái phải thay đổi

---

## 📝 Database Schema

Để tham khảo, đây là schema của bảng `promotions`:

```sql
CREATE TABLE promotions (
    promoID INT AUTO_INCREMENT PRIMARY KEY,
    promo_name VARCHAR(100) NOT NULL,
    description TEXT,
    discount_percent DECIMAL(5,2) DEFAULT 0.00,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active TINYINT(1) DEFAULT 1
);
```

---

## ⚠️ Lưu ý

- Form input vẫn sử dụng name `title` và `discount_percentage` nhưng được map sang đúng column name trong SQL
- Điều này giúp không cần sửa các input field trong HTML
- Chỉ cần sửa phần SQL query và phần đọc data từ database

---

**Ngày sửa**: 19/10/2025  
**Trạng thái**: ✅ Hoàn thành  
**File sửa**: `admin/promotions.php`  
**Vấn đề**: Undefined array key do mismatch tên cột database  
**Giải pháp**: Sửa tất cả references để khớp với tên cột thực tế
