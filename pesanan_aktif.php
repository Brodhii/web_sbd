<?php
session_start();
require_once('config.php');

// Cek apakah user sudah login dan merupakan pelanggan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$index = 1;

// Ambil semua pesanan user ini yang belum selesai (status 'Pending' atau 'Diproses')
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
    LEFT JOIN 
        detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    LEFT JOIN 
        menu m ON dp.id_menu = m.menu_id
    WHERE 
        p.user_id = ? AND (p.status_pesanan = 'Pending' OR p.status_pesanan = 'Diproses')
    GROUP BY 
        p.id_pesanan
    ORDER BY 
        p.tanggal_pesanan DESC
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

include('.includes/header.php');
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card shadow-sm">
    <div class="card-header bg-light text-dark">
      <h5 class="mb-0 fw-bold">Pesanan Aktif Saya</h5>
    </div>
    <div class="card-body">
      <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>No. Meja</th>
                <th>Detail Pesanan</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Waktu</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $index++; ?></td>
                  <td><?= htmlspecialchars($row['nomor_meja']); ?></td>
                  <td><?= $row['detail_menu']; ?></td>
                  <td>Rp <?= number_format($row['total_harga']); ?></td>
                  <td>
                    <span class="badge bg-info"><?= htmlspecialchars($row['status_pesanan']); ?></span>
                  </td>
                  <td><?= date('d M Y, H:i', strtotime($row['tanggal_pesanan'])); ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-center text-muted mb-0">Tidak ada pesanan aktif.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
$stmt->close();
$conn->close();
include('.includes/footer.php');
?>
