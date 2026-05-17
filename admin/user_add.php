<?php
require_once 'config.php';
checkAdmin();

$pageTitle   = 'Thêm tài khoản';
$currentPage = 'user_add';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $conn->real_escape_string(trim($_POST['name']));
    $username = $conn->real_escape_string(trim($_POST['username']));
    $email    = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string(trim($_POST['password']));
    $phone    = $conn->real_escape_string(trim($_POST['phone']));
    $role     = (int)$_POST['role'];

    // Kiểm tra trùng username
    $check = $conn->query("SELECT ID FROM user WHERE Username='$username'");
    if ($check->num_rows > 0) {
        $error = 'Username đã tồn tại!';
    } else {
        $conn->query("INSERT INTO user (Name,Username,Email,Password,Role,Phone,Avatar)
            VALUES ('$name','$username','$email','$password',$role,'$phone','default.jpg')");
        header('Location: user.php?ok=added'); exit;
    }
}

include 'layout_top.php';
?>

<div class="app-title">
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><a href="user.php">Quản lý người dùng</a></li>
    <li class="breadcrumb-item"><a href="#">Thêm tài khoản</a></li>
  </ul>
  <div id="clock"></div>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="tile">
      <h3 class="tile-title">👤 Tạo tài khoản mới</h3>
      <div class="tile-body">
        <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>
        <form class="row" method="POST">
          <div class="form-group col-md-6">
            <label class="control-label">Họ và tên</label>
            <input class="form-control" type="text" name="name" placeholder="Nguyễn Văn A" required>
          </div>
          <div class="form-group col-md-6">
            <label class="control-label">Username</label>
            <input class="form-control" type="text" name="username" placeholder="nguyenvana" required>
          </div>
          <div class="form-group col-md-6">
            <label class="control-label">Email</label>
            <input class="form-control" type="email" name="email" placeholder="email@gmail.com" required>
          </div>
          <div class="form-group col-md-6">
            <label class="control-label">Số điện thoại</label>
            <input class="form-control" type="text" name="phone" placeholder="09xxxxxxxx">
          </div>
          <div class="form-group col-md-6">
            <label class="control-label">Mật khẩu</label>
            <input class="form-control" type="password" name="password" placeholder="Tối thiểu 6 ký tự" required>
          </div>
          <div class="form-group col-md-6">
            <label class="control-label">Vai trò</label>
            <select class="form-control" name="role">
              <option value="0">Người dùng thường</option>
              <option value="1">Admin</option>
            </select>
          </div>
          <div class="col-md-12" style="margin-top:10px;">
            <button class="btn btn-save" type="submit">💾 Lưu lại</button>
            <a class="btn btn-cancel" href="user.php">Hủy bỏ</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'layout_bottom.php'; ?>
