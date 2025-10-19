<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session timeout (30 minutes)
$timeout_duration = 1800;

// Check if session has expired
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: /mini_shop/login.php?timeout=1");
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time();

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['userID']) && isset($_SESSION['username']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isEmployee() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'employee';
}

function requireEmployee() {
    if (!isEmployee() && !isAdmin()) {
        header("Location: /mini_shop/customer/home.php");
        exit();
    }
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /mini_shop/login.php");
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: /mini_shop/customer/home.php");
        exit();
    }
}

function getUserID() {
    return $_SESSION['userID'] ?? null;
}

function getUsername() {
    return $_SESSION['username'] ?? null;
}

function getRole() {
    return $_SESSION['role'] ?? null;
}
?>
