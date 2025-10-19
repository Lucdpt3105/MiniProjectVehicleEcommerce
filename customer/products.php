<?php
require_once '../config/database.php';
require_once '../config/session.php';

$pageTitle = 'Sản phẩm';
include '../includes/header.php';

// Get filters
$search = $_GET['q'] ?? '';
$productLine = $_GET['line'] ?? '';
$minPrice = $_GET['min'] ?? 0;
$maxPrice = $_GET['max'] ?? 999999;
$vendor = $_GET['vendor'] ?? '';
$sort = $_GET['sort'] ?? 'name_asc';
$inStock = isset($_GET['stock']) && $_GET['stock'] == '1';

// Build query
$where = ["1=1"];
$params = [];
$types = "";

if ($search) {
    $where[] = "(p.productName LIKE ? OR p.productCode LIKE ? OR p.productDescription LIKE ?)";
    $searchParam = "%$search%";
    $params[] = &$searchParam;
    $params[] = &$searchParam;
    $params[] = &$searchParam;
    $types .= "sss";
}

if ($productLine) {
    $where[] = "p.productLine = ?";
    $params[] = &$productLine;
    $types .= "s";
}

if ($minPrice > 0 || $maxPrice < 999999) {
    $where[] = "p.MSRP BETWEEN ? AND ?";
    $params[] = &$minPrice;
    $params[] = &$maxPrice;
    $types .= "dd";
}

if ($vendor) {
    $where[] = "p.productVendor = ?";
    $params[] = &$vendor;
    $types .= "s";
}

if ($inStock) {
    $where[] = "p.quantityInStock > 0";
}

// Sort
$orderBy = match($sort) {
    'price_asc' => 'p.MSRP ASC',
    'price_desc' => 'p.MSRP DESC',
    'name_desc' => 'p.productName DESC',
    'rating' => 'avg_rating DESC',
    default => 'p.productName ASC'
};

$whereClause = implode(" AND ", $where);
$sql = "SELECT p.*, 
        COALESCE(AVG(r.rating), 0) as avg_rating,
        COUNT(r.reviewID) as review_count
        FROM products p
        LEFT JOIN reviews r ON p.productCode = r.productCode
        WHERE $whereClause
        GROUP BY p.productCode
        ORDER BY $orderBy";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

// Get filter options
$vendors = $conn->query("SELECT DISTINCT productVendor FROM products ORDER BY productVendor");
$lines = $conn->query("SELECT productLine FROM productlines ORDER BY productLine");
?>

<div class="container">
    <h2 class="mb-4">
        <i class="fas fa-box"></i> 
        <?php echo $search ? "Kết quả tìm kiếm: \"$search\"" : ($productLine ? $productLine : 'Tất cả sản phẩm'); ?>
    </h2>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Dòng sản phẩm</label>
                        <select name="line" class="form-select">
                            <option value="">Tất cả</option>
                            <?php while ($line = $lines->fetch_assoc()): ?>
                                <option value="<?php echo $line['productLine']; ?>" 
                                        <?php echo $productLine === $line['productLine'] ? 'selected' : ''; ?>>
                                    <?php echo $line['productLine']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Nhà cung cấp</label>
                        <select name="vendor" class="form-select">
                            <option value="">Tất cả</option>
                            <?php while ($v = $vendors->fetch_assoc()): ?>
                                <option value="<?php echo $v['productVendor']; ?>" 
                                        <?php echo $vendor === $v['productVendor'] ? 'selected' : ''; ?>>
                                    <?php echo $v['productVendor']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Giá tối thiểu</label>
                        <input type="number" name="min" class="form-control" value="<?php echo $minPrice; ?>" min="0">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Giá tối đa</label>
                        <input type="number" name="max" class="form-control" value="<?php echo $maxPrice; ?>" min="0">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Sắp xếp</label>
                        <select name="sort" class="form-select">
                            <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Tên A-Z</option>
                            <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Tên Z-A</option>
                            <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                            <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                            <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>Đánh giá cao</option>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="stock" value="1" id="stockCheck" 
                                   <?php echo $inStock ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="stockCheck">
                                Chỉ hiển thị sản phẩm còn hàng
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="products.php" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Đặt lại
                        </a>
                    </div>
                </div>
                
                <?php if ($search): ?>
                    <input type="hidden" name="q" value="<?php echo htmlspecialchars($search); ?>">
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <!-- Products Grid -->
    <div class="row mb-3">
        <div class="col">
            <p class="text-muted">Tìm thấy <?php echo $products->num_rows; ?> sản phẩm</p>
        </div>
    </div>
    
    <div class="row">
        <?php if ($products->num_rows > 0): ?>
            <?php while ($product = $products->fetch_assoc()):
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
                                    echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                }
                                ?>
                                <small class="text-muted">(<?php echo $product['review_count']; ?>)</small>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="price-badge"><?php echo number_format($product['MSRP'], 0); ?> USD</span>
                                <button class="btn btn-sm btn-primary" 
                                        onclick="addToCart('<?php echo $product['productCode']; ?>')">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                            
                            <?php if ($product['quantityInStock'] > 0): ?>
                                <small class="text-success">
                                    <i class="fas fa-check-circle"></i> Còn hàng (<?php echo $product['quantityInStock']; ?>)
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
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h4>Không tìm thấy sản phẩm nào!</h4>
                    <p>Vui lòng thử lại với bộ lọc khác.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
