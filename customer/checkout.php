<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireLogin();

if (!isset($_SESSION['cart_sessionID'])) {
    header("Location: cart.php");
    exit();
}

$sessionID = $_SESSION['cart_sessionID'];

// Get cart items
$stmt = $conn->prepare("SELECT c.*, p.productName, p.MSRP, p.quantityInStock
                        FROM cart c
                        JOIN products p ON c.productCode = p.productCode
                        WHERE c.sessionID = ?");
$stmt->bind_param("s", $sessionID);
$stmt->execute();
$cartItems = $stmt->get_result();

if ($cartItems->num_rows === 0) {
    header("Location: cart.php");
    exit();
}

// Calculate total
$total = 0;
$items = [];
while ($item = $cartItems->fetch_assoc()) {
    $subtotal = $item['MSRP'] * $item['quantity'];
    $total += $subtotal;
    $items[] = $item;
}

// Get user info if exists
$userID = getUserID();
$customerInfo = $conn->query("SELECT * FROM customers WHERE customerNumber = $userID LIMIT 1")->fetch_assoc();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customerName']);
    $contactFirstName = trim($_POST['contactFirstName']);
    $contactLastName = trim($_POST['contactLastName']);
    $phone = trim($_POST['phone']);
    $addressLine1 = trim($_POST['addressLine1']);
    $addressLine2 = trim($_POST['addressLine2'] ?? '');
    $city = trim($_POST['city']);
    $state = trim($_POST['state'] ?? '');
    $postalCode = trim($_POST['postalCode'] ?? '');
    $country = trim($_POST['country']);
    $comments = trim($_POST['comments'] ?? '');
    
    if (empty($customerName) || empty($contactFirstName) || empty($contactLastName) || 
        empty($phone) || empty($addressLine1) || empty($city) || empty($country)) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
    } else {
        $conn->begin_transaction();
        
        try {
            // Insert or update customer
            if ($customerInfo) {
                $customerNumber = $customerInfo['customerNumber'];
                $stmt = $conn->prepare("UPDATE customers SET customerName=?, contactLastName=?, contactFirstName=?, 
                                       phone=?, addressLine1=?, addressLine2=?, city=?, state=?, postalCode=?, country=? 
                                       WHERE customerNumber=?");
                $stmt->bind_param("ssssssssssi", $customerName, $contactLastName, $contactFirstName, $phone, 
                                 $addressLine1, $addressLine2, $city, $state, $postalCode, $country, $customerNumber);
                $stmt->execute();
            } else {
                $stmt = $conn->prepare("INSERT INTO customers (customerNumber, customerName, contactLastName, contactFirstName, 
                                       phone, addressLine1, addressLine2, city, state, postalCode, country, salesRepEmployeeNumber, creditLimit) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, 0)");
                $stmt->bind_param("issssssssss", $userID, $customerName, $contactLastName, $contactFirstName, $phone, 
                                 $addressLine1, $addressLine2, $city, $state, $postalCode, $country);
                $stmt->execute();
                $customerNumber = $userID;
            }
            
            // Create order
            $orderNumber = time(); // Simple order number
            $orderDate = date('Y-m-d');
            $requiredDate = date('Y-m-d', strtotime('+7 days'));
            $status = 'Processing';
            
            $stmt = $conn->prepare("INSERT INTO orders (orderNumber, orderDate, requiredDate, shippedDate, status, comments, customerNumber) 
                                   VALUES (?, ?, ?, NULL, ?, ?, ?)");
            $stmt->bind_param("issssi", $orderNumber, $orderDate, $requiredDate, $status, $comments, $customerNumber);
            $stmt->execute();
            
            // Insert order details
            foreach ($items as $item) {
                $priceEach = $item['MSRP'];
                $stmt = $conn->prepare("INSERT INTO orderdetails (orderNumber, productCode, quantityOrdered, priceEach, orderLineNumber) 
                                       VALUES (?, ?, ?, ?, 1)");
                $stmt->bind_param("isid", $orderNumber, $item['productCode'], $item['quantity'], $priceEach);
                $stmt->execute();
                
                // Update stock
                $newStock = $item['quantityInStock'] - $item['quantity'];
                $conn->query("UPDATE products SET quantityInStock = $newStock WHERE productCode = '{$item['productCode']}'");
            }
            
            // Clear cart
            $conn->query("DELETE FROM cart WHERE sessionID = '$sessionID'");
            
            $conn->commit();
            
            // Store order number in session for payment page
            $_SESSION['pending_order'] = $orderNumber;
            
            header("Location: payment.php?order=$orderNumber");
            exit();
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Thanh toán';
include '../includes/header.php';
?>

<div class="container my-4">
    <h2 class="mb-4"><i class="fas fa-credit-card"></i> Thanh toán</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Thông tin khách hàng</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tên công ty/Tên khách hàng <span class="text-danger">*</span></label>
                                <input type="text" name="customerName" class="form-control" 
                                       value="<?php echo htmlspecialchars($customerInfo['customerName'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Họ <span class="text-danger">*</span></label>
                                <input type="text" name="contactLastName" class="form-control" 
                                       value="<?php echo htmlspecialchars($customerInfo['contactLastName'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tên <span class="text-danger">*</span></label>
                                <input type="text" name="contactFirstName" class="form-control" 
                                       value="<?php echo htmlspecialchars($customerInfo['contactFirstName'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" 
                                   value="<?php echo htmlspecialchars($customerInfo['phone'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                            <input type="text" name="addressLine1" class="form-control" 
                                   value="<?php echo htmlspecialchars($customerInfo['addressLine1'] ?? ''); ?>" 
                                   placeholder="Số nhà, tên đường" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ 2 (tùy chọn)</label>
                            <input type="text" name="addressLine2" class="form-control" 
                                   value="<?php echo htmlspecialchars($customerInfo['addressLine2'] ?? ''); ?>" 
                                   placeholder="Phường, Quận">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Thành phố <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" 
                                       value="<?php echo htmlspecialchars($customerInfo['city'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tỉnh/Thành phố</label>
                                <input type="text" name="state" class="form-control" 
                                       value="<?php echo htmlspecialchars($customerInfo['state'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mã bưu điện</label>
                                <input type="text" name="postalCode" class="form-control" 
                                       value="<?php echo htmlspecialchars($customerInfo['postalCode'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Quốc gia <span class="text-danger">*</span></label>
                            <select name="country" class="form-select" required>
                                <option value="">Chọn quốc gia</option>
                                <option value="Vietnam" <?php echo ($customerInfo['country'] ?? '') === 'Vietnam' ? 'selected' : ''; ?>>Việt Nam</option>
                                <option value="USA" <?php echo ($customerInfo['country'] ?? '') === 'USA' ? 'selected' : ''; ?>>United States</option>
                                <option value="Japan" <?php echo ($customerInfo['country'] ?? '') === 'Japan' ? 'selected' : ''; ?>>Japan</option>
                                <option value="China" <?php echo ($customerInfo['country'] ?? '') === 'China' ? 'selected' : ''; ?>>China</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ghi chú đơn hàng</label>
                            <textarea name="comments" class="form-control" rows="3" 
                                      placeholder="Ghi chú về đơn hàng của bạn..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-check-circle"></i> Đặt hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-receipt"></i> Đơn hàng của bạn</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo htmlspecialchars($item['productName']); ?> × <?php echo $item['quantity']; ?></span>
                            <strong><?php echo number_format($item['MSRP'] * $item['quantity'], 0); ?> USD</strong>
                        </div>
                    <?php endforeach; ?>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <strong><?php echo number_format($total, 0); ?> USD</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển:</span>
                        <strong>Miễn phí</strong>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <h5>Tổng cộng:</h5>
                        <h5 class="text-danger"><?php echo number_format($total, 0); ?> USD</h5>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-info-circle text-primary"></i> Lưu ý</h6>
                    <ul class="small text-muted mb-0">
                        <li>Đơn hàng sẽ được xử lý trong 1-2 ngày làm việc</li>
                        <li>Miễn phí vận chuyển toàn quốc</li>
                        <li>Hỗ trợ đổi trả trong 7 ngày</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
