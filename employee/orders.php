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

$employeeID = 1165; // Use a real employee ID from employees table

// Get employee info
$employee = $conn->query("SELECT * FROM employees WHERE employeeNumber = $employeeID")->fetch_assoc();

// Get orders assigned to this employee
$orders = $conn->query("SELECT o.*, c.customerName, c.phone,
                       (SELECT SUM(quantityOrdered * priceEach) FROM orderdetails WHERE orderNumber = o.orderNumber) as total
                       FROM orders o
                       JOIN customers c ON o.customerNumber = c.customerNumber
                       WHERE c.salesRepEmployeeNumber = $employeeID
                       ORDER BY o.orderDate DESC");

$pageTitle = 'Đơn hàng của tôi';
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
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a class="nav-link active" href="orders.php">
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
                    <i class="fas fa-receipt"></i> Đơn hàng của tôi
                </h2>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Liên hệ</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $orders->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $order['orderNumber']; ?></td>
                                            <td><?php echo htmlspecialchars($order['customerName']); ?></td>
                                            <td>
                                                <small>
                                                    <?php if (!empty($order['phone'])): ?>
                                                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['phone']); ?><br>
                                                    <?php endif; ?>
                                                    <?php if (!empty($order['contactEmail'])): ?>
                                                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($order['contactEmail']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($order['orderDate'])); ?></td>
                                            <td><?php echo number_format($order['total'] ?? 0, 0); ?> USD</td>
                                            <td>
                                                <span class="badge <?php 
                                                    echo match($order['status']) {
                                                        'In Process' => 'bg-warning',
                                                        'Shipped' => 'bg-info',
                                                        'Resolved' => 'bg-success',
                                                        'Cancelled' => 'bg-danger',
                                                        'On Hold' => 'bg-secondary',
                                                        'Disputed' => 'bg-dark',
                                                        default => 'bg-secondary'
                                                    };
                                                ?>">
                                                    <?php echo htmlspecialchars($order['status'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="order_detail.php?id=<?php echo $order['orderNumber']; ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-warning" 
                                                        onclick="updateOrderStatus(<?php echo $order['orderNumber']; ?>)">
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
            </div>
        </div>
    </div>
    
    <!-- Update Order Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật trạng thái đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="updateStatusForm">
                        <input type="hidden" id="orderNumber" name="orderNumber">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái mới</label>
                            <select class="form-select" name="status" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="In Process">In Process (Đang xử lý)</option>
                                <option value="Shipped">Shipped (Đã giao vận)</option>
                                <option value="Resolved">Resolved (Hoàn thành)</option>
                                <option value="On Hold">On Hold (Tạm giữ)</option>
                                <option value="Cancelled">Cancelled (Đã hủy)</option>
                                <option value="Disputed">Disputed (Tranh chấp)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea class="form-control" name="comments" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="submitStatusUpdate()">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateOrderStatus(orderNumber) {
            document.getElementById('orderNumber').value = orderNumber;
            new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
        }
        
        function submitStatusUpdate() {
            const form = document.getElementById('updateStatusForm');
            const formData = new FormData(form);
            
            fetch('ajax/update_order_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => {
                alert('Có lỗi xảy ra: ' + error);
            });
        }
    </script>
</body>
</html>
