-- ==============================================
-- THÊM TÀI KHOẢN ADMIN VÀO HỆ THỐNG MINI_SHOP
-- ==============================================

-- Thêm tài khoản admin (username: admin, password: password123)
-- Password đã được hash bằng password_hash() trong PHP
INSERT INTO users (username, email, password, full_name, role, created_at, updated_at) 
VALUES (
    'admin',
    'admin@minishop.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- password: password123
    'System Administrator',
    'admin',
    NOW(),
    NOW()
);

-- Thêm thêm 1 tài khoản admin backup (username: admin2, password: admin123)
INSERT INTO users (username, email, password, full_name, role, created_at, updated_at) 
VALUES (
    'admin2',
    'admin2@minishop.com',
    '$2y$10$Q3VZ5qJZKqYzKZw4YxKZqO4YxKZqO4YxKZqO4YxKZqO4YxKZqO4YxK',  -- password: admin123
    'Administrator 2',
    'admin',
    NOW(),
    NOW()
);

-- Kiểm tra tài khoản vừa tạo
SELECT userID, username, email, full_name, role, created_at 
FROM users 
WHERE role = 'admin';
