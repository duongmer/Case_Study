<?php
session_start();
include('../include/ketnoi.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$uid = $_SESSION['user_id'];
$thongbao = "";

if (!isset($_GET['id'])) {
    header("Location: quan_ly_tin.php");
    exit();
}
$motel_id = $_GET['id'];

$sql_get = "SELECT * FROM motel WHERE ID = '$motel_id' AND user_id = '$uid'";
$rs_get = $conn->query($sql_get);
if ($rs_get->num_rows == 0) {
    die("Lỗi: Không tìm thấy bài đăng hoặc bạn không có quyền sửa bài này!");
}
$row = $rs_get->fetch_assoc();

if (isset($_POST['btnLuu'])) {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $area = $_POST['area'];
    $address = $_POST['address'];
    $district_id = $_POST['district_id'];
    $utilities = $_POST['utilities'];
    $phone = $_POST['phone'];
    $description = $_POST['description'];
    
    $image_name = $row['images']; 
    
    
    if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
        $image_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $image_name);
    }

    $sql_update = "UPDATE motel SET 
                    title = '$title', 
                    description = '$description', 
                    price = '$price', 
                    area = '$area', 
                    address = '$address', 
                    district_id = '$district_id', 
                    utilities = '$utilities', 
                    phone = '$phone', 
                    images = '$image_name' 
                   WHERE ID = '$motel_id' AND user_id = '$uid'";
    
    if ($conn->query($sql_update)) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='quan_ly_tin.php';</script>";
        exit();
    } else {
        $thongbao = "Lỗi khi cập nhật: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa tin đăng - Homi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<?php include('../include/menu.php'); ?>

<div class="container" style="margin-top: 100px; max-width: 800px; margin-bottom: 50px;">
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h3 class="fw-bold mb-4 text-primary">Chỉnh sửa thông tin phòng</h3>
        
        <?php if($thongbao != "") echo "<div class='alert alert-danger'>$thongbao</div>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label fw-bold">Tiêu đề tin đăng</label>
                <input type="text" name="title" class="form-control" value="<?php echo $row['title']; ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Giá cho thuê (Triệu/tháng)</label>
                    <input type="number" name="price" class="form-control" value="<?php echo $row['price']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Diện tích (m²)</label>
                    <input type="number" name="area" class="form-control" value="<?php echo $row['area']; ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Quận/Huyện/Phường</label>
                    <select name="district_id" class="form-select" required>
                        <?php
                        $rs_dist = $conn->query("SELECT * FROM districts");
                        while($d = $rs_dist->fetch_assoc()) {
                            $selected = ($d['ID'] == $row['district_id']) ? "selected" : "";
                            echo "<option value='{$d['ID']}' $selected>{$d['Name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Địa chỉ chi tiết</label>
                    <input type="text" name="address" class="form-control" value="<?php echo $row['address']; ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Tiện ích</label>
                <input type="text" name="utilities" class="form-control" value="<?php echo $row['utilities']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Số điện thoại liên hệ</label>
                <input type="text" name="phone" class="form-control" value="<?php echo $row['phone']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Hình ảnh hiện tại</label><br>
                <img src="images/<?php echo $row['images']; ?>" width="150" class="rounded mb-2" onerror="this.src='https://picsum.photos/150'"><br>
                <small class="text-muted">Chọn ảnh mới nếu muốn thay đổi:</small>
                <input type="file" name="image" class="form-control mt-1" accept="image/*">
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Mô tả chi tiết</label>
                <textarea name="description" class="form-control" rows="5" required><?php echo $row['description']; ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="btnLuu" class="btn btn-primary fw-bold px-5">Lưu thay đổi</button>
                <a href="quan_ly_tin.php" class="btn btn-outline-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>

<?php include('../include/footer.php'); ?>
</body>
</html>