<?php
require_once '../config/database.php';
require_once '../config/session.php';

// Set default admin session if not logged in
if (!isLoggedIn()) {
    $_SESSION['userID'] = 1; // Default admin ID
    $_SESSION['username'] = 'admin';
    $_SESSION['email'] = 'admin@minishop.com';
    $_SESSION['role'] = 'admin';
    $_SESSION['LAST_ACTIVITY'] = time();
}

// Get statistics
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$totalCustomers = $conn->query("SELECT COUNT(*) as count FROM customers")->fetch_assoc()['count'];
$totalEmployees = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'employee'")->fetch_assoc()['count'];
$totalRevenue = $conn->query("SELECT SUM(amount) as total FROM payments")->fetch_assoc()['total'] ?? 0;

// Orders by status
$orderStats = $conn->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");

// Recent orders
$recentOrders = $conn->query("SELECT o.*, c.customerName,
                              (SELECT SUM(quantityOrdered * priceEach) FROM orderdetails WHERE orderNumber = o.orderNumber) as total
                              FROM orders o
                              JOIN customers c ON o.customerNumber = c.customerNumber
                              ORDER BY o.orderDate DESC
                              LIMIT 10");

// Low stock products
$lowStock = $conn->query("SELECT * FROM products WHERE quantityInStock < 20 ORDER BY quantityInStock ASC LIMIT 5");

// Top products
$topProducts = $conn->query("SELECT p.productName, SUM(od.quantityOrdered) as totalSold
                             FROM products p
                             JOIN orderdetails od ON p.productCode = od.productCode
                             GROUP BY p.productCode
                             ORDER BY totalSold DESC
                             LIMIT 5");

$pageTitle = 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Mini Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card.primary { border-left-color: #3498db; }
        .stat-card.success { border-left-color: #27ae60; }
        .stat-card.warning { border-left-color: #f39c12; }
        .stat-card.danger { border-left-color: #e74c3c; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-4">
                    <h4 class="mb-4">
                        <i class="fas fa-cog"></i> Admin Panel
                    </h4>
                    <p class="small mb-4">Xin chào, <strong><?php echo getUsername(); ?></strong></p>
                </div>
                
                <nav class="nav flex-column px-3">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a class="nav-link" href="products.php">
                        <i class="fas fa-box"></i> Sản phẩm
                    </a>
                    <a class="nav-link" href="orders.php">
                        <i class="fas fa-receipt"></i> Đơn hàng
                    </a>
                    <a class="nav-link" href="employees.php">
                        <i class="fas fa-user-tie"></i> Nhân viên
                    </a>
                    <a class="nav-link" href="customers.php">
                        <i class="fas fa-users"></i> Khách hàng
                    </a>
                    <a class="nav-link" href="offices.php">
                        <i class="fas fa-building"></i> Văn phòng
                    </a>
                    <a class="nav-link" href="banners.php">
                        <i class="fas fa-image"></i> Banner
                    </a>
                    <a class="nav-link" href="promotions.php">
                        <i class="fas fa-tags"></i> Khuyến mãi
                    </a>
                    <a class="nav-link" href="reports.php">
                        <i class="fas fa-chart-bar"></i> Báo cáo
                    </a>
                    <hr class="bg-white">
                    <a class="nav-link" href="../customer/home.php">
                        <i class="fas fa-store"></i> Xem Shop
                    </a>
                    <a class="nav-link" href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2 class="mb-4">
                    <i class="fas fa-chart-line"></i> Dashboard
                </h2>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">Tổng sản phẩm</p>
                                        <h3 class="mb-0"><?php echo number_format($totalProducts); ?></h3>
                                    </div>
                                    <div class="text-primary">
                                        <i class="fas fa-box fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">Tổng đơn hàng</p>
                                        <h3 class="mb-0"><?php echo number_format($totalOrders); ?></h3>
                                    </div>
                                    <div class="text-success">
                                        <i class="fas fa-receipt fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">Nhân viên</p>
                                        <h3 class="mb-0"><?php echo number_format($totalEmployees); ?></h3>
                                    </div>
                                    <div class="text-warning">
                                        <i class="fas fa-user-tie fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card danger">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">Doanh thu</p>
                                        <h3 class="mb-0"><?php echo number_format($totalRevenue, 0); ?> USD</h3>
                                    </div>
                                    <div class="text-danger">
                                        <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Orders by Status -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Đơn hàng theo trạng thái</h5>
                            </div>
                            <div class="card-body">
                                <?php while ($stat = $orderStats->fetch_assoc()): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><?php echo $stat['status']; ?></span>
                                        <strong><?php echo $stat['count']; ?></strong>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Top Products -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-trophy"></i> Sản phẩm bán chạy</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <?php while ($product = $topProducts->fetch_assoc()): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <small><?php echo htmlspecialchars($product['productName']); ?></small>
                                            <span class="badge bg-primary rounded-pill"><?php echo $product['totalSold']; ?></span>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Low Stock -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-white">
                                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Sắp hết hàng</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <?php while ($product = $lowStock->fetch_assoc()): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <small><?php echo htmlspecialchars($product['productName']); ?></small>
                                            <span class="badge bg-danger rounded-pill"><?php echo $product['quantityInStock']; ?></span>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <a href="products.php" class="btn btn-sm btn-warning w-100 mt-2">Xem tất cả</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> Đơn hàng gần đây</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $recentOrders->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $order['orderNumber']; ?></td>
                                            <td><?php echo htmlspecialchars($order['customerName']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($order['orderDate'])); ?></td>
                                            <td><?php echo number_format($order['total'], 0); ?> USD</td>
                                            <td>
                                                <span class="badge <?php 
                                                    echo match($order['status']) {
                                                        'Processing' => 'bg-warning',
                                                        'Shipped' => 'bg-info',
                                                        'Delivered' => 'bg-success',
                                                        'Cancelled' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                ?>">
                                                    <?php echo $order['status']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="order_detail.php?id=<?php echo $order['orderNumber']; ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="orders.php" class="btn btn-primary">Xem tất cả đơn hàng</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
