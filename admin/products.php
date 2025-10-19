<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireLogin();
requireAdmin();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $conn->prepare("INSERT INTO products (productCode, productName, productLine, productScale, productVendor, productDescription, quantityInStock, buyPrice, MSRP) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssids", $_POST['productCode'], $_POST['productName'], $_POST['productLine'], $_POST['productScale'], $_POST['productVendor'], $_POST['productDescription'], $_POST['quantityInStock'], $_POST['buyPrice'], $_POST['MSRP']);
                $stmt->execute();
                $message = 'Thêm sản phẩm thành công!';
                break;
                
            case 'update':
                $stmt = $conn->prepare("UPDATE products SET productName = ?, productLine = ?, productScale = ?, productVendor = ?, productDescription = ?, quantityInStock = ?, buyPrice = ?, MSRP = ? WHERE productCode = ?");
                $stmt->bind_param("sssssiids", $_POST['productName'], $_POST['productLine'], $_POST['productScale'], $_POST['productVendor'], $_POST['productDescription'], $_POST['quantityInStock'], $_POST['buyPrice'], $_POST['MSRP'], $_POST['productCode']);
                $stmt->execute();
                $message = 'Cập nhật sản phẩm thành công!';
                break;
                
            case 'delete':
                $stmt = $conn->prepare("DELETE FROM products WHERE productCode = ?");
                $stmt->bind_param("s", $_POST['productCode']);
                $stmt->execute();
                $message = 'Xóa sản phẩm thành công!';
                break;
        }
    }
}

// Get product lines
$productLines = $conn->query("SELECT DISTINCT productLine FROM productlines ORDER BY productLine");

// Filters
$where = [];
$params = [];
$types = '';

if (!empty($_GET['search'])) {
    $where[] = "(productName LIKE ? OR productDescription LIKE ?)";
    $searchTerm = '%' . $_GET['search'] . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
}

if (!empty($_GET['productLine'])) {
    $where[] = "productLine = ?";
    $params[] = $_GET['productLine'];
    $types .= 's';
}

if (isset($_GET['filter']) && $_GET['filter'] === 'low_stock') {
    $where[] = "quantityInStock < 10";
}

$whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$countQuery = "SELECT COUNT(*) as total FROM products $whereSQL";
if (!empty($params)) {
    $countStmt = $conn->prepare($countQuery);
    if (!empty($types)) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $totalProducts = $countStmt->get_result()->fetch_assoc()['total'];
} else {
    $totalProducts = $conn->query($countQuery)->fetch_assoc()['total'];
}

$totalPages = ceil($totalProducts / $perPage);

// Get products
$query = "SELECT * FROM products $whereSQL ORDER BY productName LIMIT $perPage OFFSET $offset";
if (!empty($params)) {
    $stmt = $conn->prepare($query);
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    $products = $conn->query($query);
}

$pageTitle = 'Quản lý sản phẩm';
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
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-box"></i> Quản lý sản phẩm</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Thêm sản phẩm
                </button>
            </div>
            
            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="<?php echo $_GET['search'] ?? ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="productLine" class="form-select">
                                <option value="">Tất cả dòng sản phẩm</option>
                                <?php while ($line = $productLines->fetch_assoc()): ?>
                                    <option value="<?php echo $line['productLine']; ?>" <?php echo (isset($_GET['productLine']) && $_GET['productLine'] === $line['productLine']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($line['productLine']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="filter" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="low_stock" <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'low_stock') ? 'selected' : ''; ?>>Sắp hết hàng</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Lọc</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Products Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã SP</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Dòng SP</th>
                                    <th>Nhà cung cấp</th>
                                    <th>Tồn kho</th>
                                    <th>Giá vốn</th>
                                    <th>Giá bán</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($product = $products->fetch_assoc()): ?>
                                    <tr>
                                        <td><code><?php echo $product['productCode']; ?></code></td>
                                        <td><?php echo htmlspecialchars($product['productName']); ?></td>
                                        <td><?php echo htmlspecialchars($product['productLine']); ?></td>
                                        <td><?php echo htmlspecialchars($product['productVendor']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $product['quantityInStock'] < 10 ? 'bg-danger' : 'bg-success'; ?>">
                                                <?php echo $product['quantityInStock']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo number_format($product['buyPrice'], 2); ?> USD</td>
                                        <td><?php echo number_format($product['MSRP'], 2); ?> USD</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning edit-btn" data-product='<?php echo json_encode($product); ?>'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Xác nhận xóa?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="productCode" value="<?php echo $product['productCode']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo !empty($_GET['productLine']) ? '&productLine=' . urlencode($_GET['productLine']) : ''; ?><?php echo !empty($_GET['filter']) ? '&filter=' . $_GET['filter'] : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm sản phẩm mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Mã sản phẩm *</label>
                                <input type="text" name="productCode" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tên sản phẩm *</label>
                                <input type="text" name="productName" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dòng sản phẩm *</label>
                                <select name="productLine" class="form-select" required>
                                    <?php
                                    $productLines = $conn->query("SELECT productLine FROM productlines");
                                    while ($line = $productLines->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $line['productLine']; ?>">
                                            <?php echo htmlspecialchars($line['productLine']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nhà cung cấp *</label>
                                <input type="text" name="productVendor" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tỷ lệ *</label>
                                <input type="text" name="productScale" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số lượng *</label>
                                <input type="number" name="quantityInStock" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Giá vốn (USD) *</label>
                                <input type="number" step="0.01" name="buyPrice" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Giá bán (USD) *</label>
                                <input type="number" step="0.01" name="MSRP" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Mô tả</label>
                                <textarea name="productDescription" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="productCode" id="edit_productCode">
                    <div class="modal-header">
                        <h5 class="modal-title">Chỉnh sửa sản phẩm</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Tên sản phẩm *</label>
                                <input type="text" name="productName" id="edit_productName" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dòng sản phẩm *</label>
                                <select name="productLine" id="edit_productLine" class="form-select" required>
                                    <?php
                                    $productLines = $conn->query("SELECT productLine FROM productlines");
                                    while ($line = $productLines->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $line['productLine']; ?>">
                                            <?php echo htmlspecialchars($line['productLine']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nhà cung cấp *</label>
                                <input type="text" name="productVendor" id="edit_productVendor" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tỷ lệ *</label>
                                <input type="text" name="productScale" id="edit_productScale" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số lượng *</label>
                                <input type="number" name="quantityInStock" id="edit_quantityInStock" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Giá vốn (USD) *</label>
                                <input type="number" step="0.01" name="buyPrice" id="edit_buyPrice" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Giá bán (USD) *</label>
                                <input type="number" step="0.01" name="MSRP" id="edit_MSRP" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Mô tả</label>
                                <textarea name="productDescription" id="edit_productDescription" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-warning">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle edit button
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const product = JSON.parse(this.dataset.product);
                document.getElementById('edit_productCode').value = product.productCode;
                document.getElementById('edit_productName').value = product.productName;
                document.getElementById('edit_productLine').value = product.productLine;
                document.getElementById('edit_productVendor').value = product.productVendor;
                document.getElementById('edit_productScale').value = product.productScale;
                document.getElementById('edit_quantityInStock').value = product.quantityInStock;
                document.getElementById('edit_buyPrice').value = product.buyPrice;
                document.getElementById('edit_MSRP').value = product.MSRP;
                document.getElementById('edit_productDescription').value = product.productDescription || '';
                
                new bootstrap.Modal(document.getElementById('editModal')).show();
            });
        });
        
        // Auto dismiss alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.display = 'none';
            });
        }, 5000);
    </script>
</body>
</html>
