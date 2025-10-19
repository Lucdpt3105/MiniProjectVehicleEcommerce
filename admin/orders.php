<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireLogin();
requireAdmin();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderNumber = (int)$_POST['orderNumber'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = ?, comments = ? WHERE orderNumber = ?");
    $stmt->bind_param("ssi", $status, $_POST['comments'], $orderNumber);
    
    if ($stmt->execute()) {
        $message = 'Cập nhật trạng thái đơn hàng thành công!';
    } else {
        $error = 'Lỗi: ' . $stmt->error;
    }
}

// Filters
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';

$where = [];
$params = [];
$types = '';

if ($status) {
    $where[] = "o.status = ?";
    $params[] = $status;
    $types .= 's';
}

if ($search) {
    $where[] = "(o.orderNumber LIKE ? OR c.customerName LIKE ? OR c.contactFirstName LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'sss';
}

if ($dateFrom) {
    $where[] = "o.orderDate >= ?";
    $params[] = $dateFrom;
    $types .= 's';
}

if ($dateTo) {
    $where[] = "o.orderDate <= ?";
    $params[] = $dateTo;
    $types .= 's';
}

$whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get orders
$query = "SELECT o.*, c.customerName, c.phone, c.contactFirstName,
          (SELECT SUM(quantityOrdered * priceEach) FROM orderdetails WHERE orderNumber = o.orderNumber) as total
          FROM orders o
          JOIN customers c ON o.customerNumber = c.customerNumber
          $whereSQL
          ORDER BY o.orderDate DESC
          LIMIT $perPage OFFSET $offset";

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $orders = $stmt->get_result();
} else {
    $orders = $conn->query($query);
}

// Count total
$countQuery = "SELECT COUNT(*) as total FROM orders o JOIN customers c ON o.customerNumber = c.customerNumber $whereSQL";
if (!empty($params)) {
    $countStmt = $conn->prepare($countQuery);
    if (!empty($types)) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $totalOrders = $countStmt->get_result()->fetch_assoc()['total'];
} else {
    $totalOrders = $conn->query($countQuery)->fetch_assoc()['total'];
}

$totalPages = ceil($totalOrders / $perPage);

// Get status counts
$statusCounts = $conn->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
$counts = [];
while ($row = $statusCounts->fetch_assoc()) {
    $counts[$row['status']] = $row['count'];
}

$pageTitle = 'Quản lý Đơn hàng';
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
            <?php if (isset($message)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <h2 class="mb-4"><i class="fas fa-receipt"></i> Quản lý Đơn hàng</h2>
            
            <!-- Status Badges -->
            <div class="mb-4">
                <a href="orders.php" class="btn btn-sm <?php echo !$status ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                    Tất cả (<?php echo $totalOrders; ?>)
                </a>
                <a href="orders.php?status=Processing" class="btn btn-sm <?php echo $status === 'Processing' ? 'btn-warning' : 'btn-outline-warning'; ?>">
                    <i class="fas fa-clock"></i> Đang xử lý (<?php echo $counts['Processing'] ?? 0; ?>)
                </a>
                <a href="orders.php?status=Shipped" class="btn btn-sm <?php echo $status === 'Shipped' ? 'btn-info' : 'btn-outline-info'; ?>">
                    <i class="fas fa-shipping-fast"></i> Đang giao (<?php echo $counts['Shipped'] ?? 0; ?>)
                </a>
                <a href="orders.php?status=Delivered" class="btn btn-sm <?php echo $status === 'Delivered' ? 'btn-success' : 'btn-outline-success'; ?>">
                    <i class="fas fa-check-circle"></i> Đã giao (<?php echo $counts['Delivered'] ?? 0; ?>)
                </a>
                <a href="orders.php?status=Cancelled" class="btn btn-sm <?php echo $status === 'Cancelled' ? 'btn-danger' : 'btn-outline-danger'; ?>">
                    <i class="fas fa-times-circle"></i> Đã hủy (<?php echo $counts['Cancelled'] ?? 0; ?>)
                </a>
            </div>
            
            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="<?php echo $dateFrom; ?>">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="<?php echo $dateTo; ?>">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Processing" <?php echo $status === 'Processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                                <option value="Shipped" <?php echo $status === 'Shipped' ? 'selected' : ''; ?>>Đang giao</option>
                                <option value="Delivered" <?php echo $status === 'Delivered' ? 'selected' : ''; ?>>Đã giao</option>
                                <option value="Cancelled" <?php echo $status === 'Cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Lọc</button>
                            <a href="orders.php" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Orders Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Ngày giao</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong>#<?php echo $order['orderNumber']; ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($order['customerName']); ?><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($order['contactFirstName']); ?></small>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($order['orderDate'])); ?></td>
                                        <td>
                                            <?php 
                                            if ($order['shippedDate']) {
                                                echo date('d/m/Y', strtotime($order['shippedDate']));
                                            } else {
                                                echo '<span class="text-muted">Chưa giao</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><strong><?php echo number_format($order['total'], 0); ?> USD</strong></td>
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
                                            <button class="btn btn-sm btn-primary view-btn" data-order='<?php echo json_encode($order); ?>'>
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning edit-status-btn" data-order='<?php echo json_encode($order); ?>'>
                                                <i class="fas fa-edit"></i>
                                            </button>
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
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $status ? '&status=' . $status : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- View Order Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết đơn hàng <span id="view_orderNumber"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetails">
                    <div class="text-center">
                        <div class="spinner-border" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Status Modal -->
    <div class="modal fade" id="editStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="update_status" value="1">
                    <input type="hidden" name="orderNumber" id="edit_orderNumber">
                    <div class="modal-header">
                        <h5 class="modal-title">Cập nhật trạng thái đơn hàng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái *</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="Processing">Đang xử lý</option>
                                <option value="Shipped">Đang giao hàng</option>
                                <option value="Delivered">Đã giao</option>
                                <option value="Cancelled">Đã hủy</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="comments" id="edit_comments" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // View order details
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const order = JSON.parse(this.dataset.order);
                document.getElementById('view_orderNumber').textContent = '#' + order.orderNumber;
                
                // Fetch order details
                fetch(`order_detail_ajax.php?id=${order.orderNumber}`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('orderDetails').innerHTML = html;
                    });
                
                new bootstrap.Modal(document.getElementById('viewModal')).show();
            });
        });
        
        // Edit status
        document.querySelectorAll('.edit-status-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const order = JSON.parse(this.dataset.order);
                document.getElementById('edit_orderNumber').value = order.orderNumber;
                document.getElementById('edit_status').value = order.status;
                document.getElementById('edit_comments').value = order.comments || '';
                
                new bootstrap.Modal(document.getElementById('editStatusModal')).show();
            });
        });
        
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => alert.style.display = 'none');
        }, 5000);
    </script>
</body>
</html>
