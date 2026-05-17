<?php
session_start();
require_once 'config.php';

// Đăng xuất
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Đã đăng nhập rồi
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM user WHERE Username = ? AND Password = ? AND Role = 1");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id']   = $user['ID'];
        $_SESSION['user_name'] = $user['Name'];
        $_SESSION['role']      = $user['Role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Sai tài khoản / mật khẩu hoặc không có quyền Admin!';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập | Quản trị Admin</title>
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  <style>
    body { background: #1a1a2e; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; font-family: Arial, sans-serif; }
    .login-wrap { background: #fff; border-radius: 10px; padding: 44px 40px 36px; width: 380px; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
    .login-wrap .logo { text-align: center; margin-bottom: 28px; }
    .login-wrap .logo i { font-size: 48px; color: #e67e22; }
    .login-wrap h2 { text-align: center; font-size: 20px; color: #2c3e50; margin-bottom: 24px; }
    .form-group { margin-bottom: 18px; }
    .form-group label { font-weight: 600; color: #555; font-size: 13px; display: block; margin-bottom: 6px; }
    .form-group input { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box; }
    .form-group input:focus { border-color: #e67e22; outline: none; }
    .btn-login { width: 100%; padding: 12px; background: #e67e22; color: #fff; border: none; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; }
    .btn-login:hover { background: #d35400; }
    .error-msg { background: #fdecea; color: #e74c3c; border-radius: 5px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px; }
    .hint { font-size: 12px; color: #aaa; text-align: center; margin-top: 16px; }
  </style>
</head>
<body>
<div class="login-wrap">
  <div class="logo"><i class='bx bx-home-heart'></i></div>
  <h2>🏠 Quản Trị Phòng Trọ</h2>
  <?php if ($error): ?>
    <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Tên đăng nhập</label>
      <input type="text" name="username" placeholder="Nhập username..." required autofocus>
    </div>
    <div class="form-group">
      <label>Mật khẩu</label>
      <input type="password" name="password" placeholder="Nhập mật khẩu..." required>
    </div>
    <button type="submit" class="btn-login">Đăng nhập</button>
  </form>
  <p class="hint">Tài khoản Admin: <b>an01</b> / <b>123456</b></p>
</div>
<script src="js/jquery-3.2.1.min.js"></script>
</body>
</html>
