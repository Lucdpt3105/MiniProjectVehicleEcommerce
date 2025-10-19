<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireLogin();

$orderNumber = $_GET['order'] ?? $_SESSION['pending_order'] ?? null;

if (!$orderNumber) {
    header("Location: orders.php");
    exit();
}

// Get order details
$stmt = $conn->prepare("SELECT o.*, c.customerName, c.phone, c.addressLine1, c.city, c.country
                        FROM orders o
                        JOIN customers c ON o.customerNumber = c.customerNumber
                        WHERE o.orderNumber = ? AND c.customerNumber = ?");
$stmt->bind_param("ii", $orderNumber, getUserID());
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: orders.php");
    exit();
}

// Get order items
$items = $conn->query("SELECT od.*, p.productName 
                       FROM orderdetails od
                       JOIN products p ON od.productCode = p.productCode
                       WHERE od.orderNumber = $orderNumber");

$total = 0;
$orderItems = [];
while ($item = $items->fetch_assoc()) {
    $subtotal = $item['priceEach'] * $item['quantityOrdered'];
    $total += $subtotal;
    $orderItems[] = $item;
}

// Check if already paid
$existingPayment = $conn->query("SELECT * FROM payments WHERE customerNumber = {$order['customerNumber']} 
                                 AND checkNumber = 'ORDER-$orderNumber'")->fetch_assoc();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$existingPayment) {
    $paymentMethod = $_POST['paymentMethod'] ?? '';
    
    if (empty($paymentMethod)) {
        $error = 'Vui lòng chọn phương thức thanh toán!';
    } else {
        // Create payment record
        $checkNumber = 'ORDER-' . $orderNumber;
        $paymentDate = date('Y-m-d');
        
        $stmt = $conn->prepare("INSERT INTO payments (customerNumber, checkNumber, paymentDate, amount) 
                               VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issd", $order['customerNumber'], $checkNumber, $paymentDate, $total);
        
        if ($stmt->execute()) {
            // Update order status
            $conn->query("UPDATE orders SET status = 'Paid' WHERE orderNumber = $orderNumber");
            
            unset($_SESSION['pending_order']);
            $success = true;
        } else {
            $error = 'Có lỗi xảy ra khi xử lý thanh toán!';
        }
    }
}

$pageTitle = 'Thanh toán đơn hàng';
include '../includes/header.php';
?>

<div class="container my-4">
    <?php if ($success): ?>
        <div class="alert alert-success text-center">
            <i class="fas fa-check-circle fa-3x mb-3"></i>
            <h3>Thanh toán thành công!</h3>
            <p>Đơn hàng #<?php echo $orderNumber; ?> đã được xác nhận</p>
        </div>
        
        <div class="card mb-4">
            <div class="card-body text-center">
                <h5 class="mb-4">Cảm ơn bạn đã mua hàng tại Mini Shop!</h5>
                <p>Đơn hàng của bạn đang được xử lý và sẽ được giao trong 3-5 ngày làm việc.</p>
                <p>Bạn có thể theo dõi đơn hàng tại trang <a href="orders.php">Đơn hàng của tôi</a></p>
                
                <div class="mt-4">
                    <a href="orders.php" class="btn btn-primary me-2">
                        <i class="fas fa-receipt"></i> Xem đơn hàng
                    </a>
                    <a href="home.php" class="btn btn-outline-secondary">
                        <i class="fas fa-home"></i> Về trang chủ
                    </a>
                </div>
            </div>
        </div>
        
    <?php elseif ($existingPayment): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-3x mb-3"></i>
            <h3>Đơn hàng đã được thanh toán</h3>
            <p>Đơn hàng #<?php echo $orderNumber; ?> đã được thanh toán vào ngày <?php echo date('d/m/Y', strtotime($existingPayment['paymentDate'])); ?></p>
            <a href="orders.php" class="btn btn-primary">Xem đơn hàng của tôi</a>
        </div>
        
    <?php else: ?>
        <h2 class="mb-4"><i class="fas fa-credit-card"></i> Thanh toán đơn hàng</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Mã đơn hàng:</strong> #<?php echo $orderNumber; ?></p>
                                <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y', strtotime($order['orderDate'])); ?></p>
                                <p><strong>Trạng thái:</strong> <span class="badge bg-warning"><?php echo $order['status']; ?></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['customerName']); ?></p>
                                <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['addressLine1'] . ', ' . $order['city'] . ', ' . $order['country']); ?></p>
                            </div>
                        </div>
                        
                        <h6 class="mb-3">Chi tiết đơn hàng:</h6>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['productName']); ?></td>
                                        <td><?php echo $item['quantityOrdered']; ?></td>
                                        <td><?php echo number_format($item['priceEach'], 0); ?> USD</td>
                                        <td><?php echo number_format($item['priceEach'] * $item['quantityOrdered'], 0); ?> USD</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Tổng cộng:</th>
                                    <th class="text-danger"><?php echo number_format($total, 0); ?> USD</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-wallet"></i> Phương thức thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="cod" value="COD" required>
                                <label class="form-check-label" for="cod">
                                    <i class="fas fa-money-bill-wave text-success"></i> <strong>Thanh toán khi nhận hàng (COD)</strong>
                                    <p class="text-muted small mb-0">Thanh toán bằng tiền mặt khi nhận hàng</p>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="bank" value="Bank Transfer">
                                <label class="form-check-label" for="bank">
                                    <i class="fas fa-university text-primary"></i> <strong>Chuyển khoản ngân hàng</strong>
                                    <p class="text-muted small mb-0">Chuyển khoản qua ngân hàng</p>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="card" value="Credit Card">
                                <label class="form-check-label" for="card">
                                    <i class="fas fa-credit-card text-warning"></i> <strong>Thẻ tín dụng/Ghi nợ</strong>
                                    <p class="text-muted small mb-0">Thanh toán bằng thẻ Visa, MasterCard</p>
                                </label>
                            </div>
                            
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="ewallet" value="E-Wallet">
                                <label class="form-check-label" for="ewallet">
                                    <i class="fas fa-mobile-alt text-info"></i> <strong>Ví điện tử</strong>
                                    <p class="text-muted small mb-0">MoMo, ZaloPay, VNPay</p>
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-check-circle"></i> Xác nhận thanh toán
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Thanh toán an toàn</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            <i class="fas fa-check text-success"></i> Thông tin được mã hóa SSL
                        </p>
                        <p class="small text-muted mb-3">
                            <i class="fas fa-check text-success"></i> Bảo mật thông tin cá nhân
                        </p>
                        <p class="small text-muted mb-3">
                            <i class="fas fa-check text-success"></i> Hỗ trợ 24/7
                        </p>
                        <p class="small text-muted mb-0">
                            <i class="fas fa-check text-success"></i> Hoàn tiền nếu có vấn đề
                        </p>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <h3 class="text-danger mb-0"><?php echo number_format($total, 0); ?> USD</h3>
                        <small class="text-muted">Tổng thanh toán</small>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
