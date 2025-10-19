# ⚠️ CẢNH BÁO BẢO MẬT - ĐỌC TRƯỚC KHI DEPLOY

## 🚨 AUTO-LOGIN ĐANG BẬT CHO ADMIN

File `config/session.php` hiện đang có **tự động đăng nhập admin** cho môi trường development.

### 📍 Vị trí code:

**File**: `config/session.php`  
**Dòng**: ~54-65 và ~27-38

```php
function requireAdmin() {
    // ⚠️ AUTO-LOGIN CODE - XÓA TRƯỚC KHI DEPLOY!
    if (!isLoggedIn()) {
        $_SESSION['userID'] = 1;
        $_SESSION['username'] = 'admin';
        $_SESSION['email'] = 'admin@minishop.com';
        $_SESSION['role'] = 'admin';
        $_SESSION['LAST_ACTIVITY'] = time();
    }
    // ...
}
```

---

## ⛔ TRƯỚC KHI DEPLOY LÊN PRODUCTION

### ✅ Checklist bảo mật:

- [ ] **Xóa hoặc comment out auto-login code** trong `requireAdmin()`
- [ ] **Xóa hoặc comment out auto-login code** trong `requireEmployee()`
- [ ] **Thay đổi default admin password** trong database
- [ ] **Kiểm tra tất cả các file admin** không có auto-login
- [ ] **Test login flow** đảm bảo user phải đăng nhập thực sự
- [ ] **Enable HTTPS** cho production server
- [ ] **Set session timeout** phù hợp (hiện tại: 30 phút)
- [ ] **Thay đổi session secret key** nếu có

---

## 🔒 Cách vô hiệu hóa AUTO-LOGIN

### Cách 1: Comment out (Khuyên dùng)

```php
function requireAdmin() {
    // DISABLED for production
    /*
    if (!isLoggedIn()) {
        $_SESSION['userID'] = 1;
        $_SESSION['username'] = 'admin';
        $_SESSION['email'] = 'admin@minishop.com';
        $_SESSION['role'] = 'admin';
        $_SESSION['LAST_ACTIVITY'] = time();
    }
    */
    
    if (!isAdmin()) {
        header("Location: /mini_shop/login.php"); // ← Đổi từ customer/home.php
        exit();
    }
}
```

### Cách 2: Dùng environment check

Tạo file `config/env.php`:
```php
<?php
// Development environment
define('ENV', 'production'); // Đổi thành 'production' khi deploy
define('DEV_MODE', ENV === 'development');
?>
```

Trong `session.php`:
```php
require_once __DIR__ . '/env.php';

function requireAdmin() {
    if (DEV_MODE && !isLoggedIn()) {
        $_SESSION['userID'] = 1;
        $_SESSION['username'] = 'admin';
        $_SESSION['email'] = 'admin@minishop.com';
        $_SESSION['role'] = 'admin';
        $_SESSION['LAST_ACTIVITY'] = time();
    }
    
    if (!isAdmin()) {
        header("Location: /mini_shop/login.php");
        exit();
    }
}
```

---

## 🎯 Các file cần kiểm tra

1. ✅ `config/session.php` - **Có auto-login**
2. ✅ `admin/dashboard.php` - **Có auto-login** (dòng 6-11)
3. ✅ Tất cả file trong `admin/` folder
4. ✅ Tất cả file trong `employee/` folder

---

## 📞 Support

Nếu có thắc mắc về bảo mật, hãy kiểm tra:
- `FIX_ADMIN_REDIRECT.md` - Giải thích chi tiết vấn đề và giải pháp
- PHP Security Best Practices
- OWASP Top 10

---

**⚠️ LƯU Ý: File này CHỈ dùng cho môi trường DEVELOPMENT LOCAL**

Khi deploy lên server thực, PHẢI disable auto-login!
