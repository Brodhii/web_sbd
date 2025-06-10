<?php
require_once('.includes/init_session.php');
require_once('config.php');

$title = "Ubah Status Pesanan";

if (!isset($_GET['pesanan_id']) || empty($_GET['pesanan_id'])) {
    $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠️ ID Pesanan tidak ditemukan!'];
    header('Location: pesanan.php');
    exit();
}

$pesanan_id = intval($_GET['pesanan_id']); 

$query = "SELECT pesanan.*, menu.nama AS menu_name, users.nama AS user_name
          FROM pesanan
          JOIN menu ON pesanan.menu_id = menu.menu_id
          JOIN users ON pesanan.user_id = users.user_id
          WHERE pesanan.pesanan_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pesanan_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = trim($_POST['status']);

    try {
        $query_update = "UPDATE pesanan SET status = ? WHERE pesanan_id = ?";
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param("si", $status, $pesanan_id);

        if ($stmt_update->execute()) {
            $_SESSION['notification'] = ['type' => 'success', 'message' => '✅ Status pesanan berhasil diubah menjadi ' . htmlspecialchars($status) . '!'];
        } else {
            throw new Exception('⚠️ Gagal mengubah status pesanan!');
        }
    } catch (Exception $e) {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => $e->getMessage()];
    }
    header('Location: pesanan.php'); 
    exit();
}

include('.includes/header.php');
include('.includes/toast_notification.php');
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <h1 class="my-4">Ubah Status Pesanan</h1>

  <?php if (isset($_SESSION['notification'])): ?>
    <div class="alert text-center" style="background-color: #8B4513; color: white;">
      <?= $_SESSION['notification']['message']; ?>
    </div>
    <?php unset($_SESSION['notification']); ?>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="POST">
        <div class="mb-3">
          <label for="menu_name" class="form-label">Menu</label>
          <input type="text" class="form-control" id="menu_name" value="<?= htmlspecialchars($row['menu_name']); ?>" readonly>
        </div>
        <div class="mb-3">
          <label for="user_name" class="form-label">Pelanggan</label>
          <input type="text" class="form-control" id="user_name" value="<?= htmlspecialchars($row['user_name']); ?>" readonly>
        </div>
        <div class="mb-3">
          <label for="status" class="form-label">Status Pesanan</label>
          <select class="form-select" id="status" name="status" required>
            <option value="PENDING" <?= $row['status'] == 'PENDING' ? 'selected' : ''; ?>>Pending</option>
            <option value="PROSES" <?= $row['status'] == 'PROSES' ? 'selected' : ''; ?>>Proses</option>
            <option value="SELESAI" <?= $row['status'] == 'SELESAI' ? 'selected' : ''; ?>>Selesai</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Ubah Status</button>
      </form>
    </div>
  </div>
</div>

<?php include('.includes/footer.php'); ?>