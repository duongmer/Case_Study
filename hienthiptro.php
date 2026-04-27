  
  <!-- Categories -->
  <section class="py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-6 col-md-3">
          <div class="category-card">
            <div class="category-icon"><i class="bi bi-building"></i></div>
            <h6 class="fw-bold mb-0">Phòng trọ</h6>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="category-card">
            <div class="category-icon"><i class="bi bi-house"></i></div>
            <h6 class="fw-bold mb-0">Căn hộ Studio</h6>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="category-card">
            <div class="category-icon"><i class="bi bi-house-gear"></i></div>
            <h6 class="fw-bold mb-0">Nhà nguyên căn</h6>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="category-card">
            <div class="category-icon"><i class="bi bi-people"></i></div>
            <h6 class="fw-bold mb-0">Ở ghép</h6>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Listings -->
  <section class="py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
          <h2 class="fw-bold">Phòng nổi bật gần bạn</h2>
          <p class="text-muted fw-medium">Các tin đăng được xác thực uy tín nhất</p>
        </div>
        <a href="#" class="text-decoration-none text-dark fw-bold">Xem tất cả <i class="bi bi-arrow-right"></i></a>
      </div>

<?php

include('ketnoi.php');
$sql = "SELECT *
        FROM motel m
        ORDER BY m.ID Asc";

$result = $conn->query($sql);
?>

<div class="row g-4">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_object()): ?>
            <!-- Card -->
            <div class="col-md-6 col-lg-4">
                <div class="listing-card">
                    <div class="position-relative">
                        <?php 
                        // Xử lý images (giả sử lưu JSON hoặc đường dẫn phân cách bởi dấu phẩy)
                        $images = !empty($row->images) ? explode(',', $row->images) : [];
                        $firstImage = !empty($images) ? $images[0] : 'https://picsum.photos/seed/' . $row->ID . '/600/400';
                        ?>
                        <img src="<?php echo 'images/' . htmlspecialchars($firstImage); ?>" class="listing-img" alt="<?php echo htmlspecialchars($row->title); ?>">
                        <span class="listing-badge shadow-sm">
                            <?php echo htmlspecialchars($row->category_name ?? 'Phòng trọ'); ?>
                        </span>
                    </div>
                    <div class="p-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small text-muted fw-bold">
                                <i class="bi bi-geo-alt me-1"></i> 
                                <?php 
                                $address = !empty($row->address) ? $row->address : '';
                                $district = $row->district_name ?? '';
                                echo htmlspecialchars($address . ' ' . $district);
                                ?>
                            </span>
                            <span class="small fw-bold">
                                <i class="bi bi-eye me-1"></i> <?php echo number_format($row->count_view ?? 0); ?>
                            </span>
                        </div>
                        <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($row->title); ?></h5>
                        <div class="d-flex gap-3 mb-4 text-muted small">
                            <span><i class="bi bi-door-open me-1"></i> 
                                <?php echo !empty($row->utilities) ? '1 PN' : 'Phòng trọ'; ?>
                            </span>
                            <span><i class="bi bi-square me-1"></i> 
                                <?php echo number_format($row->area ?? 0); ?>m²
                            </span>
                            <span><i class="bi bi-lightning-charge me-1"></i> 
                                <?php 
                                $utilities = !empty($row->utilities) ? explode(',', $row->utilities) : [];
                                echo htmlspecialchars($utilities[0] ?? 'Cơ bản');
                                ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div class="price-tag">
                                <?php echo number_format($row->price ?? 0, 0, ',', '.'); ?>Tr 
                                <small class="fs-6 text-muted fw-normal">/tháng</small>
                            </div>
                            <a href="detail.php?id=<?php echo $row->ID; ?>" class="btn btn-outline-dark rounded-circle shadow-none">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> Chưa có phòng trọ nào được đăng
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Đóng kết nối
$conn->close();
?>

    </div>
  </section>
