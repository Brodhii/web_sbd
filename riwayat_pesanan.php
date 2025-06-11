<?php
session_start();
require_once('config.php');

// Keamanan: Pastikan yang login adalah pelanggan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: auth/login.php");
    exit();
}

// Variabel untuk halaman
$user_id = $_SESSION['user_id'];
$title = "Riwayat Pesanan Saya";
$index = 1;

// Query untuk mengambil riwayat pesanan yang sudah 'Selesai'
$query = "
    SELECT 
        p.id_pesanan,
        p.nomor_meja,
        p.tanggal_pesanan,
        p.status_pesanan,
        SUM(dp.jumlah * dp.harga_saat_pesan) AS total_harga,
        GROUP_CONCAT(CONCAT(m.nama, ' (', dp.jumlah, 'x)') SEPARATOR '<br>') AS detail_menu
    FROM 
        pesanan p
    JOIN 
        detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    JOIN 
        menu m ON dp.id_menu = m.menu_id
    WHERE 
        p.user_id = ? AND TRIM(LOWER(p.status_pesanan)) = 'selesai'
    GROUP BY 
        p.id_pesanan 
    ORDER BY 
        p.tanggal_pesanan DESC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query Gagal: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pesanan_history = $stmt->get_result();
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
                        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akun Saya /</span> Riwayat Pesanan</h4>
                        <div class="card shadow-sm">
                            <div class="card-header bg-light text-dark">
                                <h5 class="mb-0 fw-bold">Pesanan Selesai</h5>
                            </div>
                            <div class="card-body">
                                      <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nomor Meja</th>
                                                <th>Detail Pesanan</th>
                                                <th>Total Harga</th>
                                                <th>Status</th>
                                                <th>Tanggal Selesai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($pesanan_history->num_rows > 0): ?>
                                                <?php while ($row = $pesanan_history->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $index++;?></td>
                                                    <td><strong><?= htmlspecialchars($row['nomor_meja']); ?></strong></td>
                                                    <td><?= $row['detail_menu']; ?></td>
                                                    <td>Rp <?= number_format($row['total_harga']); ?></td>
                                                    <td><span class="badge bg-success"><?= htmlspecialchars($row['status_pesanan']); ?></span></td>
                                                    <td><?= date('d M Y, H:i', strtotime($row['tanggal_pesanan'])); ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Anda belum memiliki riwayat pesanan yang selesai.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
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