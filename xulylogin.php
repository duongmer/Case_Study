<?php
// Bắt đầu Session theo chuẩn bài học
session_start();

// ---------------------------------------------------------
// CẤU HÌNH KẾT NỐI CƠ SỞ DỮ LIỆU
// ---------------------------------------------------------
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

// ---------------------------------------------------------
// HÀM HỖ TRỢ ĐIỀU HƯỚNG VỀ LOGIN.HTML
// ---------------------------------------------------------
function redirect($msg, $type = 'error', $userName = '') {
    $url = "login.html?msg=" . urlencode($msg) . "&type=" . urlencode($type);
    if ($userName !== '') {
        $url .= "&user=" . urlencode($userName);
    }
    header("Location: " . $url);
    exit();
}

// Kiểm tra xem form có gửi action lên không
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    // ==========================================
    // 1. XỬ LÝ ĐĂNG NHẬP (TÀI KHOẢN THƯỜNG)
    // ==========================================
    if ($action === 'login') {
        $user = trim($_POST['username']);
        $pass = $_POST['password'];

        if (empty($user) || empty($pass)) {
            redirect("Vui lòng điền đầy đủ tài khoản và mật khẩu.");
        }

        // Tìm user trong Database (Sử dụng Prepared Statement chống SQL Injection)
        $query = "SELECT * FROM USER WHERE Username = :username";
        $stmt = $conn->prepare($query);
        $stmt->execute([':username' => $user]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Kiểm tra mật khẩu mã hóa
            if (password_verify($pass, $row['Password'])) {
                // Thành công
                $_SESSION['user_id'] = $row['ID'];
                $_SESSION['user_name'] = $row['Name'];
                $_SESSION['login_fails'] = 0; // Reset số lần đếm sai
                
               
echo "<script>
    alert('Đăng nhập thành công! Chào " . $row['Name'] . "');
    window.location.href = 'index.php';
</script>";
exit();
            } else {
                // Sai mật khẩu
                $_SESSION['login_fails'] = isset($_SESSION['login_fails']) ? $_SESSION['login_fails'] + 1 : 1;
                
                $msg = "Mật khẩu không đúng! (Sai " . $_SESSION['login_fails'] . " lần)";
                if ($_SESSION['login_fails'] >= 3) {
                    $msg .= " - Cảnh báo: Yêu cầu Captcha ở lần tới!";
                }
                redirect($msg);
            }
        } else {
            redirect("Tài khoản không tồn tại trên hệ thống!");
        }
    }

    // ==========================================
    // 2. XỬ LÝ ĐĂNG KÝ
    // ==========================================
    elseif ($action === 'register') {
        $name = trim($_POST['name']);
        $user = trim($_POST['username']);
        $email = trim($_POST['email']);
        $pass = $_POST['password'];
        $re_pass = $_POST['re_password'];

        // Xác thực dữ liệu cơ bản
        if (empty($name) || empty($user) || empty($email) || empty($pass)) {
            redirect("Vui lòng điền đầy đủ tất cả các trường.");
        }

        if ($pass !== $re_pass) {
            redirect("Mật khẩu nhập lại không khớp!");
        }

        // Kiểm tra xem Username hoặc Email đã tồn tại chưa
        $check_stmt = $conn->prepare("SELECT ID FROM USER WHERE Username = :username OR Email = :email");
        $check_stmt->execute([':username' => $user, ':email' => $email]);
        
        if ($check_stmt->rowCount() > 0) {
            redirect("Tài khoản hoặc Email này đã được sử dụng!");
        }

        // Thêm vào DB
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
        $role = 0; // Mặc định người dùng thường
        
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
            redirect("Đã có lỗi hệ thống xảy ra khi tạo tài khoản.");
        }
    }

    // ==========================================
    // 3. XỬ LÝ ĐĂNG NHẬP QUA FACEBOOK / GOOGLE
    // ==========================================
    elseif ($action === 'login_social') {
        $email = trim($_POST['email'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $provider = ucfirst(trim($_POST['provider'] ?? 'Mạng xã hội')); // 'Google' hoặc 'Facebook'

        if (empty($email)) {
            redirect("Không thể lấy thông tin Email từ " . $provider);
        }

        // Kiểm tra xem Email từ Facebook/Google đã tồn tại trong CSDL chưa
        $query = "SELECT * FROM USER WHERE Email = :email";
        $stmt = $conn->prepare($query);
        $stmt->execute([':email' => $email]);

        if ($stmt->rowCount() > 0) {
            // NẾU ĐÃ CÓ TÀI KHOẢN -> Cho phép đăng nhập luôn
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $_SESSION['user_id'] = $row['ID'];
            $_SESSION['user_name'] = $row['Name'];
            $_SESSION['login_fails'] = 0;
            
            redirect("Đăng nhập bằng " . $provider . " thành công!", "success", $row['Name']);
        } else {
            // NẾU CHƯA CÓ TÀI KHOẢN -> Tự động đăng ký tài khoản mới cho họ
            
            // Tự động tạo Username từ email (Ví dụ: quangvd@gmail.com -> quangvd_2943)
            $username_base = explode('@', $email)[0];
            $username = $username_base . '_' . rand(1000, 9999);
            
            // Tạo mật khẩu ngẫu nhiên phức tạp (Họ không cần dùng pass này vì sau này cứ bấm nút Google/FB là vào)
            $random_password = password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT);
            $role = 0; 
            
            $insert_query = "INSERT INTO USER (Name, Username, Email, Password, Role, Phone, Avatar) VALUES (:name, :username, :email, :password, :role, '', '')";
            $insert_stmt = $conn->prepare($insert_query);
            
            if ($insert_stmt->execute([
                ':name' => $name,
                ':username' => $username,
                ':email' => $email,
                ':password' => $random_password,
                ':role' => $role
            ])) {
                // Đăng ký xong -> Lấy ID mới tạo để đăng nhập ngay
                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['login_fails'] = 0;
                
                redirect("Liên kết " . $provider . " thành công!", "success", $name);
            } else {
                redirect("Đã có lỗi hệ thống khi liên kết tài khoản " . $provider);
            }
        }
    }

    // ==========================================
    // 4. XỬ LÝ ĐĂNG XUẤT
    // ==========================================
    elseif ($action === 'logout') {
        session_unset();
        session_destroy();
        redirect("Đã đăng xuất thành công!", "success");
    }
}

// Trả về trang chủ nếu vào nhầm link xulylogin.php trực tiếp
header("Location: login.html");
exit();
?>