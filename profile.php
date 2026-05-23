<?php
session_start();
$conn = new PDO("mysql:host=localhost;dbname=GTPT;charset=utf8mb4", "root", "");
if (!isset($_SESSION['user_id'])) header("Location: login.html");
$uid = $_SESSION['user_id']; $msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_info'])) {
        $avatar = $conn->query("SELECT Avatar FROM USER WHERE ID=$uid")->fetchColumn();
        if (!empty($_FILES['avatar']['name'])) {
            $avatar = $_FILES['avatar']['name'];
            move_uploaded_file($_FILES['avatar']['tmp_name'], "assets/images/$avatar");
        }
        $conn->prepare("UPDATE USER SET Name=?, Phone=?, Avatar=? WHERE ID=?")->execute([$_POST['name'], $_POST['phone'], $avatar, $uid]);
        $msg = "Thành công!";
    }
    if (isset($_POST['change_pass'])) {
        $curr_p = $conn->query("SELECT Password FROM USER WHERE ID=$uid")->fetchColumn();
        if ($_POST['old_pass'] === $curr_p) {
            $conn->prepare("UPDATE USER SET Password=? WHERE ID=?")->execute([$_POST['new_pass'], $uid]);
            $msg = "Đã đổi mật khẩu!";
        } else $msg = "Sai mật khẩu cũ!";
    }
}
$u = $conn->query("SELECT * FROM USER WHERE ID=$uid")->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Tài khoản</title>
  <link rel="icon" href="assets/favicon.ico" type="image/x-icon" sizes="16x16"/>

</head>
<body class="bg-light p-5">
    <div class="card mx-auto p-4 shadow-sm" style="max-width: 420px;">
        <h4 class="text-center mb-3">Tài khoản</h4>
        <?= $msg ? "<div class='alert alert-info p-2 small'>$msg</div>" : "" ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="text-center mb-3">
                <img src="assets/images/<?= $u['Avatar'] ?: 'default.png' ?>" class="rounded-circle border" width="80" height="80" style="object-fit:cover">
            </div>
            <input type="text" name="name" class="form-control mb-2" value="<?= $u['Name'] ?>" placeholder="Họ tên" required>
            <input type="text" name="phone" class="form-control mb-2" value="<?= $u['Phone'] ?>" placeholder="SĐT">
            
            <label for="file" class="btn btn-outline-secondary w-100 mb-3">Thay đổi ảnh đại diện</label>
            <input type="file" name="avatar" id="file" class="d-none">
            <button name="update_info" class="btn btn-primary w-100 mb-4">Lưu thay đổi</button>
        </form>
        <form method="POST" class="border-top pt-3">
            <input type="password" name="old_pass" placeholder="Mật khẩu cũ" class="form-control mb-2" required>
            <input type="password" name="new_pass" placeholder="Mật khẩu mới" class="form-control mb-3" required>
            <button name="change_pass" class="btn btn-warning w-100">Đổi mật khẩu</button>
        </form>
        <a href="index.php" class="d-block text-center mt-3 text-secondary text-decoration-none small">← Về trang chủ</a>
    </div>
</body>
</html>