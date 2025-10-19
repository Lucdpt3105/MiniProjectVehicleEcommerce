<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

// Set default employee session if not logged in
if (!isLoggedIn()) {
    $_SESSION['userID'] = 2; // Default employee ID
    $_SESSION['username'] = 'employee';
    $_SESSION['email'] = 'employee@minishop.com';
    $_SESSION['role'] = 'employee';
    $_SESSION['LAST_ACTIVITY'] = time();
}

$employeeID = 1165; // Use a real employee ID from employees table

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderNumber = intval($_POST['orderNumber']);
    $status = trim($_POST['status']);
    $comments = trim($_POST['comments']);
    
    // Update order status
    $stmt = $conn->prepare("UPDATE orders SET status = ?, comments = ? WHERE orderNumber = ?");
    $stmt->bind_param("ssi", $status, $comments, $orderNumber);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
