<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (!isset($_SESSION['cart_sessionID'])) {
    $_SESSION['cart_sessionID'] = session_id();
}

$sessionID = $_SESSION['cart_sessionID'];

// Get cart items
$stmt = $conn->prepare("SELECT c.*, p.productName, p.MSRP, p.quantityInStock,
                        (SELECT image_url FROM product_images WHERE productCode = p.productCode AND is_main = 1 LIMIT 1) as main_image
                        FROM cart c
                        JOIN products p ON c.productCode = p.productCode
                        WHERE c.sessionID = ?
                        ORDER BY c.added_at DESC");
$stmt->bind_param("s", $sessionID);
$stmt->execute();
$cartItems = $stmt->get_result();

$pageTitle = 'Giỏ hàng';
include '../includes/header.php';
?>

<div class="container my-4">
    <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h2>
    
    <?php if ($cartItems->num_rows > 0): ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <?php 
                        $total = 0;
                        while ($item = $cartItems->fetch_assoc()): 
                            $subtotal = $item['MSRP'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-md-2">
                                    <?php if ($item['main_image']): ?>
                                        <img src="<?php echo htmlspecialchars($item['main_image']); ?>" 
                                             class="img-fluid" alt="<?php echo htmlspecialchars($item['productName']); ?>">
                                    <?php else: ?>
                                        <div style="width: 100%; height: 80px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-car fa-2x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <h6>
                                        <a href="product_detail.php?code=<?php echo urlencode($item['productCode']); ?>" 
                                           class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($item['productName']); ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">Mã: <?php echo htmlspecialchars($item['productCode']); ?></small>
                                </div>
                                <div class="col-md-2">
                                    <p class="mb-0"><strong><?php echo number_format($item['MSRP'], 0); ?> USD</strong></p>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-sm">
                                        <button class="btn btn-outline-secondary" type="button" 
                                                onclick="updateQuantity(<?php echo $item['cartID']; ?>, <?php echo $item['quantity'] - 1; ?>)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="text" class="form-control text-center" 
                                               value="<?php echo $item['quantity']; ?>" readonly>
                                        <button class="btn btn-outline-secondary" type="button" 
                                                onclick="updateQuantity(<?php echo $item['cartID']; ?>, <?php echo $item['quantity'] + 1; ?>)"
                                                <?php echo $item['quantity'] >= $item['quantityInStock'] ? 'disabled' : ''; ?>>
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <?php if ($item['quantity'] >= $item['quantityInStock']): ?>
                                        <small class="text-danger">Đạt tối đa</small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-2 text-end">
                                    <p class="mb-2"><strong><?php echo number_format($subtotal, 0); ?> USD</strong></p>
                                    <button class="btn btn-sm btn-danger" 
                                            onclick="removeFromCart(<?php echo $item['cartID']; ?>)">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                
                <a href="products.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                </a>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tóm tắt đơn hàng</h5>
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <strong><?php echo number_format($total, 0); ?> USD</strong>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <strong>Miễn phí</strong>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Tổng cộng:</h5>
                            <h5 class="text-danger"><?php echo number_format($total, 0); ?> USD</h5>
                        </div>
                        
                        <?php if (isLoggedIn()): ?>
                            <a href="checkout.php" class="btn btn-primary w-100 btn-lg">
                                <i class="fas fa-credit-card"></i> Thanh toán
                            </a>
                        <?php else: ?>
                            <a href="../login.php?redirect=cart" class="btn btn-warning w-100 btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập để thanh toán
                            </a>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <p class="text-muted small mb-1">
                                <i class="fas fa-shield-alt text-success"></i> Thanh toán an toàn
                            </p>
                            <p class="text-muted small mb-1">
                                <i class="fas fa-truck text-primary"></i> Miễn phí vận chuyển
                            </p>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-undo text-info"></i> Đổi trả trong 7 ngày
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
            <h3>Giỏ hàng trống</h3>
            <p class="text-muted">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
            <a href="products.php" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag"></i> Mua sắm ngay
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function updateQuantity(cartID, quantity) {
    updateCartQuantity(cartID, quantity);
}
</script>

<?php include '../includes/footer.php'; ?>
