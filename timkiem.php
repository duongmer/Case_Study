<?php
include('ketnoi.php');

// =============================================
// XỬ LÝ THAM SỐ TÌM KIẾM TỪ URL
// =============================================
$keyword   = isset($_GET['keyword'])  ? trim($_GET['keyword'])       : '';
$loai      = isset($_GET['loai'])     ? trim($_GET['loai'])          : '';
$gia_min   = isset($_GET['gia_min'])  ? (int)$_GET['gia_min']       : 0;
$gia_max   = isset($_GET['gia_max'])  ? (int)$_GET['gia_max']       : 0;
$dien_tich = isset($_GET['dien_tich'])? (int)$_GET['dien_tich']     : 0;
$sap_xep   = isset($_GET['sap_xep']) ? trim($_GET['sap_xep'])       : 'moi_nhat';
$page      = isset($_GET['page'])     ? max(1, (int)$_GET['page'])  : 1;
$per_page  = 9;
$offset    = ($page - 1) * $per_page;

// =============================================
// XÂY DỰNG CÂU QUERY ĐỘNG
// =============================================
$where_parts = ["approve = 1"];
$params      = [];
$types       = '';

if (!empty($keyword)) {
    $where_parts[] = "(title LIKE ? OR address LIKE ? OR description LIKE ?)";
    $kw = "%$keyword%";
    $params[] = $kw; $params[] = $kw; $params[] = $kw;
    $types .= 'sss';
}

if (!empty($loai)) {
    $where_parts[] = "category_name = ?";
    $params[] = $loai;
    $types .= 's';
}

if ($gia_min > 0) {
    $where_parts[] = "price >= ?";
    $params[] = $gia_min * 1000000;
    $types .= 'i';
}

if ($gia_max > 0) {
    $where_parts[] = "price <= ?";
    $params[] = $gia_max * 1000000;
    $types .= 'i';
}

if ($dien_tich > 0) {
    $where_parts[] = "area >= ?";
    $params[] = $dien_tich;
    $types .= 'i';
}

$where_sql = implode(' AND ', $where_parts);

// Sắp xếp
$order_map = [
    'moi_nhat'  => 'created_at DESC',
    'gia_tang'  => 'price ASC',
    'gia_giam'  => 'price DESC',
    'xem_nhieu' => 'count_view DESC',
    'dien_tich' => 'area DESC',
];
$order_sql = $order_map[$sap_xep] ?? 'created_at DESC';

// Đếm tổng kết quả
$count_sql = "SELECT COUNT(*) as total FROM motel WHERE $where_sql";
$stmt_count = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_rows = $stmt_count->get_result()->fetch_object()->total ?? 0;
$total_pages = ceil($total_rows / $per_page);

// Lấy kết quả phân trang
$sql = "SELECT * FROM motel WHERE $where_sql ORDER BY $order_sql LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$all_params   = array_merge($params, [$per_page, $offset]);
$all_types    = $types . 'ii';
$stmt->bind_param($all_types, ...$all_params);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();

// Helper: giữ lại tham số GET khi phân trang
function buildUrl($extra = []) {
    $params = array_merge($_GET, $extra);
    unset($params['page']);
    return '?' . http_build_query(array_filter($params, fn($v) => $v !== '')) . ($extra ? '&page=' . ($extra['page'] ?? 1) : '');
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kết quả tìm kiếm<?php echo !empty($keyword) ? ' - ' . htmlspecialchars($keyword) : ''; ?> | Homi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
    /* ===== SEARCH PAGE EXTRAS ===== */
    .search-hero {
      background: linear-gradient(135deg, #fff7ed 0%, #fff 60%);
      padding: 100px 0 32px;
      border-bottom: 1px solid #f1f5f9;
    }
    .search-bar-wrap {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(245,158,11,.10), 0 2px 8px rgba(0,0,0,.05);
      padding: 10px 12px;
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      align-items: center;
    }
    .search-field {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 6px 14px;
      flex: 1;
      min-width: 160px;
    }
    .search-field input,
    .search-field select {
      border: none;
      background: transparent;
      font-family: inherit;
      font-weight: 600;
      font-size: .95rem;
      color: #0f172a;
      outline: none;
      width: 100%;
    }
    .search-field select option { font-weight: 400; }
    .search-field i { color: #f59e0b; flex-shrink: 0; }
    .divider-v { width: 1px; background: #f1f5f9; height: 36px; }
    .btn-search-main {
      background: #f59e0b;
      color: #fff;
      border: none;
      border-radius: 14px;
      padding: 12px 28px;
      font-weight: 700;
      font-family: inherit;
      font-size: .95rem;
      white-space: nowrap;
      transition: background .2s;
    }
    .btn-search-main:hover { background: #d97706; color: #fff; }

    /* Filter bar */
    .filter-bar {
      background: #fff;
      border-radius: 16px;
      padding: 14px 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,.04);
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }
    .filter-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      border-radius: 50px;
      border: 1.5px solid #e2e8f0;
      background: #fff;
      font-size: .82rem;
      font-weight: 600;
      color: #334155;
      cursor: pointer;
      transition: all .2s;
      text-decoration: none;
    }
    .filter-chip:hover, .filter-chip.active {
      border-color: #f59e0b;
      background: #fffbeb;
      color: #d97706;
    }
    .filter-chip .bi-x { font-size: .9rem; }

    /* Result card */
    .result-card {
      background: #fff;
      border-radius: 20px;
      border: 1px solid rgba(0,0,0,.04);
      overflow: hidden;
      transition: transform .3s, box-shadow .3s;
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    .result-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 40px -10px rgba(0,0,0,.10);
    }
    .result-img-wrap { position: relative; overflow: hidden; }
    .result-img {
      height: 200px;
      object-fit: cover;
      width: 100%;
      transition: transform .4s;
    }
    .result-card:hover .result-img { transform: scale(1.05); }
    .result-badge {
      position: absolute;
      top: 12px; left: 12px;
      background: #fff;
      color: #0f172a;
      font-size: .72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .5px;
      padding: 4px 10px;
      border-radius: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,.10);
    }
    .result-body { padding: 16px 20px 20px; flex: 1; display: flex; flex-direction: column; }
    .result-title {
      font-weight: 700;
      color: #0f172a;
      font-size: .97rem;
      margin-bottom: 6px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .result-address { font-size: .8rem; color: #94a3b8; font-weight: 500; }
    .result-meta { display: flex; gap: 12px; font-size: .8rem; color: #64748b; font-weight: 500; margin: 10px 0; }
    .result-price { font-size: 1.25rem; font-weight: 800; color: #f59e0b; }
    .btn-detail {
      background: #0f172a;
      color: #fff;
      border: none;
      border-radius: 50%;
      width: 38px; height: 38px;
      display: flex; align-items: center; justify-content: center;
      transition: background .2s;
      flex-shrink: 0;
      text-decoration: none;
    }
    .btn-detail:hover { background: #f59e0b; color: #fff; }

    /* Pagination */
    .pagination .page-link {
      border-radius: 10px !important;
      margin: 0 3px;
      font-weight: 600;
      border: 1.5px solid #e2e8f0;
      color: #334155;
      padding: 8px 14px;
    }
    .pagination .page-item.active .page-link {
      background: #f59e0b;
      border-color: #f59e0b;
      color: #fff;
    }
    .pagination .page-link:hover:not(.active) { background: #fffbeb; border-color: #f59e0b; color: #d97706; }

    /* Empty state */
    .empty-state { text-align: center; padding: 64px 20px; }
    .empty-icon { font-size: 4rem; color: #f1f5f9; margin-bottom: 16px; }

    /* Advanced filter panel */
    .adv-filter {
      background: #fff;
      border: 1.5px solid #f1f5f9;
      border-radius: 16px;
      padding: 20px;
      position: sticky;
      top: 80px;
    }
    .adv-filter h6 { font-weight: 700; color: #0f172a; margin-bottom: 16px; }
    .form-range::-webkit-slider-thumb { background: #f59e0b; }
    .form-range::-moz-range-thumb { background: #f59e0b; }
    .range-label { font-size: .82rem; font-weight: 600; color: #64748b; }

    @media (max-width: 768px) {
      .search-hero { padding-top: 80px; }
      .divider-v { display: none; }
    }
  </style>
</head>
<body>

<?php include('menu.php'); ?>

<!-- Search Hero -->
<div class="search-hero">
  <div class="container">
    <h2 class="fw-bold mb-1" style="font-size:1.5rem">
      <?php if (!empty($keyword)): ?>
        Kết quả cho "<span class="text-amber"><?php echo htmlspecialchars($keyword); ?></span>"
      <?php else: ?>
        Tìm phòng trọ <span class="text-amber">phù hợp</span>
      <?php endif; ?>
    </h2>
    <p class="text-muted fw-medium mb-4">
      Tìm thấy <strong><?php echo number_format($total_rows); ?></strong> kết quả
    </p>

    <!-- Search bar -->
    <form action="timkiem.php" method="GET">
      <div class="search-bar-wrap">
        <div class="search-field">
          <i class="bi bi-search"></i>
          <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Tìm theo tên, địa chỉ...">
        </div>
        <div class="divider-v"></div>
        <div class="search-field">
          <i class="bi bi-buildings"></i>
          <select name="loai">
            <option value="">Tất cả loại</option>
            <option value="Phòng trọ"     <?php if($loai=='Phòng trọ')     echo 'selected'; ?>>Phòng trọ</option>
            <option value="Căn hộ Studio" <?php if($loai=='Căn hộ Studio') echo 'selected'; ?>>Căn hộ Studio</option>
            <option value="Nhà nguyên căn"<?php if($loai=='Nhà nguyên căn')echo 'selected'; ?>>Nhà nguyên căn</option>
            <option value="Ở ghép"        <?php if($loai=='Ở ghép')        echo 'selected'; ?>>Ở ghép</option>
          </select>
        </div>
        <div class="divider-v"></div>
        <div class="search-field">
          <i class="bi bi-cash-coin"></i>
          <select name="gia_max">
            <option value="">Giá thuê</option>
            <option value="2"  <?php if($gia_max==2)  echo 'selected'; ?>>Dưới 2 triệu</option>
            <option value="3"  <?php if($gia_max==3)  echo 'selected'; ?>>Dưới 3 triệu</option>
            <option value="5"  <?php if($gia_max==5)  echo 'selected'; ?>>Dưới 5 triệu</option>
            <option value="8"  <?php if($gia_max==8)  echo 'selected'; ?>>Dưới 8 triệu</option>
            <option value="15" <?php if($gia_max==15) echo 'selected'; ?>>Dưới 15 triệu</option>
          </select>
        </div>
        <button type="submit" class="btn-search-main">
          <i class="bi bi-search me-1"></i> Tìm ngay
        </button>
      </div>
    </form>
  </div>
</div>

<div class="container py-4">
  <div class="row g-4">

    <!-- LEFT: Advanced Filter -->
    <div class="col-lg-3 d-none d-lg-block">
      <form action="timkiem.php" method="GET">
        <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
        <div class="adv-filter">
          <h6><i class="bi bi-sliders me-2"></i>Lọc nâng cao</h6>

          <!-- Loại phòng -->
          <div class="mb-4">
            <label class="range-label mb-2 d-block">Loại hình</label>
            <?php
            $loai_list = ['Phòng trọ','Căn hộ Studio','Nhà nguyên căn','Ở ghép'];
            foreach ($loai_list as $l):
            ?>
            <div class="form-check mb-1">
              <input class="form-check-input" type="radio" name="loai" id="loai_<?php echo md5($l); ?>"
                value="<?php echo htmlspecialchars($l); ?>"
                <?php if ($loai === $l) echo 'checked'; ?>>
              <label class="form-check-label fw-semibold small" for="loai_<?php echo md5($l); ?>">
                <?php echo htmlspecialchars($l); ?>
              </label>
            </div>
            <?php endforeach; ?>
            <div class="form-check mb-1">
              <input class="form-check-input" type="radio" name="loai" id="loai_all" value="" <?php if(empty($loai)) echo 'checked'; ?>>
              <label class="form-check-label fw-semibold small" for="loai_all">Tất cả</label>
            </div>
          </div>

          <!-- Mức giá -->
          <div class="mb-4">
            <label class="range-label mb-2 d-block">Giá tối đa (triệu/tháng)</label>
            <input type="range" class="form-range" id="giaRange" name="gia_max"
              min="0" max="20" step="1"
              value="<?php echo $gia_max ?: 20; ?>"
              oninput="document.getElementById('giaVal').textContent = this.value == 20 ? 'Không giới hạn' : this.value + ' triệu'">
            <div class="d-flex justify-content-between range-label">
              <span>0</span>
              <span id="giaVal" class="fw-bold text-amber">
                <?php echo ($gia_max && $gia_max < 20) ? $gia_max . ' triệu' : 'Không giới hạn'; ?>
              </span>
              <span>20+</span>
            </div>
          </div>

          <!-- Diện tích -->
          <div class="mb-4">
            <label class="range-label mb-2 d-block">Diện tích tối thiểu (m²)</label>
            <input type="range" class="form-range" id="dtRange" name="dien_tich"
              min="0" max="100" step="5"
              value="<?php echo $dien_tich; ?>"
              oninput="document.getElementById('dtVal').textContent = this.value == 0 ? 'Không giới hạn' : this.value + ' m²'">
            <div class="d-flex justify-content-between range-label">
              <span>0</span>
              <span id="dtVal" class="fw-bold text-amber">
                <?php echo $dien_tich > 0 ? $dien_tich . ' m²' : 'Không giới hạn'; ?>
              </span>
              <span>100m²</span>
            </div>
          </div>

          <button type="submit" class="btn w-100 fw-bold" style="background:#f59e0b;color:#fff;border-radius:12px">
            <i class="bi bi-funnel me-1"></i> Áp dụng lọc
          </button>
          <a href="timkiem.php" class="btn w-100 mt-2 fw-bold" style="border:1.5px solid #e2e8f0;border-radius:12px;color:#64748b">
            <i class="bi bi-x-circle me-1"></i> Xóa bộ lọc
          </a>
        </div>
      </form>
    </div>

    <!-- RIGHT: Results -->
    <div class="col-lg-9">

      <!-- Sort & Active Filters -->
      <div class="filter-bar mb-4">
        <span class="fw-bold small text-muted me-2">Sắp xếp:</span>
        <?php
        $sort_opts = [
          'moi_nhat'  => ['label'=>'Mới nhất',    'icon'=>'bi-clock'],
          'gia_tang'  => ['label'=>'Giá tăng dần', 'icon'=>'bi-sort-up'],
          'gia_giam'  => ['label'=>'Giá giảm dần', 'icon'=>'bi-sort-down'],
          'xem_nhieu' => ['label'=>'Xem nhiều',   'icon'=>'bi-eye'],
          'dien_tich' => ['label'=>'Diện tích',   'icon'=>'bi-aspect-ratio'],
        ];
        foreach ($sort_opts as $key => $opt):
          $active = ($sap_xep === $key) ? 'active' : '';
          $url = '?' . http_build_query(array_merge($_GET, ['sap_xep'=>$key, 'page'=>1]));
        ?>
          <a href="<?php echo htmlspecialchars($url); ?>" class="filter-chip <?php echo $active; ?>">
            <i class="<?php echo $opt['icon']; ?>"></i> <?php echo $opt['label']; ?>
          </a>
        <?php endforeach; ?>

        <div class="ms-auto text-muted small fw-semibold">
          Trang <?php echo $page; ?>/<?php echo max(1,$total_pages); ?>
        </div>
      </div>

      <!-- Active filter chips -->
      <?php
      $active_filters = [];
      if (!empty($loai))      $active_filters[] = ['label'=>$loai, 'key'=>'loai'];
      if ($gia_max > 0)       $active_filters[] = ['label'=>'Dưới '.$gia_max.'tr', 'key'=>'gia_max'];
      if ($dien_tich > 0)     $active_filters[] = ['label'=>'Từ '.$dien_tich.'m²', 'key'=>'dien_tich'];
      if (!empty($active_filters)):
      ?>
      <div class="d-flex gap-2 flex-wrap mb-3">
        <span class="small fw-bold text-muted align-self-center">Đang lọc:</span>
        <?php foreach ($active_filters as $f):
          $remove_params = array_diff_key($_GET, [$f['key'] => '']);
          $remove_url = 'timkiem.php?' . http_build_query($remove_params);
        ?>
          <a href="<?php echo htmlspecialchars($remove_url); ?>" class="filter-chip active">
            <?php echo htmlspecialchars($f['label']); ?>
            <i class="bi bi-x"></i>
          </a>
        <?php endforeach; ?>
        <a href="timkiem.php<?php echo !empty($keyword) ? '?keyword='.urlencode($keyword) : ''; ?>" class="filter-chip">
          <i class="bi bi-x-circle"></i> Xóa hết
        </a>
      </div>
      <?php endif; ?>

      <!-- Cards -->
      <?php if ($total_rows == 0): ?>
        <div class="empty-state">
          <div class="empty-icon"><i class="bi bi-search"></i></div>
          <h4 class="fw-bold mb-2">Không tìm thấy phòng nào</h4>
          <p class="text-muted">Hãy thử thay đổi từ khóa hoặc điều chỉnh bộ lọc.</p>
          <a href="timkiem.php" class="btn btn-dark-round mt-2">Xem tất cả phòng</a>
        </div>
      <?php else: ?>
        <div class="row g-4">
          <?php while ($row = $result->fetch_object()):
            $images = !empty($row->images) ? explode(',', $row->images) : [];
            $img = !empty($images) ? 'assets/images/' . $images[0] : 'https://picsum.photos/seed/'.$row->ID.'/600/400';
            $utils = !empty($row->utilities) ? explode(',', $row->utilities) : [];
          ?>
          <div class="col-md-6 col-xl-4">
            <div class="result-card">
              <div class="result-img-wrap">
                <img src="<?php echo htmlspecialchars($img); ?>" class="result-img" alt="<?php echo htmlspecialchars($row->title); ?>">
                <span class="result-badge"><?php echo htmlspecialchars($row->category_name ?? 'Phòng trọ'); ?></span>
              </div>
              <div class="result-body">
                <h6 class="result-title"><?php echo htmlspecialchars($row->title); ?></h6>
                <div class="result-address mb-1">
                  <i class="bi bi-geo-alt me-1"></i>
                  <?php echo htmlspecialchars($row->address ?? ''); ?>
                </div>
                <div class="result-meta">
                  <span><i class="bi bi-square me-1"></i><?php echo number_format($row->area ?? 0); ?>m²</span>
                  <span><i class="bi bi-lightning-charge me-1"></i><?php echo htmlspecialchars($utils[0] ?? 'Cơ bản'); ?></span>
                  <span><i class="bi bi-eye me-1"></i><?php echo number_format($row->count_view ?? 0); ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                  <div>
                    <div class="result-price"><?php echo number_format($row->price ?? 0, 0, ',', '.'); ?>đ</div>
                    <small class="text-muted fw-semibold">/tháng</small>
                  </div>
                  <a href="detail.php?id=<?php echo $row->ID; ?>" class="btn-detail">
                    <i class="bi bi-chevron-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav class="mt-5 d-flex justify-content-center">
          <ul class="pagination">
            <?php if ($page > 1): ?>
              <li class="page-item">
                <a class="page-link" href="<?php echo 'timkiem.php?' . http_build_query(array_merge($_GET, ['page'=>$page-1])); ?>">
                  <i class="bi bi-chevron-left"></i>
                </a>
              </li>
            <?php endif; ?>

            <?php
            $start = max(1, $page - 2);
            $end   = min($total_pages, $page + 2);
            for ($p = $start; $p <= $end; $p++):
            ?>
              <li class="page-item <?php if($p==$page) echo 'active'; ?>">
                <a class="page-link" href="<?php echo 'timkiem.php?' . http_build_query(array_merge($_GET, ['page'=>$p])); ?>">
                  <?php echo $p; ?>
                </a>
              </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
              <li class="page-item">
                <a class="page-link" href="<?php echo 'timkiem.php?' . http_build_query(array_merge($_GET, ['page'=>$page+1])); ?>">
                  <i class="bi bi-chevron-right"></i>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </nav>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include('footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
