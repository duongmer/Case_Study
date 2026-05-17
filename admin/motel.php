<?php
require_once 'config.php';
checkAdmin();

$pageTitle   = 'Quản lý phòng trọ';
$currentPage = 'motel';
// Duyệt phòng
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $conn->query("UPDATE motel SET approve=1 WHERE ID=$id");
    header("Location: motel.php?ok=approve"); exit;
}
// Ẩn phòng
if (isset($_GET['hide'])) {
    $id = (int)$_GET['hide'];
    $conn->query("UPDATE motel SET approve=0 WHERE ID=$id");
    header("Location: motel.php?ok=hide"); exit;
}
// Xóa phòng
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM motel WHERE ID=$id");
    header("Location: motel.php?ok=delete"); exit;
}

// Thông báo sau redirect
$alertScript = '';
if (isset($_GET['ok'])) {
    $msgs = ['approve'=>'Đã duyệt phòng trọ!','hide'=>'Đã ẩn phòng trọ!','delete'=>'Đã xóa phòng trọ!','saved'=>'Lưu thành công!'];
    $alertScript = "swal('Thành công!','" . ($msgs[$_GET['ok']] ?? '') . "','success');";
}

// Tìm kiếm & lọc
$keyword   = trim($_GET['keyword'] ?? '');
$districtF = (int)($_GET['district'] ?? 0);
$approveF  = $_GET['approve_filter'] ?? '';

$where = '1=1';
if ($keyword)   $where .= " AND m.title LIKE '%" . $conn->real_escape_string($keyword) . "%'";
if ($districtF) $where .= " AND m.district_id=$districtF";
if ($approveF !== '') $where .= " AND m.approve=" . (int)$approveF;

$motels = $conn->query("SELECT m.*, u.Name owner, d.Name district 
    FROM motel m 
    JOIN user u ON m.user_id=u.ID 
    JOIN districts d ON m.district_id=d.ID 
    WHERE $where ORDER BY m.created_at DESC");

$districts = $conn->query("SELECT * FROM districts ORDER BY Name");

$extraHead = '<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">';
include 'layout_top.php';
?>

<!-- Breadcrumb -->
<div class="app-title">
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><a href="#"><b>Quản lý phòng trọ</b></a></li>
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
            <input type="text" name="keyword" class="form-control form-control-sm"
                   placeholder="🔍 Tìm theo tiêu đề..." value="<?= htmlspecialchars($keyword) ?>">
          </div>
          <div class="col-md-3">
            <select name="district" class="form-control form-control-sm">
              <option value="">-- Tất cả quận/phường --</option>
              <?php $districts->data_seek(0); while ($d = $districts->fetch_assoc()): ?>
              <option value="<?= $d['ID'] ?>" <?= $districtF==$d['ID']?'selected':'' ?>><?= $d['Name'] ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-3">
            <select name="approve_filter" class="form-control form-control-sm">
              <option value="">-- Tất cả trạng thái --</option>
              <option value="1" <?= $approveF==='1'?'selected':'' ?>>Đã duyệt</option>
              <option value="0" <?= $approveF==='0'?'selected':'' ?>>Chờ duyệt</option>
            </select>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-add btn-sm"><i class="fas fa-search"></i> Lọc</button>
            <a href="motel.php" class="btn btn-delete btn-sm">Reset</a>
          </div>
        </form>

        <!-- Bảng danh sách -->
        <table class="table table-hover table-bordered" id="motelTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tiêu đề</th>
              <th>Giá (VNĐ)</th>
              <th>DT</th>
              <th>Quận</th>
              <th>Người đăng</th>
              <th>SĐT</th>
              <th>Lượt xem</th>
              <th>Trạng thái</th>
              <th style="min-width:150px">Tính năng</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $motels->fetch_assoc()): ?>
            <tr>
              <td>#<?= $row['ID'] ?></td>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><?= number_format($row['price']) ?></td>
              <td><?= $row['area'] ?> m²</td>
              <td><?= htmlspecialchars($row['district']) ?></td>
              <td><?= htmlspecialchars($row['owner']) ?></td>
              <td><?= $row['phone'] ?></td>
              <td><?= $row['count_view'] ?></td>
              <td>
                <?php if ($row['approve'] == 1): ?>
                  <span class="badge bg-success">Đã duyệt</span>
                <?php else: ?>
                  <span class="badge bg-warning">Chờ duyệt</span>
                <?php endif; ?>
              </td>
              <td class="table-td-center">
                <!-- Sửa -->
                <button class="btn btn-primary btn-sm" title="Sửa"
                  onclick="openEdit(<?= $row['ID'] ?>,'<?= addslashes($row['title']) ?>',<?= $row['price'] ?>,<?= $row['area'] ?>,'<?= addslashes($row['address']) ?>','<?= $row['phone'] ?>',<?= $row['district_id'] ?>,<?= $row['approve'] ?>,'<?= addslashes($row['description']) ?>')"
                  data-toggle="modal" data-target="#ModalEdit">
                  <i class="fas fa-edit"></i>
                </button>
                <!-- Duyệt / Ẩn -->
                <?php if ($row['approve'] == 0): ?>
                <a class="btn btn-success btn-sm" href="motel.php?approve=<?= $row['ID'] ?>" title="Duyệt"
                   onclick="return confirm('Duyệt phòng trọ này?')">
                  <i class="fas fa-check"></i>
                </a>
                <?php else: ?>
                <a class="btn btn-warning btn-sm" href="motel.php?hide=<?= $row['ID'] ?>" title="Ẩn"
                   onclick="return confirm('Ẩn phòng trọ này?')">
                  <i class="fas fa-eye-slash"></i>
                </a>
                <?php endif; ?>
                <!-- Xóa -->
                <button class="btn btn-primary btn-sm trash" title="Xóa" data-id="<?= $row['ID'] ?>">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</div>

<!-- Modal Sửa phòng trọ -->
<div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <h5>✏️ Chỉnh sửa thông tin phòng trọ</h5>
        <hr>
        <form method="POST" action="motel_edit.php">
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
          <button class="btn btn-save" type="submit">Lưu lại</button>
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
    $('#motelTable').DataTable({ language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json' } });

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
}
</script>";
include 'layout_bottom.php';
?>
