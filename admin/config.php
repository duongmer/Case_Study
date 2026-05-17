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

function alert($msg, $type = 'success') {
    return "<script>
        $(document).ready(function(){
            swal('" . ($type == 'success' ? 'Thành công!' : 'Lỗi!') . "', '$msg', '$type');
        });
    </script>";
}
?>
