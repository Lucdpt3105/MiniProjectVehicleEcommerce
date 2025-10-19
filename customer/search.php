<?php
require_once '../config/database.php';
require_once '../config/session.php';

$search = $_GET['q'] ?? '';

if (empty($search)) {
    header("Location: products.php");
    exit();
}

$pageTitle = "Tìm kiếm: $search";
include '../includes/header.php';

// Search products
$searchParam = "%$search%";
$stmt = $conn->prepare("SELECT p.*, 
                        COALESCE(AVG(r.rating), 0) as avg_rating,
                        COUNT(r.reviewID) as review_count,
                        (SELECT image_url FROM product_images WHERE productCode = p.productCode AND is_main = 1 LIMIT 1) as main_image
                        FROM products p
                        LEFT JOIN reviews r ON p.productCode = r.productCode
                        WHERE p.productName LIKE ? 
                        OR p.productCode LIKE ? 
                        OR p.productDescription LIKE ?
                        OR p.productLine LIKE ?
                        GROUP BY p.productCode
                        ORDER BY p.productName ASC");
$stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
$stmt->execute();
$results = $stmt->get_result();
?>

<div class="container my-4">
    <h2 class="mb-4">
        <i class="fas fa-search"></i> Kết quả tìm kiếm cho "<?php echo htmlspecialchars($search); ?>"
    </h2>
    
    <p class="text-muted">Tìm thấy <?php echo $results->num_rows; ?> sản phẩm</p>
    
    <?php if ($results->num_rows > 0): ?>
        <div class="row">
            <?php while ($product = $results->fetch_assoc()): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card product-card h-100">
                        <a href="product_detail.php?code=<?php echo urlencode($product['productCode']); ?>">
                            <?php if ($product['main_image']): ?>
                                <img src="<?php echo htmlspecialchars($product['main_image']); ?>" 
                                     class="card-img-top" style="height: 200px; object-fit: contain; padding: 15px;"
                                     alt="<?php echo htmlspecialchars($product['productName']); ?>">
                            <?php else: ?>
                                <div style="height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-car fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="product_detail.php?code=<?php echo urlencode($product['productCode']); ?>" 
                                   class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($product['productName']); ?>
                                </a>
                            </h6>
                            
                            <div class="rating mb-2">
                                <?php
                                $rating = round($product['avg_rating']);
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                }
                                ?>
                                <small class="text-muted">(<?php echo $product['review_count']; ?>)</small>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="price-badge"><?php echo number_format($product['MSRP'], 0); ?> USD</span>
                                <?php if ($product['quantityInStock'] > 0): ?>
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="addToCart('<?php echo $product['productCode']; ?>')">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($product['quantityInStock'] > 0): ?>
                                <small class="text-success">
                                    <i class="fas fa-check-circle"></i> Còn hàng
                                </small>
                            <?php else: ?>
                                <small class="text-danger">
                                    <i class="fas fa-times-circle"></i> Hết hàng
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-3x mb-3"></i>
            <h4>Không tìm thấy sản phẩm nào!</h4>
            <p>Vui lòng thử lại với từ khóa khác.</p>
            <a href="products.php" class="btn btn-primary">Xem tất cả sản phẩm</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
