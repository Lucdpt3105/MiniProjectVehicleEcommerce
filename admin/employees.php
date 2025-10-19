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

// Get all employees
$employees = $conn->query("SELECT u.*, 
                          COUNT(c.customerNumber) as assigned_customers,
                          COUNT(o.orderNumber) as total_orders,
                          SUM(p.amount) as total_revenue
                          FROM users u
                          LEFT JOIN customers c ON u.userID = c.salesRepEmployeeNumber
                          LEFT JOIN orders o ON c.customerNumber = o.customerNumber
                          LEFT JOIN payments p ON c.customerNumber = p.customerNumber
                          WHERE u.role = 'employee'
                          GROUP BY u.userID
                          ORDER BY u.username");

$pageTitle = 'Quản lý nhân viên';
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
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a class="nav-link" href="products.php">
                        <i class="fas fa-box"></i> Sản phẩm
                    </a>
                    <a class="nav-link" href="orders.php">
                        <i class="fas fa-receipt"></i> Đơn hàng
                    </a>
                    <a class="nav-link active" href="employees.php">
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
                    <i class="fas fa-user-tie"></i> Quản lý nhân viên
                </h2>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh sách nhân viên</h5>
                        <button class="btn btn-primary" onclick="addEmployee()">
                            <i class="fas fa-plus"></i> Thêm nhân viên
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên đăng nhập</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Khách hàng</th>
                                        <th>Đơn hàng</th>
                                        <th>Doanh thu</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($employee = $employees->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $employee['userID']; ?></td>
                                            <td><?php echo htmlspecialchars($employee['username']); ?></td>
                                            <td><?php echo htmlspecialchars($employee['full_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($employee['email']); ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo $employee['assigned_customers']; ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success"><?php echo $employee['total_orders']; ?></span>
                                            </td>
                                            <td><?php echo number_format($employee['total_revenue'] ?? 0, 0); ?> USD</td>
                                            <td><?php echo date('d/m/Y', strtotime($employee['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" 
                                                        onclick="editEmployee(<?php echo $employee['userID']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info" 
                                                        onclick="viewEmployeeDetails(<?php echo $employee['userID']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="deleteEmployee(<?php echo $employee['userID']; ?>)">
                                                    <i class="fas fa-trash"></i>
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
    
    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm nhân viên mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addEmployeeForm">
                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Họ tên</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="submitAddEmployee()">Thêm</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addEmployee() {
            new bootstrap.Modal(document.getElementById('addEmployeeModal')).show();
        }
        
        function editEmployee(employeeID) {
            alert('Chức năng chỉnh sửa nhân viên sẽ được triển khai');
        }
        
        function viewEmployeeDetails(employeeID) {
            alert('Chi tiết nhân viên ID: ' + employeeID);
        }
        
        function deleteEmployee(employeeID) {
            if (confirm('Bạn có chắc chắn muốn xóa nhân viên này?')) {
                alert('Chức năng xóa nhân viên sẽ được triển khai');
            }
        }
        
        function submitAddEmployee() {
            const form = document.getElementById('addEmployeeForm');
            const formData = new FormData(form);
            
            fetch('ajax/add_employee.php', {
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