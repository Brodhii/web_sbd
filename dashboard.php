<?php
// 1. Mulai session dan panggil konfigurasi. Ini harus selalu di paling atas.
session_start();
require_once('config.php');

// 2. Lakukan semua pengecekan dan redirect SEBELUM ada output HTML.
// Cek jika pengguna belum login, langsung redirect.
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}

// Ambil data dari session.
$role    = $_SESSION["role"];
$user_id = $_SESSION["user_id"];
$nama    = isset($_SESSION["username"]) ? $_SESSION["username"] : 'Admin';

// Cek role. Jika pelanggan, langsung redirect ke beranda mereka.
if ($role === "pelanggan") {
    header("Location: beranda_pelanggan.php");
    exit();
}

// Jika kode berlanjut sampai sini, berarti yang login adalah ADMIN.
// Sekarang kita siapkan data khusus untuk dashboard admin.
$title = "Dashboard Admin";

// Total Pendapatan
$query_revenue = "SELECT SUM(harga_saat_pesan * jumlah) AS total_revenue FROM detail_pesanan";
$result_revenue = mysqli_query($conn, $query_revenue);
$total_revenue = mysqli_fetch_assoc($result_revenue)['total_revenue'] ?? 0;

// Total Pesanan
$query_orders = "SELECT COUNT(id_pesanan) AS total_orders FROM pesanan";
$result_orders = mysqli_query($conn, $query_orders);
$total_orders = mysqli_fetch_assoc($result_orders)['total_orders'] ?? 0;

// Total Pelanggan
$query_customers = "SELECT COUNT(user_id) AS total_customers FROM users WHERE role = 'pelanggan'";
$result_customers = mysqli_query($conn, $query_customers);
$total_customers = mysqli_fetch_assoc($result_customers)['total_customers'] ?? 0;

// Query untuk Ringkasan Penjualan per Menu
$query_admin_summary = "
    SELECT m.nama AS menu, 
           SUM(dp.jumlah) AS total_quantity_terjual,
           SUM(dp.harga_saat_pesan * dp.jumlah) AS total_pendapatan_per_menu
    FROM detail_pesanan dp
    JOIN menu m ON dp.id_menu = m.menu_id
    GROUP BY m.menu_id
    ORDER BY total_pendapatan_per_menu DESC
";
$result_admin = mysqli_query($conn, $query_admin_summary);


// 3. Setelah SEMUA logika selesai, baru kita mulai output HTML dengan memanggil header.
// Pastikan path ke file header sudah benar.
// Jika file Anda bernama 'header.php' dan ada di folder '.includes', path-nya adalah '.includes/header.php'
include('.includes/header.php'); 
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Selamat Datang, Admin <?= htmlspecialchars($nama); ?>!</h4>
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
            <div class="card"><div class="card-body text-center"><span class="fw-semibold d-block mb-1">Total Pendapatan</span><h3 class="card-title mb-2">Rp <?= number_format($total_revenue); ?></h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
            <div class="card"><div class="card-body text-center"><span class="fw-semibold d-block mb-1">Total Pesanan</span><h3 class="card-title mb-2"><?= $total_orders; ?></h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
            <div class="card"><div class="card-body text-center"><span class="fw-semibold d-block mb-1">Total Pelanggan</span><h3 class="card-title mb-2"><?= $total_customers; ?></h3></div></div>
        </div>
    </div>
    
    <div class="card">
        <h5 class="card-header">Ringkasan Penjualan per Menu</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Jumlah Terjual</th>
                        <th>Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php if ($result_admin && mysqli_num_rows($result_admin) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_admin)): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row["menu"]); ?></strong></td>
                                <td><?= $row["total_quantity_terjual"]; ?></td>
                                <td>Rp <?= number_format($row["total_pendapatan_per_menu"]); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center">Belum ada penjualan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
// Memanggil footer Anda
// Pastikan path ke file footer sudah benar.
include("./.includes/footer.php"); 
?>