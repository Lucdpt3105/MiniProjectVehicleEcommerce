<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireLogin();

$orderNumber = $_GET['id'] ?? null;
$userID = getUserID();

if (!$orderNumber) {
    header("Location: orders.php");
    exit();
}

// Get order
$stmt = $conn->prepare("SELECT o.*, c.* 
                        FROM orders o
                        JOIN customers c ON o.customerNumber = c.customerNumber
                        WHERE o.orderNumber = ? AND o.customerNumber = ?");
$stmt->bind_param("ii", $orderNumber, $userID);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: orders.php");
    exit();
}

// Get order items
$items = $conn->query("SELECT od.*, p.productName,
                       (SELECT image_url FROM product_images WHERE productCode = p.productCode AND is_main = 1 LIMIT 1) as main_image
                       FROM orderdetails od
                       JOIN products p ON od.productCode = p.productCode
                       WHERE od.orderNumber = $orderNumber");

$total = 0;

$pageTitle = "Chi tiết đơn hàng #$orderNumber";
include '../includes/header.php';
?>

<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="orders.php">Đơn hàng</a></li>
            <li class="breadcrumb-item active">Chi tiết đơn hàng #<?php echo $orderNumber; ?></li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-receipt"></i> Chi tiết đơn hàng #<?php echo $orderNumber; ?></h2>
        <span class="badge <?php 
            echo match($order['status']) {
                'Processing' => 'bg-warning',
                'Shipped' => 'bg-info',
                'Delivered' => 'bg-success',
                'Cancelled' => 'bg-danger',
                'Paid' => 'bg-primary',
                default => 'bg-secondary'
            };
        ?> fs-6">
            <?php echo $order['status']; ?>
        </span>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-box"></i> Sản phẩm</h5>
                </div>
                <div class="card-body">
                    <?php while ($item = $items->fetch_assoc()): 
                        $subtotal = $item['priceEach'] * $item['quantityOrdered'];
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
                            <div class="col-md-5">
                                <h6><?php echo htmlspecialchars($item['productName']); ?></h6>
                                <small class="text-muted">Mã: <?php echo htmlspecialchars($item['productCode']); ?></small>
                            </div>
                            <div class="col-md-2 text-center">
                                <p class="mb-0">x<?php echo $item['quantityOrdered']; ?></p>
                            </div>
                            <div class="col-md-3 text-end">
                                <p class="mb-0"><strong><?php echo number_format($subtotal, 0); ?> USD</strong></p>
                                <small class="text-muted"><?php echo number_format($item['priceEach'], 0); ?> USD/sp</small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    
                    <div class="text-end">
                        <h4 class="text-danger">Tổng cộng: <?php echo number_format($total, 0); ?> USD</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <p><strong>Ngày đặt:</strong><br>
                       <?php echo date('d/m/Y H:i', strtotime($order['orderDate'])); ?></p>
                    
                    <p><strong>Ngày giao dự kiến:</strong><br>
                       <?php echo date('d/m/Y', strtotime($order['requiredDate'])); ?></p>
                    
                    <?php if ($order['shippedDate']): ?>
                        <p><strong>Ngày giao thực tế:</strong><br>
                           <span class="text-success"><?php echo date('d/m/Y', strtotime($order['shippedDate'])); ?></span></p>
                    <?php endif; ?>
                    
                    <?php if ($order['comments']): ?>
                        <p><strong>Ghi chú:</strong><br>
                           <?php echo nl2br(htmlspecialchars($order['comments'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong><?php echo htmlspecialchars($order['customerName']); ?></strong></p>
                    <p class="mb-1"><?php echo htmlspecialchars($order['phone']); ?></p>
                    <p class="mb-1"><?php echo htmlspecialchars($order['addressLine1']); ?></p>
                    <?php if ($order['addressLine2']): ?>
                        <p class="mb-1"><?php echo htmlspecialchars($order['addressLine2']); ?></p>
                    <?php endif; ?>
                    <p class="mb-0">
                        <?php echo htmlspecialchars($order['city']); ?>, 
                        <?php echo htmlspecialchars($order['country']); ?>
                        <?php echo $order['postalCode'] ? ' ' . htmlspecialchars($order['postalCode']) : ''; ?>
                    </p>
                </div>
            </div>
            
            <?php if ($order['status'] === 'Processing'): ?>
                <button class="btn btn-danger w-100" onclick="cancelOrder(<?php echo $orderNumber; ?>)">
                    <i class="fas fa-times"></i> Hủy đơn hàng
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function cancelOrder(orderNumber) {
    if (!confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
        return;
    }
    
    fetch('ajax/cancel_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `orderNumber=${orderNumber}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Đã hủy đơn hàng thành công', 'success');
            setTimeout(() => location.href = 'orders.php', 1500);
        } else {
            showNotification(data.message || 'Có lỗi xảy ra', 'danger');
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>
