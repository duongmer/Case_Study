<?php
include "config.php";
checkAdmin();
//sua
$error_edit = '';
if (isset($_POST['btnsua'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $price = $_POST['price'];
    $area = $_POST['area'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $district_id = $_POST['district_id'];
    $approve = (int)$_POST['approve'];
    $description = $_POST['description'];
    
    $sql_check = "SELECT * FROM motel WHERE (title='$title') AND ID!=$id";
    $check = $conn->query($sql_check);
    if ($check->num_rows > 0) {
        $row = $check->fetch_assoc();
        if ($row['title'] == $title){
            $error_edit = 'Tên trọ đã tồn tại, vui lòng chọn tiêu đề khác.';
        }
    } 
    else {
    $conn->query("UPDATE motel SET 
        title='$title', price=$price, area=$area, address='$address',
        phone='$phone', district_id=$district_id, approve=$approve,
        description='$description'
        WHERE ID=$id");
        header('Location: motel.php');
        exit();
    }
}
// Duyệt phòng
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $sql_duyet = "UPDATE motel SET approve = 1 WHERE ID=$id";
    $conn->query("$sql_duyet");
    header("Location: motel.php");
    exit();
}
// Ẩn phòng
if (isset($_GET['hide'])) {
    $id = (int)$_GET['hide'];
    $sql_an = "UPDATE motel SET approve = 0 WHERE ID=$id";
    $conn->query("$sql_an");
    header("Location: motel.php");
    exit();
}
// Xóa phòng
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $sql_xoa = "DELETE FROM motel WHERE ID=$id";
    $conn->query("$sql_xoa");
    header("Location: motel.php");
    exit();
}

$tim = '';
if (isset($_GET['tim'])) {
    $tim = $_GET['tim'];
}
$khuvuc = (int)($_GET['district'] ?? 0);
$trangthai = $_GET['approve_filter'] ?? '';
$sql = "SELECT m.*, u.Name ten, d.Name district FROM motel m JOIN user u ON m.user_id = u.ID JOIN districts d ON m.district_id = d.ID";
if (!empty($tim)){
    $sql .= " WHERE m.title LIKE '%$tim%'";
}
if ($khuvuc){
    $sql .= " AND m.district_id = $khuvuc";
}
if ($trangthai !== '') {
    $sql .= " AND m.approve=" . (int)$trangthai;
}
$sql .= " ORDER BY m.ID DESC";
$motels = $conn->query($sql);

$districts = $conn->query("SELECT * FROM districts ORDER BY Name");
include 'layout_top.php';
?>

<!-- Breadcrumb -->
<div class="app-title">
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><a href="#"><b>Quản lý phòng trọ</b></a></li>
  </ul>
  <div id="clock"></div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">

        <!-- Nút chức năng -->
        <div class="row element-button" style="margin-bottom:16px;">
          <div class="col-sm-2">
            <a class="btn btn-add btn-sm" href="motel_add.php">
              <i class="fas fa-plus"></i> Thêm phòng mới
            </a>
          </div>
          <div class="col-sm-2">
            <a class="btn btn-delete btn-sm" onclick="window.print()">
              <i class="fas fa-print"></i> In dữ liệu
            </a>
          </div>
        </div>

        <!-- Bộ lọc -->
        <form method="GET" class="row" style="margin-bottom:16px; background:#f8f9fa; padding:14px; border-radius:6px;">
          <div class="col-md-4">
            <input type="text" name="tim" class="form-control form-control-sm"
                   placeholder="🔍 Tìm theo tiêu đề..." value="<?php echo $tim ;?>">
          </div>
          <div class="col-md-3">
            <select name="district" class="form-control form-control-sm">
              <option value="">-- Tất cả quận/phường --</option>
              <?php $districts->data_seek(0); while ($d = $districts->fetch_assoc()){ ?>
              <option value="<?php echo $d['ID']; ?>" <?= $khuvuc == $d['ID']?> > <?= $d['Name'] ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-3">
            <select name="approve_filter" class="form-control form-control-sm">
              <option value="">-- Tất cả trạng thái --</option>
              <option value="1" <?php echo $trangthai =='1'?>>Đã duyệt</option>
              <option value="0" <?php echo $trangthai =='0'?>>Chờ duyệt</option>
            </select>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-add btn-sm"><i class="fas fa-search"></i> Lọc</button>
          </div>
        </form>

        <!-- Bảng danh sách -->
        <table class="table table-hover table-bordered" id="motelTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tiêu đề</th>
              <th>Giá (VNĐ)</th>
              <th>Diện tích</th>
              <th>Quận</th>
              <th>Người đăng</th>
              <th>SĐT</th>
              <th>Lượt xem</th>
              <th>Trạng thái</th>
              <th style="min-width:150px">Tính năng</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($motels as $row) {?>
            <tr>
              <td><?php echo $row['ID']; ?></td>
              <td><?php echo ($row['title']); ?></td>
              <td><?php echo ($row['price']); ?> đ</td>
              <td><?php echo ($row['area']); ?> m²</td>
              <td><?php echo ($row['district']); ?></td>
              <td><?php echo ($row['ten']); ?></td>
              <td><?php echo ($row['phone']); ?></td>
              <td><?php echo ($row['count_view']); ?></td>
              <td>
                <?php if ($row['approve'] == 1) { ?>
                  <span class="badge bg-success">Đã duyệt</span>
                <?php } else { ?>
                  <span class="badge bg-warning">Chờ duyệt</span>
                <?php } ?>
              </td>
              <td class="table-td-center">
                <button class="btn btn-primary btn-sm" title="Sửa"
                  onclick="openEdit(<?php echo $row['ID']; ?>,'<?php echo $row['title']; ?>','<?php echo $row['price']; ?>','<?php echo $row['area']; ?>','<?php echo $row['address']; ?>','<?php echo $row['phone']; ?>',<?php echo $row['district_id']; ?>,<?php echo $row['approve']; ?>,'<?php echo $row['description']; ?>')"
                  data-toggle="modal" data-target="#ModalEdit">
                  <i class="fas fa-edit"></i>
                </button>
                <?php if ($row['approve'] == 0){ ?>
                <a class="btn btn-success btn-sm" href="motel.php?approve=<?php echo $row['ID'];?>"
                   onclick="return confirm('Duyệt phòng trọ này?')">
                  <i class="fas fa-check"></i>
                </a>
                <?php } else{ ?>
                <a class="btn btn-warning btn-sm" href="motel.php?hide=<?php echo $row['ID'];?>"
                   onclick="return confirm('Ẩn phòng trọ này?')">
                  <i class="fas fa-eye-slash"></i>
                </a>
                <?php } ?>
                <button class="btn btn-primary btn-sm trash" data-id="<?php echo $row['ID']; ?>">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <h5>✏️ Chỉnh sửa thông tin phòng trọ</h5>
        <hr>
        <?php if (!empty($error_edit)) { ?>
          <div id = "loi_sua" class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error_edit; ?></div>
        <?php } ?>
        <form method="POST">
          <input type="hidden" name="id" id="edit_id">
          <div class="row">
            <div class="form-group col-md-12">
              <label class="control-label">Tiêu đề</label>
              <input class="form-control" type="text" name="title" id="edit_title" required>
            </div>
            <div class="form-group col-md-4">
              <label class="control-label">Giá (VNĐ)</label>
              <input class="form-control" type="number" name="price" id="edit_price" required>
            </div>
            <div class="form-group col-md-4">
              <label class="control-label">Diện tích (m²)</label>
              <input class="form-control" type="number" name="area" id="edit_area" required>
            </div>
            <div class="form-group col-md-4">
              <label class="control-label">Số điện thoại</label>
              <input class="form-control" type="text" name="phone" id="edit_phone">
            </div>
            <div class="form-group col-md-6">
              <label class="control-label">Địa chỉ</label>
              <input class="form-control" type="text" name="address" id="edit_address">
            </div>
            <div class="form-group col-md-3">
              <label class="control-label">Quận/Phường</label>
              <select class="form-control" name="district_id" id="edit_district">
                <?php $districts->data_seek(0); while ($d = $districts->fetch_assoc()): ?>
                <option value="<?= $d['ID'] ?>"><?= $d['Name'] ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-group col-md-3">
              <label class="control-label">Trạng thái</label>
              <select class="form-control" name="approve" id="edit_approve">
                <option value="1">Đã duyệt</option>
                <option value="0">Ẩn / Chờ duyệt</option>
              </select>
            </div>
            <div class="form-group col-md-12">
              <label class="control-label">Mô tả</label>
              <textarea class="form-control" name="description" id="edit_desc" rows="3"></textarea>
            </div>
          </div>
          <button class="btn btn-save" type="submit" name="btnsua">Lưu lại</button>
          <a class="btn btn-cancel" data-dismiss="modal" href="#">Hủy bỏ</a>
        </form>
      </div>
    </div>
  </div>
</div>
<?php if (!empty($error_edit)) { ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Hàm này chạy sẽ điền lại dữ liệu, nhưng vô tình giấu luôn khung lỗi
        openEdit(
            <?php echo $_POST['id']; ?>,
            '<?php echo $_POST['title']; ?>',
            '<?php echo $_POST['price']; ?>',
            '<?php echo $_POST['area']; ?>',
            '<?php echo $_POST['address']; ?>',
            '<?php echo $_POST['phone']; ?>',
            '<?php echo $_POST['district_id']; ?>',
            '<?php echo $_POST['approve']; ?>',
            '<?php echo $_POST['description']; ?>'
        );

        // 2. THÊM DÒNG NÀY VÀO ĐÂY: Ép khung lỗi hiển thị trở lại!
        document.getElementById('loi_sua').style.display = 'block';

        // 3. Tự động bật Modal Sửa lên
        $('#ModalEdit').modal('show');
    });
</script>
<?php } ?>
<?php
$extraScript = "
<script src='https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js'></script>
<script>
$(document).ready(function(){
    $('#motelTable').DataTable({searching: false,language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json' } });

    // Xóa phòng (sweetalert)
    $('.trash').click(function(){
        var id = $(this).data('id');
        swal({ title:'Cảnh báo', text:'Bạn chắc muốn xóa phòng trọ này?', buttons:['Hủy','Đồng ý'] })
        .then(function(ok){ if(ok) window.location='motel.php?delete='+id; });
    });
});

function openEdit(id,title,price,area,address,phone,district,approve,desc){
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_area').value = area;
    document.getElementById('edit_address').value = address;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_district').value = district;
    document.getElementById('edit_approve').value = approve;
    document.getElementById('edit_desc').value = desc;
    // THÊM ĐOẠN NÀY VÀO: Tìm và giấu khung báo lỗi đi
    var khungLoi = document.getElementById('loi_sua');
    if (khungLoi) {
        khungLoi.style.display = 'none'; // Ẩn nó đi
    }
}
</script>";
include 'layout_bottom.php';
?>
