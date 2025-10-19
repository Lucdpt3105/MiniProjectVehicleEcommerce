<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$productCode = $_POST['productCode'] ?? '';
$quantity = (int)($_POST['quantity'] ?? 1);

if (empty($productCode) || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit();
}

// Check product exists and has stock
$stmt = $conn->prepare("SELECT quantityInStock FROM products WHERE productCode = ?");
$stmt->bind_param("s", $productCode);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
    exit();
}

if ($product['quantityInStock'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Không đủ hàng trong kho']);
    exit();
}

// Generate session ID if not exists
if (!isset($_SESSION['cart_sessionID'])) {
    $_SESSION['cart_sessionID'] = session_id();
}

$sessionID = $_SESSION['cart_sessionID'];

// Check if product already in cart
$stmt = $conn->prepare("SELECT cartID, quantity FROM cart WHERE sessionID = ? AND productCode = ?");
$stmt->bind_param("ss", $sessionID, $productCode);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();

if ($existing) {
    // Update quantity
    $newQuantity = $existing['quantity'] + $quantity;
    if ($newQuantity > $product['quantityInStock']) {
        echo json_encode(['success' => false, 'message' => 'Vượt quá số lượng trong kho']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cartID = ?");
    $stmt->bind_param("ii", $newQuantity, $existing['cartID']);
    $stmt->execute();
} else {
    // Insert new
    $stmt = $conn->prepare("INSERT INTO cart (sessionID, productCode, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $sessionID, $productCode, $quantity);
    $stmt->execute();
}

// Get cart count
$stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE sessionID = ?");
$stmt->bind_param("s", $sessionID);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$cartCount = $result['total'] ?? 0;

echo json_encode([
    'success' => true,
    'message' => 'Đã thêm vào giỏ hàng',
    'cartCount' => $cartCount
]);
?>
