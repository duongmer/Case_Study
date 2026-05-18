<?php
// session_start();
?>
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container text-safe">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <div class="brand-icon"><i class="bi bi-house-door-fill"></i></div>
        Homi
      </a>
      <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <i class="bi bi-list fs-2"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item px-3"><a class="nav-link fw-semibold" href="index.php">Trang chủ</a></li>
          <li class="nav-item px-3"><a class="nav-link fw-semibold" href="#">Tìm phòng</a></li>
          <li class="nav-item px-3"><a class="nav-link fw-semibold" href="#">Cẩm nang</a></li>
          <li class="nav-item px-3"><a class="nav-link fw-semibold" href="#">Hỗ trợ</a></li>
        </ul>
        
        <div class="d-flex align-items-center gap-3">
          <?php if (isset($_SESSION['user_id'])): ?>
            <div class="d-flex align-items-center gap-2">
                <a href="profile.php" class="text-decoration-none d-flex align-items-center gap-2">
                    <i class="bi bi-person-circle fs-4 text-dark"></i>
                    <span class="text-dark fw-bold"><?php echo $_SESSION['user_name']; ?></span>
                </a>
                <span class="text-muted">|</span>
                <a href="logout.php" class="text-decoration-none text-danger fw-small">Thoát</a>
            </div>
          <?php else: ?>
            <a href="login.html" class="text-decoration-none text-muted fw-bold me-2">Đăng nhập</a>
          <?php endif; ?>

          <a href="nguoidung/quan_ly_tin.php" class="btn btn-dark rounded-pill px-4">Đăng tin ngay</a>
        </div>
      </div>
    </div>
  </nav>