<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireLogin();
requireAdmin();

// Filters
$search = $_GET['search'] ?? '';
$country = $_GET['country'] ?? '';

$where = [];
$params = [];
$types = '';

if ($search) {
    $where[] = "(c.customerName LIKE ? OR c.contactFirstName LIKE ? OR c.contactLastName LIKE ? OR c.phone LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ssss';
}

if ($country) {
    $where[] = "c.country = ?";
    $params[] = $country;
    $types .= 's';
}

$whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get customers with stats
$query = "SELECT c.*,
          COUNT(DISTINCT o.orderNumber) as total_orders,
          COALESCE(SUM(od.quantityOrdered * od.priceEach), 0) as total_spent,
          MAX(o.orderDate) as last_order,
          CONCAT(e.firstName, ' ', e.lastName) as salesRep
          FROM customers c
          LEFT JOIN orders o ON c.customerNumber = o.customerNumber
          LEFT JOIN orderdetails od ON o.orderNumber = od.orderNumber
          LEFT JOIN employees e ON c.salesRepEmployeeNumber = e.employeeNumber
          $whereSQL
          GROUP BY c.customerNumber
          ORDER BY c.customerName
          LIMIT $perPage OFFSET $offset";

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $customers = $stmt->get_result();
} else {
    $customers = $conn->query($query);
}

// Count total
$countQuery = "SELECT COUNT(*) as total FROM customers c $whereSQL";
if (!empty($params)) {
    $countStmt = $conn->prepare($countQuery);
    if (!empty($types)) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $totalCustomers = $countStmt->get_result()->fetch_assoc()['total'];
} else {
    $totalCustomers = $conn->query($countQuery)->fetch_assoc()['total'];
}

$totalPages = ceil($totalCustomers / $perPage);

// Get countries for filter
$countries = $conn->query("SELECT DISTINCT country FROM customers ORDER BY country");

$pageTitle = 'Quản lý Khách hàng';
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
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid p-4">
            <h2 class="mb-4"><i class="fas fa-users"></i> Quản lý Khách hàng</h2>
            
            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6><i class="fas fa-users"></i> Tổng khách hàng</h6>
                            <h3><?php echo number_format($totalCustomers); ?></h3>
                        </div>
                    </div>
                </div>
                <?php
                $stats = $conn->query("SELECT 
                    COUNT(*) as active_customers,
                    SUM(total_orders) as all_orders,
                    SUM(total_revenue) as all_revenue
                    FROM (
                        SELECT c.customerNumber,
                        COUNT(o.orderNumber) as total_orders,
                        COALESCE(SUM(od.quantityOrdered * od.priceEach), 0) as total_revenue
                        FROM customers c
                        LEFT JOIN orders o ON c.customerNumber = o.customerNumber
                        LEFT JOIN orderdetails od ON o.orderNumber = od.orderNumber
                        WHERE o.orderDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                        GROUP BY c.customerNumber
                        HAVING total_orders > 0
                    ) as active")->fetch_assoc();
                ?>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6><i class="fas fa-user-check"></i> KH hoạt động (6 tháng)</h6>
                            <h3><?php echo number_format($stats['active_customers'] ?? 0); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6><i class="fas fa-receipt"></i> Tổng đơn hàng</h6>
                            <h3><?php echo number_format($stats['all_orders'] ?? 0); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6><i class="fas fa-dollar-sign"></i> Tổng doanh thu</h6>
                            <h3><?php echo number_format($stats['all_revenue'] ?? 0, 0); ?> USD</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Tìm tên, liên hệ, SĐT..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-4">
                            <select name="country" class="form-select">
                                <option value="">Tất cả quốc gia</option>
                                <?php while ($c = $countries->fetch_assoc()): ?>
                                    <option value="<?php echo $c['country']; ?>" <?php echo $country === $c['country'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['country']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Lọc</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Customers Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Liên hệ</th>
                                    <th>Địa chỉ</th>
                                    <th>Sales Rep</th>
                                    <th>Đơn hàng</th>
                                    <th>Tổng chi</th>
                                    <th>Đơn cuối</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($customer = $customers->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $customer['customerNumber']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($customer['customerName']); ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($customer['contactFirstName'] . ' ' . $customer['contactLastName']); ?><br>
                                            <small class="text-muted">
                                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($customer['phone']); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                <?php echo htmlspecialchars($customer['city'] . ', ' . $customer['country']); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($customer['salesRep'] ?? 'N/A'); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $customer['total_orders']; ?></span>
                                        </td>
                                        <td><strong><?php echo number_format($customer['total_spent'], 0); ?> USD</strong></td>
                                        <td>
                                            <?php 
                                            if ($customer['last_order']) {
                                                echo '<small>' . date('d/m/Y', strtotime($customer['last_order'])) . '</small>';
                                            } else {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $country ? '&country=' . urlencode($country) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
