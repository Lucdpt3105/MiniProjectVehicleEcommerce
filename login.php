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
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    } else {
        $stmt = $conn->prepare("SELECT userID, username, password, email, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['userID'] = $user['userID'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['LAST_ACTIVITY'] = time();
                
                // Generate cart session ID if not exists
                if (!isset($_SESSION['cart_sessionID'])) {
                    $_SESSION['cart_sessionID'] = session_id();
                }
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } elseif ($user['role'] === 'employee') {
                    header("Location: employee/dashboard.php");
                } else {
                    header("Location: customer/home.php");
                }
                exit();
            } else {
                $error = 'Sai tên đăng nhập hoặc mật khẩu!';
            }
        } else {
            $error = 'Sai tên đăng nhập hoặc mật khẩu!';
        }
    }
}

$pageTitle = 'Đăng nhập';
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
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 450px;
            width: 100%;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo i {
            font-size: 4rem;
            color: #667eea;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: bold;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-store"></i>
            <h2 class="mt-3">Mini Shop</h2>
            <p class="text-muted">Đăng nhập vào tài khoản</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['registered']) && $_GET['registered'] == 'success'): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Đăng ký thành công! Vui lòng đăng nhập.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['timeout'])): ?>
            <div class="alert alert-warning">
                <i class="fas fa-clock"></i> Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-user"></i> Tên đăng nhập
                </label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-lock"></i> Mật khẩu
                </label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-login">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <p class="text-muted">
                Chưa có tài khoản? 
                <a href="register.php" class="text-decoration-none fw-bold">Đăng ký ngay</a>
            </p>
            <a href="customer/home.php" class="text-muted text-decoration-none">
                <i class="fas fa-arrow-left"></i> Quay lại trang chủ
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
