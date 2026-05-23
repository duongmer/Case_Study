<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Homi</title>
  <link rel="icon" href="assets/favicon.ico" type="image/x-icon" sizes="16x16"/>
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
  include('menu.php');
  
  include('timkiemptro.php');
  include('hienthiptro.php');
  include('homepage_sections.php');
  include('footer.php');
  ?>
  <!-- Bootstrap 5 Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Intersection Observer for scroll-reveal animations
      const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.08
      };

      const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
            observer.unobserve(entry.target);
          }
        });
      }, observerOptions);

      // Select all elements to animate and add the reveal-element class
      const animElements = document.querySelectorAll('[data-aos], .reveal-element, .listing-card, .benefit-card, .step-item');
      animElements.forEach((el, index) => {
        el.classList.add('reveal-element');
        // Add a slight stagger effect based on index if they are columns
        if (el.classList.contains('listing-card') || el.classList.contains('benefit-card')) {
          const colIndex = index % 3;
          if (colIndex > 0) {
            el.classList.add('reveal-delay-' + colIndex);
          }
        }
        observer.observe(el);
      });
      
      // Navbar scroll behavior
      const navbar = document.querySelector('.navbar');
      if (navbar) {
        window.addEventListener('scroll', () => {
          if (window.scrollY > 30) {
            navbar.classList.add('scrolled');
          } else {
            navbar.classList.remove('scrolled');
          }
        });
        // Initial check
        if (window.scrollY > 30) {
          navbar.classList.add('scrolled');
        }
      }
    });
  </script>
</body>
</html>

