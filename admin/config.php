<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'gtpt');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
function checkAdmin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
        header('Location: login.php');
        exit;
    }
}
?>
