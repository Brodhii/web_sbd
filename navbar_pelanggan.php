<?php
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Pelanggan';
?>
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>
    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <li class="nav-item me-3">
                <a class="nav-link" href="keranjang_lihat.php" title="Lihat Keranjang">
                    <i class="bx bx-cart bx-sm"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="badge bg-danger rounded-pill badge-notifications"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online"><img src="assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" /></div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block"><?= htmlspecialchars($username); ?></span>
                                    <small class="text-muted">Pelanggan</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <li><a class="dropdown-item" href="auth/logout.php"><i class="bx bx-power-off me-2"></i><span class="align-middle">Log Out</span></a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>