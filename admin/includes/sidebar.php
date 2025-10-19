<div class="col-md-2 sidebar p-0">
    <div class="p-4">
        <h4 class="text-white mb-4">
            <i class="fas fa-cog"></i> Admin Panel
        </h4>
        <div class="text-center mb-4">
            <i class="fas fa-user-circle fa-3x mb-2"></i>
            <p class="mb-0"><?php echo htmlspecialchars(getUsername()); ?></p>
            <small>Administrator</small>
        </div>
    </div>
    
    <nav class="nav flex-column px-3">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
            <i class="fas fa-home"></i> Dashboard
        </a>
        
        <div class="text-white-50 mt-3 mb-2 px-3" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
            <strong>Quản lý bán hàng</strong>
        </div>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : ''; ?>" href="products.php">
            <i class="fas fa-box"></i> Sản phẩm
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : ''; ?>" href="orders.php">
            <i class="fas fa-receipt"></i> Đơn hàng
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'customers.php' ? 'active' : ''; ?>" href="customers.php">
            <i class="fas fa-users"></i> Khách hàng
        </a>
        
        <div class="text-white-50 mt-3 mb-2 px-3" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
            <strong>Quản lý hệ thống</strong>
        </div>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'employees.php' ? 'active' : ''; ?>" href="employees.php">
            <i class="fas fa-users-cog"></i> Nhân viên
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'offices.php' ? 'active' : ''; ?>" href="offices.php">
            <i class="fas fa-building"></i> Văn phòng
        </a>
        
        <div class="text-white-50 mt-3 mb-2 px-3" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
            <strong>Marketing</strong>
        </div>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'banners.php' ? 'active' : ''; ?>" href="banners.php">
            <i class="fas fa-image"></i> Banners
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'promotions.php' ? 'active' : ''; ?>" href="promotions.php">
            <i class="fas fa-gift"></i> Khuyến mãi
        </a>
        
        <div class="text-white-50 mt-3 mb-2 px-3" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
            <strong>Báo cáo</strong>
        </div>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : ''; ?>" href="reports.php">
            <i class="fas fa-chart-bar"></i> Thống kê & Báo cáo
        </a>
        
        <hr class="bg-white mt-4">
        <a class="nav-link" href="../customer/home.php">
            <i class="fas fa-store"></i> Về trang chủ
        </a>
        <a class="nav-link" href="../logout.php">
            <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </a>
    </nav>
</div>

<style>
    .sidebar {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 100;
    }
    .sidebar .nav-link {
        color: rgba(255,255,255,0.8);
        padding: 12px 20px;
        margin: 5px 0;
        border-radius: 8px;
        transition: all 0.3s;
    }
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
        background: rgba(255,255,255,0.2);
        color: white;
    }
    .main-content {
        margin-left: 16.66667%;
    }
</style>
