<?php
require_once('.includes/init_session.php');
require_once('config.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠️ ID menu tidak ditemukan!'];
    header('Location: menu.php');
    exit();
}

$ID = intval($_GET['id']);

// Ambil data menu berdasarkan ID
$query = "SELECT * FROM menu WHERE menu_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $ID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠️ Menu tidak ditemukan!'];
    header('Location: menu.php');
    exit();
}

$r = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $nama     = trim($_POST['nama']);
    $harga    = trim($_POST['harga']);
    $kategori = trim($_POST['kategori']);

    $query_update = "UPDATE menu SET nama=?, harga=?, kategori=? WHERE menu_id=?";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bind_param("sssi", $nama, $harga, $kategori, $ID);
    $stmt_update->execute();

    $_SESSION['notification'] = ['type' => 'success', 'message' => '✅ Menu berhasil diperbarui!'];
    header("Location: menu.php");
    exit();
}

include('./.includes/header.php');
include('./.includes/toast_notification.php'); 
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <h1 class="my-4">Edit Menu</h1>

  <?php if (isset($_SESSION['notification'])): ?>
    <div class="alert alert-<?= $_SESSION['notification']['type']; ?> text-center" role="alert">
      <?= $_SESSION['notification']['message']; ?>
    </div>
    <?php unset($_SESSION['notification']); ?>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="nama" class="form-label">Nama Menu</label>
          <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($r['nama']); ?>" required>
        </div>
        <div class="mb-3">
          <label for="harga" class="form-label">Harga</label>
          <input type="number" class="form-control" id="harga" name="harga" value="<?= htmlspecialchars($r['harga']); ?>" required>
        </div>
        <div class="mb-3">
        <label for="nama" class="form-label">Kategori</label>
                    <select name="kategori" class="form-select w-100" id="kategori" required>
                        <option value="Makanan" <?= ($r['kategori'] == 'Makanan') ? 'selected' : ''; ?>>Makanan</option>
                        <option value="Minuman" <?= ($r['kategori'] == 'Minuman') ? 'selected' : ''; ?>>Minuman</option>
                        <option value="Snack" <?= ($r['kategori'] == 'Snack') ? 'selected' : ''; ?>>Snack</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary w-100">Perbarui Menu</button>
        </form>
    </div>
  </div>
</div>

<?php include('./.includes/footer.php'); ?>