# 👥 HƯỚNG DẪN CHO TEAM - MINI SHOP

## 🔄 CẬP NHẬT QUAN TRỌNG

Dự án đã chuyển sang sử dụng **Environment Variables (.env)** để quản lý cấu hình database.

---

## 🚀 SETUP CHO TEAMMATES MỚI

### Bước 1: Clone project

```bash
git clone https://github.com/Lucdpt3105/MiniProject.git
cd MiniProject
```

### Bước 2: Tạo file `.env`

**Cách 1: Copy từ template**
```bash
cp .env.example .env
```

**Cách 2: Tạo thủ công**

Tạo file `.env` trong thư mục gốc với nội dung:

```env
# Database Configuration
DB_HOST=localhost
DB_USER=root
DB_PASS=Luc3105dev#
DB_NAME=classicmodels

# Environment
ENV=development
DEV_MODE=true

# Session Configuration
SESSION_TIMEOUT=1800
```

### Bước 3: Setup Database

```bash
# 1. Tạo database
mysql -u root -pLuc3105dev# -e "CREATE DATABASE IF NOT EXISTS classicmodels;"

# 2. Import dữ liệu (nếu có file SQL)
mysql -u root -pLuc3105dev# classicmodels < database.sql
```

### Bước 4: Chạy project

```
http://localhost/mini_shop/
```

---

## ⚠️ LƯU Ý QUAN TRỌNG

### 🔒 KHÔNG BAO GIỜ commit file `.env`

File `.env` chứa thông tin nhạy cảm và đã được thêm vào `.gitignore`.

**Kiểm tra trước khi commit:**
```bash
git status
```

Nếu thấy `.env` trong danh sách → **DỪNG LẠI**, đừng commit!

### ✅ CÓ THỂ commit file `.env.example`

File này chỉ là template, không chứa password thật.

---

## 🐛 XỬ LÝ LỖI THƯỜNG GẶP

### Lỗi: "Access denied for user 'root'"

**Nguyên nhân**: Password trong `.env` không đúng

**Giải pháp**:
```env
# Kiểm tra lại password trong file .env
DB_PASS=Luc3105dev#
```

### Lỗi: ".env file not found"

**Nguyên nhân**: Chưa tạo file `.env`

**Giải pháp**:
```bash
cp .env.example .env
```

### Lỗi: "Call to undefined function EnvLoader::load()"

**Nguyên nhân**: PHP không tìm thấy class EnvLoader

**Giải pháp**:
```php
// Kiểm tra file config/env.php có tồn tại không
// Và đảm bảo path require_once đúng
```

### Website không kết nối database

**Kiểm tra:**
1. File `.env` đã tồn tại? → `ls .env`
2. Password đúng chưa? → Mở `.env` và kiểm tra
3. MySQL đang chạy? → Check XAMPP Control Panel
4. Database đã tạo? → `SHOW DATABASES;`

---

## 🔐 BẢO MẬT

### Thông tin hiện tại:

- **Môi trường**: Development/Local only
- **Database**: classicmodels (test data)
- **Password**: Được chia sẻ trong team
- **Trạng thái**: An toàn cho development

### Quy tắc team:

1. ✅ **File `.env`**: KHÔNG commit lên Git
2. ✅ **File `.env.example`**: CÓ THỂ commit (đã xóa password)
3. ✅ **Password development**: `Luc3105dev#` (chỉ dùng local)
4. ⚠️ **Password production**: SẼ KHÁC, không share công khai
5. ✅ **Thay đổi password**: Thông báo cả team qua Discord/Slack/...

---

## 📦 CẤU TRÚC FILE MỚI

```
mini_shop/
├── .env                    # ❌ KHÔNG commit (chứa password)
├── .env.example            # ✅ Commit được (template)
├── .gitignore              # ✅ Ignore .env
├── config/
│   ├── env.php            # ✅ Class đọc .env
│   ├── database.php       # ✅ Đã update dùng .env
│   └── session.php
├── db.php                 # ✅ Đã update dùng .env
└── ...
```

---

## 🆘 HỖ TRỢ

### Cần giúp đỡ?

1. **Đọc documentation**:
   - `README.md` - Hướng dẫn chung
   - `SECURITY_GUIDE.md` - Chi tiết về bảo mật
   - `ACTION_REQUIRED.md` - Checklist setup

2. **Hỏi team**:
   - Discord/Slack channel
   - Project lead: Lucdpt3105

3. **Check Git Issues**:
   - GitHub Issues của project

---

## 📊 WORKFLOW KHI LÀM VIỆC

### Khi bắt đầu ngày mới:

```bash
# 1. Pull code mới nhất
git pull origin main

# 2. Kiểm tra .env vẫn còn
ls .env

# 3. Nếu mất, copy lại từ template
cp .env.example .env

# 4. Update password
notepad .env
```

### Trước khi commit:

```bash
# 1. Check status
git status

# 2. Đảm bảo .env KHÔNG có trong danh sách
# Nếu có → DỪNG LẠI!

# 3. Add files
git add .

# 4. Commit
git commit -m "Your message"

# 5. Push
git push origin your-branch
```

### Khi thêm config mới vào .env:

```bash
# 1. Thêm vào .env (file thật)
echo "NEW_CONFIG=value" >> .env

# 2. Thêm vào .env.example (template)
echo "NEW_CONFIG=your_value_here" >> .env.example

# 3. Commit .env.example
git add .env.example
git commit -m "Add NEW_CONFIG to environment variables"
git push

# 4. Thông báo team update .env
# Post in Discord/Slack
```

---

## ✅ CHECKLIST SETUP MỚI

Khi teammate mới join project:

```
[ ] Đã clone repository
[ ] Đã cài XAMPP/PHP/MySQL
[ ] Đã tạo file .env từ .env.example
[ ] Đã điền password vào .env
[ ] Đã tạo database classicmodels
[ ] Đã import dữ liệu (nếu có)
[ ] Test website chạy được: http://localhost/mini_shop/
[ ] Đã đọc README.md
[ ] Đã đọc TEAM_GUIDE.md (file này)
[ ] Đã verify .env KHÔNG bị commit (git status)
```

---

## 🎓 TÀI LIỆU THAM KHẢO

- [README.md](README.md) - Tổng quan dự án
- [SECURITY_GUIDE.md](SECURITY_GUIDE.md) - Hướng dẫn bảo mật chi tiết
- [USER_GUIDE.md](USER_GUIDE.md) - Hướng dẫn sử dụng cho user
- [ADMIN_GUIDE.md](ADMIN_GUIDE.md) - Hướng dẫn cho admin

---

## 💡 TIPS & TRICKS

### Git best practices:

```bash
# Luôn check trước khi commit
git status

# Xem changes
git diff

# Commit từng file cụ thể
git add specific_file.php
git commit -m "Update specific file"

# Tạo branch cho feature mới
git checkout -b feature/your-feature-name
```

### Database best practices:

```sql
-- Backup trước khi thay đổi structure
mysqldump -u root -pLuc3105dev# classicmodels > backup_$(date +%Y%m%d).sql

-- Test query trước khi chạy trên data thật
SELECT * FROM table WHERE condition LIMIT 1;
```

---

**Cập nhật lần cuối**: 19/10/2025  
**Version**: 1.0  
**Maintainer**: Lucdpt3105

---

_Happy Coding! 🚀_
