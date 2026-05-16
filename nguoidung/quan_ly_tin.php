<?php
session_start();
include('ketnoi.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$uid = $_SESSION['user_id'];

if (isset($_GET['xoa_id'])) {
    $xoa_id = $_GET['xoa_id'];
    $sql_xoa = "DELETE FROM motel WHERE ID = '$xoa_id' AND user_id = '$uid'";
    $conn->query($sql_xoa);
    header("Location: quan_ly_tin.php"); // Load lại trang
    exit();
}


if (isset($_GET['thue_id'])) {
    $thue_id = $_GET['thue_id'];
    $sql_thue = "UPDATE motel SET approve = 2 WHERE ID = '$thue_id' AND user_id = '$uid'";
    $conn->query($sql_thue);
    header("Location: quan_ly_tin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý tin đăng - Homi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<?php include('menu.php'); ?>

<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-list-task me-2 text-warning"></i>Quản lý tin đăng của bạn</h2>
        <a href="them_tin.php" class="btn btn-dark rounded-pill px-4">
            <i class="bi bi-plus-circle me-2"></i>Đăng tin mới
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Hình ảnh</th>
                        <th>Tiêu đề / Địa chỉ</th>
                        <th>Giá (Tr)</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM motel WHERE user_id = '$uid' ORDER BY created_at DESC";
                    $rs = $conn->query($sql);

                    if ($rs->num_rows > 0) {
                        while ($row = $rs->fetch_assoc()) {
                            if ($row['approve'] == 1) {
                                $badge = '<span class="badge bg-success">Đang cho thuê</span>';
                            } elseif ($row['approve'] == 2) {
                                $badge = '<span class="badge bg-secondary">Đã cho thuê</span>';
                            } else {
                                $badge = '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
                            }
                    ?>
                    <tr>
                        <td class="ps-4">
                            <img src="images/<?php echo $row['images']; ?>" class="rounded" width="80" height="60" style="object-fit: cover;" onerror="this.src='https://picsum.photos/80/60'">
                        </td>
                        <td>
                            <h6 class="mb-1 fw-bold"><?php echo $row['title']; ?></h6>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?php echo $row['address']; ?></small>
                        </td>
                        <td class="fw-bold text-warning"><?php echo number_format($row['price']); ?></td>
                        <td><?php echo $badge; ?></td>
                        <td class="text-end pe-4">
                            <?php if ($row['approve'] == 1): ?>
                                <a href="?thue_id=<?php echo $row['ID']; ?>" class="btn btn-sm btn-success" title="Đánh dấu đã thuê" onclick="return confirm('Xác nhận phòng này đã có người thuê? (Sẽ bị ẩn khỏi trang tìm kiếm)');">
                                    <i class="bi bi-check-circle"></i>
                                </a>
                            <?php endif; ?>
                            <a href="sua_tin.php?id=<?php echo $row['ID']; ?>" class="btn btn-sm btn-outline-primary" title="Sửa tin">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="?xoa_id=<?php echo $row['ID']; ?>" class="btn btn-sm btn-outline-danger" title="Xóa tin" onclick="return confirm('Bạn có chắc chắn muốn xóa tin này không?');">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else { 
                    ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Bạn chưa đăng tin phòng trọ nào.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
</body>
</html>