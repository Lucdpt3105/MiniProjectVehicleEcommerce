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
                $stmt = $conn->prepare("INSERT INTO banners (title, description, image_url, link_url, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $_POST['title'], $_POST['description'], $_POST['image_url'], $_POST['link_url'], $_POST['is_active']);
                $stmt->execute();
                $message = 'Thêm banner thành công!';
                break;
                
            case 'update':
                $stmt = $conn->prepare("UPDATE banners SET title = ?, description = ?, image_url = ?, link_url = ?, is_active = ? WHERE id = ?");
                $stmt->bind_param("ssssii", $_POST['title'], $_POST['description'], $_POST['image_url'], $_POST['link_url'], $_POST['is_active'], $_POST['id']);
                $stmt->execute();
                $message = 'Cập nhật banner thành công!';
                break;
                
            case 'delete':
                $stmt = $conn->prepare("DELETE FROM banners WHERE id = ?");
                $stmt->bind_param("i", $_POST['id']);
                $stmt->execute();
                $message = 'Xóa banner thành công!';
                break;
                
            case 'toggle':
                $stmt = $conn->prepare("UPDATE banners SET is_active = NOT is_active WHERE id = ?");
                $stmt->bind_param("i", $_POST['id']);
                $stmt->execute();
                $message = 'Cập nhật trạng thái thành công!';
                break;
        }
    }
}

$banners = $conn->query("SELECT * FROM banners ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Banners - Mini Shop Admin</title>
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
                <h2><i class="fas fa-image"></i> Quản lý Banners</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Thêm Banner
                </button>
            </div>
            
            <div class="row">
                <?php while ($banner = $banners->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars($banner['image_url']); ?>" class="card-img-top" alt="Banner" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($banner['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($banner['description']); ?></p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <i class="fas fa-link"></i> <?php echo htmlspecialchars($banner['link_url'] ?: 'Không có link'); ?>
                                    </small>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge <?php echo $banner['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo $banner['is_active'] ? 'Đang hiển thị' : 'Đã ẩn'; ?>
                                    </span>
                                    <div class="btn-group">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id" value="<?php echo $banner['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-info">
                                                <i class="fas fa-<?php echo $banner['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-warning edit-btn" data-banner='<?php echo json_encode($banner); ?>'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Xác nhận xóa?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $banner['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm Banner mới</h5>
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
                            <label class="form-label">URL hình ảnh *</label>
                            <input type="url" name="image_url" class="form-control" placeholder="https://example.com/banner.jpg" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link liên kết</label>
                            <input type="url" name="link_url" class="form-control" placeholder="https://example.com">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="add_is_active">
                                <label class="form-check-label" for="add_is_active">
                                    Hiển thị banner này
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm Banner</button>
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
                        <h5 class="modal-title">Chỉnh sửa Banner</h5>
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
                            <label class="form-label">URL hình ảnh *</label>
                            <input type="url" name="image_url" id="edit_image_url" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link liên kết</label>
                            <input type="url" name="link_url" id="edit_link_url" class="form-control">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active">
                                <label class="form-check-label" for="edit_is_active">
                                    Hiển thị banner này
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
                const banner = JSON.parse(this.dataset.banner);
                document.getElementById('edit_id').value = banner.id;
                document.getElementById('edit_title').value = banner.title;
                document.getElementById('edit_description').value = banner.description || '';
                document.getElementById('edit_image_url').value = banner.image_url;
                document.getElementById('edit_link_url').value = banner.link_url || '';
                document.getElementById('edit_is_active').checked = banner.is_active == 1;
                
                new bootstrap.Modal(document.getElementById('editModal')).show();
            });
        });
        
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => alert.style.display = 'none');
        }, 5000);
    </script>
</body>
</html>
