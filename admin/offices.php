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
                $stmt = $conn->prepare("INSERT INTO offices (officeCode, city, phone, addressLine1, addressLine2, state, country, postalCode, territory) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssss", $_POST['officeCode'], $_POST['city'], $_POST['phone'], $_POST['addressLine1'], $_POST['addressLine2'], $_POST['state'], $_POST['country'], $_POST['postalCode'], $_POST['territory']);
                if ($stmt->execute()) {
                    $message = 'Thêm văn phòng thành công!';
                } else {
                    $error = 'Lỗi: ' . $stmt->error;
                }
                break;
                
            case 'update':
                $stmt = $conn->prepare("UPDATE offices SET city=?, phone=?, addressLine1=?, addressLine2=?, state=?, country=?, postalCode=?, territory=? WHERE officeCode=?");
                $stmt->bind_param("sssssssss", $_POST['city'], $_POST['phone'], $_POST['addressLine1'], $_POST['addressLine2'], $_POST['state'], $_POST['country'], $_POST['postalCode'], $_POST['territory'], $_POST['officeCode']);
                if ($stmt->execute()) {
                    $message = 'Cập nhật văn phòng thành công!';
                } else {
                    $error = 'Lỗi: ' . $stmt->error;
                }
                break;
                
            case 'delete':
                $stmt = $conn->prepare("DELETE FROM offices WHERE officeCode = ?");
                $stmt->bind_param("s", $_POST['officeCode']);
                if ($stmt->execute()) {
                    $message = 'Xóa văn phòng thành công!';
                } else {
                    $error = 'Không thể xóa văn phòng này (có nhân viên đang làm việc)';
                }
                break;
        }
    }
}

// Get offices with employee count
$offices = $conn->query("SELECT o.*, COUNT(e.employeeNumber) as employeeCount
                        FROM offices o
                        LEFT JOIN employees e ON o.officeCode = e.officeCode
                        GROUP BY o.officeCode
                        ORDER BY o.city");

$pageTitle = 'Quản lý Văn phòng';
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
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-building"></i> Quản lý Văn phòng</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Thêm Văn phòng
                </button>
            </div>
            
            <!-- Offices Grid -->
            <div class="row">
                <?php while ($office = $offices->fetch_assoc()): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title">
                                            <i class="fas fa-building text-primary"></i> 
                                            <?php echo htmlspecialchars($office['city']); ?>
                                        </h5>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($office['officeCode']); ?></span>
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-warning edit-btn" data-office='<?php echo json_encode($office); ?>'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Xác nhận xóa?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="officeCode" value="<?php echo $office['officeCode']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="mb-2">
                                    <i class="fas fa-map-marker-alt text-muted"></i>
                                    <strong>Địa chỉ:</strong><br>
                                    <?php echo htmlspecialchars($office['addressLine1']); ?><br>
                                    <?php if ($office['addressLine2']): ?>
                                        <?php echo htmlspecialchars($office['addressLine2']); ?><br>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($office['city'] . ', ' . ($office['state'] ? $office['state'] . ', ' : '') . $office['country'] . ' ' . $office['postalCode']); ?>
                                </div>
                                
                                <div class="mb-2">
                                    <i class="fas fa-phone text-muted"></i>
                                    <strong>Điện thoại:</strong> 
                                    <a href="tel:<?php echo $office['phone']; ?>"><?php echo htmlspecialchars($office['phone']); ?></a>
                                </div>
                                
                                <div class="mb-2">
                                    <i class="fas fa-globe text-muted"></i>
                                    <strong>Khu vực:</strong> <?php echo htmlspecialchars($office['territory']); ?>
                                </div>
                                
                                <hr>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-users text-success"></i> 
                                        <strong><?php echo $office['employeeCount']; ?></strong> nhân viên
                                    </span>
                                    <a href="employees.php?office=<?php echo $office['officeCode']; ?>" class="btn btn-sm btn-outline-primary">
                                        Xem nhân viên <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    
    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm Văn phòng mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Mã văn phòng *</label>
                                <input type="text" name="officeCode" class="form-control" placeholder="VD: 8" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Thành phố *</label>
                                <input type="text" name="city" class="form-control" placeholder="VD: Hanoi" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quốc gia *</label>
                                <input type="text" name="country" class="form-control" placeholder="VD: Vietnam" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Điện thoại *</label>
                                <input type="text" name="phone" class="form-control" placeholder="+84 xxx xxx xxx" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Địa chỉ 1 *</label>
                                <input type="text" name="addressLine1" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Địa chỉ 2</label>
                                <input type="text" name="addressLine2" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tỉnh/Bang</label>
                                <input type="text" name="state" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mã bưu điện</label>
                                <input type="text" name="postalCode" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Khu vực *</label>
                                <input type="text" name="territory" class="form-control" placeholder="VD: APAC" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm Văn phòng</button>
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
                    <input type="hidden" name="officeCode" id="edit_officeCode">
                    <div class="modal-header">
                        <h5 class="modal-title">Chỉnh sửa Văn phòng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Thành phố *</label>
                                <input type="text" name="city" id="edit_city" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quốc gia *</label>
                                <input type="text" name="country" id="edit_country" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Điện thoại *</label>
                                <input type="text" name="phone" id="edit_phone" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Địa chỉ 1 *</label>
                                <input type="text" name="addressLine1" id="edit_addressLine1" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Địa chỉ 2</label>
                                <input type="text" name="addressLine2" id="edit_addressLine2" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tỉnh/Bang</label>
                                <input type="text" name="state" id="edit_state" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mã bưu điện</label>
                                <input type="text" name="postalCode" id="edit_postalCode" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Khu vực *</label>
                                <input type="text" name="territory" id="edit_territory" class="form-control" required>
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
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const office = JSON.parse(this.dataset.office);
                document.getElementById('edit_officeCode').value = office.officeCode;
                document.getElementById('edit_city').value = office.city;
                document.getElementById('edit_country').value = office.country;
                document.getElementById('edit_phone').value = office.phone;
                document.getElementById('edit_addressLine1').value = office.addressLine1;
                document.getElementById('edit_addressLine2').value = office.addressLine2 || '';
                document.getElementById('edit_state').value = office.state || '';
                document.getElementById('edit_postalCode').value = office.postalCode;
                document.getElementById('edit_territory').value = office.territory;
                
                new bootstrap.Modal(document.getElementById('editModal')).show();
            });
        });
        
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => alert.style.display = 'none');
        }, 5000);
    </script>
</body>
</html>
