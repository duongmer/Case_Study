<?php
include 'config.php';
checkAdmin();
$error = '';
if (isset($_POST["btnsave"])) {
    $name     = $_POST['name'];
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $phone    = $_POST['phone'];
    $role     = (int)$_POST['role'];

    $sql_check = "SELECT Username, Email, Phone FROM user WHERE Username='$username' OR Email='$email' OR Phone='$phone'";
    $check = $conn->query($sql_check);
    if ($check->num_rows > 0) {
        $row = $check->fetch_assoc();
        if ($row['Username'] == $username) {
            $error = 'Username đã tồn tại!';
        } elseif ($row['Email'] == $email) {
            $error = 'Email đã tồn tại trong hệ thống!';
        } elseif ($row['Phone'] == $phone) {
            $error = 'Số điện thoại này đã được sử dụng!';
        }
    } else {
        $sql = "INSERT INTO user (Name, Username, Email, Password, Role, Phone, Avatar) VALUES ('$name', '$username', '$email', '$password', $role, '$phone', 'default.jpg')";
        $conn->query($sql);
        header('Location: user.php');
        exit;
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
        <?php if (!empty($error)) { ?>
          <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php } ?>
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
            <button class="btn btn-save" type="submit" name="btnsave">💾 Lưu lại</button>
            <a class="btn btn-cancel" href="user.php">Hủy bỏ</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'layout_bottom.php'; ?>
