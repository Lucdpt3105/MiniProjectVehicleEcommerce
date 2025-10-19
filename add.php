<?php 
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productCode = $_POST['productCode'];
    $productName = $_POST['productName'];
    $productLine = $_POST['productLine'];
    $productScale = $_POST['productScale'];
    $productVendor = $_POST['productVendor'];
    $productDescription = $_POST['productDescription'];
    $quantityInStock = $_POST['quantityInStock'];
    $buyPrice = $_POST['buyPrice'];
    $MSRP = $_POST['MSRP'];
    
    $stmt = $conn->prepare("INSERT INTO products (productCode, productName, productLine, productScale, productVendor, productDescription, quantityInStock, buyPrice, MSRP) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssids", $productCode, $productName, $productLine, $productScale, $productVendor, $productDescription, $quantityInStock, $buyPrice, $MSRP);
    
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Lỗi: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sản phẩm mới</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #fafafa; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 600px; }
        h1 { color: #333; }
        label { display: block; margin-top: 15px; color: #555; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 100px; resize: vertical; }
        button { margin-top: 20px; background: #27ae60; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #229954; }
        .back { display: inline-block; margin-top: 10px; text-decoration: none; color: #3498db; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thêm sản phẩm mới</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <label>Mã sản phẩm:</label>
            <input type="text" name="productCode" required>
            
            <label>Tên sản phẩm:</label>
            <input type="text" name="productName" required>
            
            <label>Dòng sản phẩm:</label>
            <select name="productLine" required>
                <option value="Classic Cars">Classic Cars</option>
                <option value="Motorcycles">Motorcycles</option>
                <option value="Planes">Planes</option>
                <option value="Ships">Ships</option>
                <option value="Trains">Trains</option>
                <option value="Trucks and Buses">Trucks and Buses</option>
                <option value="Vintage Cars">Vintage Cars</option>
            </select>
            
            <label>Tỷ lệ:</label>
            <input type="text" name="productScale" value="1:18" required>
            
            <label>Nhà cung cấp:</label>
            <input type="text" name="productVendor" required>
            
            <label>Mô tả:</label>
            <textarea name="productDescription" required></textarea>
            
            <label>Số lượng tồn kho:</label>
            <input type="number" name="quantityInStock" required>
            
            <label>Giá mua (USD):</label>
            <input type="number" step="0.01" name="buyPrice" required>
            
            <label>Giá bán lẻ đề xuất (USD):</label>
            <input type="number" step="0.01" name="MSRP" required>
            
            <button type="submit">Thêm sản phẩm</button>
        </form>
        
        <a class="back" href="index.php">← Quay lại danh sách</a>
    </div>
</body>
</html>
