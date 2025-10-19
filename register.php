<?php
require_once 'config/database.php';
require_once 'config/session.php';

$error = '';
$success = '';

if (isLoggedIn()) {
    header("Location: customer/home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ!';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự!';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp!';
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT userID FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Tên đăng nhập đã tồn tại!';
        } else {
            // Check if email exists
            $stmt = $conn->prepare("SELECT userID FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $error = 'Email đã được sử dụng!';
            } else {
                // Register user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'customer')");
                $stmt->bind_param("sss", $username, $email, $hashed_password);
                
                if ($stmt->execute()) {
                    header("Location: login.php?registered=success");
                    exit();
                } else {
                    $error = 'Có lỗi xảy ra. Vui lòng thử lại!';
                }
            }
        }
    }
}

$pageTitle = 'Đăng ký';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Mini Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            margin: 20px 0;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo i {
            font-size: 4rem;
            color: #667eea;
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: bold;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <i class="fas fa-user-plus"></i>
            <h2 class="mt-3">Đăng ký tài khoản</h2>
            <p class="text-muted">Tạo tài khoản mới tại Mini Shop</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-user"></i> Tên đăng nhập
                </label>
                <input type="text" name="username" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required autofocus>
                <small class="text-muted">Chỉ chứa chữ cái, số và dấu gạch dưới</small>
            </div>
            
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input type="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-lock"></i> Mật khẩu
                </label>
                <input type="password" name="password" class="form-control" required>
                <small class="text-muted">Ít nhất 6 ký tự</small>
            </div>
            
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-lock"></i> Xác nhận mật khẩu
                </label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-register">
                    <i class="fas fa-user-plus"></i> Đăng ký
                </button>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <p class="text-muted">
                Đã có tài khoản? 
                <a href="login.php" class="text-decoration-none fw-bold">Đăng nhập ngay</a>
            </p>
            <a href="customer/home.php" class="text-muted text-decoration-none">
                <i class="fas fa-arrow-left"></i> Quay lại trang chủ
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
