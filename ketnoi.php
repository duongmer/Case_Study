<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gtpt";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // kiểm tra lỗi
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }
?>