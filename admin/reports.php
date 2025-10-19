<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireLogin();
requireAdmin();

// Get date range
$startDate = $_GET['start'] ?? date('Y-01-01');
$endDate = $_GET['end'] ?? date('Y-m-d');
$period = $_GET['period'] ?? 'month';

// Revenue by period
$revenueQuery = "SELECT 
    DATE_FORMAT(p.paymentDate, " . match($period) {
        'day' => "'%Y-%m-%d'",
        'month' => "'%Y-%m'",
        'quarter' => "'%Y-Q%q'",
        'year' => "'%Y'",
        default => "'%Y-%m'"
    } . ") as period,
    SUM(p.amount) as total_revenue,
    COUNT(DISTINCT o.orderNumber) as order_count
    FROM payments p
    JOIN orders o ON p.customerNumber = o.customerNumber
    WHERE p.paymentDate BETWEEN ? AND ?
    GROUP BY period
    ORDER BY period DESC";

$stmt = $conn->prepare($revenueQuery);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$revenueData = $stmt->get_result();

// Top selling products
$topProducts = $conn->query("SELECT p.productCode, p.productName, p.productLine,
                            SUM(od.quantityOrdered) as total_sold,
                            SUM(od.quantityOrdered * od.priceEach) as total_revenue
                            FROM products p
                            JOIN orderdetails od ON p.productCode = od.productCode
                            JOIN orders o ON od.orderNumber = o.orderNumber
                            WHERE o.orderDate BETWEEN '$startDate' AND '$endDate'
                            GROUP BY p.productCode
                            ORDER BY total_sold DESC
                            LIMIT 10");

// Low stock products
$lowStock = $conn->query("SELECT productCode, productName, productLine, quantityInStock, MSRP
                         FROM products
                         WHERE quantityInStock < 20
                         ORDER BY quantityInStock ASC
                         LIMIT 10");

// Employee performance
$employeePerf = $conn->query("SELECT e.employeeNumber, 
                              CONCAT(e.firstName, ' ', e.lastName) as employeeName,
                              e.jobTitle,
                              COUNT(DISTINCT o.orderNumber) as total_orders,
                              COUNT(DISTINCT c.customerNumber) as total_customers,
                              COALESCE(SUM(p.amount), 0) as total_sales
                              FROM employees e
                              LEFT JOIN customers c ON e.employeeNumber = c.salesRepEmployeeNumber
                              LEFT JOIN orders o ON c.customerNumber = o.customerNumber AND o.orderDate BETWEEN '$startDate' AND '$endDate'
                              LEFT JOIN payments p ON o.customerNumber = p.customerNumber AND p.paymentDate BETWEEN '$startDate' AND '$endDate'
                              WHERE e.jobTitle LIKE '%Sales%'
                              GROUP BY e.employeeNumber
                              ORDER BY total_sales DESC
                              LIMIT 10");

// Product line performance
$productLinePerf = $conn->query("SELECT pl.productLine,
                                 COUNT(DISTINCT p.productCode) as product_count,
                                 SUM(od.quantityOrdered) as total_sold,
                                 SUM(od.quantityOrdered * od.priceEach) as total_revenue
                                 FROM productlines pl
                                 LEFT JOIN products p ON pl.productLine = p.productLine
                                 LEFT JOIN orderdetails od ON p.productCode = od.productCode
                                 LEFT JOIN orders o ON od.orderNumber = o.orderNumber
                                 WHERE o.orderDate BETWEEN '$startDate' AND '$endDate'
                                 GROUP BY pl.productLine
                                 ORDER BY total_revenue DESC");

// Summary statistics
$totalRevenue = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE paymentDate BETWEEN '$startDate' AND '$endDate'")->fetch_assoc()['total'];
$totalOrders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE orderDate BETWEEN '$startDate' AND '$endDate'")->fetch_assoc()['total'];
$totalProducts = $conn->query("SELECT SUM(quantityOrdered) as total FROM orderdetails od JOIN orders o ON od.orderNumber = o.orderNumber WHERE o.orderDate BETWEEN '$startDate' AND '$endDate'")->fetch_assoc()['total'];
$avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

$pageTitle = 'Báo cáo & Thống kê';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Mini Shop Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid p-4">
            <h2 class="mb-4"><i class="fas fa-chart-bar"></i> Báo cáo & Thống kê</h2>
            
            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="start" class="form-control" value="<?php echo $startDate; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="end" class="form-control" value="<?php echo $endDate; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Chu kỳ</label>
                            <select name="period" class="form-select">
                                <option value="day" <?php echo $period === 'day' ? 'selected' : ''; ?>>Theo ngày</option>
                                <option value="month" <?php echo $period === 'month' ? 'selected' : ''; ?>>Theo tháng</option>
                                <option value="quarter" <?php echo $period === 'quarter' ? 'selected' : ''; ?>>Theo quý</option>
                                <option value="year" <?php echo $period === 'year' ? 'selected' : ''; ?>>Theo năm</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Xem báo cáo</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6><i class="fas fa-dollar-sign"></i> Tổng doanh thu</h6>
                            <h3><?php echo number_format($totalRevenue, 0); ?> USD</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6><i class="fas fa-receipt"></i> Tổng đơn hàng</h6>
                            <h3><?php echo number_format($totalOrders); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6><i class="fas fa-box"></i> Sản phẩm đã bán</h6>
                            <h3><?php echo number_format($totalProducts); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6><i class="fas fa-chart-line"></i> Giá trị TB/Đơn</h6>
                            <h3><?php echo number_format($avgOrderValue, 0); ?> USD</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Revenue Chart -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-line"></i> Biểu đồ doanh thu theo thời gian</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <!-- Top Products -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-trophy"></i> Top 10 Sản phẩm bán chạy</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Sản phẩm</th>
                                            <th>Dòng SP</th>
                                            <th>Đã bán</th>
                                            <th>Doanh thu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $rank = 1;
                                        while ($product = $topProducts->fetch_assoc()): 
                                        ?>
                                            <tr>
                                                <td><?php echo $rank++; ?></td>
                                                <td>
                                                    <small><?php echo htmlspecialchars($product['productName']); ?></small>
                                                </td>
                                                <td><small><?php echo htmlspecialchars($product['productLine']); ?></small></td>
                                                <td><span class="badge bg-success"><?php echo $product['total_sold']; ?></span></td>
                                                <td><strong><?php echo number_format($product['total_revenue'], 0); ?> USD</strong></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Low Stock -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5><i class="fas fa-exclamation-triangle"></i> Cảnh báo tồn kho thấp</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mã SP</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Tồn kho</th>
                                            <th>Giá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($product = $lowStock->fetch_assoc()): ?>
                                            <tr>
                                                <td><code><?php echo $product['productCode']; ?></code></td>
                                                <td><small><?php echo htmlspecialchars($product['productName']); ?></small></td>
                                                <td>
                                                    <span class="badge <?php echo $product['quantityInStock'] < 10 ? 'bg-danger' : 'bg-warning'; ?>">
                                                        <?php echo $product['quantityInStock']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo number_format($product['MSRP'], 2); ?> USD</td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Employee Performance -->
            <div class="row mb-4">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-users"></i> Hiệu suất nhân viên kinh doanh</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nhân viên</th>
                                            <th>Chức vụ</th>
                                            <th>Khách hàng</th>
                                            <th>Đơn hàng</th>
                                            <th>Doanh số</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($emp = $employeePerf->fetch_assoc()): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($emp['employeeName']); ?></strong></td>
                                                <td><small><?php echo htmlspecialchars($emp['jobTitle']); ?></small></td>
                                                <td><span class="badge bg-info"><?php echo $emp['total_customers']; ?></span></td>
                                                <td><span class="badge bg-success"><?php echo $emp['total_orders']; ?></span></td>
                                                <td><strong><?php echo number_format($emp['total_sales'], 0); ?> USD</strong></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product Line Performance -->
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-list"></i> Doanh thu theo dòng sản phẩm</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="productLineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Revenue Chart
        <?php 
        $revenueData->data_seek(0);
        $periods = [];
        $revenues = [];
        while ($row = $revenueData->fetch_assoc()) {
            $periods[] = $row['period'];
            $revenues[] = $row['total_revenue'];
        }
        ?>
        
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_reverse($periods)); ?>,
                datasets: [{
                    label: 'Doanh thu (USD)',
                    data: <?php echo json_encode(array_reverse($revenues)); ?>,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Product Line Chart
        <?php 
        $productLinePerf->data_seek(0);
        $lines = [];
        $lineRevenues = [];
        while ($row = $productLinePerf->fetch_assoc()) {
            $lines[] = $row['productLine'];
            $lineRevenues[] = $row['total_revenue'];
        }
        ?>
        
        const productLineCtx = document.getElementById('productLineChart').getContext('2d');
        new Chart(productLineCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($lines); ?>,
                datasets: [{
                    data: <?php echo json_encode($lineRevenues); ?>,
                    backgroundColor: [
                        '#667eea', '#764ba2', '#f093fb', '#4facfe',
                        '#43e97b', '#fa709a', '#fee140', '#30cfd0'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
