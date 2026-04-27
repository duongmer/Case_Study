<?php
include('ketnoi.php');

// Lấy ID từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Cập nhật lượt xem
$update_view = "UPDATE motel SET count_view = count_view + 1 WHERE ID = ?";
$stmt = $conn->prepare($update_view);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Lấy thông tin chi tiết phòng trọ
$sql = "SELECT * FROM motel WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: index.php');
    exit;
}

$room = $result->fetch_object();

// Xử lý images
$images = [];
if (!empty($room->images)) {
    $images = explode(',', $room->images);
}

// Xử lý tiện ích
$utilities = [];
if (!empty($room->utilities)) {
    $utilities = explode(',', $room->utilities);
}

// Lấy 3 phòng tương tự (cùng khu vực)
$similar_sql = "SELECT * FROM motel 
                WHERE approve = 1 
                AND ID != ? 
                AND district_id = ?
                ORDER BY created_at DESC 
                LIMIT 3";
$stmt_similar = $conn->prepare($similar_sql);
$stmt_similar->bind_param("ii", $id, $room->district_id);
$stmt_similar->execute();
$similar_result = $stmt_similar->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($room->title); ?> - Homi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .gallery-img { height: 400px; object-fit: cover; cursor: pointer; }
        .gallery-thumb { height: 100px; width: 100%; object-fit: cover; cursor: pointer; border-radius: 8px; }
        .info-card { background: white; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .price-badge { font-size: 2rem; font-weight: 700; color: #f59e0b; }
        .contact-card { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border-radius: 16px; padding: 24px; position: sticky; top: 20px; }
        .contact-card .btn { background: white; color: #f59e0b; font-weight: 600; }
        .similar-card { background: white; border-radius: 12px; overflow: hidden; transition: all 0.3s; text-decoration: none; color: inherit; display: block; }
        .similar-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .similar-img { height: 180px; object-fit: cover; width: 100%; }
        @media (max-width: 768px) { .gallery-img { height: 300px; } .price-badge { font-size: 1.5rem; } }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.php" style="color: #f59e0b;">Homi</a>
    </div>
</nav>

<div class="container py-4">
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Gallery -->
            <div class="info-card">
                <div class="row g-2">
                    <div class="col-12">
                        <?php if (!empty($images) && isset($images[0])): ?>
                            <img src="<?php echo 'images/' . htmlspecialchars($images[0]); ?>" 
                                 class="gallery-img w-100 rounded-3" 
                                 alt="<?php echo htmlspecialchars($room->title); ?>"
                                 id="mainImage">
                        <?php else: ?>
                            <img src="https://picsum.photos/seed/<?php echo $room->ID; ?>/800/500" 
                                 class="gallery-img w-100 rounded-3" 
                                 alt="<?php echo htmlspecialchars($room->title); ?>"
                                 id="mainImage">
                        <?php endif; ?>
                    </div>
                    <?php if (count($images) > 1): ?>
                        <div class="col-12">
                            <div class="row g-2">
                                <?php foreach (array_slice($images, 0, 4) as $index => $img): ?>
                                    <div class="col-3">
                                        <img src="<?php echo htmlspecialchars($img); ?>" 
                                             class="gallery-thumb" 
                                             alt="Hình <?php echo $index + 1; ?>"
                                             onclick="changeImage(this.src)">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Title & Price -->
            <div class="info-card">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h1 class="fw-bold mb-2" style="font-size: 1.8rem;"><?php echo htmlspecialchars($room->title); ?></h1>
                        <div class="d-flex gap-3 text-muted">
                            <span><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($room->address); ?></span>
                            <span><i class="bi bi-eye"></i> <?php echo number_format($room->count_view ?? 0); ?> lượt xem</span>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="price-badge"><?php echo number_format($room->price ?? 0, 0, ',', '.'); ?>đ</div>
                        <small class="text-muted">/tháng</small>
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
                                <div class="fw-bold"><?php echo number_format($room->area ?? 0); ?> m²</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                            <i class="bi bi-calendar fs-2 text-warning"></i>
                            <div>
                                <div class="small text-muted">Ngày đăng</div>
                                <div class="fw-bold"><?php echo date('d/m/Y', strtotime($room->created_at)); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mô tả chi tiết -->
            <div class="info-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-file-text me-2"></i> Mô tả chi tiết</h5>
                <div class="lh-lg">
                    <?php echo nl2br(htmlspecialchars($room->description ?? 'Chưa có mô tả chi tiết cho phòng này.')); ?>
                </div>
            </div>

            <!-- Tiện ích -->
            <div class="info-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-grid-3x3-gap-fill me-2"></i> Tiện ích</h5>
                <div class="row g-2">
                    <?php if (!empty($utilities)): ?>
                        <?php foreach ($utilities as $util): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-warning"></i>
                                    <span><?php echo htmlspecialchars(trim($util)); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Chưa cập nhật thông tin tiện ích</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bản đồ -->
            <?php if (!empty($room->latlng)): ?>
            <div class="info-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-map me-2"></i> Vị trí trên bản đồ</h5>
                <div style="border-radius: 12px; overflow: hidden; height: 300px;">
                    <iframe width="100%" height="100%" frameborder="0" style="border:0"
                        src="https://maps.google.com/maps?q=<?php echo urlencode($room->latlng); ?>&z=15&output=embed"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
            <?php endif; ?>
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
                    <strong><?php echo htmlspecialchars($room->phone ?? 'Chưa cập nhật'); ?></strong>
                </div>
                <hr class="border-light">
                <a href="tel:<?php echo htmlspecialchars($room->phone ?? ''); ?>" class="btn w-100 mb-2">
                    <i class="bi bi-telephone-fill me-2"></i> Gọi điện ngay
                </a>
                <a href="#" class="btn btn-outline-light w-100">
                    <i class="bi bi-chat-dots-fill me-2"></i> Nhắn tin Zalo
                </a>
            </div>
        </div>
    </div>

    <!-- Similar Rooms -->
    <?php if ($similar_result && $similar_result->num_rows > 0): ?>
    <div class="mt-5">
        <h4 class="fw-bold mb-4"><i class="bi bi-building me-2"></i> Phòng tương tự cùng khu vực</h4>
        <div class="row g-4">
            <?php while($similar = $similar_result->fetch_object()): 
                $similar_images = !empty($similar->images) ? explode(',', $similar->images) : [];
                $first_img = !empty($similar_images) ? $similar_images[0] : 'https://picsum.photos/seed/' . $similar->ID . '/400/300';
            ?>
                <div class="col-md-4">
                    <a href="chi-tiet.php?id=<?php echo $similar->ID; ?>" class="similar-card">
                        <img src="<?php echo htmlspecialchars($first_img); ?>" class="similar-img" alt="<?php echo htmlspecialchars($similar->title); ?>">
                        <div class="p-3">
                            <h6 class="fw-bold mb-2" style="color: #333;"><?php echo htmlspecialchars($similar->title); ?></h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-warning fw-bold"><?php echo number_format($similar->price ?? 0, 0, ',', '.'); ?>đ</span>
                                <small class="text-muted"><?php echo number_format($similar->area ?? 0); ?>m²</small>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function changeImage(src) {
    document.getElementById('mainImage').src = src;
}
</script>
</body>
</html>