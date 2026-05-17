<?php
// user_edit.php
require_once 'config.php';
checkAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id       = (int)$_POST['id'];
    $name     = $conn->real_escape_string(trim($_POST['name']));
    $username = $conn->real_escape_string(trim($_POST['username']));
    $email    = $conn->real_escape_string(trim($_POST['email']));
    $phone    = $conn->real_escape_string(trim($_POST['phone']));
    $role     = (int)$_POST['role'];

    $conn->query("UPDATE user SET Name='$name', Username='$username', Email='$email', Phone='$phone', Role=$role WHERE ID=$id");
}
header('Location: user.php?ok=saved');
exit;
?>
