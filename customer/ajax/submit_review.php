<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$productCode = $_POST['productCode'] ?? '';
$rating = (int)($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
$userID = getUserID();

if (empty($productCode) || $rating < 1 || $rating > 5 || empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit();
}

// Check if user already reviewed this product
$stmt = $conn->prepare("SELECT reviewID FROM reviews WHERE productCode = ? AND userID = ?");
$stmt->bind_param("si", $productCode, $userID);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi']);
    exit();
}

// Insert review
$stmt = $conn->prepare("INSERT INTO reviews (productCode, userID, rating, comment) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siis", $productCode, $userID, $rating, $comment);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Đã gửi đánh giá thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
}
?>
