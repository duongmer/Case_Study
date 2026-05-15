  <form action="" method="get">
    <div class="py-5 container row g-4">
      <button class="col-6 col-md-3" name="tatca" type="submit">Tất cả</button>
      <button class="col-6 col-md-3" name="xemnn" type="submit">Xem nhiều nhất</button>
      <button class="col-6 col-md-3" name="dangtai" type="submit">Mới được đăng tải</button>
      <button class="col-6 col-md-3" name="gandhv" type="submit">Gần trường ĐH Vinh nhất</button>
    </div>
    </div>
  </form>
  <?php
  include('ketnoi.php');
  $sql = "SELECT *
        FROM motel m
        ORDER BY m.ID Asc";
  if (isset($_GET["tatca"])) {
    $sql = "SELECT *
        FROM motel m
        ORDER BY m.ID Asc";
  } else {
    if (isset($_GET["xemnn"])) {
      $sql = "SELECT *
        FROM motel m
        ORDER BY m.count_view  DESC
        LIMIT 5";
    } else {
      if (isset($_GET["dangtai"])) {
        $sql = "SELECT *
        FROM motel m
        ORDER BY m.created_at  DESC
        LIMIT 5";
      } else {
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
          ORDER BY distance DESC
          limit 5;";
                }
    }
  }
  $result = $conn->query($sql);
  ?>

  <div class="row g-4">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()) { ?>
        <!-- Card -->
        <div class="col-md-6 col-lg-4">
          <div class="listing-card">
            <div class="position-relative">
              <img src="<?php echo 'images/' . $row['images']; ?>" class="listing-img" alt="<?php echo $row['title']; ?>">
              <span class="listing-badge shadow-sm">
                <?php echo htmlspecialchars($row->category_name ?? 'Phòng trọ'); ?>
              </span>
            </div>
            <div class="p-4">
              <div class="d-flex justify-content-between mb-2">
                <span class="small text-muted fw-bold">
                  <i class="bi bi-geo-alt me-1"></i>
                  <?php
                  echo $row['address'];
                  ?>
                </span>
                <span class="small fw-bold">
                  <i class="bi bi-eye me-1"></i> <?php echo $row['count_view']; ?>
                </span>
              </div>
              <h5 class="fw-bold mb-3"><?php echo $row['title'] ; ?></h5>
              <div class="d-flex gap-3 mb-4 text-muted small">
                <span><i class="bi bi-door-open me-1"></i>
                  <?php echo $row['utilities'] ; ?>
                </span>
                <span><i class="bi bi-square me-1"></i>
                  <?php echo $row['area']; ?>m²
                </span>
                <span><i class="bi bi-lightning-charge me-1"></i>
                  <?php echo $row['utilities'] ; ?>
                </span>
              </div>
              <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                <div class="price-tag">
                  <?php echo $row['price'] ; ?>
                  <small class="fs-6 text-muted fw-normal">Tr/tháng</small>
                </div>
                <a href="detail.php?id=<?php echo $row['ID']; ?>" class="btn btn-outline-dark rounded-circle shadow-none">
                  <i class="bi bi-chevron-right"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    <?php else :  ?>
      <div class="col-12">
        <div class="alert alert-info text-center">
          <i class="bi bi-info-circle"></i> Chưa có phòng trọ nào được đăng
        </div>
      </div>
    <?php endif ?>
  </div>
  <?php
  // Đóng kết nối
  $conn->close();
  ?>
 