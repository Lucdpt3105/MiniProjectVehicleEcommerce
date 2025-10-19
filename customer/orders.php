<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireLogin();

$userID = getUserID();

// Get all orders
$orders = $conn->query("SELECT o.*, 
                        (SELECT SUM(quantityOrdered * priceEach) FROM orderdetails WHERE orderNumber = o.orderNumber) as total,
                        (SELECT COUNT(*) FROM orderdetails WHERE orderNumber = o.orderNumber) as item_count
                        FROM orders o
                        WHERE o.customerNumber = $userID
                        ORDER BY o.orderDate DESC");

$pageTitle = 'Đơn hàng của tôi';
include '../includes/header.php';
?>

<div class="container my-4">
    <h2 class="mb-4"><i class="fas fa-receipt"></i> Đơn hàng của tôi</h2>
    
    <?php if ($orders->num_rows > 0): ?>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Đơn hàng #<?php echo $order['orderNumber']; ?></strong>
                        <span class="text-muted ms-3">
                            <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($order['orderDate'])); ?>
                        </span>
                    </div>
                    <span class="badge <?php 
                        echo match($order['status']) {
                            'Processing' => 'bg-warning',
                            'Shipped' => 'bg-info',
                            'Delivered' => 'bg-success',
                            'Cancelled' => 'bg-danger',
                            'Paid' => 'bg-primary',
                            default => 'bg-secondary'
                        };
                    ?>">
                        <?php echo $order['status']; ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-2">
                                <i class="fas fa-box"></i> <strong><?php echo $order['item_count']; ?></strong> sản phẩm
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-money-bill-wave"></i> Tổng tiền: 
                                <strong class="text-danger"><?php echo number_format($order['total'], 0); ?> USD</strong>
                            </p>
                            <?php if ($order['shippedDate']): ?>
                                <p class="mb-2 text-success">
                                    <i class="fas fa-truck"></i> Đã giao: <?php echo date('d/m/Y', strtotime($order['shippedDate'])); ?>
                                </p>
                            <?php else: ?>
                                <p class="mb-2 text-muted">
                                    <i class="fas fa-clock"></i> Dự kiến giao: <?php echo date('d/m/Y', strtotime($order['requiredDate'])); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($order['comments']): ?>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-comment"></i> <?php echo htmlspecialchars($order['comments']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="order_detail.php?id=<?php echo $order['orderNumber']; ?>" class="btn btn-sm btn-primary mb-2">
                                <i class="fas fa-eye"></i> Chi tiết
                            </a>
                            <?php if ($order['status'] === 'Processing'): ?>
                                <button class="btn btn-sm btn-danger mb-2" 
                                        onclick="cancelOrder(<?php echo $order['orderNumber']; ?>)">
                                    <i class="fas fa-times"></i> Hủy đơn
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-bag fa-5x text-muted mb-4"></i>
            <h3>Bạn chưa có đơn hàng nào</h3>
            <p class="text-muted">Hãy bắt đầu mua sắm ngay!</p>
            <a href="products.php" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-cart"></i> Mua sắm ngay
            </a>
        </div>
    <?php endif; ?>
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
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(data.message || 'Có lỗi xảy ra', 'danger');
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>
