<?php
session_start();
require_once('config.php');

// Keamanan: Pastikan yang login adalah pelanggan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$title = "Beranda";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Pelanggan';

// ---- LOGIKA BARU: Ambil 1 pesanan aktif yang paling baru ----
$active_order = null;
$query_active = "
    SELECT 
        p.id_pesanan,
        p.nomor_meja,
        p.status_pesanan,
        GROUP_CONCAT(CONCAT(m.nama, ' (', dp.jumlah, 'x)') SEPARATOR ', ') AS detail_menu
    FROM pesanan p
    JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    JOIN menu m ON dp.id_menu = m.menu_id
    WHERE p.user_id = ? AND TRIM(LOWER(p.status_pesanan)) IN ('diproses', 'pending')
    GROUP BY p.id_pesanan
    ORDER BY p.tanggal_pesanan DESC
    LIMIT 1";

$stmt_active = $conn->prepare($query_active);
$stmt_active->bind_param("i", $user_id);
$stmt_active->execute();
$result_active = $stmt_active->get_result();
if ($result_active->num_rows > 0) {
    $active_order = $result_active->fetch_assoc();
}
$stmt_active->close();
// ---- AKHIR LOGIKA BARU ----

?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Temu Bual Coffee &mdash; <?= htmlspecialchars($title); ?></title>
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.png" />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="assets/vendor/css/core.css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="assets/css/styles.css" />
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
                        
                        <h4 class="fw-bold py-3 mb-4">Selamat Datang, <?= htmlspecialchars($username) ?>!</h4>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Status Pesanan Terkini</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($active_order): ?>
                                    <h6 class="card-title">Pesanan Anda untuk Meja: <strong><?= htmlspecialchars($active_order['nomor_meja']) ?></strong></h6>
                                    <p>Status: <span class="badge bg-label-primary"><?= htmlspecialchars($active_order['status_pesanan']) ?></span></p>
                                    <p class="mb-2"><strong>Item:</strong></p>
                                    <p><?= str_replace(', ', '<br>', htmlspecialchars($active_order['detail_menu'])) ?></p>
                                    <a href="pesanan_aktif.php" class="btn btn-sm btn-outline-primary">Lihat Semua Pesanan Aktif</a>
                                <?php else: ?>
                                    <div class="text-center">
                                        <i class="bx bx-info-circle bx-lg text-primary mb-3"></i>
                                        <p>Anda tidak memiliki pesanan yang sedang diproses saat ini.</p>
                                        <a href="menu_pelanggan.php" class="btn btn-primary">Ayo Pesan Sekarang!</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3"><span class="avatar-initial rounded bg-label-success"><i class="bx bx-history"></i></span></div>
                                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0">Riwayat Pesanan</h6>
                                                    <small class="text-muted">Lihat semua pesanan yang telah selesai.</small>
                                                </div>
                                                <a href="riwayat_pesanan.php" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3"><span class="avatar-initial rounded bg-label-warning"><i class="bx bx-cart"></i></span></div>
                                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0">Keranjang Saya</h6>
                                                    <small class="text-muted">Lihat item di keranjang sebelum memesan.</small>
                                                </div>
                                                <a href="keranjang_lihat.php" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/js/menu.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>