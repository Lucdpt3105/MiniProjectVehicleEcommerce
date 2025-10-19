# 🔒 HƯỚNG DẪN BẢO MẬT - MINI SHOP

## ⚠️ QUAN TRỌNG: ĐÃ XÓA MẬT KHẨU KHỎI CODE

Dự án này đã được cập nhật để sử dụng **Environment Variables** (.env) thay vì hardcode mật khẩu.

---

## 🚨 Vấn đề trước đây

**TRƯỚC KHI SỬA:**
```php

```

Mật khẩu database xuất hiện ở:
- ❌ `config/database.php`
- ❌ `db.php`
- ❌ `README.md`

**Rủi ro:**
- 🔓 Bất kỳ ai xem GitHub đều thấy được mật khẩu
- 🔓 Hacker có thể truy cập vào database
- 🔓 Thông tin người dùng bị lộ
- 🔓 Vi phạm best practices về bảo mật

---

## ✅ Giải pháp đã áp dụng

### 1. Sử dụng Environment Variables (.env)

**File `.env`** (KHÔNG commit lên Git):
```env
DB_HOST=localhost
DB_USER=root

DB_NAME=classicmodels
```

**File `.env.example`** (Template - CÓ commit lên Git):
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=your_database_password_here
DB_NAME=classicmodels
```

### 2. Tạo EnvLoader class

File `config/env.php` để đọc file `.env`:
```php
class EnvLoader {
    public static function load($path) { ... }
    public static function get($key, $default = null) { ... }
}
```

### 3. Cập nhật config files

**`config/database.php`:**
```php
// ✅ ĐỌC TỪ .ENV - AN TOÀN!
require_once __DIR__ . '/env.php';
EnvLoader::load(__DIR__ . '/../.env');

define('DB_HOST', EnvLoader::get('DB_HOST', 'localhost'));
define('DB_USER', EnvLoader::get('DB_USER', 'root'));
define('DB_PASS', EnvLoader::get('DB_PASS', ''));
define('DB_NAME', EnvLoader::get('DB_NAME', 'classicmodels'));
```

### 4. Thêm `.gitignore`

```gitignore
# QUAN TRỌNG: Không commit file .env
.env
.env.local
.env.*.local
```

---

## 📋 Checklist Bảo mật

### ✅ Đã làm:
- [x] Tạo file `.env` chứa thông tin nhạy cảm
- [x] Tạo file `.env.example` làm template
- [x] Tạo `EnvLoader` class để đọc .env
- [x] Cập nhật `config/database.php` sử dụng env
- [x] Cập nhật `db.php` sử dụng env
- [x] Cập nhật `README.md` ẩn mật khẩu
- [x] Tạo `.gitignore` để ignore file .env
- [x] Tạo tài liệu hướng dẫn bảo mật

### 🔄 Cần làm thêm (quan trọng!):

#### 1. **Xóa mật khẩu khỏi Git History**

Mật khẩu đã từng được commit, vẫn còn trong history!

**Cách 1: Dùng BFG Repo Cleaner (Khuyên dùng)**
```bash
# Tải BFG: https://rtyley.github.io/bfg-repo-cleaner/
java -jar bfg.jar --replace-text passwords.txt mini_shop.git
cd mini_shop.git
git reflog expire --expire=now --all
git gc --prune=now --aggressive
git push --force
```

**Cách 2: Dùng git filter-branch**
```bash
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch config/database.php db.php" \
  --prune-empty --tag-name-filter cat -- --all

git push origin --force --all
```

**Cách 3: Đơn giản nhất - Tạo repo mới**
```bash
# 1. Xóa folder .git
rm -rf .git

# 2. Tạo repo mới
git init
git add .
git commit -m "Initial commit - without sensitive data"
git branch -M main
git remote add origin your-new-repo-url
git push -u origin main
```

#### 2. **Đổi mật khẩu database**

Vì mật khẩu cũ đã bị lộ trên GitHub:
```sql
-- Đổi mật khẩu MySQL
ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_secure_password';
FLUSH PRIVILEGES;
```

Sau đó cập nhật file `.env`:
```env
DB_PASS=new_secure_password
```

#### 3. **Review toàn bộ code**

Kiểm tra không còn thông tin nhạy cảm khác:
```bash
# Tìm các từ khóa nguy hiểm
grep -r "password" .
grep -r "secret" .
grep -r "key" .
grep -r "token" .
grep -r "api_key" .
```

---

## 🛡️ Best Practices Bảo mật

### 1. **Không bao giờ hardcode credentials**
```php
// ❌ NEVER DO THIS
$password = "my_secret_password";

// ✅ ALWAYS DO THIS
$password = EnvLoader::get('DB_PASS');
```

### 2. **Sử dụng .env cho mọi thông tin nhạy cảm**
- Database credentials
- API keys
- Secret tokens
- Email passwords
- Payment gateway credentials

### 3. **Luôn có .gitignore**
```gitignore
.env
.env.*
!.env.example
```

### 4. **Tách biệt môi trường**
```env
# .env.development
ENV=development
DB_PASS=dev_password

# .env.production
ENV=production
DB_PASS=super_secure_production_password
```

### 5. **Sử dụng HTTPS trong production**
- Không bao giờ truyền mật khẩu qua HTTP
- Sử dụng SSL certificate
- Force HTTPS redirect

### 6. **Hạn chế quyền truy cập**
```sql
-- Tạo user riêng cho app, không dùng root
CREATE USER 'minishop_app'@'localhost' IDENTIFIED BY 'secure_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON classicmodels.* TO 'minishop_app'@'localhost';
FLUSH PRIVILEGES;
```

---

## 🔍 Kiểm tra sau khi sửa

### 1. Test local
```bash
# Đảm bảo app vẫn chạy được
http://localhost/mini_shop/
```

### 2. Kiểm tra Git status
```bash
git status
# Đảm bảo .env KHÔNG xuất hiện trong danh sách
```

### 3. Kiểm tra .gitignore
```bash
git check-ignore .env
# Phải output: .env
```

### 4. Review GitHub repo
- Vào Settings → Security
- Kiểm tra không có secret scanning alerts
- Xem file history không còn mật khẩu

---

## 📞 Nếu mật khẩu bị lộ

### Hành động ngay lập tức:

1. **ĐỔI MẬT KHẨU NGAY**
   ```sql
   ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_password';
   ```

2. **Xóa token/key cũ**
   - Revoke API keys
   - Regenerate secrets
   - Update all services

3. **Kiểm tra logs**
   - Xem có access bất thường không
   - Check database logs
   - Monitor cho hoạt động suspicious

4. **Thông báo**
   - Nếu là production → thông báo users
   - Nếu có data breach → follow GDPR guidelines

---

## 📚 Tài liệu tham khảo

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [GitHub Security Best Practices](https://docs.github.com/en/code-security)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
- [12 Factor App - Config](https://12factor.net/config)

---

## ✅ Kết luận

- ✅ Mật khẩu đã được di chuyển vào file `.env`
- ✅ File `.env` được ignore bởi Git
- ✅ Code giờ an toàn để public trên GitHub
- ⚠️ CẦN đổi mật khẩu database vì đã từng bị lộ
- ⚠️ CẦN clean Git history để xóa mật khẩu cũ

---

**Ngày cập nhật**: 19/10/2025  
**Trạng thái**: ✅ Đã bảo mật cơ bản  
**Cần làm tiếp**: Đổi password + Clean Git history
