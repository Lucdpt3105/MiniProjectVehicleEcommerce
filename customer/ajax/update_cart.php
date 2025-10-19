<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit();
}

$cartID = (int)($_POST['cartID'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 0);

if ($cartID < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart ID']);
    exit();
}

if ($quantity < 1) {
    // Delete if quantity is 0
    $stmt = $conn->prepare("DELETE FROM cart WHERE cartID = ?");
    $stmt->bind_param("i", $cartID);
    $stmt->execute();
} else {
    // Update quantity
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cartID = ?");
    $stmt->bind_param("ii", $quantity, $cartID);
    $stmt->execute();
}

echo json_encode(['success' => true]);
?>
