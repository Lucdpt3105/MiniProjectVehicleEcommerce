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

// Get products with low stock
$lowStockProducts = $conn->query("SELECT * FROM products WHERE quantityInStock < 20 ORDER BY quantityInStock ASC");

// Get all products for management
$products = $conn->query("SELECT p.*, pl.productLine FROM products p 
                         LEFT JOIN productlines pl ON p.productLine = pl.productLine 
                         ORDER BY p.productName");

$pageTitle = 'Quản lý sản phẩm';
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
                    <a class="nav-link active" href="products.php">
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
                    <i class="fas fa-box"></i> Quản lý sản phẩm
                </h2>
                
                <!-- Low Stock Alert -->
                <?php if ($lowStockProducts->num_rows > 0): ?>
                <div class="alert alert-warning mb-4">
                    <h5><i class="fas fa-exclamation-triangle"></i> Cảnh báo sắp hết hàng</h5>
                    <div class="row">
                        <?php 
                        $lowStockProducts->data_seek(0);
                        while ($product = $lowStockProducts->fetch_assoc()): 
                        ?>
                            <div class="col-md-3 mb-2">
                                <small>
                                    <strong><?php echo htmlspecialchars($product['productName']); ?></strong><br>
                                    Chỉ còn: <span class="badge bg-danger"><?php echo $product['quantityInStock']; ?></span>
                                </small>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh sách sản phẩm</h5>
                        <button class="btn btn-primary" onclick="addProduct()">
                            <i class="fas fa-plus"></i> Thêm sản phẩm
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã SP</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Dòng SP</th>
                                        <th>Giá nhập</th>
                                        <th>Giá bán</th>
                                        <th>Tồn kho</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($product = $products->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($product['productCode']); ?></td>
                                            <td><?php echo htmlspecialchars($product['productName']); ?></td>
                                            <td><?php echo htmlspecialchars($product['productLine']); ?></td>
                                            <td><?php echo number_format($product['buyPrice'], 2); ?> USD</td>
                                            <td><?php echo number_format($product['MSRP'], 2); ?> USD</td>
                                            <td>
                                                <span class="badge <?php echo $product['quantityInStock'] < 20 ? 'bg-danger' : 'bg-success'; ?>">
                                                    <?php echo $product['quantityInStock']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($product['quantityInStock'] > 0): ?>
                                                    <span class="badge bg-success">Còn hàng</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Hết hàng</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" 
                                                        onclick="editProduct('<?php echo $product['productCode']; ?>')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info" 
                                                        onclick="updateStock('<?php echo $product['productCode']; ?>')">
                                                    <i class="fas fa-warehouse"></i>
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
    
    <!-- Update Stock Modal -->
    <div class="modal fade" id="updateStockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật tồn kho</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="updateStockForm">
                        <input type="hidden" id="productCode" name="productCode">
                        <div class="mb-3">
                            <label class="form-label">Số lượng tồn kho mới</label>
                            <input type="number" class="form-control" name="quantity" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá nhập mới (USD)</label>
                            <input type="number" class="form-control" name="buyPrice" step="0.01" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá bán mới (USD)</label>
                            <input type="number" class="form-control" name="msrp" step="0.01" min="0">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="submitStockUpdate()">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addProduct() {
            alert('Chức năng thêm sản phẩm sẽ được triển khai');
        }
        
        function editProduct(productCode) {
            alert('Chức năng chỉnh sửa sản phẩm sẽ được triển khai');
        }
        
        function updateStock(productCode) {
            document.getElementById('productCode').value = productCode;
            new bootstrap.Modal(document.getElementById('updateStockModal')).show();
        }
        
        function submitStockUpdate() {
            const form = document.getElementById('updateStockForm');
            const formData = new FormData(form);
            
            fetch('ajax/update_product_stock.php', {
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
