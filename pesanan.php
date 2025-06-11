<?php
session_start(); // Pastikan session dimulai
require_once('config.php');

// Cek jika yang login adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth/login.php");
    exit();
}

// Menggunakan file header dari folder .includes Anda
// Pastikan di dalam .includes/header.php TIDAK ADA session_start() lagi
include('.includes/header.php');
include('.includes/toast_notification.php');

$title = "Daftar Pesanan Masuk";
$index = 1;

// Logika untuk filter status pesanan
$selected_status = isset($_GET['status']) ? $_GET['status'] : "Diproses"; // Default menampilkan yang 'Diproses'

// ===================================================================
// QUERY BARU UNTUK ADMIN - MENAMPILKAN SEMUA PESANAN
// ===================================================================
$query = "
    SELECT 
        p.id_pesanan,
        p.nomor_meja,
        p.tanggal_pesanan,
        p.status_pesanan,
        u.username AS nama_pelanggan,
        SUM(dp.jumlah * dp.harga_saat_pesan) AS total_harga,
        GROUP_CONCAT(CONCAT(m.nama, ' (', dp.jumlah, 'x)') SEPARATOR '<br>') AS detail_menu
    FROM 
        pesanan p
    LEFT JOIN 
        detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    LEFT JOIN 
        menu m ON dp.id_menu = m.menu_id
    LEFT JOIN
        users u ON p.user_id = u.user_id
";

// Tambahkan filter status jika dipilih
if (!empty($selected_status)) {
    $query .= " WHERE p.status_pesanan = ?";
}

$query .= " GROUP BY p.id_pesanan ORDER BY p.tanggal_pesanan DESC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

// Bind parameter untuk filter status
if (!empty($selected_status)) {
    $stmt->bind_param("s", $selected_status);
}

$stmt->execute();
$pesanan = $stmt->get_result();
?>

<style>
  .container-xxl.container-p-y {
    padding-top: 1rem !important;
    padding-bottom: 1rem !important;
  }
  .card .card-header {
    padding: 0.75rem 1rem;
  }
  .card .card-body {
    padding: 0.75rem;
  }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card shadow-sm">
    <div class="card-header bg-light text-dark d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold">Daftar Pesanan</h5>
      
      <form method="GET" class="d-flex align-items-center">
        <label for="filter-status" class="form-label me-2 mb-0">Status:</label>
        <select name="status" id="filter-status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">Semua</option>
            <option value="Diproses" <?= $selected_status == "Diproses" ? "selected" : ""; ?>>Diproses</option>
            <option value="Selesai" <?= $selected_status == "Selesai" ? "selected" : ""; ?>>Selesai</option>
        </select>
      </form>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          
          <thead>
            <tr>
              <th>#</th>
              <th>No. Meja</th>
              <th>Pemesan</th>
              <th>Detail Pesanan</th>
              <th>Total Harga</th>
              <th>Status</th>
              <th>Waktu</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($pesanan->num_rows > 0): ?>
                <?php while ($row = $pesanan->fetch_assoc()): ?>
                <tr>
                  <td><?= $index++;?></td>
                  <td><strong><?= htmlspecialchars($row['nomor_meja']); ?></strong></td>
                  <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                  <td><?= $row['detail_menu']; ?></td>
                  <td>Rp <?= number_format($row['total_harga']); ?></td>
                  <td><span class="badge bg-primary"><?= htmlspecialchars($row['status_pesanan']); ?></span></td>
                  <td><?= date('d M Y, H:i', strtotime($row['tanggal_pesanan'])); ?></td>
                  <td>
                    <a href="ubah_status.php?id_pesanan=<?= $row['id_pesanan']; ?>" class="btn btn-sm btn-info">Ubah Status</a>
                  </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="text-center text-muted">Tidak ada pesanan dengan status yang dipilih.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php
$stmt->close();
$conn->close();
// Memanggil footer Anda dari folder .includes
include('.includes/footer.php');
?>