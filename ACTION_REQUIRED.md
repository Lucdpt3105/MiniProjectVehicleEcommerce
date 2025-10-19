# 🚨 HÀNH ĐỘNG CẦN LÀM NGAY

## ✅ ĐÃ HOÀN THÀNH

1. ✅ Tạo file `.env` chứa mật khẩu
2. ✅ Tạo file `.env.example` làm template
3. ✅ Tạo `config/env.php` để đọc .env
4. ✅ Cập nhật `config/database.php` sử dụng .env
5. ✅ Cập nhật `db.php` sử dụng .env
6. ✅ Cập nhật `README.md` ẩn mật khẩu
7. ✅ Tạo `.gitignore` để ignore .env
8. ✅ Tạo tài liệu bảo mật

---

## ⚠️ PHẢI LÀM NGAY BÂY GIỜ

### 1. ĐỔI MẬT KHẨU DATABASE (QUAN TRỌNG!)

Vì mật khẩu `Luc3105dev#` đã bị lộ trên GitHub:

```bash
# Mở MySQL
mysql -u root -pLuc3105dev#

# Đổi password
ALTER USER 'root'@'localhost' IDENTIFIED BY 'NEW_SECURE_PASSWORD_HERE';
FLUSH PRIVILEGES;
exit;
```

Sau đó cập nhật file `.env`:
```env
DB_PASS=NEW_SECURE_PASSWORD_HERE
```

---

### 2. XÓA MẬT KHẨU KHỎI GIT HISTORY

Mật khẩu đã từng được commit vẫn còn trong Git history!

**Option A: Tạo repo mới (Đơn giản nhất - Khuyên dùng)**

```bash
# 1. Backup code hiện tại
cd c:\xampp\htdocs
cp -r mini_shop mini_shop_backup

# 2. Xóa Git history cũ
cd mini_shop
Remove-Item -Recurse -Force .git

# 3. Init Git mới
git init
git add .
git commit -m "Initial commit - secured version without passwords"

# 4. Tạo repo MỚI trên GitHub
# Đừng dùng repo cũ! Tạo repo mới hoàn toàn!

# 5. Push lên repo mới
git remote add origin https://github.com/Lucdpt3105/MiniProject-NEW.git
git branch -M main
git push -u origin main
```

**Option B: Clean history repo hiện tại (Phức tạp hơn)**

```bash
# Cài BFG Repo Cleaner
# Download: https://rtyley.github.io/bfg-repo-cleaner/

# Tạo file chứa text cần xóa
echo "Luc3105dev#" > passwords.txt

# Chạy BFG
java -jar bfg.jar --replace-text passwords.txt

# Clean up
git reflog expire --expire=now --all
git gc --prune=now --aggressive

# Force push
git push origin --force --all
```

---

### 3. COMMIT CHANGES MỚI

```bash
# Add tất cả file mới
git add .gitignore
git add .env.example
git add config/env.php
git add config/database.php
git add db.php
git add README.md
git add SECURITY_GUIDE.md
git add SECURITY_WARNING.md
git add FIX_ADMIN_REDIRECT.md
git add FIX_PROMOTIONS_ERROR.md
git add FIX_PRODUCT_IMAGES_LOG.md
git add ADD_FEATURED_IMAGES_LOG.md

# Đảm bảo .env KHÔNG được add
git status
# Phải thấy: .env (untracked) - KHÔNG có trong staged changes

# Commit
git commit -m "Security: Move sensitive data to .env file

- Add .env and .env.example for environment variables
- Create EnvLoader class to read .env file
- Update database.php to use environment variables
- Update db.php to use environment variables
- Add .gitignore to exclude .env file
- Update README.md with security instructions
- Add comprehensive security documentation
- Remove hardcoded passwords from all files

IMPORTANT: Please change your database password as the old one was exposed!"

# Push (chỉ khi đã làm bước 2)
git push origin main
```

---

### 4. KIỂM TRA

```bash
# 1. Kiểm tra .env không được track
git ls-files | grep ".env"
# Không được có .env (chỉ có .env.example là OK)

# 2. Kiểm tra app vẫn chạy
# Mở browser: http://localhost/mini_shop/

# 3. Kiểm tra GitHub
# Vào repo → tìm kiếm "Luc3105dev#"
# Không được tìm thấy trong current code!
```

---

## 📋 CHECKLIST

```
TRƯỚC KHI PUSH LÊN GITHUB:

[ ] Đã đổi mật khẩu database
[ ] Đã cập nhật .env với password mới
[ ] Đã test app vẫn chạy được với password mới
[ ] Đã xóa Git history cũ HOẶC tạo repo mới
[ ] Đã verify .env KHÔNG có trong git status
[ ] Đã verify .gitignore hoạt động (git check-ignore .env)
[ ] Đã đọc SECURITY_GUIDE.md
[ ] Đã commit tất cả changes
[ ] ĐÃ SẴN SÀNG PUSH!
```

---

## 🆘 NẾU GẶP LỖI

### Lỗi: "Call to undefined function EnvLoader::load()"

```php
// Đảm bảo file env.php tồn tại
ls config/env.php

// Kiểm tra require_once đúng path
require_once __DIR__ . '/config/env.php';
```

### Lỗi: ".env file not found"

```bash
# Tạo file .env từ template
cp .env.example .env

# Cập nhật password
notepad .env
```

### Lỗi: "Access denied for user 'root'"

```bash
# Password trong .env chưa đúng
# Mở .env và sửa lại DB_PASS
```

---

## 📞 LIÊN HỆ

Nếu có vấn đề, xem:
- [SECURITY_GUIDE.md](SECURITY_GUIDE.md) - Hướng dẫn chi tiết
- [SECURITY_WARNING.md](SECURITY_WARNING.md) - Cảnh báo auto-login admin

---

**CẬP NHẬT**: 19/10/2025
**TRẠNG THÁI**: ⚠️ CẦN HÀNH ĐỘNG NGAY!
