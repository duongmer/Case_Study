<?php
include 'config.php';
checkAdmin();

$districts = $conn->query("SELECT * FROM districts ORDER BY Name");
$users = $conn->query("SELECT ID, Name FROM user ORDER BY Name");
$error = '';
if (isset($_POST['btn-save'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $area = $_POST['area'];
    $address = $_POST['address'];
    $phone  = $_POST['phone'];
    $user_id = $_POST['user_id'];
    $district_id = $_POST['district_id'];
    $utilities = $_POST['utilities'];
    $approve = (int)($_POST['approve']);

    // Upload ảnh
    $images = 'no-image.jpg';
    if (!empty($_FILES['images']['name'])) {
        $ext = pathinfo($_FILES['images']['name'], PATHINFO_EXTENSION);
        $images = 'motel_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['images']['tmp_name'], '../assets/images/' . $images);
    }
    
    $sql_check = "SELECT title FROM motel WHERE title='$title'";
    $check = $conn->query($sql_check);
    if ($check->num_rows > 0){
      $row = $check->fetch_assoc();
      if ($row['title'] == $title){
        $error = 'Tên phòng trọ đã tồn tại!';
      }
    } 
    else {
    $conn->query("INSERT INTO motel (title,description,price,area,count_view,address,images,user_id,district_id,utilities,phone,approve)
        VALUES ('$title','$description','$price','$area',0,'$address','$images','$user_id','$district_id','$utilities','$phone','$approve')");

    header('Location: motel.php');
    exit();
    }
}

include 'layout_top.php';
?>

<div class="app-title">
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><a href="motel.php">Quản lý phòng trọ</a></li>
    <li class="breadcrumb-item"><a href="#">Thêm mới</a></li>
  </ul>
  <div id="clock"></div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">🏘 Tạo mới phòng trọ</h3>
      <div class="tile-body">
        <?php if (!empty($error)) { ?>
          <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php } ?>
        <form class="row" method="POST" enctype="multipart/form-data">
          <div class="form-group col-md-6">
            <label class="control-label">Tiêu đề tin đăng</label>
            <input class="form-control" type="text" name="title" placeholder="Nhập tiêu đề..." required>
          </div>
          <div class="form-group col-md-3">
            <label class="control-label">Giá thuê (VNĐ/tháng)</label>
            <input class="form-control" type="number" name="price" placeholder="VD: 1500000" required>
          </div>
          <div class="form-group col-md-3">
            <label class="control-label">Diện tích (m²)</label>
            <input class="form-control" type="number" name="area" placeholder="VD: 20" required>
          </div>

          <div class="form-group col-md-6">
            <label class="control-label">Địa chỉ</label>
            <input class="form-control" type="text" name="address" placeholder="Số nhà, đường..." required>
          </div>
          <div class="form-group col-md-3">
            <label class="control-label">Quận/Phường</label>
            <select class="form-control" name="district_id" required>
              <option value="">-- Chọn quận/phường --</option>
              <?php while ($d = $districts->fetch_assoc()){ ?>
              <option value="<?= $d['ID'] ?>"> <?= $d['Name'] ?> </option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group col-md-3">
            <label class="control-label">Số điện thoại</label>
            <input class="form-control" type="text" name="phone" placeholder="09xxxxxxxx" required>
          </div>

          <div class="form-group col-md-6">
            <label class="control-label">Tiện ích (wifi, điều hòa...)</label>
            <input class="form-control" type="text" name="utilities" placeholder="wifi, nóng lạnh, điều hòa...">
          </div>
          <div class="form-group col-md-3">
            <label class="control-label">Người đăng</label>
            <select class="form-control" name="user_id" required>
              <option value="">-- Chọn tài khoản --</option>
              <?php while ($u = $users->fetch_assoc()){ ?>
              <option value="<?php echo $u['ID']; ?>"> <?php echo $u['Name']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group col-md-3">
            <label class="control-label">Trạng thái</label>
            <select class="form-control" name="approve">
              <option value="1">Đã duyệt</option>
              <option value="0">Chờ duyệt</option>
            </select>
          </div>

          <div class="form-group col-md-12">
            <label class="control-label">Mô tả chi tiết</label>
            <textarea class="form-control" name="description" rows="4" placeholder="Mô tả phòng trọ..."></textarea>
          </div>

          <div class="form-group col-md-12">
            <label class="control-label">Ảnh phòng trọ</label><br>
            <input type="file" name="images" id="uploadfile" onchange="previewImg(this)" accept="image/*">
            <div id="thumbbox" style="margin-top:10px;">
              <img id="thumbimage" height="200" style="display:none; border-radius:6px; border:1px solid #ddd;" alt="">
            </div>
          </div>

          <div class="col-md-12">
            <button class="btn btn-save" type="submit" name="btn-save">💾 Lưu lại</button>
            <a class="btn btn-cancel" href="motel.php">Hủy bỏ</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$extraScript = "
<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('thumbimage').src = e.target.result;
            document.getElementById('thumbimage').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>";
include 'layout_bottom.php';
?>
