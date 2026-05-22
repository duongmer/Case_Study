  <!-- Hero Section -->
  <section class="hero-section">
    <div class="container mt-5">
      <div class="row align-items-center">
        <div class="col-lg-7" data-aos>
          <h1 class="hero-title">
            Tìm không gian <br> 
            <span class="text-amber">sống lý tưởng</span> <br> 
            cho hành trình mới
          </h1>
          <p class="lead text-muted mb-5">Hệ thống tìm trọ thông minh lớn nhất Việt Nam. Kết nối chính chủ, thông tin minh bạch, trải nghiệm dễ dàng.</p>
          
          <!-- ✅ FORM TÌM KIẾM - trỏ sang timkiem.php -->
          <form action="timkiem.php" method="GET">
            <div class="search-box mb-4">
              <div class="search-input-group border-end-0 border-sm-end">
                <i class="bi bi-geo-alt text-amber"></i>
                <input type="text" name="keyword" class="form-control border-0 shadow-none fw-bold" 
                       placeholder="Bạn muốn ở đâu? (địa chỉ, tên phòng...)">
              </div>
              <div class="search-input-group d-none d-md-flex">
                <i class="bi bi-buildings text-amber"></i>
                <select name="loai" class="form-select border-0 shadow-none fw-bold">
                  <option value="">Loại hình</option>
                  <option value="1">Phòng trọ</option>
                  <option value="2">Căn hộ Studio</option>
                  <option value="3">Nhà nguyên căn</option>
                  <option value="4">Ở ghép</option>
                </select>
              </div>
              <div class="search-input-group d-none d-md-flex">
                <i class="bi bi-cash-coin text-amber"></i>
                <select name="gia_max" class="form-select border-0 shadow-none fw-bold">
                  <option value="">Khoảng giá</option>
                  <option value="2">Dưới 2 triệu</option>
                  <option value="3">Dưới 3 triệu</option>
                  <option value="5">Dưới 5 triệu</option>
                  <option value="8">Dưới 8 triệu</option>
                  <option value="15">Dưới 15 triệu</option>
                </select>
              </div>
              <button type="submit" class="search-btn">
                <i class="bi bi-search me-2"></i> Tìm ngay
              </button>
            </div>
          </form>

          <!-- Quick category links -->
          <div class="d-flex gap-2 flex-wrap mb-4">
            <span class="text-muted small fw-bold align-self-center">Tìm nhanh:</span>
            <a href="timkiem.php?loai=1" class="badge text-bg-light fw-bold text-muted text-decoration-none py-2 px-3 rounded-pill">Phòng trọ</a>
            <a href="timkiem.php?loai=2" class="badge text-bg-light fw-bold text-muted text-decoration-none py-2 px-3 rounded-pill">Căn hộ Studio</a>
            <a href="timkiem.php?gia_max=3" class="badge text-bg-light fw-bold text-muted text-decoration-none py-2 px-3 rounded-pill">Dưới 3 triệu</a>
            <a href="timkiem.php?sap_xep=xem_nhieu" class="badge text-bg-light fw-bold text-muted text-decoration-none py-2 px-3 rounded-pill">Xem nhiều nhất</a>
          </div>
          
          <div class="d-flex align-items-center gap-3">
            <div class="avatar-group d-flex">
              <img src="https://picsum.photos/seed/u1/40/40" class="rounded-circle border border-2 border-white me-n2" width="40" height="40" alt="">
              <img src="https://picsum.photos/seed/u2/40/40" class="rounded-circle border border-2 border-white me-n2" width="40" height="40" alt="">
              <img src="https://picsum.photos/seed/u3/40/40" class="rounded-circle border border-2 border-white ms-n2" width="40" height="40" alt="">
            </div>
            <p class="small text-muted fw-bold m-0"><span class="text-dark">50,000+</span> người đã tin dùng Homi</p>
          </div>
        </div>
        <div class="col-lg-5 d-none d-lg-block">
          <img src="https://picsum.photos/seed/home/800/1000" class="img-fluid rounded-5 shadow-lg" alt="Home">
        </div>
      </div>
    </div>
  </section>
