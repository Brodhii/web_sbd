<?php
session_start();
require_once('config.php');

// Pengecekan jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php"); // Arahkan ke halaman login
    exit();
}

// Menggunakan file header yang benar untuk pelanggan
include('.includes/header.php'); 
$title = "Pesanan Saya";
$user_id = $_SESSION['user_id']; // Mengambil ID user yang sedang login
$index = 1;

// ===================================================================
// QUERY BARU SESUAI STRUKTUR DATABASE BARU
// ===================================================================
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
        p.user_id = ?  -- Filter berdasarkan user yang login
    GROUP BY 
        p.id_pesanan 
    ORDER BY 
        p.tanggal_pesanan DESC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<main id="main">
<section class="container" style="padding-top: 120px;">
    <h2 class="text-center mb-4"><?= $title ?></h2>
    <div class="card shadow-sm">
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
                            <th>Waktu Pesan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $index++; ?></td>
                                    <td><strong><?= htmlspecialchars($row['nomor_meja']); ?></strong></td>
                                    <td><?= $row['detail_menu']; ?></td>
                                    <td>Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?= htmlspecialchars($row['status_pesanan']); ?></span>
                                    </td>
                                    <td><?= date('d M Y, H:i', strtotime($row['tanggal_pesanan'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Anda belum memiliki pesanan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
</main>

<?php
$stmt->close();
$conn->close();
include('.includes/footer.php'); // Menggunakan file footer yang benar
?>



