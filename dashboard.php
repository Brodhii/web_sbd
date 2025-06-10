<?php
require_once('.includes/init_session.php');
require_once("config.php");

// Pastikan pengguna sudah login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$role    = $_SESSION["role"];
$user_id = $_SESSION["user_id"];
$nama    = $_SESSION["nama"];

include("./.includes/header.php");

// Jika role admin, ambil data untuk kartu statistik secara otomatis
if ($role === "admin") {
    // Total revenue (asumsi: revenue = SUM(menu.harga * pesanan.jumlah))
    $query_revenue = "SELECT SUM(m.harga * p.jumlah) AS total_revenue 
                      FROM pesanan p 
                      JOIN menu m ON p.menu_id = m.menu_id";
    $result_revenue = mysqli_query($conn, $query_revenue);
    $data_revenue = mysqli_fetch_assoc($result_revenue);
    $total_revenue = $data_revenue['total_revenue'] ?? 0;

    // Total orders (jumlah pesanan)
    $query_orders = "SELECT COUNT(pesanan_id) AS total_orders FROM pesanan";
    $result_orders = mysqli_query($conn, $query_orders);
    $data_orders = mysqli_fetch_assoc($result_orders);
    $total_orders = $data_orders['total_orders'] ?? 0;

    // Total customers
    $query_customers = "SELECT COUNT(user_id) AS total_customers FROM users";
    $result_customers = mysqli_query($conn, $query_customers);
    $data_customers = mysqli_fetch_assoc($result_customers);
    $total_customers = $data_customers['total_customers'] ?? 0;
}
?>

<!-- Custom CSS untuk membuat tabel dengan hanya garis horizontal -->
<style>
.table-custom {
    border-collapse: collapse;
    width: 100%;
}
.table-custom thead th {
    border-bottom: 2px solid #dee2e6;
    padding: 0.75rem;
    vertical-align: middle;
    text-align: left;
}
.table-custom tbody td {
    border-bottom: 1px solid #dee2e6;
    padding: 0.75rem;
    vertical-align: middle;
}
.table-custom tbody tr:last-child td {
    border-bottom: none;
}
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Dashboard Pelanggan -->
    <?php if ($role === "pelanggan"): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h4>Riwayat Pesanan Terbaru</h4>
            </div>
            <div class="card-body">
                <?php
                // Ambil 5 pesanan terakhir milik pelanggan
                $query = "SELECT p.pesanan_id, m.nama AS menu, p.jumlah, p.status, p.tanggal_pemesanan 
                          FROM pesanan p 
                          JOIN menu m ON p.menu_id = m.menu_id 
                          WHERE p.user_id = ? 
                          ORDER BY p.tanggal_pemesanan DESC 
                          LIMIT 5";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                ?>
                <?php if ($result->num_rows > 0): ?>
                    <table class="table-custom table-striped">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Tanggal Pemesanan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row["menu"]); ?></td>
                                <td><?= $row["jumlah"]; ?></td>
                                <td>
                                    <?php
                                    switch ($row["status"]) {
                                        case 'pending':
                                            echo '<span class="badge bg-warning text-dark">Pending</span>';
                                            break;
                                        case 'proses':
                                            echo '<span class="badge bg-info text-white">Proses</span>';
                                            break;
                                        case 'selesai':
                                            echo '<span class="badge bg-success text-white">Selesai</span>';
                                            break;
                                        default:
                                            echo '<span class="badge bg-secondary text-white">Unknown</span>';
                                            break;
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($row["tanggal_pemesanan"]); ?></td>
                                <td>
                                    <a href="pesananpelanggan.php?pesanan_id=<?= $row["pesanan_id"]; ?>" class="btn btn-sm btn-info">Detail</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Belum ada pesanan yang dilakukan.</p>
                <?php endif; ?>
            </div>
        </div>
        <!-- Tombol menuju halaman pemesanan menu -->
        <div class="text-end">
            <a href="menu_pelanggan.php" class="btn btn-primary">
                <i class="bx bx-food-menu me-1"></i> Pesan Menu Sekarang
            </a>
        </div>
    <?php endif; ?>

    <!-- Dashboard Admin -->
    <?php if ($role === "admin"): ?>
        <!-- Kartu Statistik untuk Admin menggunakan data otomatis -->
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">Transaksi</span>
                        <h3 class="card-title mb-2">Rp <?= number_format($total_revenue, 0, ',', '.'); ?></h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> Auto</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">Pendapatan</span>
                        <h3 class="card-title mb-2">Rp <?= number_format($total_revenue, 0, ',', '.'); ?></h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> Auto</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">Pelanggan</span>
                        <h3 class="card-title mb-2"><?= $total_customers; ?></h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> Auto</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">Pesanan</span>
                        <h3 class="card-title mb-2"><?= $total_orders; ?></h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> Auto</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabel Ringkasan Pesanan untuk Admin (Summary) -->
        <?php
        // Query untuk mendapatkan ringkasan pesanan berdasarkan menu
        $query_admin = "
            SELECT m.menu_id, m.nama AS menu, 
                   COUNT(p.pesanan_id) AS total_orders, 
                   SUM(p.jumlah) AS total_quantity, 
                   SUM(m.harga * p.jumlah) AS total_revenue
            FROM pesanan p
            JOIN menu m ON p.menu_id = m.menu_id
            GROUP BY m.menu_id
        ";
        $result_admin = mysqli_query($conn, $query_admin);
        ?>
        <div class="card">
            <div class="card-header">
                <h4>Ringkasan Pesanan</h4>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result_admin) > 0): ?>
                    <table class="table-custom table-striped">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Total Orders</th>
                                <th>Total Quantity</th>
                                <th>Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result_admin)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row["menu"]); ?></td>
                                    <td><?= $row["total_orders"]; ?></td>
                                    <td><?= $row["total_quantity"]; ?></td>
                                    <td>Rp <?= number_format($row["total_revenue"], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Tidak ada pesanan.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Hanya Bootstrap JS (tanpa plugin tambahan) -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include("./.includes/footer.php"); ?>