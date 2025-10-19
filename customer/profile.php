<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireLogin();

$userID = getUserID();
$user = $conn->query("SELECT * FROM users WHERE userID = $userID")->fetch_assoc();
$customer = $conn->query("SELECT * FROM customers WHERE customerNumber = $userID")->fetch_assoc();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_info') {
        $email = trim($_POST['email']);
        $customerName = trim($_POST['customerName'] ?? '');
        $contactFirstName = trim($_POST['contactFirstName'] ?? '');
        $contactLastName = trim($_POST['contactLastName'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $addressLine1 = trim($_POST['addressLine1'] ?? '');
        $addressLine2 = trim($_POST['addressLine2'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $postalCode = trim($_POST['postalCode'] ?? '');
        $country = trim($_POST['country'] ?? '');
        
        // Update user email
        $stmt = $conn->prepare("UPDATE users SET email = ? WHERE userID = ?");
        $stmt->bind_param("si", $email, $userID);
        $stmt->execute();
        
        // Update or insert customer info
        if ($customer) {
            $stmt = $conn->prepare("UPDATE customers SET customerName=?, contactLastName=?, contactFirstName=?, 
                                   phone=?, addressLine1=?, addressLine2=?, city=?, state=?, postalCode=?, country=? 
                                   WHERE customerNumber=?");
            $stmt->bind_param("ssssssssssi", $customerName, $contactLastName, $contactFirstName, $phone, 
                             $addressLine1, $addressLine2, $city, $state, $postalCode, $country, $userID);
        } else {
            $stmt = $conn->prepare("INSERT INTO customers (customerNumber, customerName, contactLastName, contactFirstName, 
                                   phone, addressLine1, addressLine2, city, state, postalCode, country, creditLimit) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
            $stmt->bind_param("issssssssss", $userID, $customerName, $contactLastName, $contactFirstName, $phone, 
                             $addressLine1, $addressLine2, $city, $state, $postalCode, $country);
        }
        
        if ($stmt->execute()) {
            $success = 'Cập nhật thông tin thành công!';
            $customer = $conn->query("SELECT * FROM customers WHERE customerNumber = $userID")->fetch_assoc();
        } else {
            $error = 'Có lỗi xảy ra!';
        }
        
    } elseif ($action === 'change_password') {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'Vui lòng điền đầy đủ thông tin!';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Mật khẩu mới không khớp!';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Mật khẩu phải có ít nhất 6 ký tự!';
        } else {
            // Verify current password
            if (password_verify($currentPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE userID = ?");
                $stmt->bind_param("si", $hashedPassword, $userID);
                
                if ($stmt->execute()) {
                    $success = 'Đổi mật khẩu thành công!';
                } else {
                    $error = 'Có lỗi xảy ra!';
                }
            } else {
                $error = 'Mật khẩu hiện tại không đúng!';
            }
        }
    }
}

$pageTitle = 'Hồ sơ của tôi';
include '../includes/header.php';
?>

<div class="container my-4">
    <h2 class="mb-4"><i class="fas fa-user-circle"></i> Hồ sơ của tôi</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h5><?php echo htmlspecialchars($user['username']); ?></h5>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                    <hr>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="showTab('info')">
                            <i class="fas fa-user"></i> Thông tin
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="showTab('password')">
                            <i class="fas fa-lock"></i> Đổi mật khẩu
                        </button>
                        <a href="orders.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-receipt"></i> Đơn hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <!-- Personal Info Tab -->
            <div id="infoTab" class="tab-content-custom">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Thông tin cá nhân</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_info">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                            
                            <hr>
                            <h6 class="mb-3">Thông tin liên hệ</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Tên công ty/Tên đầy đủ</label>
                                <input type="text" name="customerName" class="form-control" 
                                       value="<?php echo htmlspecialchars($customer['customerName'] ?? ''); ?>">
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Họ</label>
                                    <input type="text" name="contactLastName" class="form-control" 
                                           value="<?php echo htmlspecialchars($customer['contactLastName'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tên</label>
                                    <input type="text" name="contactFirstName" class="form-control" 
                                           value="<?php echo htmlspecialchars($customer['contactFirstName'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="tel" name="phone" class="form-control" 
                                       value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ</label>
                                <input type="text" name="addressLine1" class="form-control" 
                                       value="<?php echo htmlspecialchars($customer['addressLine1'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ 2</label>
                                <input type="text" name="addressLine2" class="form-control" 
                                       value="<?php echo htmlspecialchars($customer['addressLine2'] ?? ''); ?>">
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Thành phố</label>
                                    <input type="text" name="city" class="form-control" 
                                           value="<?php echo htmlspecialchars($customer['city'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tỉnh/Thành phố</label>
                                    <input type="text" name="state" class="form-control" 
                                           value="<?php echo htmlspecialchars($customer['state'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Mã bưu điện</label>
                                    <input type="text" name="postalCode" class="form-control" 
                                           value="<?php echo htmlspecialchars($customer['postalCode'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Quốc gia</label>
                                <input type="text" name="country" class="form-control" 
                                       value="<?php echo htmlspecialchars($customer['country'] ?? 'Vietnam'); ?>">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Change Password Tab -->
            <div id="passwordTab" class="tab-content-custom" style="display: none;">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-lock"></i> Đổi mật khẩu</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="change_password">
                            
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" name="new_password" class="form-control" required minlength="6">
                                <small class="text-muted">Ít nhất 6 ký tự</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" name="confirm_password" class="form-control" required minlength="6">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i> Đổi mật khẩu
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content-custom').forEach(el => el.style.display = 'none');
    document.getElementById(tab + 'Tab').style.display = 'block';
}
</script>

<?php include '../includes/footer.php'; ?>
