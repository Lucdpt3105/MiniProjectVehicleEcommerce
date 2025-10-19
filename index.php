<?php
require_once 'config/database.php';
require_once 'config/session.php';

$pageTitle = 'Mini Shop - Chọn vai trò';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
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
        .role-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 800px;
            width: 100%;
        }
        .role-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            height: 100%;
        }
        .role-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
        }
        .role-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .customer-icon { color: #28a745; }
        .employee-icon { color: #ffc107; }
        .admin-icon { color: #dc3545; }
        .btn-role {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: bold;
            border-radius: 25px;
        }
        .btn-role:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="role-container">
        <div class="text-center mb-5">
            <h1 class="display-4 mb-3">
                <i class="fas fa-store"></i> Mini Shop
            </h1>
            <p class="lead text-muted">Chọn vai trò của bạn để tiếp tục</p>
        </div>
        
        <div class="row g-4">
            <!-- Customer Role -->
            <div class="col-md-4">
                <div class="role-card" onclick="selectRole('customer')">
                    <div class="role-icon customer-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 class="mb-3">Khách hàng</h3>
                    <p class="text-muted mb-4">
                        Mua sắm sản phẩm, quản lý giỏ hàng, theo dõi đơn hàng
                        <br><small class="text-info"><i class="fas fa-info-circle"></i> Có thể đăng ký tài khoản</small>
                    </p>
                    <ul class="list-unstyled text-start">
                        <li><i class="fas fa-check text-success"></i> Xem sản phẩm</li>
                        <li><i class="fas fa-check text-success"></i> Mua hàng</li>
                        <li><i class="fas fa-check text-success"></i> Theo dõi đơn hàng</li>
                        <li><i class="fas fa-check text-success"></i> Đánh giá sản phẩm</li>
                    </ul>
                </div>
            </div>
            
            <!-- Employee Role -->
            <div class="col-md-4">
                <div class="role-card" onclick="selectRole('employee')">
                    <div class="role-icon employee-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3 class="mb-3">Nhân viên</h3>
                    <p class="text-muted mb-4">
                        Quản lý đơn hàng, khách hàng, sản phẩm và thanh toán
                        <br><small class="text-success"><i class="fas fa-unlock"></i> Truy cập trực tiếp</small>
                    </p>
                    <ul class="list-unstyled text-start">
                        <li><i class="fas fa-check text-success"></i> Quản lý đơn hàng</li>
                        <li><i class="fas fa-check text-success"></i> Quản lý khách hàng</li>
                        <li><i class="fas fa-check text-success"></i> Quản lý sản phẩm</li>
                        <li><i class="fas fa-check text-success"></i> Quản lý thanh toán</li>
                    </ul>
                </div>
            </div>
            
            <!-- Admin Role -->
            <div class="col-md-4">
                <div class="role-card" onclick="selectRole('admin')">
                    <div class="role-icon admin-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h3 class="mb-3">Quản trị viên</h3>
                    <p class="text-muted mb-4">
                        Quản lý toàn bộ hệ thống, nhân viên, báo cáo và cấu hình
                        <br><small class="text-success"><i class="fas fa-unlock"></i> Truy cập trực tiếp</small>
                    </p>
                    <ul class="list-unstyled text-start">
                        <li><i class="fas fa-check text-success"></i> Quản lý nhân viên</li>
                        <li><i class="fas fa-check text-success"></i> Quản lý văn phòng</li>
                        <li><i class="fas fa-check text-success"></i> Báo cáo thống kê</li>
                        <li><i class="fas fa-check text-success"></i> Cấu hình hệ thống</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> Hướng dẫn truy cập</h5>
                <p class="mb-2">
                    <strong>Admin & Employee:</strong> Click trực tiếp vào vai trò để truy cập panel (không cần đăng nhập)
                </p>
                <p class="mb-0">
                    <strong>Customer:</strong> Click vào vai trò để xem shop, hoặc đăng ký tài khoản để mua hàng
                </p>
            </div>
            
            <p class="text-muted">
                Đã có tài khoản Customer? 
                <a href="login.php" class="text-decoration-none fw-bold">Đăng nhập ngay</a>
            </p>
            <p class="text-muted">
                Chưa có tài khoản Customer? 
                <a href="register.php" class="text-decoration-none fw-bold">Đăng ký ngay</a>
            </p>
        </div>
    </div>
    
    <script>
        function selectRole(role) {
            if (role === 'customer') {
                window.location.href = 'customer/home.php';
            } else if (role === 'employee') {
                window.location.href = 'employee/dashboard.php';
            } else if (role === 'admin') {
                window.location.href = 'admin/dashboard.php';
            }
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

