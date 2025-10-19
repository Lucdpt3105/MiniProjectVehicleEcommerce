<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

// Set default admin session if not logged in
if (!isLoggedIn()) {
    $_SESSION['userID'] = 1; // Default admin ID
    $_SESSION['username'] = 'admin';
    $_SESSION['email'] = 'admin@minishop.com';
    $_SESSION['role'] = 'admin';
    $_SESSION['LAST_ACTIVITY'] = time();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Check if username already exists
    $check = $conn->prepare("SELECT userID FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Tên đăng nhập đã tồn tại']);
        exit();
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new employee
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role, created_at, updated_at) VALUES (?, ?, ?, ?, 'employee', NOW(), NOW())");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $full_name);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Thêm nhân viên thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi thêm nhân viên']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
