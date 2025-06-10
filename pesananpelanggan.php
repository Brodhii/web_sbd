<?php
require_once('config.php');
include('.includes/header.php');
include('.includes/toast_notification.php');

$title = "Pesanan Aktif";
$user_id = $_SESSION['user_id'];

$index = 1;
$query = "SELECT pesanan.pesanan_id, pesanan.jumlah, pesanan.status, pesanan.tanggal_pemesanan, menu.nama AS menu_name
          FROM pesanan
          JOIN menu ON pesanan.menu_id = menu.menu_id
          WHERE pesanan.user_id = ? AND pesanan.status IN ('pending', 'proses')
          ORDER BY pesanan.tanggal_pemesanan DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pesanan = $stmt->get_result();
?>

<!-- CSS kustom untuk mengurangi ruang antar elemen -->
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
  .d-flex.justify-content-between.align-items-center.mb-2 {
    margin-bottom: 1rem !important;
  }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card shadow-sm">
    <!-- Card header dengan styling light netral -->
    <div class="card-header bg-light text-dark">
      <h5 class="mb-0 fw-bold">Pesanan Aktif</h5>
    </div>
    <div class="card-body">
      <?php if ($pesanan->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
            <th>#</th>
              <th>Menu</th>
              <th>Jumlah</th>
              <th>Status</th>
              <th>Tanggal Pemesanan</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $pesanan->fetch_assoc()): ?>
            <tr>
              <td><?= $index++;?></td>
              <td><?= htmlspecialchars($row['menu_name']); ?></td>
              <td><?= htmlspecialchars($row['jumlah']); ?></td>
              <td><?= ucfirst(htmlspecialchars($row['status'])); ?></td>
              <td><?= htmlspecialchars($row['tanggal_pemesanan']); ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <p class="text-center text-muted">Tidak ada pesanan aktif.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
$stmt->close();
$conn->close();
include('.includes/footer.php');
?>