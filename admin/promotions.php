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
                $stmt = $conn->prepare("INSERT INTO promotions (title, description, discount_percentage, start_date, end_date, is_active) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdssi", $_POST['title'], $_POST['description'], $_POST['discount_percentage'], $_POST['start_date'], $_POST['end_date'], $_POST['is_active']);
                $stmt->execute();
                $message = 'Thêm khuyến mãi thành công!';
                break;
                
            case 'update':
                $stmt = $conn->prepare("UPDATE promotions SET title = ?, description = ?, discount_percentage = ?, start_date = ?, end_date = ?, is_active = ? WHERE id = ?");
                $stmt->bind_param("ssdssii", $_POST['title'], $_POST['description'], $_POST['discount_percentage'], $_POST['start_date'], $_POST['end_date'], $_POST['is_active'], $_POST['id']);
                $stmt->execute();
                $message = 'Cập nhật khuyến mãi thành công!';
                break;
                
            case 'delete':
                $stmt = $conn->prepare("DELETE FROM promotions WHERE id = ?");
                $stmt->bind_param("i", $_POST['id']);
                $stmt->execute();
                $message = 'Xóa khuyến mãi thành công!';
                break;
                
            case 'toggle':
                $stmt = $conn->prepare("UPDATE promotions SET is_active = NOT is_active WHERE id = ?");
                $stmt->bind_param("i", $_POST['id']);
                $stmt->execute();
                $message = 'Cập nhật trạng thái thành công!';
                break;
        }
    }
}

$promotions = $conn->query("SELECT * FROM promotions ORDER BY start_date DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Khuyến mãi - Mini Shop Admin</title>
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
                <h2><i class="fas fa-gift"></i> Quản lý Khuyến mãi</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Thêm Khuyến mãi
                </button>
            </div>
            
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tiêu đề</th>
                                    <th>Mô tả</th>
                                    <th>Giảm giá</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày kết thúc</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($promo = $promotions->fetch_assoc()): 
                                    $now = date('Y-m-d');
                                    $isActive = $promo['is_active'] && $promo['start_date'] <= $now && $promo['end_date'] >= $now;
                                ?>
                                    <tr>
                                        <td><?php echo $promo['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($promo['title']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($promo['description']); ?></td>
                                        <td>
                                            <span class="badge bg-danger">
                                                <?php echo $promo['discount_percentage']; ?>% OFF
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($promo['start_date'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($promo['end_date'])); ?></td>
                                        <td>
                                            <span class="badge <?php echo $isActive ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php 
                                                if (!$promo['is_active']) {
                                                    echo 'Đã tắt';
                                                } elseif ($now < $promo['start_date']) {
                                                    echo 'Sắp diễn ra';
                                                } elseif ($now > $promo['end_date']) {
                                                    echo 'Đã hết hạn';
                                                } else {
                                                    echo 'Đang hoạt động';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id" value="<?php echo $promo['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-info">
                                                    <i class="fas fa-<?php echo $promo['is_active'] ? 'toggle-on' : 'toggle-off'; ?>"></i>
                                                </button>
                                            </form>
                                            <button class="btn btn-sm btn-warning edit-btn" data-promo='<?php echo json_encode($promo); ?>'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Xác nhận xóa?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $promo['id']; ?>">
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
        </div>
    </div>
    
    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm Khuyến mãi mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phần trăm giảm giá (%) *</label>
                            <input type="number" step="0.01" min="0" max="100" name="discount_percentage" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày bắt đầu *</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày kết thúc *</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="add_is_active">
                                <label class="form-check-label" for="add_is_active">
                                    Kích hoạt khuyến mãi này
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm Khuyến mãi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Chỉnh sửa Khuyến mãi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề *</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phần trăm giảm giá (%) *</label>
                            <input type="number" step="0.01" min="0" max="100" name="discount_percentage" id="edit_discount_percentage" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày bắt đầu *</label>
                                <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày kết thúc *</label>
                                <input type="date" name="end_date" id="edit_end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active">
                                <label class="form-check-label" for="edit_is_active">
                                    Kích hoạt khuyến mãi này
                                </label>
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
                const promo = JSON.parse(this.dataset.promo);
                document.getElementById('edit_id').value = promo.id;
                document.getElementById('edit_title').value = promo.title;
                document.getElementById('edit_description').value = promo.description || '';
                document.getElementById('edit_discount_percentage').value = promo.discount_percentage;
                document.getElementById('edit_start_date').value = promo.start_date;
                document.getElementById('edit_end_date').value = promo.end_date;
                document.getElementById('edit_is_active').checked = promo.is_active == 1;
                
                new bootstrap.Modal(document.getElementById('editModal')).show();
            });
        });
        
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => alert.style.display = 'none');
        }, 5000);
    </script>
</body>
</html>
