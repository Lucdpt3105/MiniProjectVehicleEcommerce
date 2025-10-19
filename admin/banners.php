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
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $image_url = trim($_POST['image_url'] ?? '');
                $link_url = trim($_POST['link_url'] ?? '');
                $is_active = (isset($_POST['is_active']) && $_POST['is_active']) ? 1 : 0;

                $stmt = $conn->prepare("INSERT INTO banners (title, description, image_url, link_url, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $title, $description, $image_url, $link_url, $is_active);
                $stmt->execute();
                $message = 'Thêm banner thành công!';
                break;

            case 'update':
                $id = intval($_POST['id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $image_url = trim($_POST['image_url'] ?? '');
                $link_url = trim($_POST['link_url'] ?? '');
                $is_active = (isset($_POST['is_active']) && $_POST['is_active']) ? 1 : 0;

                $stmt = $conn->prepare("UPDATE banners SET title = ?, description = ?, image_url = ?, link_url = ?, is_active = ? WHERE bannerID = ?");
                $stmt->bind_param("ssssii", $title, $description, $image_url, $link_url, $is_active, $id);
                $stmt->execute();
                $message = 'Cập nhật banner thành công!';
                break;

            case 'delete':
                $id = intval($_POST['id'] ?? 0);
                $stmt = $conn->prepare("DELETE FROM banners WHERE bannerID = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $message = 'Xóa banner thành công!';
                break;

            case 'toggle':
                $id = intval($_POST['id'] ?? 0);
                $stmt = $conn->prepare("UPDATE banners SET is_active = NOT is_active WHERE bannerID = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $message = 'Cập nhật trạng thái thành công!';
                break;

            case 'bulk_add':
                // Expecting textarea 'image_urls' with one URL per line
                $raw = trim($_POST['image_urls'] ?? '');
                $title_prefix = trim($_POST['title_prefix'] ?? 'Banner');
                $lines = preg_split('/\r?\n/', $raw);
                $added = 0;
                $stmt = $conn->prepare("INSERT INTO banners (title, description, image_url, link_url, is_active) VALUES (?, ?, ?, ?, ?)");
                foreach ($lines as $i => $line) {
                    $url = trim($line);
                    if (empty($url)) continue;
                    $title = $title_prefix . ' ' . ($i + 1);
                    $description = '';
                    $link_url = '';
                    $is_active = 1;
                    $stmt->bind_param("ssssi", $title, $description, $url, $link_url, $is_active);
                    $stmt->execute();
                    $added++;
                }
                $message = "Đã thêm $added banners từ danh sách.";
                break;
        }
    }
}

$banners = $conn->query("SELECT * FROM banners ORDER BY bannerID DESC");
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
                <button class="btn btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#bulkModal">
                    <i class="fas fa-file-import"></i> Import Nhiều
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
                                            <input type="hidden" name="id" value="<?php echo $banner['bannerID']; ?>">
                                            <button type="submit" class="btn btn-sm btn-info">
                                                <i class="fas fa-<?php echo $banner['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-warning edit-btn" data-banner='<?php echo json_encode($banner); ?>'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Xác nhận xóa?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $banner['bannerID']; ?>">
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
    
    <!-- Bulk Import Modal -->
    <div class="modal fade" id="bulkModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="bulk_add">
                    <div class="modal-header">
                        <h5 class="modal-title">Import nhiều ảnh làm Banner (mỗi URL 1 dòng)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tiền tố tiêu đề (ví dụ: Banner)</label>
                            <input type="text" name="title_prefix" class="form-control" value="Banner">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Danh sách URL hình ảnh</label>
                            <textarea name="image_urls" id="bulk_image_urls" class="form-control" rows="8" placeholder="https://example.com/1.jpg\nhttps://example.com/2.jpg"></textarea>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Bạn có thể dùng các nút bên dưới để chèn nhanh các URL bạn đã chuẩn bị.</small>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" id="prefill_all_images">Chèn ảnh xe & phương tiện</button>
                                <button type="button" class="btn btn-sm btn-outline-success me-1" id="prefill_banners">Chèn ảnh cho banner</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clear_bulk">Xóa</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm tất cả</button>
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
                document.getElementById('edit_id').value = banner.bannerID;
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
    <script>
        // Image lists provided by user
        const allImages = [
            // vehicle / general images
            'https://i.pinimg.com/736x/90/37/61/903761830591b5b8826f2b96d04b042e.jpg',
            'https://www.thehenryford.org/linkedpub-image/qY8EE1_447shRg1F7LyjmbuscyLUg_WU7vXb3cTQvhfVeusjclNyhN-prpRluxhxAIYU8WihcMex2MhwXPsGOw',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTPva-VGZxqiGtHYDtyV87hZe4BYvhZbtSdgQ&s',
            'https://i.pinimg.com/736x/39/2d/ac/392dacf452882ca3bb9314f1195fa754.jpg',
            'https://ultimatemotorcycling.com/wp-content/uploads/2024/09/1969-swap-chopper-sportster-74-mystery-1.jpg',
            'https://scalethumb.leparking.fr/unsafe/331x248/smart/https://cloud.leparking.fr/2020/10/12/00/12/alpine-a110-renault-alpine-a110-original-gordini-1300-va-bleu_7808574403.jpg',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSusJ4RhTyApqrhVcNXe4DH-yMn27OPwGjtTg&s',
            'https://www.motorious.com/content/images/2020/09/canepa4.jpg',
            'https://images.collectingcars.com/022241/AT-307.jpg?w=1263&fit=fillmax&crop=edges&auto=format,compress&cs=srgb&q=85',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkPCUfuPH1cdyBRuwg1QXWnw5l9StbAF4p7A&s',
            'https://cdn.dealeraccelerate.com/vanguard/1/27845/1422036/1920x1440/1968-ford-mustang-fastback-restomod',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTfZvjwe--IdK1SmLuVlJGMIg5tdrtk1M5gSw&s',
            'https://cdn.dealeraccelerate.com/survivor/1/1072/94997/1920x1440/1969-chevrolet-corvair-monza-convertible',
            'https://www.carscoops.com/wp-content/uploads/2022/04/1968-dodge-charger-rt-hem.jpg',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTggSniCM8NDwinrPtUf87tHpdidYXBbCViMA&s',
            'https://www.sportscarmarket.com/wp-content/uploads/2021/05/1970-plymouth-hemi-cuda-front.jpg',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRlOTTCaC8pEpPJexQ17ByHByVuWiqgLsIMrA&s',
            'https://cdn.dealeraccelerate.com/saratoga/1/127/2309/790x1024/1999-yamaha-jet-boat',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTWBMQAlsZRRJzNsnUpCUM7buhPnMK2ra3e2g&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQipPRQbYHVsBrKsXCI0bdfSOub1Fstmp3K-g&s',
            'https://media.defense.gov/2005/Dec/26/2000574565/1200/1200/0/050317-F-1234P-002.JPG',
            'https://www.americanairpowermuseum.com/wp-content/uploads/2025/04/Template-1920-x-1080-px.webp',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSma_QC9BrXX_xV-QyD_DbGx3ooN_AL2Amz-w&s',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Queen_Mary_ship%2C_Long_Beach%2C_California_LCCN2011630073_%28crop%29.tif/lossy-page1-1200px-Queen_Mary_ship%2C_Long_Beach%2C_California_LCCN2011630073_%28crop%29.tif.jpg',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSNt68vxwjpRnrtJXiIHAuMmayte160e_Jc9g&s',
            'https://media.defense.gov/2024/Nov/05/2003578295/2000/2000/0/241105-F-IO108-001.JPG'
        ];

        const bannerImages = [
            'https://down-vn.img.susercontent.com/file/sg-11134258-825ay-mfw4qgw0s4y276@resize_w1594_nl.webp',
            'https://down-vn.img.susercontent.com/file/sg-11134258-82595-mfv8329mzw9732@resize_w1594_nl.webp',
            'https://down-vn.img.susercontent.com/file/sg-11134258-8259l-mfuu136na6tr7d@resize_w1594_nl.webp',
            'https://down-vn.img.susercontent.com/file/sg-11134258-8259o-mfw4qioourrh8d@resize_w1594_nl.webp'
        ];

        document.getElementById('prefill_all_images').addEventListener('click', function() {
            document.getElementById('bulk_image_urls').value = allImages.join('\n');
        });

        document.getElementById('prefill_banners').addEventListener('click', function() {
            document.getElementById('bulk_image_urls').value = bannerImages.join('\n');
        });

        document.getElementById('clear_bulk').addEventListener('click', function() {
            document.getElementById('bulk_image_urls').value = '';
        });
    </script>
</body>
</html>
