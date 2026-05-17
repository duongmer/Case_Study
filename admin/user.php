<?php
include 'config.php';
checkAdmin();

// sửa
$error_edit = '';
if (isset($_POST["btn-sua"])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = (int)$_POST['role'];
   
    $sql_check = "SELECT Username, Email, Phone FROM user WHERE (Username='$username' OR Email='$email' OR Phone='$phone') AND ID!=$id";
    $check = $conn->query($sql_check);
    if ($check->num_rows > 0) {
        $row = $check->fetch_assoc();
        if ($row['Username'] == $username) {
            $error_edit = 'Username đã tồn tại!';
        } elseif ($row['Email'] == $email) {
            $error_edit = 'Email đã tồn tại trong hệ thống!';
        } elseif ($row['Phone'] == $phone) {
            $error_edit = 'Số điện thoại này đã được sử dụng!';
        }
    } else {
        $sql = "UPDATE user SET Name='$name', Username='$username', Email='$email', Phone='$phone', Role=$role WHERE ID=$id";
        $conn->query($sql);
        header('Location: user.php');
        exit();
    }
}
//đổi mk
if (isset($_POST['doimk'])) {
    $id = $_POST['id'];
    $newpw = ($_POST['newpw']);
    $cfpw = ($_POST['confirmpw']);
    if ($newpw === $cfpw) {
        $sql_mk = "UPDATE user SET Password='$newpw' WHERE ID=$id";
        $conn->query($sql_mk);
    }
    header('Location: user.php');
    exit();
}
//xóa
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id != $_SESSION['user_id']) {
      $sql_xoa = "DELETE FROM user WHERE ID=$id";
      $conn->query($sql_xoa);
    }
    header('Location: user.php');
    exit();
}
// tìm kiếm
$tim = '';
if (isset($_GET['tim'])) {
    $tim = $_GET['tim'];
}
$sql = "SELECT * FROM user";
if (!empty($tim)) {
    $sql .= " WHERE Name LIKE '%$tim%' OR Username LIKE '%$tim%' OR Email LIKE '%$tim%' OR Phone LIKE '%$tim%'";
}
$sql .= " ORDER BY ID";
$result = $conn->query($sql);

include 'layout_top.php';
?>

<div class="app-title">
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><a href="#"><b>Quản lý người dùng</b></a></li>
  </ul>
  <div id="clock"></div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <div class="row element-button" style="margin-bottom:16px;">
          <div class="col-sm-2">
            <a class="btn btn-add btn-sm" href="user_add.php">
              <i class="fas fa-user-plus"></i> Thêm tài khoản
            </a>
          </div>
          <div class="col-sm-2">
            <a class="btn btn-delete btn-sm" onclick="window.print()">
              <i class="fas fa-print"></i> In dữ liệu
            </a>
          </div>
        </div>
        <!-- Tìm kiếm -->
        <form method="GET" class="row" style="margin-bottom:16px; background:#f8f9fa; padding:14px; border-radius:6px;">
          <div class="col-md-6">
            <input type="text" name="tim" class="form-control form-control-sm"
                   placeholder="🔍 Tìm theo tên, username, email, sdt" value="<?php echo $tim;?>">
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-add btn-sm"><i class="fas fa-search"></i> Tìm</button>
          </div>
        </form>

        <!-- Bảng danh sách -->
        <table class="table table-hover table-bordered" id="userTable">
          <thead>
            <tr>
              <th width="20px">ID</th>
              <th>Họ tên</th>
              <th>Username</th>
              <th>Email</th>
              <th>Điện thoại</th>
              <th>Vai trò</th>
              <th style="min-width:150px">Tính năng</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($result as $row) {?>
            <tr>
              <td><?php echo $row['ID']; ?></td>
              <td><?php echo $row['Name']; ?></td>
              <td><?php echo $row['Username']; ?></td>
              <td><?php echo $row['Email']; ?></td>
              <td><?php echo $row['Phone']; ?></td>
              <td>
                <?php if ($row['Role'] == 1) { ?>
                  <span class="badge bg-danger">Admin</span>
                <?php } else { ?>
                  <span class="badge bg-info">Người dùng</span>
                <?php } ?>
              </td>
              <td class="table-td-center">
                <!-- Nút Sửa -->
                <button class="btn btn-primary btn-sm" title="Sửa thông tin"
                  onclick="openEdit(<?php echo $row['ID']; ?>,'<?php echo ($row['Name']); ?>','<?php echo ($row['Username']); ?>','<?php echo ($row['Email']); ?>','<?php echo $row['Phone']; ?>',<?php echo $row['Role']; ?>)"
                  data-toggle="modal" data-target="#ModalEdit">
                  <i class="fas fa-edit"></i>
                </button>
                <!-- Nút Đổi mật khẩu -->
                <button class="btn btn-primary btn-sm" title="Đổi mật khẩu"
                  onclick="document.getElementById('pw_id').value=<?php echo $row['ID'];?>"
                  data-toggle="modal" data-target="#ModalPW">
                  <i class="fas fa-key"></i>
                </button>
                <!-- Nút Xóa (không tự xóa mình) -->
                <?php if ($row['ID'] != $_SESSION['user_id']) { ?>
                <button class="btn btn-primary btn-sm trash" title="Xóa" data-id="<?= $row['ID'] ?>">
                  <i class="fas fa-trash-alt"></i>
                </button>
                <?php } ?>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</div>

<!-- Modal: Sửa thông tin -->
<div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <h5>✏️ Chỉnh sửa thông tin tài khoản</h5><hr>
        <?php if (!empty($error_edit)) { ?>
          <div id = "loi_sua" class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error_edit; ?></div>
        <?php } ?>
        <form method="POST">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="id" id="edit_id">
          <div class="row">
            <div class="form-group col-md-6">
              <label>Họ và tên</label>
              <input class="form-control" type="text" name="name" id="edit_name" required>
            </div>
            <div class="form-group col-md-6">
              <label>Username</label>
              <input class="form-control" type="text" name="username" id="edit_username" required>
            </div>
            <div class="form-group col-md-6">
              <label>Email</label>
              <input class="form-control" type="email" name="email" id="edit_email" required>
            </div>
            <div class="form-group col-md-6">
              <label>Số điện thoại</label>
              <input class="form-control" type="text" name="phone" id="edit_phone">
            </div>
            <div class="form-group col-md-6">
              <label>Vai trò</label>
              <select class="form-control" name="role" id="edit_role">
                <option value="0">Người dùng</option>
                <option value="1">Admin</option>
              </select>
            </div>
          </div>
          <button class="btn btn-save" type="submit" name="btn-sua">Lưu lại</button>
          <a class="btn btn-cancel" data-dismiss="modal" href="#">Hủy bỏ</a>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Đổi mật khẩu -->
<div class="modal fade" id="ModalPW" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <h5>🔑 Đổi mật khẩu tài khoản</h5><hr>
        <form method="POST">
          <input type="hidden" name="action" value="changepw">
          <input type="hidden" name="id" id="pw_id">
          <div class="form-group">
            <label>Mật khẩu mới</label>
            <input class="form-control" type="password" name="newpw" placeholder="Nhập mật khẩu mới..." required>
          </div>
          <div class="form-group">
            <label>Xác nhận mật khẩu</label>
            <input class="form-control" type="password" name="confirmpw" placeholder="Nhập lại..." required>
          </div>
          <button class="btn btn-save" type="submit" name="doimk">Đổi mật khẩu</button>
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
            '<?php echo ($_POST['name']); ?>',
            '<?php echo ($_POST['username']); ?>',
            '<?php echo ($_POST['email']); ?>',
            '<?php echo ($_POST['phone']); ?>',
            <?php echo (int)$_POST['role']; ?>
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
    $('#userTable').DataTable({ searching: false, language:{ url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json' } });

    // Xác nhận trước khi xóa
    $('.trash').click(function(){
        var id = $(this).data('id');
        swal({ title:'Cảnh báo', text:'Bạn chắc muốn xóa tài khoản này?', buttons:['Hủy','Đồng ý'] })
        .then(function(ok){ if(ok) window.location='user.php?delete='+id; });
    });
});

// Điền dữ liệu vào modal Sửa
function openEdit(id, name, username, email, phone, role) {
    document.getElementById('edit_id').value       = id;
    document.getElementById('edit_name').value     = name;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value    = email;
    document.getElementById('edit_phone').value    = phone;
    document.getElementById('edit_role').value     = role;
    // THÊM ĐOẠN NÀY VÀO: Tìm và giấu khung báo lỗi đi
    var khungLoi = document.getElementById('loi_sua');
    if (khungLoi) {
        khungLoi.style.display = 'none'; // Ẩn nó đi
    }
}
</script>";
include 'layout_bottom.php';
?>
