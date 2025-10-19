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
    $productCode = trim($_POST['productCode']);
    $quantity = intval($_POST['quantity']);
    $buyPrice = floatval($_POST['buyPrice']);
    $msrp = floatval($_POST['msrp']);
    
    // Update product stock and prices
    $stmt = $conn->prepare("UPDATE products SET quantityInStock = ?, buyPrice = ?, MSRP = ? WHERE productCode = ?");
    $stmt->bind_param("idds", $quantity, $buyPrice, $msrp, $productCode);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật sản phẩm thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
