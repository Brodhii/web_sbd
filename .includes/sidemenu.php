<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="./dashboard.php" class="app-brand-link">
      <span class="app-brand-text demo menu-text fw-bolder ms-2 text-uppercase">BiteNest 🍖</span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>
  <div class="menu-inner-shadow"></div>
  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item">
      <a href="dashboard.php" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div>Dashboard</div>
      </a>
    </li>

    <!-- Menu Admin -->
    <?php if ($_SESSION['role'] === 'admin'): ?>
      <li class="menu-header small text-uppercase"><span class="menu-header-text">Fitur Admin</span></li>
      <li class="menu-item">
        <a href="menu.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-menu"></i>
          <div>Menu</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="pesanan.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-cart"></i>
          <div>Pesanan</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="users.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-user"></i>
          <div>Pengguna</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="reports.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
          <div>Laporan</div>
        </a>
      </li>
    <?php endif; ?>

    <!-- Menu Pelanggan -->
    <?php if ($_SESSION['role'] === 'pelanggan'): ?>
      <li class="menu-header small text-uppercase"><span class="menu-header-text">Fitur Pelanggan</span></li>
      <li class="menu-item">
        <a href="menu_pelanggan.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-menu"></i>
          <div>Menu</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="pesananpelanggan.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-cart"></i>
          <div>Pesanan</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="riwayat_pesanan.php" class="menu-link">
          <i class="menu-icon tf-icons bx bx-history"></i>
          <div>Riwayat Pesanan</div>
        </a>
      </li>
    <?php endif; ?>

    <li class="menu-item">
      <a href="./auth/logout.php" class="menu-link">
        <i class="menu-icon tf-icons bx bx-log-out"></i>
        <div>Logout</div>
      </a>
    </li>
  </ul>
</aside>