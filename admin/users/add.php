<?php
include('../../ketnoi.php');

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $phone = $_POST['phone'];

    // upload avatar
    $avatar = $_FILES['avatar']['name'];
    move_uploaded_file($_FILES['avatar']['tmp_name'], "../../images/".$avatar);

    $sql = "INSERT INTO user(Name, Username, Email, Password, Role, Phone, Avatar)
            VALUES('$name','$username','$email','$password','$role','$phone','$avatar')";

    $conn->query($sql);

    header("Location: ../index.php?page=users");
}
?>

<h3>Thêm người dùng</h3>

<form method="POST" enctype="multipart/form-data">
Tên: <input name="name"><br>
Username: <input name="username"><br>
Email: <input name="email"><br>
Password: <input type="password" name="password"><br>

Role:
<select name="role">
    <option value="0">User</option>
    <option value="1">Admin</option>
</select><br>

Phone: <input name="phone"><br>
Avatar: <input type="file" name="avatar"><br>

<button name="submit">Thêm</button>
</form>