<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

header('Content-Type: application/json');

// Set default employee session if not logged in
if (!isLoggedIn()) {
    $_SESSION['userID'] = 2; // Default employee ID
    $_SESSION['username'] = 'employee';
    $_SESSION['email'] = 'employee@minishop.com';
    $_SESSION['role'] = 'employee';
    $_SESSION['LAST_ACTIVITY'] = time();
}

$employeeID = $_SESSION['userID'] ?? 1165;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderNumber = intval($_POST['orderNumber'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    $comments = trim($_POST['comments'] ?? '');
    
    if ($orderNumber <= 0) {
        echo json_encode(['success' => false, 'message' => 'Mã đơn hàng không hợp lệ']);
        exit;
    }
    
    // Validate status
    $validStatuses = ['In Process', 'Shipped', 'Resolved', 'Cancelled', 'On Hold', 'Disputed'];
    if (!in_array($status, $validStatuses)) {
        echo json_encode(['success' => false, 'message' => 'Trạng thái không hợp lệ']);
        exit;
    }
    
    // Check if order belongs to this employee's customers
    $checkStmt = $conn->prepare("SELECT o.orderNumber 
                                  FROM orders o 
                                  JOIN customers c ON o.customerNumber = c.customerNumber 
                                  WHERE o.orderNumber = ? AND c.salesRepEmployeeNumber = ?");
    $checkStmt->bind_param("ii", $orderNumber, $employeeID);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Bạn không có quyền cập nhật đơn hàng này']);
        exit;
    }
    
    // Update order status (bảng orders có cột: status, comments)
    // Note: Bảng orders trong classicmodels có cột comments
    $stmt = $conn->prepare("UPDATE orders SET status = ?, comments = ? WHERE orderNumber = ?");
    $stmt->bind_param("ssi", $status, $comments, $orderNumber);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Cập nhật trạng thái thành công',
            'newStatus' => $status
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
}
?>
