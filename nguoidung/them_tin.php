<?php
session_start();
include('../include/ketnoi.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$uid = $_SESSION['user_id'];
$thongbao = "";
$sdt_user = $conn->query("SELECT Phone FROM USER WHERE ID='$uid'")->fetch_column();
if (isset($_POST['btnDang'])) {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $area = $_POST['area'];
    $address = $_POST['address'];
    $district_id = $_POST['district_id'];
    $utilities = $_POST['utilities'];
    $phone = $_POST['phone'];
    $description = $_POST['description'];
    $image_name = "default.jpg";
    if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
        $image_name = time() . "_" . $_FILES['image']['name']; 
        move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $image_name);
    }

    $latlng = "18.6791,105.6813"; 
    
    $sql_insert = "INSERT INTO motel (title, description, price, area, count_view, address, latlng, images, user_id, category_id, district_id, utilities, phone, approve) 
                   VALUES ('$title', '$description', '$price', '$area', 0, '$address', '$latlng', '$image_name', '$uid', 1, '$district_id', '$utilities', '$phone', 1)";
    
    if ($conn->query($sql_insert)) {
        echo "<script>alert('Đăng tin thành công!'); window.location.href='quan_ly_tin.php';</script>";
        exit();
    } else {
        $thongbao = "Lỗi khi đăng tin: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng tin mới - Homi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<?php include('../include/menu.php'); ?>

<div class="container" style="margin-top: 100px; max-width: 800px; margin-bottom: 50px;">
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h3 class="fw-bold mb-4">Đăng tin phòng trọ mới</h3>
        
        <?php if($thongbao != "") echo "<div class='alert alert-danger'>$thongbao</div>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label fw-bold">Tiêu đề tin đăng</label>
                <input type="text" name="title" class="form-control" required placeholder="VD: Cho thuê phòng trọ gần ĐH Vinh...">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Giá cho thuê (Triệu/tháng)</label>
                    <input type="number" name="price" class="form-control" required placeholder="VD: 1.5">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Diện tích (m²)</label>
                    <input type="number" name="area" class="form-control" required placeholder="VD: 20">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Quận/Huyện/Phường</label>
                    <select name="district_id" class="form-select" required>
                        <?php
                        // Lấy danh sách Quận/Huyện từ CSDL
                        $rs_dist = $conn->query("SELECT * FROM districts");
                        while($d = $rs_dist->fetch_assoc()) {
                            echo "<option value='{$d['ID']}'>{$d['Name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Địa chỉ chi tiết</label>
                    <input type="text" name="address" class="form-control" required placeholder="Số nhà, tên đường...">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Tiện ích (ngăn cách bằng dấu phẩy)</label>
                <input type="text" name="utilities" class="form-control" required placeholder="VD: wifi, điều hòa, chỗ để xe...">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Số điện thoại liên hệ</label>
                <input type="text" name="phone" class="form-control" value="<?php echo $sdt_user; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Hình ảnh (1 ảnh đại diện)</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Mô tả chi tiết</label>
                <textarea name="description" class="form-control" rows="5" required placeholder="Mô tả về phòng trọ, điện nước, an ninh..."></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="btnDang" class="btn btn-warning fw-bold px-5">Đăng tin ngay</button>
                <a href="quan_ly_tin.php" class="btn btn-outline-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>

<?php include('../include/footer.php'); ?>
</body>
</html>