<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Homi - Thuê Trọ Hiện Đại</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
include('ketnoi.php');

if(isset($_GET['id'])){
$id = $_GET['id'];

// Cập nhật lượt xem
$update_view = "UPDATE motel SET count_view = count_view + 1 WHERE ID = '$id'";
$conn->query($update_view);

// Lấy thông tin chi tiết phòng trọ
$sql = "SELECT * FROM motel WHERE ID = '$id'";
$result = $conn->query($sql);
$row  = $result-> fetch_assoc();

if ($result->num_rows == 0) {
    header('Location: index.php');
    exit;
}

// Lấy 3 phòng tương tự (cùng khu vực)
$similar_sql = "SELECT * FROM motel 
                WHERE approve = 1 
                AND ID != '$id' 
                AND district_id = '$id'
                ORDER BY created_at DESC 
                LIMIT 3";
$result_similar = $conn->query($similar_sql);
}
else{
    header('Location: index.php');
    exit;
}
include("menu.php");
?>

<div class="container py-4">
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Gallery -->
            <div class="info-card">
                <div class="row g-2">
                    <div class="col-12">
                            <img src="<?php echo 'images/' .  $row["images"];?>" class="gallery-img w-100 rounded-3" alt="<?php echo $row["title"]; ?>" id="mainImage">
                    </div>                    
                </div>
            </div>
            <!-- Title & Price -->
            <div class="info-card">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h1 class="fw-bold mb-2" style="font-size: 1.8rem;"><?php echo $row["title"]; ?></h1>
                        <div class="d-flex gap-3 text-muted">
                            <span><i class="bi bi-geo-alt"></i> <?php echo $row["address"]; ?></span>
                            <span><i class="bi bi-eye"></i> <?php echo $row["count_view"]; ?> lượt xem</span>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="price-badge"><?php echo $row["price"]; ?></div>
                        <small class="text-muted">Tr/tháng</small>
                    </div>
                </div>
            </div>

            <!-- Thông tin cơ bản -->
            <div class="info-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i> Thông tin cơ bản</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                            <i class="bi bi-aspect-ratio fs-2 text-warning"></i>
                            <div>
                                <div class="small text-muted">Diện tích</div>
                                <div class="fw-bold"><?php echo $row["area"]; ?> m²</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                            <i class="bi bi-calendar fs-2 text-warning"></i>
                            <div>
                                <div class="small text-muted">Ngày đăng</div>
                                <div class="fw-bold"><?php echo $row["created_at"]; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mô tả chi tiết -->
            <div class="info-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-file-text me-2"></i> Mô tả chi tiết</h5>
                <div class="lh-lg">
                    <?php echo $row["description"]; ?>
                </div>
            </div>

            <!-- Tiện ích -->
            <div class="info-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-grid-3x3-gap-fill me-2"></i> Tiện ích</h5>
                <div class="row g-2">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-warning"></i>
                                    <span><?php echo $row["utilities"] ?></span>
                                </div>
                            </div>
                </div>
            </div>
        </div>
            <!-- Bản đồ -->
            <div class="info-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-map me-2"></i> Vị trí trên bản đồ</h5>
                <div style="border-radius: 12px; overflow: hidden; height: 300px;">
                    <iframe width="100%" height="100%" frameborder="0" style="border:0"
                        src="https://maps.google.com/maps?q=<?php echo $row["latlng"]; ?>&z=15&output=embed"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>

        <!-- Right Column - Contact -->
        <div class="col-lg-4">
            <div class="contact-card">
                <div class="text-center mb-3">
                    <i class="bi bi-person-circle fs-1"></i>
                    <h5 class="mt-2">Chủ trọ</h5>
                </div>
                <hr class="border-light">
                <div class="mb-3">
                    <i class="bi bi-telephone-fill me-2"></i>
                    <strong><?php echo $row["phone"]; ?></strong>
                </div>
                <hr class="border-light">
                <a href="tel:<?php echo $row["phone"]; ?>" class="btn w-100 mb-2">
                    <i class="bi bi-telephone-fill me-2"></i> Gọi điện ngay
                </a>
                <a href="#" class="btn btn-outline-light w-100">
                    <i class="bi bi-chat-dots-fill me-2"></i> Nhắn tin Zalo
                </a>
            </div>
        </div>
    </div>

    <!-- Similar Rooms -->
    <?php if ($result_similar->num_rows > 0): ?>
    <div class="mt-5">
        <h4 class="fw-bold mb-4"><i class="bi bi-building me-2"></i> Phòng tương tự cùng khu vực</h4>
        <div class="row g-4">
            <?php while($row_similar  = $result_similar -> fetch_assoc()): ?>    
                <div class="col-md-4">
                    <a href="detail.php?id=<?php echo $row_similar["ID"] ?>" class="similar-card">
                        <img src="<?php echo "images/". $row_similar["images"]; ?>" class="similar-img" alt="<?php echo $row_similar["title"]; ?>">
                        <div class="p-3">
                            <h6 class="fw-bold mb-2" style="color: #333;"><?php echo $row_similar["title"]; ?></h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-warning fw-bold"><?php echo $row_similar["price"] ?>Tr/tháng</span>
                                <small class="text-muted"><?php echo $row_similar["area"]; ?>m²</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php
// Đóng kết nối
$conn->close();
?>
<!-- Bootstrap 5 Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Simple AOS emulation
    document.addEventListener('DOMContentLoaded', function() {
      const aosElements = document.querySelectorAll('[data-aos]');
      aosElements.forEach(el => el.classList.add('aos-init'));
    });
  </script>
</body>
</html>