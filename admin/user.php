<?php
include 'config.php';
checkAdmin();

$pageTitle   = 'Quản lý người dùng';
$currentPage = 'user';
$msg = '';

// ── Xóa tài khoản ──────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM user WHERE ID=$id");
    }
    header('Location: user.php?ok=delete'); exit;
}

// ── Sửa thông tin ──────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id       = (int)$_POST['id'];
    $name     = $conn->real_escape_string(trim($_POST['name']));
    $username = $conn->real_escape_string(trim($_POST['username']));
    $email    = $conn->real_escape_string(trim($_POST['email']));
    $phone    = $conn->real_escape_string(trim($_POST['phone']));
    $role     = (int)$_POST['role'];
    $conn->query("UPDATE user SET Name='$name', Username='$username', Email='$email', Phone='$phone', Role=$role WHERE ID=$id");
    header('Location: user.php?ok=saved'); exit;
}

// ── Đổi mật khẩu ───────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] == 'changepw') {
    $id    = (int)$_POST['id'];
    $newpw = trim($_POST['newpw']);
    $cfpw  = trim($_POST['confirmpw']);
    if ($newpw === $cfpw && strlen($newpw) >= 4) {
        $pw = $conn->real_escape_string($newpw);
        $conn->query("UPDATE user SET Password='$pw' WHERE ID=$id");
    }
    header('Location: user.php?ok=saved'); exit;
}

// ── Thông báo ───────────────────────────────────────────────────
$msgs = ['delete' => 'Đã xóa tài khoản!', 'saved' => 'Lưu thành công!', 'added' => 'Thêm tài khoản thành công!'];
$alertScript = isset($_GET['ok']) ? "swal('Thành công!','" . ($msgs[$_GET['ok']] ?? '') . "','success');" : '';

// ── Tìm kiếm ────────────────────────────────────────────────────
$keyword = trim($_GET['keyword'] ?? '');
$where   = '1=1';
if ($keyword) {
    $kw    = $conn->real_escape_string($keyword);
    $where .= " AND (Name LIKE '%$kw%' OR Username LIKE '%$kw%' OR Email LIKE '%$kw%')";
}
$users = $conn->query("SELECT * FROM user WHERE $where ORDER BY ID");

$extraHead = '<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">';
include 'layout_top.php';
?>

<div class="app-title">
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><a href="#"><b>Quản lý người dùng</b></a></li>
  </ul>
  <div id="clock"></div>
</div>

<?php if ($alertScript): ?>
<script>$(document).ready(function(){ <?= $alertScript ?> });</script>
<?php endif; ?>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">

        <!-- Nút chức năng -->
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
            <input type="text" name="keyword" class="form-control form-control-sm"
                   placeholder="🔍 Tìm theo tên, username, email..." value="<?= htmlspecialchars($keyword) ?>">
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-add btn-sm"><i class="fas fa-search"></i> Tìm</button>
            <a href="user.php" class="btn btn-delete btn-sm">Reset</a>
          </div>
        </form>

        <!-- Bảng danh sách -->
        <table class="table table-hover table-bordered" id="userTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Họ tên</th>
              <th>Username</th>
              <th>Email</th>
              <th>Điện thoại</th>
              <th>Vai trò</th>
              <th style="min-width:150px">Tính năng</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($u = $users->fetch_assoc()): ?>
            <tr>
              <td>#<?= $u['ID'] ?></td>
              <td><?= htmlspecialchars($u['Name']) ?></td>
              <td><?= htmlspecialchars($u['Username']) ?></td>
              <td><?= htmlspecialchars($u['Email']) ?></td>
              <td><?= $u['Phone'] ?></td>
              <td>
                <?php if ($u['Role'] == 1): ?>
                  <span class="badge bg-danger">Admin</span>
                <?php else: ?>
                  <span class="badge bg-info">Người dùng</span>
                <?php endif; ?>
              </td>
              <td class="table-td-center">
                <!-- Nút Sửa -->
                <button class="btn btn-primary btn-sm" title="Sửa thông tin"
                  onclick="openEdit(<?= $u['ID'] ?>,'<?= addslashes($u['Name']) ?>','<?= addslashes($u['Username']) ?>','<?= addslashes($u['Email']) ?>','<?= $u['Phone'] ?>',<?= $u['Role'] ?>)"
                  data-toggle="modal" data-target="#ModalEdit">
                  <i class="fas fa-edit"></i>
                </button>
                <!-- Nút Đổi mật khẩu -->
                <button class="btn btn-primary btn-sm" title="Đổi mật khẩu"
                  onclick="document.getElementById('pw_id').value=<?= $u['ID'] ?>"
                  data-toggle="modal" data-target="#ModalPW">
                  <i class="fas fa-key"></i>
                </button>
                <!-- Nút Xóa (không tự xóa mình) -->
                <?php if ($u['ID'] != $_SESSION['user_id']): ?>
                <button class="btn btn-primary btn-sm trash" title="Xóa" data-id="<?= $u['ID'] ?>">
                  <i class="fas fa-trash-alt"></i>
                </button>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
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
          <button class="btn btn-save" type="submit">Lưu lại</button>
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
          <button class="btn btn-save" type="submit">Đổi mật khẩu</button>
          <a class="btn btn-cancel" data-dismiss="modal" href="#">Hủy bỏ</a>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$extraScript = "
<script src='https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js'></script>
<script>
$(document).ready(function(){
    // Khởi tạo DataTable tiếng Việt
    $('#userTable').DataTable({ language:{ url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json' } });

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
}
</script>";
include 'layout_bottom.php';
?>
