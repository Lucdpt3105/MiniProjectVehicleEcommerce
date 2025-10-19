<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$orderNumber = (int)($_POST['orderNumber'] ?? 0);
$userID = getUserID();

if ($orderNumber < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid order']);
    exit();
}

// Check order belongs to user and is in Processing status
$stmt = $conn->prepare("SELECT orderNumber FROM orders WHERE orderNumber = ? AND customerNumber = ? AND status = 'Processing'");
$stmt->bind_param("ii", $orderNumber, $userID);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Không thể hủy đơn hàng này']);
    exit();
}

// Update order status
$stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE orderNumber = ?");
$stmt->bind_param("i", $orderNumber);

if ($stmt->execute()) {
    // Return products to stock
    $items = $conn->query("SELECT productCode, quantityOrdered FROM orderdetails WHERE orderNumber = $orderNumber");
    while ($item = $items->fetch_assoc()) {
        $conn->query("UPDATE products SET quantityInStock = quantityInStock + {$item['quantityOrdered']} 
                     WHERE productCode = '{$item['productCode']}'");
    }
    
    echo json_encode(['success' => true, 'message' => 'Đã hủy đơn hàng']);
} else {
    echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
}
?>
