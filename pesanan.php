<?php
session_start(); 
require_once('config.php');
include('.includes/header.php');
$title = "Daftar Pesanan";

if (isset($_SESSION['notification'])) {
    echo '<div class="alert text-center" style="background-color: #8B4513; color: white;">' . $_SESSION['notification']['message'] . '</div>';
    unset($_SESSION['notification']); 
}

include('./.includes/toast_notification.php');

$selected_status = isset($_GET['status']) ? $_GET['status'] : "";
$index = 1;
$query = "SELECT pesanan.pesanan_id, pesanan.jumlah, pesanan.status, pesanan.tanggal_pemesanan, menu.nama AS menu_name, users.nama AS user_name
          FROM pesanan
          JOIN menu ON pesanan.menu_id = menu.menu_id
          JOIN users ON pesanan.user_id = users.user_id";

if (!empty($selected_status)) {
    $query .= " WHERE pesanan.status = ?";
}

$stmt = $conn->prepare($query);

if (!empty($selected_status)) {
    $stmt->bind_param("s", $selected_status);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <h1 class="my-4">Daftar Pesanan</h1>

  <form method="GET" class="mb-3">
    <label for="filter-status" class="form-label">Filter Status:</label>
    <select name="status" id="filter-status" class="form-select" onchange="this.form.submit()">
      <option value="">Semua</option>
      <option value="PENDING" <?= $selected_status == "PENDING" ? "selected" : ""; ?>>Pending</option>
      <option value="PROSES" <?= $selected_status == "PROSES" ? "selected" : ""; ?>>Proses</option>
      <option value="SELESAI" <?= $selected_status == "SELESAI" ? "selected" : ""; ?>>Selesai</option>
    </select>
  </form>

  <div class="card">
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Pelanggan</th>
            <th>Menu</th>
            <th>Jumlah</th>
            <th>Status</th>
            <th>Tanggal Pemesanan</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
             <td><?= $index++;?></td>
              <td><?= $row['user_name']; ?></td>
              <td><?= $row['menu_name']; ?></td>
              <td><?= $row['jumlah']; ?></td>
              <td><?= ucfirst($row['status']); ?></td>
              <td><?= $row['tanggal_pemesanan']; ?></td>
              <td>
                <a href="ubah_status.php?pesanan_id=<?= $row['pesanan_id']; ?>" class="btn btn-primary">Ubah Status</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include('.includes/footer.php'); ?>