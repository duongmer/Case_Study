<!DOCTYPE html>
<html lang="vi">
<head>
  <title>Quản trị Admin</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</head>
<body onload="time()" class="app sidebar-mini rtl">

<!-- Navbar -->
<header class="app-header">
  <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
  <ul class="app-nav">
    <li>
      <a class="app-nav__item" href="../login.html" title="Đăng xuất">
        <i class='bx bx-log-out bx-rotate-180'></i>
      </a>
    </li>
  </ul>
</header>
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
  <div class="app-sidebar__user">
    <img class="app-sidebar__user-avatar" src="images/avatar.png" width="50px" alt="User Image"
         onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name'] ?? 'Admin') ?>&background=e67e22&color=fff'">
    <div>
      <p class="app-sidebar__user-name"><b><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></b></p>
      <p class="app-sidebar__user-designation">Chào mừng bạn trở lại</p>
    </div>
  </div>
  <hr>
  <ul class="app-menu">
    <li>
      <a class="app-menu__item" href="index.php">
        <i class='app-menu__icon bx bx-tachometer'></i>
        <span class="app-menu__label">Bảng điều khiển</span>
      </a>
    </li>
    <li>
      <a class="app-menu__item" href="motel.php">
        <i class='app-menu__icon bx bx-home-alt'></i>
        <span class="app-menu__label">Quản lý phòng trọ</span>
      </a>
    </li>
    <li>
      <a class="app-menu__item" href="user.php">
        <i class='app-menu__icon bx bx-id-card'></i>
        <span class="app-menu__label">Quản lý người dùng</span>
      </a>
    </li>
    <li>
      <a class="app-menu__item" href="thongke.php">
        <i class='app-menu__icon bx bx-pie-chart-alt-2'></i>
        <span class="app-menu__label">Báo cáo thống kê</span>
      </a>
    </li>
  </ul>
</aside>
<main class="app-content">
