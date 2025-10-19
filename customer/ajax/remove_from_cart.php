<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit();
}

$cartID = (int)($_POST['cartID'] ?? 0);

if ($cartID < 1) {
    echo json_encode(['success' => false]);
    exit();
}

$stmt = $conn->prepare("DELETE FROM cart WHERE cartID = ?");
$stmt->bind_param("i", $cartID);
$stmt->execute();

echo json_encode(['success' => true]);
?>
