<?php
require_once '../config/database.php';
require_once '../config/session.php';

// Set default employee session if not logged in
if (!isLoggedIn()) {
    $_SESSION['userID'] = 2; // Default employee ID
    $_SESSION['username'] = 'employee';
    $_SESSION['email'] = 'employee@minishop.com';
    $_SESSION['role'] = 'employee';
    $_SESSION['LAST_ACTIVITY'] = time();
}

// Get employee info - use a real employee from employees table
$employeeID = 1165; // Use a real employee ID from employees table
$employee = $conn->query("SELECT * FROM employees WHERE employeeNumber = $employeeID")->fetch_assoc();

// Get statistics for employee
$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders o 
                             JOIN customers c ON o.customerNumber = c.customerNumber 
                             WHERE c.salesRepEmployeeNumber = $employeeID")->fetch_assoc()['count'];
$totalCustomers = $conn->query("SELECT COUNT(*) as count FROM customers WHERE salesRepEmployeeNumber = $employeeID")->fetch_assoc()['count'];
$totalRevenue = $conn->query("SELECT SUM(amount) as total FROM payments p 
                             JOIN customers c ON p.customerNumber = c.customerNumber 
                             WHERE c.salesRepEmployeeNumber = $employeeID")->fetch_assoc()['total'] ?? 0;

// Recent orders assigned to this employee
$recentOrders = $conn->query("SELECT o.*, c.customerName,
                              (SELECT SUM(quantityOrdered * priceEach) FROM orderdetails WHERE orderNumber = o.orderNumber) as total
                              FROM orders o
                              JOIN customers c ON o.customerNumber = c.customerNumber
                              WHERE c.salesRepEmployeeNumber = $employeeID
                              ORDER BY o.orderDate DESC
                              LIMIT 10");

// Customers assigned to this employee
$myCustomers = $conn->query("SELECT * FROM customers WHERE salesRepEmployeeNumber = $employeeID ORDER BY customerName LIMIT 5");

$pageTitle = 'Employee Dashboard';
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
            background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
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
                        <i class="fas fa-user-tie"></i> Employee Panel
                    </h4>
                    <p class="small mb-4">Xin chào, <strong><?php echo htmlspecialchars($employee['firstName'] . ' ' . $employee['lastName']); ?></strong></p>
                </div>
                
                <nav class="nav flex-column px-3">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a class="nav-link" href="orders.php">
                        <i class="fas fa-receipt"></i> Đơn hàng của tôi
                    </a>
                    <a class="nav-link" href="customers.php">
                        <i class="fas fa-users"></i> Khách hàng của tôi
                    </a>
                    <a class="nav-link" href="products.php">
                        <i class="fas fa-box"></i> Quản lý sản phẩm
                    </a>
                    <a class="nav-link" href="payments.php">
                        <i class="fas fa-credit-card"></i> Thanh toán
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
                    <i class="fas fa-chart-line"></i> Employee Dashboard
                </h2>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">Đơn hàng của tôi</p>
                                        <h3 class="mb-0"><?php echo number_format($totalOrders); ?></h3>
                                    </div>
                                    <div class="text-primary">
                                        <i class="fas fa-receipt fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">Khách hàng của tôi</p>
                                        <h3 class="mb-0"><?php echo number_format($totalCustomers); ?></h3>
                                    </div>
                                    <div class="text-success">
                                        <i class="fas fa-users fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">Doanh thu của tôi</p>
                                        <h3 class="mb-0"><?php echo number_format($totalRevenue, 0); ?> USD</h3>
                                    </div>
                                    <div class="text-warning">
                                        <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Recent Orders -->
                    <div class="col-md-6 mb-4">
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
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="orders.php" class="btn btn-primary">Xem tất cả đơn hàng</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- My Customers -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-users"></i> Khách hàng của tôi</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <?php while ($customer = $myCustomers->fetch_assoc()): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div>
                                                <strong><?php echo htmlspecialchars($customer['customerName']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($customer['city']); ?></small>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">
                                                <?php echo htmlspecialchars($customer['country']); ?>
                                            </span>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <a href="customers.php" class="btn btn-primary w-100 mt-2">Xem tất cả khách hàng</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
