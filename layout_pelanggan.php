<?php
// File ini adalah kerangka utama untuk semua halaman pelanggan.
// session_start() sudah dipanggil dari file pemanggil (misal: riwayat_pesanan.php).

// require_once('config.php') juga sudah dipanggil dari file pemanggil.
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Temu Bual Coffee &mdash; <?= isset($title) ? htmlspecialchars($title) : 'Selamat Datang'; ?></title>
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/styles.css" />
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/js/config.js"></script>
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            
            <?php include "sidemenu_pelanggan.php"; ?>

            <div class="layout-page">
                
                <?php include "navbar_pelanggan.php"; ?>

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <?php
                        // Ini adalah "lubang" tempat konten halaman akan dimuat.
                        // Variabel $content_page diambil dari file pemanggil (misal: riwayat_pesanan.php).
                        if (isset($content_page) && file_exists($content_page)) {
                            include($content_page);
                        } else {
                            echo "<div class='alert alert-danger'>Kesalahan: File konten tidak ditemukan.</div>";
                        }
                        ?>
                    </div>
                    <div class="content-backdrop fade"></div>
                </div>
                </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/js/menu.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>