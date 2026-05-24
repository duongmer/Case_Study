<?php

session_start();


$host = "localhost";
$db_name = "GTPT";
$db_user = "root";
$db_pass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Lỗi kết nối CSDL: Vui lòng kiểm tra lại XAMPP.");
}


function redirect($msg, $type = 'error', $userName = '') {
    $url = "login.html?msg=" . urlencode($msg) . "&type=" . urlencode($type);
    if ($userName !== '') {
        $url .= "&user=" . urlencode($userName);
    }
    header("Location: " . $url);
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'login') {
        $user = trim($_POST['username']);
        $pass = $_POST['password'];

        if (empty($user) || empty($pass)) {
            redirect("Vui lòng điền đầy đủ tài khoản và mật khẩu.");
        }

        $query = "SELECT * FROM USER WHERE Username = :username";
        $stmt = $conn->prepare($query);
        $stmt->execute([':username' => $user]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
         
if (password_verify($pass, $row['Password']) || $pass === $row['Password']) {
 
    $_SESSION['user_id'] = $row['ID'];
    $_SESSION['user_name'] = $row['Name'];
    $_SESSION['role'] = $row['Role']; 
    $_SESSION['login_fails'] = 0; 
    
   
    if ($row['Role'] == 1) {
       
        $trang_chuyen_huong = 'admin/index.php';
    } else {
        
        $trang_chuyen_huong = 'index.php';
    }

    echo "<script>
        alert('Đăng nhập thành công! Chào " . $row['Name'] . "');
        window.location.href = '" . $trang_chuyen_huong . "';
    </script>";
    exit();
} else {
                // Sai mật khẩu
                $_SESSION['login_fails'] = isset($_SESSION['login_fails']) ? $_SESSION['login_fails'] + 1 : 1;
                $msg = "Mật khẩu không đúng! (Sai " . $_SESSION['login_fails'] . " lần)";
                redirect($msg);
            }
        } else {
            redirect("Tài khoản không tồn tại trên hệ thống!");
        }
    }

   
    elseif ($action === 'register') {
        $name = trim($_POST['name']);
        $user = trim($_POST['username']);
        $email = trim($_POST['email']);
        $pass = $_POST['password'];
        $re_pass = $_POST['re_password'];

        if (empty($name) || empty($user) || empty($email) || empty($pass)) {
            redirect("Vui lòng điền đầy đủ tất cả các trường.");
        }

        if ($pass !== $re_pass) {
            redirect("Mật khẩu nhập lại không khớp!");
        }

        $check_stmt = $conn->prepare("SELECT ID FROM USER WHERE Username = :username OR Email = :email");
        $check_stmt->execute([':username' => $user, ':email' => $email]);
        
        if ($check_stmt->rowCount() > 0) {
            redirect("Tài khoản hoặc Email này đã được sử dụng!");
        }


        $hashed_password = $pass; 
        $role = 0; 
        
        $query = "INSERT INTO USER (Name, Username, Email, Password, Role, Phone, Avatar) VALUES (:name, :username, :email, :password, :role, '', '')";
        $stmt = $conn->prepare($query);
        
        $success = $stmt->execute([
            ':name' => $name,
            ':username' => $user,
            ':email' => $email,
            ':password' => $hashed_password,
            ':role' => $role
        ]);

        if ($success) {
            redirect("Đăng ký thành công! Vui lòng đăng nhập.", "success");
        } else {
            redirect("Đã có lỗi hệ thống xảy ra.");
        }
    }
}


header("Location: login.html");
exit();
?>