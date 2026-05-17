<?php
include 'config.php';
checkAdmin();

// Thống kê
$totalMotel    = $conn->query("SELECT COUNT(*) c FROM motel")->fetch_assoc()['c'];
$approvedMotel = $conn->query("SELECT COUNT(*) c FROM motel WHERE approve=1")->fetch_assoc()['c'];
$pendingMotel  = $conn->query("SELECT COUNT(*) c FROM motel WHERE approve=0")->fetch_assoc()['c'];
$totalUser     = $conn->query("SELECT COUNT(*) c FROM user")->fetch_assoc()['c'];

// Tin đăng mới nhất
$recent = $conn->query("SELECT m.*, u.Name owner, d.Name district 
    FROM motel m JOIN user u ON m.user_id=u.ID JOIN districts d ON m.district_id=d.ID 
    ORDER BY m.created_at DESC LIMIT 6");

include 'layout_top.php';
?>
<div class="row">
  <div class="col-md-12">
    <div class="app-title">
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="#"><b>Bảng điều khiển</b></a></li>
      </ul>
      <div id="clock"></div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6 col-lg-3">
    <div class="widget-small primary coloured-icon">
      <i class='icon bx bx-home-alt fa-3x'></i>
      <div class="info">
        <h4>Tổng phòng trọ</h4>
        <p><b><?= $totalMotel ?> phòng</b></p>
        <p class="info-tong">Tổng số phòng trọ trong hệ thống.</p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small info coloured-icon">
      <i class='icon bx bx-check-circle fa-3x'></i>
      <div class="info">
        <h4>Đã duyệt</h4>
        <p><b><?= $approvedMotel ?> phòng</b></p>
        <p class="info-tong">Số phòng đã được duyệt hiển thị.</p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small warning coloured-icon">
      <i class='icon bx bx-time fa-3x'></i>
      <div class="info">
        <h4>Chờ duyệt</h4>
        <p><b><?= $pendingMotel ?> phòng</b></p>
        <p class="info-tong">Số phòng đang chờ phê duyệt.</p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small danger coloured-icon">
      <i class='icon bx bx-group fa-3x'></i>
      <div class="info">
        <h4>Người dùng</h4>
        <p><b><?= $totalUser ?> tài khoản</b></p>
        <p class="info-tong">Tổng tài khoản người dùng.</p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Biểu đồ cột -->
  <div class="col-md-12 col-lg-6">
    <div class="tile">
      <h3 class="tile-title">Tin đăng 6 tháng gần nhất</h3>
      <div class="embed-responsive embed-responsive-16by9">
        <canvas class="embed-responsive-item" id="barChart"></canvas>
      </div>
    </div>
  </div>
  <!-- Biểu đồ tròn -->
  <div class="col-md-12 col-lg-6">
    <div class="tile">
      <h3 class="tile-title">Tỉ lệ duyệt / chờ duyệt</h3>
      <div class="embed-responsive embed-responsive-16by9">
        <canvas class="embed-responsive-item" id="pieChart"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Tin mới nhất -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Tin đăng mới nhất</h3>
      <div class="tile-body">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tiêu đề</th>
              <th>Giá (VNĐ)</th>
              <th>Diện tích</th>
              <th>Quận/Phường</th>
              <th>Người đăng</th>
              <th>Trạng thái</th>
              <th>Ngày đăng</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $recent->fetch_assoc()): ?>
            <tr>
              <td>#<?= $row['ID'] ?></td>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><?= number_format($row['price']) ?> đ</td>
              <td><?= $row['area'] ?> m²</td>
              <td><?= htmlspecialchars($row['district']) ?></td>
              <td><?= htmlspecialchars($row['owner']) ?></td>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: ['T12', 'T1', 'T2', 'T3', 'T4', 'T5'],
            datasets: [{
                label: 'Số tin đăng',
                data: [18, 24, 20, 31, 28, 35],
                backgroundColor: 'rgba(230,126,34,0.7)',
                borderColor: '#e67e22',
                borderWidth: 1
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: ['Đã duyệt', 'Chờ duyệt'],
            datasets: [{
                data: [94, 34],
                backgroundColor: ['rgba(39,174,96,0.8)', 'rgba(241,196,15,0.8)'],
                borderColor: ['#27ae60', '#f1c40f'],
                borderWidth: 2
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
});
</script>

<?php include 'layout_bottom.php'; ?>