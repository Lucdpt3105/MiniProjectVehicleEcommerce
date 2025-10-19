<?php
require_once '../config/database.php';
require_once '../config/session.php';

$productCode = $_GET['code'] ?? '';

if (empty($productCode)) {
    header("Location: products.php");
    exit();
}

// Get product details
$stmt = $conn->prepare("SELECT p.*, 
                        COALESCE(AVG(r.rating), 0) as avg_rating,
                        COUNT(r.reviewID) as review_count
                        FROM products p
                        LEFT JOIN reviews r ON p.productCode = r.productCode
                        WHERE p.productCode = ?
                        GROUP BY p.productCode");
$stmt->bind_param("s", $productCode);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: products.php");
    exit();
}

// Get product images
$images = $conn->query("SELECT * FROM product_images WHERE productCode = '$productCode' ORDER BY is_main DESC");

// Get reviews
$reviews = $conn->query("SELECT r.*, u.username 
                         FROM reviews r 
                         JOIN users u ON r.userID = u.userID 
                         WHERE r.productCode = '$productCode' 
                         ORDER BY r.created_at DESC");

$pageTitle = $product['productName'];
include '../includes/header.php';
?>

<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="products.php">Sản phẩm</a></li>
            <li class="breadcrumb-item"><a href="products.php?line=<?php echo urlencode($product['productLine']); ?>">
                <?php echo htmlspecialchars($product['productLine']); ?>
            </a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['productName']); ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6">
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php if ($images->num_rows > 0): ?>
                        <?php 
                        $first = true;
                        while ($img = $images->fetch_assoc()): 
                        ?>
                            <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                                <img src="<?php echo htmlspecialchars($img['image_url']); ?>" 
                                     class="d-block w-100" style="height: 400px; object-fit: contain;" 
                                     alt="<?php echo htmlspecialchars($product['productName']); ?>">
                            </div>
                        <?php 
                        $first = false;
                        endwhile; 
                        ?>
                    <?php else: ?>
                        <div class="carousel-item active">
                            <div style="height: 400px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-car fa-5x text-muted"></i>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($images->num_rows > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($product['productName']); ?></h2>
            
            <div class="mb-3">
                <div class="rating">
                    <?php
                    $rating = round($product['avg_rating']);
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                    }
                    ?>
                    <span class="ms-2"><?php echo number_format($product['avg_rating'], 1); ?>/5</span>
                    <span class="text-muted">(<?php echo $product['review_count']; ?> đánh giá)</span>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="text-danger mb-0"><?php echo number_format($product['MSRP'], 0); ?> USD</h3>
                    <small class="text-muted">Giá nhập: <?php echo number_format($product['buyPrice'], 0); ?> USD</small>
                </div>
            </div>
            
            <div class="mb-3">
                <p><strong>Mã sản phẩm:</strong> <?php echo htmlspecialchars($product['productCode']); ?></p>
                <p><strong>Dòng sản phẩm:</strong> <?php echo htmlspecialchars($product['productLine']); ?></p>
                <p><strong>Tỷ lệ:</strong> <?php echo htmlspecialchars($product['productScale']); ?></p>
                <p><strong>Nhà cung cấp:</strong> <?php echo htmlspecialchars($product['productVendor']); ?></p>
                
                <?php if ($product['quantityInStock'] > 0): ?>
                    <p class="text-success">
                        <i class="fas fa-check-circle"></i> 
                        <strong>Còn hàng:</strong> <?php echo $product['quantityInStock']; ?> sản phẩm
                    </p>
                <?php else: ?>
                    <p class="text-danger">
                        <i class="fas fa-times-circle"></i> <strong>Hết hàng</strong>
                    </p>
                <?php endif; ?>
            </div>
            
            <?php if ($product['quantityInStock'] > 0): ?>
                <div class="mb-3">
                    <label class="form-label"><strong>Số lượng:</strong></label>
                    <div class="input-group" style="max-width: 200px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="changeQty(-1)">-</button>
                        <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="<?php echo $product['quantityInStock']; ?>">
                        <button class="btn btn-outline-secondary" type="button" onclick="changeQty(1)">+</button>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg" onclick="addToCartDetail()">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <a href="cart.php" class="btn btn-outline-secondary">
                        <i class="fas fa-shopping-cart"></i> Xem giỏ hàng
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Sản phẩm hiện đã hết hàng
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Description -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#description">Mô tả</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#reviews">Đánh giá (<?php echo $product['review_count']; ?>)</a>
                </li>
            </ul>
            
            <div class="tab-content p-4 bg-white border border-top-0">
                <div id="description" class="tab-pane fade show active">
                    <h4>Mô tả sản phẩm</h4>
                    <p><?php echo nl2br(htmlspecialchars($product['productDescription'])); ?></p>
                </div>
                
                <div id="reviews" class="tab-pane fade">
                    <h4>Đánh giá từ khách hàng</h4>
                    
                    <?php if (isLoggedIn()): ?>
                        <!-- Review Form -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5>Viết đánh giá của bạn</h5>
                                <form id="reviewForm" onsubmit="submitReview(event)">
                                    <div class="mb-3">
                                        <label class="form-label">Đánh giá:</label>
                                        <div class="rating-input">
                                            <input type="radio" name="rating" value="5" id="star5" required>
                                            <label for="star5"><i class="fas fa-star"></i></label>
                                            <input type="radio" name="rating" value="4" id="star4">
                                            <label for="star4"><i class="fas fa-star"></i></label>
                                            <input type="radio" name="rating" value="3" id="star3">
                                            <label for="star3"><i class="fas fa-star"></i></label>
                                            <input type="radio" name="rating" value="2" id="star2">
                                            <label for="star2"><i class="fas fa-star"></i></label>
                                            <input type="radio" name="rating" value="1" id="star1">
                                            <label for="star1"><i class="fas fa-star"></i></label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nhận xét:</label>
                                        <textarea class="form-control" name="comment" rows="4" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Gửi đánh giá
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <a href="../login.php">Đăng nhập</a> để viết đánh giá
                        </div>
                    <?php endif; ?>
                    
                    <!-- Reviews List -->
                    <?php if ($reviews->num_rows > 0): ?>
                        <?php while ($review = $reviews->fetch_assoc()): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-0">
                                                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($review['username']); ?>
                                            </h6>
                                            <div class="rating text-warning">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star<?php echo $i > $review['rating'] ? '-o' : ''; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?>
                                        </small>
                                    </div>
                                    <p class="mt-2 mb-0"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted">Chưa có đánh giá nào.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4"><i class="fas fa-th-large"></i> Sản phẩm liên quan</h3>
            <div class="row">
                <?php
                $related = $conn->query("SELECT p.*, 
                                        (SELECT image_url FROM product_images 
                                         WHERE productCode = p.productCode AND is_main = 1 LIMIT 1) as main_image
                                        FROM products p
                                        WHERE p.productLine = '{$product['productLine']}' 
                                        AND p.productCode != '$productCode'
                                        AND p.quantityInStock > 0
                                        ORDER BY RAND()
                                        LIMIT 4");
                while ($rel = $related->fetch_assoc()):
                ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card product-card h-100">
                            <a href="product_detail.php?code=<?php echo urlencode($rel['productCode']); ?>">
                                <?php if ($rel['main_image']): ?>
                                    <img src="<?php echo htmlspecialchars($rel['main_image']); ?>" 
                                         class="card-img-top" style="height: 150px; object-fit: contain; padding: 10px;"
                                         alt="<?php echo htmlspecialchars($rel['productName']); ?>">
                                <?php else: ?>
                                    <div style="height: 150px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-car fa-2x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="product_detail.php?code=<?php echo urlencode($rel['productCode']); ?>" 
                                       class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($rel['productName']); ?>
                                    </a>
                                </h6>
                                <p class="text-danger mb-2"><strong><?php echo number_format($rel['MSRP'], 0); ?> USD</strong></p>
                                <button class="btn btn-sm btn-primary w-100" 
                                        onclick="addToCart('<?php echo $rel['productCode']; ?>')">
                                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.rating-input input {
    display: none;
}
.rating-input label {
    cursor: pointer;
    font-size: 2rem;
    color: #ddd;
    transition: color 0.2s;
}
.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input:checked ~ label {
    color: #ffc107;
}
</style>

<script>
const productCode = '<?php echo $productCode; ?>';
const maxQuantity = <?php echo $product['quantityInStock']; ?>;

function changeQty(delta) {
    const input = document.getElementById('quantity');
    let newVal = parseInt(input.value) + delta;
    if (newVal < 1) newVal = 1;
    if (newVal > maxQuantity) newVal = maxQuantity;
    input.value = newVal;
}

function addToCartDetail() {
    const quantity = parseInt(document.getElementById('quantity').value);
    addToCart(productCode, quantity);
}

function submitReview(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.append('productCode', productCode);
    
    fetch('ajax/submit_review.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Đã gửi đánh giá thành công!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(data.message || 'Có lỗi xảy ra!', 'danger');
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>
