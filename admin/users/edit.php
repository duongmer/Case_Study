<?php
include('../../ketnoi.php');

$id = $_GET['id'];
$data = $conn->query("SELECT * FROM user WHERE ID=$id")->fetch_assoc();

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $phone = $_POST['phone'];

    // nếu nhập password mới
    if(!empty($_POST['password'])){
        $password = $_POST['password'];
        $conn->query("UPDATE user 
            SET Name='$name', Username='$username', Email='$email',
                Password='$password', Role='$role', Phone='$phone'
            WHERE ID=$id");
    }else{
        $conn->query("UPDATE user 
            SET Name='$name', Username='$username', Email='$email',
                Role='$role', Phone='$phone'
            WHERE ID=$id");
    }

    // upload avatar mới nếu có
    if(!empty($_FILES['avatar']['name'])){
        $avatar = $_FILES['avatar']['name'];
        move_uploaded_file($_FILES['avatar']['tmp_name'], "../../images/".$avatar);

        $conn->query("UPDATE user SET Avatar='$avatar' WHERE ID=$id");
    }

    header("Location: ../index.php?page=users");
}
?>

<h3>Sửa người dùng</h3>

<form method="POST" enctype="multipart/form-data">
Tên: <input value="<?= $data['Name'] ?>" name="name"><br>
Username: <input value="<?= $data['Username'] ?>" name="username"><br>
Email: <input value="<?= $data['Email'] ?>" name="email"><br>

Password mới: <input type="password" name="password"><br>

Role:
<select name="role">
    <option value="0" <?= $data['Role']==0?'selected':'' ?>>User</option>
    <option value="1" <?= $data['Role']==1?'selected':'' ?>>Admin</option>
</select><br>

Phone: <input value="<?= $data['Phone'] ?>" name="phone"><br>

Ảnh hiện tại:<br>
<img src="../../images/<?= $data['Avatar'] ?>" width="100"><br>

Avatar mới: <input type="file" name="avatar"><br>

<button name="submit">Cập nhật</button>
</form>