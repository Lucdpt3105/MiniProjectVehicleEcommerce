<?php 
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$productCode = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productName = $_POST['productName'];
    $productLine = $_POST['productLine'];
    $productScale = $_POST['productScale'];
    $productVendor = $_POST['productVendor'];
    $productDescription = $_POST['productDescription'];
    $quantityInStock = $_POST['quantityInStock'];
    $buyPrice = $_POST['buyPrice'];
    $MSRP = $_POST['MSRP'];
    
    $stmt = $conn->prepare("UPDATE products SET productName=?, productLine=?, productScale=?, productVendor=?, productDescription=?, quantityInStock=?, buyPrice=?, MSRP=? WHERE productCode=?");
    $stmt->bind_param("sssssidds", $productName, $productLine, $productScale, $productVendor, $productDescription, $quantityInStock, $buyPrice, $MSRP, $productCode);
    
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Lỗi: " . $stmt->error;
    }
}

$result = $conn->query("SELECT * FROM products WHERE productCode = '$productCode'");
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
    <title>Sửa sản phẩm</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #fafafa; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 600px; }
        h1 { color: #333; }
        label { display: block; margin-top: 15px; color: #555; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 100px; resize: vertical; }
        button { margin-top: 20px; background: #f39c12; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #e67e22; }
        .back { display: inline-block; margin-top: 10px; text-decoration: none; color: #3498db; }
        .error { color: red; margin-top: 10px; }
        .readonly { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sửa sản phẩm</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <label>Mã sản phẩm:</label>
            <input type="text" value="<?= htmlspecialchars($product['productCode']) ?>" class="readonly" readonly>
            
            <label>Tên sản phẩm:</label>
            <input type="text" name="productName" value="<?= htmlspecialchars($product['productName']) ?>" required>
            
            <label>Dòng sản phẩm:</label>
            <select name="productLine" required>
                <option value="Classic Cars" <?= $product['productLine'] == 'Classic Cars' ? 'selected' : '' ?>>Classic Cars</option>
                <option value="Motorcycles" <?= $product['productLine'] == 'Motorcycles' ? 'selected' : '' ?>>Motorcycles</option>
                <option value="Planes" <?= $product['productLine'] == 'Planes' ? 'selected' : '' ?>>Planes</option>
                <option value="Ships" <?= $product['productLine'] == 'Ships' ? 'selected' : '' ?>>Ships</option>
                <option value="Trains" <?= $product['productLine'] == 'Trains' ? 'selected' : '' ?>>Trains</option>
                <option value="Trucks and Buses" <?= $product['productLine'] == 'Trucks and Buses' ? 'selected' : '' ?>>Trucks and Buses</option>
                <option value="Vintage Cars" <?= $product['productLine'] == 'Vintage Cars' ? 'selected' : '' ?>>Vintage Cars</option>
            </select>
            
            <label>Tỷ lệ:</label>
            <input type="text" name="productScale" value="<?= htmlspecialchars($product['productScale']) ?>" required>
            
            <label>Nhà cung cấp:</label>
            <input type="text" name="productVendor" value="<?= htmlspecialchars($product['productVendor']) ?>" required>
            
            <label>Mô tả:</label>
            <textarea name="productDescription" required><?= htmlspecialchars($product['productDescription']) ?></textarea>
            
            <label>Số lượng tồn kho:</label>
            <input type="number" name="quantityInStock" value="<?= $product['quantityInStock'] ?>" required>
            
            <label>Giá mua (USD):</label>
            <input type="number" step="0.01" name="buyPrice" value="<?= $product['buyPrice'] ?>" required>
            
            <label>Giá bán lẻ đề xuất (USD):</label>
            <input type="number" step="0.01" name="MSRP" value="<?= $product['MSRP'] ?>" required>
            
            <button type="submit">Cập nhật sản phẩm</button>
        </form>
        
        <a class="back" href="index.php">← Quay lại danh sách</a>
    </div>
</body>
</html>
