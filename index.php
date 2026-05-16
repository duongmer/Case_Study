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
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php
  include('include/menu.php');
  include('include/timkiemptro.php');
  include('include/hienthiptro.php');
  include('include/footer.php');
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

