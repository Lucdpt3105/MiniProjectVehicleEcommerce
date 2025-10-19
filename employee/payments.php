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

// Get payments for customers assigned to this employee
$payments = $conn->query("SELECT p.*, c.customerName, c.customerNumber
                         FROM payments p
                         JOIN customers c ON p.customerNumber = c.customerNumber
                         WHERE c.salesRepEmployeeNumber = $employeeID
                         ORDER BY p.paymentDate DESC");

$pageTitle = 'Quản lý thanh toán';
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
                    <a class="nav-link" href="orders.php">
                        <i class="fas fa-receipt"></i> Đơn hàng của tôi
                    </a>
                    <a class="nav-link" href="customers.php">
                        <i class="fas fa-users"></i> Khách hàng của tôi
                    </a>
                    <a class="nav-link" href="products.php">
                        <i class="fas fa-box"></i> Quản lý sản phẩm
                    </a>
                    <a class="nav-link active" href="payments.php">
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
                    <i class="fas fa-credit-card"></i> Quản lý thanh toán
                </h2>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã thanh toán</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày thanh toán</th>
                                        <th>Số tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($payment = $payments->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $payment['customerNumber'] . '-' . $payment['checkNumber']; ?></td>
                                            <td><?php echo htmlspecialchars($payment['customerName']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($payment['paymentDate'])); ?></td>
                                            <td><?php echo number_format($payment['amount'], 2); ?> USD</td>
                                            <td>
                                                <span class="badge bg-success">Đã thanh toán</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" 
                                                        onclick="viewPaymentDetails(<?php echo $payment['customerNumber']; ?>, '<?php echo $payment['checkNumber']; ?>')">
                                                    <i class="fas fa-eye"></i>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewPaymentDetails(customerNumber, checkNumber) {
            alert('Chi tiết thanh toán: Khách hàng #' + customerNumber + ', Séc #' + checkNumber);
        }
    </script>
</body>
</html>
