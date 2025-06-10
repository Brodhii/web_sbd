<?php
require_once('config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$title = "Pemesanan Menu";

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['notification'] = ['type' => 'danger', 'message' => 'Silakan login terlebih dahulu!'];
    header('Location: ./auth/login.php');
    exit;
}

// Ambil menu dari database
$query = "SELECT * FROM menu";
$stmt = $conn->prepare($query);
$stmt->execute();
$menus = $stmt->get_result();

// Cek stok menu yang dipilih
$menu_id = isset($_GET['menu_id']) ? $_GET['menu_id'] : null;
$rowStok = null;

if ($menu_id) {
    $cekStok = "SELECT stok FROM menu WHERE menu_id = ?";
    $stmtCek = $conn->prepare($cekStok);
    $stmtCek->bind_param("i", $menu_id);
    $stmtCek->execute();
    $resultStok = $stmtCek->get_result();
    $rowStok = $resultStok->fetch_assoc();
}

// Proses pemesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_id = $_POST['menu_id'];
    $jumlah = $_POST['jumlah'];
    $user_id = $_SESSION['user_id'];
    $tanggal_pemesanan = date('Y-m-d');

    // Validasi jumlah pesanan tidak melebihi stok
    $cekStok = "SELECT stok FROM menu WHERE menu_id = ?";
    $stmtCek = $conn->prepare($cekStok);
    $stmtCek->bind_param("i", $menu_id);
    $stmtCek->execute();
    $resultStok = $stmtCek->get_result();
    $rowStok = $resultStok->fetch_assoc();

    if ($jumlah > $rowStok['stok']) {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => 'Maaf, jumlah pesanan melebihi stok yang tersedia!'];
        header('Location: menu_pelanggan.php');
        exit;
    }

    if ($rowStok['stok'] >= $jumlah) {
        // Simpan pesanan ke database
        $query = "INSERT INTO pesanan (user_id, menu_id, jumlah, status, tanggal_pemesanan)
                  VALUES (?, ?, ?, 'pending', ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiis", $user_id, $menu_id, $jumlah, $tanggal_pemesanan);

        if ($stmt->execute()) {
            // Kurangi stok menu
            $updateStok = "UPDATE menu SET stok = stok - ? WHERE menu_id = ?";
            $stmtStok = $conn->prepare($updateStok);
            $stmtStok->bind_param("ii", $jumlah, $menu_id);
            $stmtStok->execute();

            $_SESSION['notification'] = ['type' => 'success', 'message' => 'Pesanan berhasil ditambahkan!'];
            header('Location: riwayat_pesanan.php');
            exit;
        } else {
            $_SESSION['notification'] = ['type' => 'danger', 'message' => 'Gagal membuat pesanan!'];
        }
    } else {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => 'Maaf, stok tidak mencukupi!'];
        header('Location: menu_pelanggan.php');
        exit;
    }
}

include('./.includes/header.php');
include('./.includes/toast_notification.php');
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <h1 class="my-4">Pilih Menu untuk Dipesan</h1>

  <?php if (isset($_SESSION['notification'])): ?>
    <div class="alert alert-<?php echo $_SESSION['notification']['type']; ?> text-center" role="alert">
      <?php echo $_SESSION['notification']['message']; ?>
    </div>
    <?php unset($_SESSION['notification']); ?>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST">
        <div class="mb-3">
          <label for="menu_id" class="form-label">Menu</label>
          <select class="form-select" id="menu_id" name="menu_id" required>
            <?php while ($row = $menus->fetch_assoc()): ?>
              <option value="<?= htmlspecialchars($row['menu_id']); ?>" 
                      <?= ($menu_id == $row['menu_id']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($row['nama']); ?> - Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="jumlah" class="form-label">Jumlah</label>
          <input type="number" class="form-control" id="jumlah" name="jumlah" required min="1"
                 max="<?= isset($rowStok['stok']) ? $rowStok['stok'] : 1; ?>" value="1">
        </div>
        <button type="submit" class="btn btn-primary w-100">Pesan Menu</button>
      </form>
    </div>
  </div>
</div>

<?php include('./.includes/footer.php'); ?>