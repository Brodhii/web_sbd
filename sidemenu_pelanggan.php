<?php
// Mendapatkan nama file dari halaman yang sedang dibuka agar highlight menu berfungsi
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="beranda_pelanggan.php" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">Temu Bual</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        <li class="menu-item <?= ($current_page == 'beranda_pelanggan.php') ? 'active' : '' ?>">
            <a href="beranda_pelanggan.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Beranda">Beranda</div>
            </a>
        </li>
        <li class="menu-item <?= ($current_page == 'menu_pelanggan.php') ? 'active' : '' ?>">
            <a href="menu_pelanggan.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-coffee"></i>
                <div data-i18n="Pesan Menu">Pesan Menu</div>
            </a>
        </li>
        <li class="menu-item <?= ($current_page == 'keranjang_lihat.php') ? 'active' : '' ?>">
            <a href="keranjang_lihat.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cart"></i>
                <div data-i18n="Keranjang">Keranjang Saya</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase"><span class="menu-header-text">Akun</span></li>
        <li class="menu-item">
            <a href="auth/logout.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-log-out"></i>
                <div data-i18n="Logout">Logout</div>
            </a>
        </li>
    </ul>
</aside>