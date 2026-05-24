<?php
// Xác định tab đang active để đổi màu nút
$active_tab = 'tatca';
if (isset($_GET["xemnn"])) $active_tab = 'xemnn';
if (isset($_GET["dangtai"])) $active_tab = 'dangtai';
if (isset($_GET["gandhv"])) $active_tab = 'gandhv';
?>

<!-- THANH BỘ LỌC TÌM KIẾM NHANH -->
<form action="" method="get" class="container pt-5 pb-3">
  <div class="d-flex justify-content-center flex-wrap gap-3">
    <button class="btn <?php echo $active_tab == 'tatca' ? 'btn-dark' : 'btn-outline-secondary bg-white'; ?> rounded-pill px-4 fw-bold shadow-sm transition-all" name="tatca" type="submit">
      <i class="bi bi-grid-fill me-2 <?php echo $active_tab == 'tatca' ? 'text-warning' : ''; ?>"></i>Tất cả
    </button>
    <button class="btn <?php echo $active_tab == 'xemnn' ? 'btn-dark' : 'btn-outline-secondary bg-white'; ?> rounded-pill px-4 fw-bold shadow-sm transition-all" name="xemnn" type="submit">
      <i class="bi bi-fire me-2 text-danger"></i>Xem nhiều nhất
    </button>
    <button class="btn <?php echo $active_tab == 'dangtai' ? 'btn-dark' : 'btn-outline-secondary bg-white'; ?> rounded-pill px-4 fw-bold shadow-sm transition-all" name="dangtai" type="submit">
      <i class="bi bi-clock-fill me-2 text-primary"></i>Mới đăng tải
    </button>
    <button class="btn <?php echo $active_tab == 'gandhv' ? 'btn-dark' : 'btn-outline-secondary bg-white'; ?> rounded-pill px-4 fw-bold shadow-sm transition-all" name="gandhv" type="submit">
      <i class="bi bi-geo-alt-fill me-2 text-success"></i>Gần ĐH Vinh
    </button>
  </div>
</form>

<?php
include('ketnoi.php');


$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$limit = 6;

$offset = ($page - 1) * $limit;


// MẶC ĐỊNH: Lấy những phòng Đang cho thuê (approve=1) và Mới nhất lên đầu
$sql = "SELECT * FROM motel WHERE approve = 1 ORDER BY created_at DESC LIMIT $limit OFFSET $offset";

if (isset($_GET["tatca"])) {
  $sql = "SELECT * FROM motel WHERE approve = 1 ORDER BY created_at DESC  LIMIT $limit OFFSET $offset";
} else {
  if (isset($_GET["xemnn"])) {
    $sql = "SELECT * FROM motel WHERE approve = 1 ORDER BY count_view DESC LIMIT $limit OFFSET $offset";
  } else {
    if (isset($_GET["dangtai"])) {
      $sql = "SELECT * FROM motel WHERE approve = 1 ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    } else if (isset($_GET["gandhv"])) {
      // Lấy phòng có khoảng cách GẦN NHẤT
      $sql = "SELECT *,
              (
                  6371 * ACOS(
                      COS(RADIANS(18.6607))
                      * COS(RADIANS(
                          CAST(SUBSTRING_INDEX(latlng, ',', 1) AS DECIMAL(10,6))
                      ))
                      * COS(
                          RADIANS(
                              CAST(SUBSTRING_INDEX(latlng, ',', -1) AS DECIMAL(10,6))
                          ) - RADIANS(105.6817)
                      )
                      + SIN(RADIANS(18.6607))
                      * SIN(RADIANS(
                          CAST(SUBSTRING_INDEX(latlng, ',', 1) AS DECIMAL(10,6))
                      ))
                  )
              ) AS distance
              FROM motel
              WHERE approve = 1
              ORDER BY distance ASC
              LIMIT $limit OFFSET $offset";
    }
  }
}

$result = $conn->query($sql);

$count_sql =
  "SELECT COUNT(*) as total
FROM motel
WHERE approve = 1";

$count_result =
  $conn->query($count_sql);

$total_rows =
  $count_result
    ->fetch_assoc()['total'];

$total_pages =
  ceil(
    $total_rows / $limit
  );
?>

<!-- DANH SÁCH PHÒNG TRỌ -->
<div class="container mx-auto pb-5">
  <div class="d-flex justify-content-between align-items-end mb-4 px-2">
    <?php
    $title = 'Phòng trọ nổi bật';

    switch ($active_tab) {
      case 'xemnn':
        $title = 'Phòng trọ xem nhiều nhất';
        break;

      case 'dangtai':
        $title = 'Phòng trọ mới đăng tải';
        break;

      case 'gandhv':
        $title = 'Phòng trọ gần ĐH Vinh';
        break;
    }
    ?>
    <h4 class="fw-bold mb-0">
      <?php echo $title; ?>
    </h4>
    <a href="timkiem.php" class="text-decoration-none fw-bold text-warning small">Xem tất cả <i class="bi bi-arrow-right"></i></a>
  </div>

  <div class="row g-4">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()) { ?>
        <!-- Card -->
        <div class="col-md-6 col-lg-4">
          <div class="listing-card h-100 d-flex flex-column bg-white rounded-4 shadow-sm border-0 overflow-hidden">
            <div class="position-relative">
              <img src="<?php echo 'assets/images/' . $row['images']; ?>" class="listing-img w-100" style="height: 220px; object-fit: cover;" alt="<?php echo $row['title']; ?>" onerror="this.src='https://picsum.photos/600/400'">
              <span class="listing-badge position-absolute top-0 start-0 m-3 px-3 py-1 bg-white rounded-pill shadow-sm fw-bold" style="font-size: 0.8rem;">
                <?php
                if ($row['category_id'] == 1) echo '<i class="bi bi-house-door-fill text-warning me-1"></i>Phòng trọ';
                elseif ($row['category_id'] == 2) echo '<i class="bi bi-building text-info me-1"></i>Căn hộ';
                else echo '<i class="bi bi-people-fill text-success me-1"></i>Ở ghép';
                ?>
              </span>
            </div>

            <div class="p-4 d-flex flex-column flex-grow-1">
              <div class="d-flex justify-content-between mb-2">
                <span class="small text-muted fw-semibold text-truncate pe-2" style="max-width: 75%;">
                  <i class="bi bi-geo-alt-fill me-1 text-danger"></i>
                  <?php echo $row['address']; ?>
                </span>
                <span class="small fw-bold text-muted bg-light px-2 py-1 rounded-3">
                  <i class="bi bi-eye text-primary"></i> <?php echo $row['count_view']; ?>
                </span>
              </div>

              <h5 class="fw-bold mb-3 text-truncate" title="<?php echo $row['title']; ?>"><?php echo $row['title']; ?></h5>

              <div class="d-flex gap-3 mb-4 text-muted small mt-auto">
                <span class="bg-light px-2 py-1 rounded-2"><i class="bi bi-aspect-ratio text-secondary me-1"></i><?php echo $row['area']; ?>m²</span>
                <span class="text-truncate bg-light px-2 py-1 rounded-2" style="max-width: 60%;">
                  <i class="bi bi-stars text-warning me-1"></i><?php echo $row['utilities']; ?>
                </span>
              </div>

              <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                <div class="price-tag text-warning fw-bolder fs-5">
                  <?php echo number_format($row['price']); ?> <span class="fs-6 text-muted fw-normal">đ/tháng</span>
                </div>
                <a href="detail.php?id=<?php echo $row['ID']; ?>" class="btn btn-dark rounded-circle shadow-sm d-flex align-items-center justify-content-center transition-all hover-scale" style="width: 40px; height: 40px;">
                  <i class="bi bi-chevron-right text-white"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    <?php else :  ?>
      <div class="col-12">
        <div class="alert alert-light text-center py-5 rounded-4 shadow-sm border">
          <i class="bi bi-search fs-1 d-block mb-3 text-muted opacity-50"></i>
          <h5 class="fw-bold text-secondary">Chưa có phòng trọ nào ở danh mục này!</h5>
          <p class="text-muted small">Hãy thử chọn một bộ lọc khác xem sao nhé.</p>
        </div>
      </div>
    <?php endif ?>
  </div>
</div>
<div class="text-center mt-4">

  <?php
  for (
    $i = 1;
    $i <= $total_pages;
    $i++
  ) {
  ?>
    <a
      href="?page=<?php echo $i; ?>"
      class="
btn
<?php
    echo ($page == $i)
      ?
      'btn-dark'
      :
      'btn-outline-dark';
?>
mx-1
">
      <?php echo $i; ?>
    </a>
  <?php } ?>

</div>
<?php
// Đóng kết nối
if (isset($conn)) $conn->close();
?>