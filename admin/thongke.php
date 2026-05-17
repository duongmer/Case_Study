<?php
include 'config.php';
checkAdmin();

// Tổng quan
$totalMotel    = $conn->query("SELECT COUNT(*) c FROM motel")->fetch_assoc()['c'];
$approvedMotel = $conn->query("SELECT COUNT(*) c FROM motel WHERE approve=1")->fetch_assoc()['c'];
$pendingMotel  = $conn->query("SELECT COUNT(*) c FROM motel WHERE approve=0")->fetch_assoc()['c'];
$totalUser     = $conn->query("SELECT COUNT(*) c FROM user")->fetch_assoc()['c'];
$totalViews    = $conn->query("SELECT SUM(count_view) c FROM motel")->fetch_assoc()['c'] ?? 0;
$avgPrice      = $conn->query("SELECT AVG(price) c FROM motel WHERE approve=1")->fetch_assoc()['c'] ?? 0;

// Thống kê theo tháng
$monthLabels = $monthData = [];
for ($i = 5; $i >= 0; $i--) {
    $m = date('m', strtotime("-$i month"));
    $y = date('Y', strtotime("-$i month"));
    $count = $conn->query("SELECT COUNT(*) c FROM motel WHERE MONTH(created_at)=$m AND YEAR(created_at)=$y")->fetch_assoc()['c'];
    $monthLabels[] = 'T' . (int)$m . '/' . $y;
    $monthData[]   = (int)$count;
}

// Top quận/phường có nhiều phòng nhất
$topDistricts = $conn->query("SELECT d.Name, COUNT(m.ID) cnt FROM motel m JOIN districts d ON m.district_id=d.ID GROUP BY d.ID ORDER BY cnt DESC LIMIT 5");

// Lọc theo tháng
$filterMonth = $_GET['month'] ?? date('m');
$filterYear  = $_GET['year']  ?? date('Y');

// Tìm kiếm theo tài khoản & khoảng giá & khoảng thời gian
$searchUser  = (int)($_GET['user_id'] ?? 0);
$priceFrom   = (int)($_GET['price_from'] ?? 0);
$priceTo     = (int)($_GET['price_to'] ?? 0);
$dateFrom    = $_GET['date_from'] ?? '';
$dateTo      = $_GET['date_to']   ?? '';

$where = '1=1';
if ($searchUser) $where .= " AND m.user_id=$searchUser";
if ($priceFrom)  $where .= " AND m.price >= $priceFrom";
if ($priceTo)    $where .= " AND m.price <= $priceTo";
if ($dateFrom)   $where .= " AND DATE(m.created_at) >= '" . $conn->real_escape_string($dateFrom) . "'";
if ($dateTo)     $where .= " AND DATE(m.created_at) <= '" . $conn->real_escape_string($dateTo) . "'";

$filteredMotels = $conn->query("SELECT m.*, u.Name owner, d.Name district 
    FROM motel m JOIN user u ON m.user_id=u.ID JOIN districts d ON m.district_id=d.ID 
    WHERE $where ORDER BY m.created_at DESC");

// Thống kê tháng hiện tại
$thisMonthCount = $conn->query("SELECT COUNT(*) c FROM motel WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())")->fetch_assoc()['c'];

$users = $conn->query("SELECT ID, Name FROM user ORDER BY Name");
include 'layout_top.php';
?>

<div class="app-title">
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><a href="#"><b>Báo cáo thống kê</b></a></li>
  </ul>
  <div id="clock"></div>
</div>

<!-- Widget tổng quan -->
<div class="row">
  <div class="col-md-6 col-lg-3">
    <div class="widget-small primary coloured-icon">
      <i class='icon bx bxs-chart fa-3x'></i>
      <div class="info">
        <h4>Tổng thu nhập (ước tính)</h4>
        <p><b><?=($avgPrice) ?> đ/tháng TB</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small info coloured-icon">
      <i class='icon bx bxs-user-badge fa-3x'></i>
      <div class="info">
        <h4>Tổng người dùng</h4>
        <p><b><?= $totalUser ?> tài khoản</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small warning coloured-icon">
      <i class='icon bx bx-calendar fa-3x'></i>
      <div class="info">
        <h4>Đăng tháng này</h4>
        <p><b><?= $thisMonthCount ?> tin</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small danger coloured-icon">
      <i class='icon bx bx-show fa-3x'></i>
      <div class="info">
        <h4>Tổng lượt xem</h4>
        <p><b><?=($totalViews) ?> lượt</b></p>
      </div>
    </div>
  </div>
</div>
<!-- Bộ lọc tìm kiếm -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">🔍 Tìm kiếm & Lọc tin đăng</h3>
      <div class="tile-body">
        <form method="GET" class="row" style="background:#f8f9fa; padding:16px; border-radius:6px; margin-bottom:16px;">
          <div class="form-group col-md-3">
            <label class="control-label">Tài khoản đăng</label>
            <select class="form-control form-control-sm" name="user_id">
              <option value="">-- Tất cả --</option>
              <?php while ($u = $users->fetch_assoc()): ?>
              <option value="<?= $u['ID'] ?>" <?= $searchUser==$u['ID']?'selected':'' ?>><?= ($u['Name']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group col-md-2">
            <label class="control-label">Giá từ (VNĐ)</label>
            <input class="form-control form-control-sm" type="number" name="price_from"
                   value="<?= $priceFrom ?: '' ?>" placeholder="500000">
          </div>
          <div class="form-group col-md-2">
            <label class="control-label">Giá đến (VNĐ)</label>
            <input class="form-control form-control-sm" type="number" name="price_to"
                   value="<?= $priceTo ?: '' ?>" placeholder="3000000">
          </div>
          <div class="form-group col-md-2">
            <label class="control-label">Từ ngày</label>
            <input class="form-control form-control-sm" type="date" name="date_from" value="<?= $dateFrom ?>">
          </div>
          <div class="form-group col-md-2">
            <label class="control-label">Đến ngày</label>
            <input class="form-control form-control-sm" type="date" name="date_to" value="<?= $dateTo ?>">
          </div>
          <div class="form-group col-md-1" style="display:flex; align-items:flex-end;">
            <button type="submit" class="btn btn-add btn-sm w-100">Lọc</button>
          </div>
          <div class="col-md-12" style="margin-top:4px;">
            <span class="ml-3" style="color:#e67e22; font-weight:600;">
              Kết quả: <?= $filteredMotels->num_rows ?> tin đăng
            </span>
          </div>
        </form>

        <!-- Bảng kết quả -->
        <table class="table table-hover table-bordered" id="reportTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tiêu đề</th>
              <th>Giá</th>
              <th>Quận</th>
              <th>Người đăng</th>
              <th>Lượt xem</th>
              <th>Trạng thái</th>
              <th>Ngày đăng</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $filteredMotels->fetch_assoc()): ?>
            <tr>
              <td><?= $row['ID'] ?></td>
              <td><?= ($row['title']) ?></td>
              <td><?= ($row['price']) ?> đ</td>
              <td><?= ($row['district']) ?></td>
              <td><?= ($row['owner']) ?></td>
              <td><?= $row['count_view'] ?></td>
              <td>
                <?php if ($row['approve'] == 1): ?>
                  <span class="badge bg-success">Đã duyệt</span>
                <?php else: ?>
                  <span class="badge bg-warning">Chờ duyệt</span>
                <?php endif; ?>
              </td>
              <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Biểu đồ & Top quận -->
<div class="row">
  <div class="col-md-8">
    <div class="tile">
      <h3 class="tile-title">📊 Số tin đăng theo tháng</h3>
      <div class="embed-responsive embed-responsive-16by9">
        <canvas class="embed-responsive-item" id="lineChart"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="tile">
      <h3 class="tile-title">🏆 Top quận/phường</h3>
      <table class="table table-bordered">
        <thead><tr><th>Quận/Phường</th><th>Số phòng</th></tr></thead>
        <tbody>
          <?php while ($td = $topDistricts->fetch_assoc()): ?>
          <tr>
            <td><?=($td['Name']) ?></td>
            <td><span class="badge bg-primary"><?= $td['cnt'] ?></span></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>



<?php
$labelsJson = json_encode($monthLabels);
$dataJson   = json_encode($monthData);
$extraScript = "
<script src='https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js'></script>
<script>
$(document).ready(function(){
    $('#reportTable').DataTable({searching: false, language:{ url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json' } });
});
var ctx = document.getElementById('lineChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: $labelsJson,
        datasets: [{
            label: 'Số tin đăng',
            data: $dataJson,
            borderColor: '#e67e22',
            backgroundColor: 'rgba(230,126,34,0.15)',
            borderWidth: 2,
            pointRadius: 5,
            fill: true,
            tension: 0.3
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
</script>";
include 'layout_bottom.php';
?>
