<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Homi - Thuê Trọ Hiện Đại</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

        body{
            background: #f6f7fb;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #222;
        }

        .info-card{
            background: #fff;
            border-radius: 24px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 6px 25px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
        }

        .gallery-img{
            width: 100%;
            height: 520px;
            object-fit: cover;
            border-radius: 20px;
        }

        .price-badge{
            background: #ffc107;
            color: #000;
            padding: 14px 22px;
            border-radius: 16px;
            font-size: 1.4rem;
            font-weight: 800;
            display: inline-block;
            box-shadow: 0 8px 18px rgba(255,193,7,0.35);
        }

        .feature-box{
            background: #fffaf0;
            border-radius: 18px;
            padding: 20px;
            height: 100%;
            border: 1px solid rgba(255,193,7,0.18);
            transition: .25s;
        }

        .feature-box:hover{
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(255,193,7,0.18);
        }

        .description-content{
            line-height: 2;
            color: #555;
        }

        .map-wrapper{
            overflow: hidden;
            border-radius: 18px;
            height: 350px;
        }

        .contact-card{
            background: #fff;
            color: #222;
            border-radius: 24px;
            padding: 30px;
            position: sticky;
            top: 20px;
            box-shadow: 0 10px 35px rgba(255,193,7,0.15);
            border: 1px solid rgba(255,193,7,0.2);
        }

        .contact-card .btn{
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
        }

        .contact-card .btn-warning{
            color: #000;
        }

        .contact-card hr{
            border-color: rgba(0,0,0,0.08);
        }

        .similar-card{
            display: block;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            text-decoration: none;
            transition: .3s;
            height: 100%;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.03);
        }

        .similar-card:hover{
            transform: translateY(-6px);
            box-shadow: 0 12px 35px rgba(255,193,7,0.18);
        }

        .similar-img{
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .section-title{
            font-size: 1.35rem;
            font-weight: 700;
        }

        .info-icon{
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(255,193,7,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media(max-width: 768px){

            .gallery-img{
                height: 260px;
            }

            .contact-card{
                position: static;
            }

            .price-badge{
                font-size: 1.1rem;
            }

        }

    </style>
</head>

<body>

<?php

include('include/ketnoi.php');

if(isset($_GET['id'])){

    $id = $_GET['id'];

    // Tăng lượt xem
    $update_view = "UPDATE motel SET count_view = count_view + 1 WHERE ID = '$id'";
    $conn->query($update_view);

    // Lấy chi tiết
    $sql = "SELECT * FROM motel WHERE ID = '$id'";
    $result = $conn->query($sql);

    $row = $result->fetch_assoc();

    if ($result->num_rows == 0) {
        header('Location: index.php');
        exit;
    }

    // Phòng tương tự
    $similar_sql = "SELECT * FROM motel 
                    WHERE approve = 1 
                    AND ID != '$id' 
                    AND district_id = '$id'
                    ORDER BY created_at DESC 
                    LIMIT 3";

    $result_similar = $conn->query($similar_sql);

}else{
    header('Location: index.php');
    exit;
}

include("include/menu.php");

?>

<div class="container py-4 py-lg-5">

    <div class="row g-4">

        <!-- LEFT -->
        <div class="col-lg-8">

            <!-- IMAGE -->
            <div class="info-card p-2">
                <img src="<?php echo 'images/' .  $row["images"]; ?>"
                     class="gallery-img"
                     alt="<?php echo $row["title"]; ?>">
            </div>

            <!-- TITLE -->
            <div class="info-card">

                <div class="d-flex justify-content-between align-items-start flex-wrap gap-4">

                    <div>

                        <h1 class="display-6 fw-bold mb-3">
                            <?php echo $row["title"]; ?>
                        </h1>

                        <div class="d-flex flex-wrap gap-4 text-muted">

                            <span>
                                <i class="bi bi-geo-alt-fill text-warning me-1"></i>
                                <?php echo $row["address"]; ?>
                            </span>

                            <span>
                                <i class="bi bi-eye-fill text-warning me-1"></i>
                                <?php echo $row["count_view"]; ?> lượt xem
                            </span>

                        </div>

                    </div>

                    <div class="text-lg-end">

                        <div class="price-badge">
                            <?php echo $row["price"]; ?>Tr
                        </div>

                        <div class="small text-muted mt-2">
                            / tháng
                        </div>

                    </div>

                </div>

            </div>

            <!-- INFO -->
            <div class="info-card">

                <h5 class="fw-bold mb-4">
                    <i class="bi bi-info-circle-fill text-warning me-2"></i>
                    Thông tin cơ bản
                </h5>

                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="feature-box d-flex align-items-center gap-3">

                            <div class="info-icon">
                                <i class="bi bi-aspect-ratio fs-3 text-warning"></i>
                            </div>

                            <div>
                                <div class="small text-muted">Diện tích</div>
                                <div class="fw-bold fs-5">
                                    <?php echo $row["area"]; ?> m²
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="feature-box d-flex align-items-center gap-3">

                            <div class="info-icon">
                                <i class="bi bi-calendar-event fs-3 text-warning"></i>
                            </div>

                            <div>
                                <div class="small text-muted">Ngày đăng</div>
                                <div class="fw-bold">
                                    <?php echo $row["created_at"]; ?>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- DESCRIPTION -->
            <div class="info-card">

                <h5 class="fw-bold mb-4">
                    <i class="bi bi-file-text-fill text-warning me-2"></i>
                    Mô tả chi tiết
                </h5>

                <div class="description-content">
                    <?php echo $row["description"]; ?>
                </div>

            </div>

            <!-- UTILITIES -->
            <div class="info-card">

                <h5 class="fw-bold mb-4">
                    <i class="bi bi-grid-3x3-gap-fill text-warning me-2"></i>
                    Tiện ích
                </h5>

                <div class="feature-box d-flex align-items-center gap-3">

                    <i class="bi bi-check-circle-fill text-warning fs-4"></i>

                    <span class="fw-medium">
                        <?php echo $row["utilities"]; ?>
                    </span>

                </div>

            </div>

            <!-- MAP -->
            <div class="info-card">

                <h5 class="fw-bold mb-4">
                    <i class="bi bi-map-fill text-warning me-2"></i>
                    Vị trí trên bản đồ
                </h5>

                <div class="map-wrapper">

                    <iframe width="100%"
                            height="100%"
                            frameborder="0"
                            style="border:0"
                            src="https://maps.google.com/maps?q=<?php echo $row["latlng"]; ?>&z=15&output=embed"
                            allowfullscreen>
                    </iframe>

                </div>

            </div>

        </div>

        <!-- RIGHT -->
        <div class="col-lg-4">

            <div class="contact-card">

                <div class="text-center mb-4">

                    <div class="bg-warning bg-opacity-25 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:90px;height:90px;">

                        <i class="bi bi-person-fill fs-1"></i>

                    </div>

                    <h4 class="fw-bold mb-1">
                        Chủ trọ
                    </h4>

                    <p class="text-muted mb-0">
                        Liên hệ trực tiếp với chủ phòng
                    </p>

                </div>

                <hr>

                <div class="mb-4 text-center">

                    <div class="small text-muted mb-2">
                        Số điện thoại
                    </div>

                    <div class="fs-4 fw-bold">
                        <?php echo $row["phone"]; ?>
                    </div>

                </div>

                <a href="tel:<?php echo $row["phone"]; ?>"
                   class="btn btn-warning w-100 mb-3">

                    <i class="bi bi-telephone-fill me-2"></i>
                    Gọi điện ngay

                </a>

                <a href="#"
                   class="btn btn-outline-warning w-100">

                    <i class="bi bi-chat-dots-fill me-2"></i>
                    Nhắn tin Zalo

                </a>

            </div>

        </div>

    </div>

    <!-- SIMILAR -->
    <?php if ($result_similar->num_rows > 0): ?>

    <div class="mt-5 pt-2">

        <h4 class="section-title mb-4">
            <i class="bi bi-buildings-fill text-warning me-2"></i>
            Phòng tương tự cùng khu vực
        </h4>

        <div class="row g-4">

            <?php while($row_similar = $result_similar->fetch_assoc()): ?>

            <div class="col-md-4">

                <a href="detail.php?id=<?php echo $row_similar["ID"] ?>"
                   class="similar-card">

                    <img src="<?php echo 'images/' . $row_similar["images"]; ?>"
                         class="similar-img"
                         alt="<?php echo $row_similar["title"]; ?>">

                    <div class="p-4">

                        <h6 class="fw-bold text-dark mb-3">
                            <?php echo $row_similar["title"]; ?>
                        </h6>

                        <div class="d-flex justify-content-between align-items-center">

                            <span class="fw-bold text-warning fs-5">
                                <?php echo $row_similar["price"]; ?>Tr
                            </span>

                            <span class="text-muted">
                                <?php echo $row_similar["area"]; ?>m²
                            </span>

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
$conn->close();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>