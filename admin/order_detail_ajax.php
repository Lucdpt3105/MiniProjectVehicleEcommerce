<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireLogin();
requireAdmin();

$orderNumber = $_GET['id'] ?? 0;

// Get order details
$order = $conn->query("SELECT o.*, c.*
                       FROM orders o
                       JOIN customers c ON o.customerNumber = c.customerNumber
                       WHERE o.orderNumber = $orderNumber")->fetch_assoc();

if (!$order) {
    echo '<div class="alert alert-danger">Không tìm thấy đơn hàng</div>';
    exit;
}

// Get order items
$items = $conn->query("SELECT od.*, p.productName
                      FROM orderdetails od
                      JOIN products p ON od.productCode = p.productCode
                      WHERE od.orderNumber = $orderNumber");
?>

<div class="row">
    <div class="col-md-6">
        <h6><i class="fas fa-user"></i> Thông tin khách hàng</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Tên:</strong></td>
                <td><?php echo htmlspecialchars($order['customerName']); ?></td>
            </tr>
            <tr>
                <td><strong>Liên hệ:</strong></td>
                <td><?php echo htmlspecialchars($order['contactFirstName'] . ' ' . $order['contactLastName']); ?></td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td><?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td><strong>Điện thoại:</strong></td>
                <td><?php echo htmlspecialchars($order['phone']); ?></td>
            </tr>
            <tr>
                <td><strong>Địa chỉ:</strong></td>
                <td>
                    <?php echo htmlspecialchars($order['addressLine1']); ?><br>
                    <?php if ($order['addressLine2']): ?>
                        <?php echo htmlspecialchars($order['addressLine2']); ?><br>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($order['city'] . ', ' . ($order['state'] ? $order['state'] . ', ' : '') . $order['country'] . ' ' . $order['postalCode']); ?>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h6><i class="fas fa-receipt"></i> Thông tin đơn hàng</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Mã đơn:</strong></td>
                <td>#<?php echo $order['orderNumber']; ?></td>
            </tr>
            <tr>
                <td><strong>Ngày đặt:</strong></td>
                <td><?php echo date('d/m/Y H:i', strtotime($order['orderDate'])); ?></td>
            </tr>
            <tr>
                <td><strong>Ngày yêu cầu:</strong></td>
                <td><?php echo date('d/m/Y', strtotime($order['requiredDate'])); ?></td>
            </tr>
            <tr>
                <td><strong>Ngày giao:</strong></td>
                <td>
                    <?php 
                    if ($order['shippedDate']) {
                        echo date('d/m/Y', strtotime($order['shippedDate']));
                    } else {
                        echo '<span class="text-muted">Chưa giao</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td><strong>Trạng thái:</strong></td>
                <td>
                    <span class="badge <?php 
                        echo match($order['status']) {
                            'Processing' => 'bg-warning',
                            'Shipped' => 'bg-info',
                            'Delivered' => 'bg-success',
                            'Cancelled' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    ?>">
                        <?php echo $order['status']; ?>
                    </span>
                </td>
            </tr>
            <?php if ($order['comments']): ?>
            <tr>
                <td><strong>Ghi chú:</strong></td>
                <td><?php echo htmlspecialchars($order['comments']); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<hr>

<h6><i class="fas fa-box"></i> Sản phẩm trong đơn</h6>
<div class="table-responsive">
    <table class="table table-sm">
        <thead class="table-light">
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            while ($item = $items->fetch_assoc()): 
                $subtotal = $item['quantityOrdered'] * $item['priceEach'];
                $total += $subtotal;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['productName']); ?></td>
                    <td><?php echo $item['quantityOrdered']; ?></td>
                    <td><?php echo number_format($item['priceEach'], 2); ?> USD</td>
                    <td><strong><?php echo number_format($subtotal, 2); ?> USD</strong></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot class="table-light">
            <tr>
                <th colspan="3" class="text-end">Tổng cộng:</th>
                <th><?php echo number_format($total, 2); ?> USD</th>
            </tr>
        </tfoot>
    </table>
</div>
