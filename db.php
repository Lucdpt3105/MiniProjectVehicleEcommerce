<?php
$servername = "localhost";
$username = "root"; // hoặc user bạn đã tạo
$password = "Luc3105dev#"; // nếu bạn không đặt mật khẩu thì để trống
$database = "classicmodels";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
