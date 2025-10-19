<?php
require_once '../config/database.php';
require_once '../config/session.php';

// Generate cart session ID if not exists
if (!isset($_SESSION['cart_sessionID'])) {
    $_SESSION['cart_sessionID'] = session_id();
}

$pageTitle = 'Trang chủ';
include '../includes/header.php';
?>

<div class="container-fluid px-0">
    <!-- Banner Carousel -->
    <?php
    $banners = $conn->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY bannerID DESC LIMIT 5");
    if ($banners->num_rows > 0):
    ?>
    <div id="mainCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php for ($i = 0; $i < $banners->num_rows; $i++): ?>
                <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="<?php echo $i; ?>" 
                        <?php echo $i === 0 ? 'class="active"' : ''; ?>></button>
            <?php endfor; ?>
        </div>
        
        <div class="carousel-inner">
            <?php 
            $banners->data_seek(0);
            $first = true;
            while ($banner = $banners->fetch_assoc()): 
            ?>
                <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                    <?php if ($banner['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($banner['image_url']); ?>" 
                             class="d-block w-100" alt="<?php echo htmlspecialchars($banner['title']); ?>">
                    <?php else: ?>
                        <div style="height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                    <?php endif; ?>
                    <div class="carousel-caption">
                        <h3><?php echo htmlspecialchars($banner['title']); ?></h3>
                        <?php if ($banner['link_url']): ?>
                            <a href="<?php echo htmlspecialchars($banner['link_url']); ?>" class="btn btn-primary">
                                Xem thêm
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php 
            $first = false;
            endwhile; 
            ?>
        </div>
        
        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
    <?php endif; ?>
</div>

<div class="container">
    <?php if (isset($_GET['logged_out'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Đã đăng xuất thành công!
        </div>
    <?php endif; ?>
    
    <!-- Product Lines -->
    <section class="mb-5">
        <h2 class="mb-4"><i class="fas fa-list"></i> Danh mục sản phẩm</h2>
        <div class="row">
            <?php
            $productLines = $conn->query("SELECT pl.*, COUNT(p.productCode) as productCount 
                                          FROM productlines pl 
                                          LEFT JOIN products p ON pl.productLine = p.productLine 
                                          GROUP BY pl.productLine 
                                          ORDER BY productCount DESC");
            while ($line = $productLines->fetch_assoc()):
            ?>
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="products.php?line=<?php echo urlencode($line['productLine']); ?>" 
                       class="text-decoration-none">
                        <div class="card h-100 text-center">
                            <?php if ($line['image']): ?>
                                <img src="<?php echo htmlspecialchars($line['image']); ?>" 
                                     class="card-img-top" style="height: 150px; object-fit: contain; padding: 15px;" 
                                     alt="<?php echo htmlspecialchars($line['productLine']); ?>">
                            <?php else: ?>
                                <div style="height: 150px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($line['productLine']); ?></h5>
                                <p class="text-muted"><?php echo $line['productCount']; ?> sản phẩm</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    
    <!-- Featured Products -->
    <section class="mb-5">
        <h2 class="mb-4"><i class="fas fa-star"></i> Sản phẩm nổi bật</h2>
        <div class="row">
            <?php
            $featured = $conn->query("SELECT p.*, 
                                      COALESCE(AVG(r.rating), 0) as avg_rating,
                                      COUNT(r.reviewID) as review_count
                                      FROM products p
                                      LEFT JOIN reviews r ON p.productCode = r.productCode
                                      WHERE p.quantityInStock > 0
                                      GROUP BY p.productCode
                                      ORDER BY avg_rating DESC, review_count DESC
                                      LIMIT 8");
            while ($product = $featured->fetch_assoc()):
                $mainImage = $conn->query("SELECT image_url FROM product_images 
                                          WHERE productCode = '{$product['productCode']}' 
                                          AND is_main = 1 LIMIT 1")->fetch_assoc();
            ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card product-card h-100">
                        <a href="product_detail.php?code=<?php echo urlencode($product['productCode']); ?>">
                            <?php if ($mainImage): ?>
                                <img src="<?php echo htmlspecialchars($mainImage['image_url']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($product['productName']); ?>">
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
                                    if ($i <= $rating) {
                                        echo '<i class="fas fa-star"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                                <small class="text-muted">(<?php echo $product['review_count']; ?>)</small>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-badge"><?php echo number_format($product['MSRP'], 0); ?> USD</span>
                                <button class="btn btn-sm btn-primary" 
                                        onclick="addToCart('<?php echo $product['productCode']; ?>')">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                            
                            <?php if ($product['quantityInStock'] < 10): ?>
                                <small class="text-danger">
                                    <i class="fas fa-exclamation-triangle"></i> Chỉ còn <?php echo $product['quantityInStock']; ?> sản phẩm
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="products.php" class="btn btn-lg btn-primary">
                <i class="fas fa-th"></i> Xem tất cả sản phẩm
            </a>
        </div>
    </section>
    
    <!-- Active Promotions -->
    <?php
    $today = date('Y-m-d');
    $promotions = $conn->query("SELECT * FROM promotions 
                                WHERE is_active = 1 
                                AND start_date <= '$today' 
                                AND end_date >= '$today' 
                                ORDER BY discount_percent DESC 
                                LIMIT 3");
    if ($promotions->num_rows > 0):
    ?>
    <section class="mb-5">
        <h2 class="mb-4"><i class="fas fa-gift"></i> Khuyến mãi đang diễn ra</h2>
        <div class="row">
            <?php while ($promo = $promotions->fetch_assoc()): ?>
                <div class="col-md-4 mb-3">
                    <div class="card border-warning">
                        <div class="card-body">
                            <h5 class="card-title text-warning">
                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($promo['promo_name']); ?>
                            </h5>
                            <p class="card-text"><?php echo htmlspecialchars($promo['description']); ?></p>
                            <h3 class="text-danger">-<?php echo $promo['discount_percent']; ?>%</h3>
                            <small class="text-muted">
                                Từ <?php echo date('d/m/Y', strtotime($promo['start_date'])); ?> 
                                đến <?php echo date('d/m/Y', strtotime($promo['end_date'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
