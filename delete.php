<?php
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$productCode = $_GET['id'];

// Nếu xác nhận xóa
if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    $stmt = $conn->prepare("DELETE FROM products WHERE productCode = ?");
    $stmt->bind_param("s", $productCode);
    
    if ($stmt->execute()) {
        header("Location: index.php?deleted=success");
    } else {
        header("Location: index.php?deleted=error");
    }
    exit();
}

// Lấy thông tin sản phẩm để hiển thị
$result = $conn->query("SELECT productCode, productName, buyPrice FROM products WHERE productCode = '$productCode'");
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xóa sản phẩm</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #fafafa; display: flex; justify-content: center; align-items: center; min-height: 80vh; }
        .container { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); max-width: 500px; text-align: center; }
        h1 { color: #e74c3c; margin-bottom: 20px; }
        .product-info { background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .product-info h3 { margin: 0 0 10px 0; color: #333; }
        .product-info p { margin: 5px 0; color: #666; }
        .buttons { margin-top: 30px; }
        .btn { display: inline-block; padding: 12px 30px; margin: 0 10px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn-secondary:hover { background: #7f8c8d; }
        .warning { color: #e74c3c; font-weight: bold; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚠️ Xác nhận xóa sản phẩm</h1>
        
        <div class="product-info">
            <h3><?= htmlspecialchars($product['productName']) ?></h3>
            <p><strong>Mã:</strong> <?= htmlspecialchars($product['productCode']) ?></p>
            <p><strong>Giá:</strong> <?= number_format($product['buyPrice'], 2) ?> USD</p>
        </div>
        
        <p class="warning">Bạn có chắc chắn muốn xóa sản phẩm này?<br>Hành động này không thể hoàn tác!</p>
        
        <div class="buttons">
            <a href="delete.php?id=<?= urlencode($productCode) ?>&confirm=yes" class="btn btn-danger">Xóa sản phẩm</a>
            <a href="index.php" class="btn btn-secondary">Hủy bỏ</a>
        </div>
    </div>
</body>
</html>
