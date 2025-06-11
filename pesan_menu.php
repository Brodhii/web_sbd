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

// Ambil menu dari database (ambil juga status_aktif)
$query = "SELECT menu_id, nama, harga, stok, status_aktif FROM menu"; // Tambahkan status_aktif
$stmt = $conn->prepare($query);
$stmt->execute();
$menus = $stmt->get_result();

// Cek stok menu yang dipilih (sekarang juga cek status_aktif)
$menu_id = isset($_GET['menu_id']) ? $_GET['menu_id'] : null;
$rowMenu = null; // Ganti nama variabel agar lebih jelas

if ($menu_id) {
    // Ambil detail menu, termasuk stok dan status_aktif
    $cekMenu = "SELECT nama, harga, stok, status_aktif FROM menu WHERE menu_id = ?"; // Tambahkan status_aktif
    $stmtCek = $conn->prepare($cekMenu);
    $stmtCek->bind_param("i", $menu_id);
    $stmtCek->execute();
    $resultMenu = $stmtCek->get_result();
    $rowMenu = $resultMenu->fetch_assoc();
}

// Proses pemesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_id = $_POST['menu_id'];
    $jumlah = $_POST['jumlah'];
    $user_id = $_SESSION['user_id'];
    $tanggal_pemesanan = date('Y-m-d');

    // Ambil ulang data menu untuk validasi terkini
    $cekMenu = "SELECT nama, stok, status_aktif FROM menu WHERE menu_id = ?"; // Ambil status_aktif
    $stmtCek = $conn->prepare($cekMenu);
    $stmtCek->bind_param("i", $menu_id);
    $stmtCek->execute();
    $resultMenu = $stmtCek->get_result();
    $current_menu_data = $resultMenu->fetch_assoc();

    if (!$current_menu_data) {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => 'Menu tidak ditemukan.'];
        header('Location: menu_pelanggan.php');
        exit;
    }

    $current_stock = $current_menu_data['stok'];
    $status_aktif = $current_menu_data['status_aktif'];

    // Validasi Ganda: Cek status_aktif dan juga cek kuantitas stok
    if ($status_aktif == 0) { // Jika admin menonaktifkan menu ini
        $_SESSION['notification'] = ['type' => 'danger', 'message' => 'Maaf, menu ini sedang tidak tersedia.'];
        header('Location: menu_pelanggan.php');
        exit;
    }

    if ($jumlah > $current_stock) { // Jika kuantitas yang dipesan melebihi stok yang ada
        $_SESSION['notification'] = ['type' => 'danger', 'message' => 'Maaf, jumlah pesanan melebihi stok yang tersedia! (Sisa stok: ' . $current_stock . ')'];
        header('Location: menu_pelanggan.php');
        exit;
    }

    // Jika semua validasi lolos:
    // Simpan pesanan ke database
    $query_insert_pesanan = "INSERT INTO pesanan (user_id, menu_id, jumlah, status, tanggal_pemesanan)
                             VALUES (?, ?, ?, 'pending', ?)";
    $stmt_insert_pesanan = $conn->prepare($query_insert_pesanan);
    $stmt_insert_pesanan->bind_param("iiis", $user_id, $menu_id, $jumlah, $tanggal_pemesanan);

    if ($stmt_insert_pesanan->execute()) {
        // Kurangi stok menu (kuantitas)
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
}

include('./.includes/header.php'); // Path ini mungkin perlu disesuaikan dengan struktur folder Anda
include('./.includes/toast_notification.php'); // Path ini mungkin perlu disesuaikan
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
            <?php
            // Pastikan Anda memuat ulang menu setelah proses POST jika ada perubahan stok yang memengaruhi tampilan
            $stmt_display = $conn->prepare("SELECT menu_id, nama, harga, stok, status_aktif FROM menu"); // Ambil juga status_aktif
            $stmt_display->execute();
            $menus_updated = $stmt_display->get_result();
            while ($row = $menus_updated->fetch_assoc()):
                $is_disabled = ($row['stok'] == 0 || $row['status_aktif'] == 0); // Nonaktif jika stok 0 ATAU status_aktif 0
            ?>
              <option value="<?= htmlspecialchars($row['menu_id']); ?>"
                      <?= ($menu_id == $row['menu_id']) ? 'selected' : ''; ?>
                      <?= $is_disabled ? 'disabled' : ''; ?>> <?= htmlspecialchars($row['nama']); ?> - Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                <?php if ($row['status_aktif'] == 0): ?>
                    (Tidak Tersedia)
                <?php elseif ($row['stok'] == 0): ?>
                    (Stok Habis)
                <?php endif; ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="jumlah" class="form-label">Jumlah</label>
          <input type="number" class="form-control" id="jumlah" name="jumlah" required min="1"
                 max="<?= isset($rowMenu['stok']) ? $rowMenu['stok'] : 1; ?>" value="1">
        </div>
        <button type="submit" class="btn btn-primary w-100">Pesan Menu</button>
      </form>
    </div>
  </div>
</div>

<?php include('./.includes/footer.php'); // Path ini mungkin perlu disesuaikan ?>