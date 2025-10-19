<?php
// Load environment variables
require_once __DIR__ . '/config/env.php';
EnvLoader::load(__DIR__ . '/.env');

$servername = EnvLoader::get('DB_HOST', 'localhost');
$username = EnvLoader::get('DB_USER', 'root');
$password = EnvLoader::get('DB_PASS', '');
$database = EnvLoader::get('DB_NAME', 'classicmodels');

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
