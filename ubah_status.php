<?php
// Menggunakan init_session.php Anda jika ada, pastikan isinya hanya session_start()
require_once('.includes/init_session.php');
require_once('config.php');

$title = "Ubah Status Pesanan";

// Cek jika yang login adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth/login.php");
    exit();
}

// Ambil ID Pesanan, baik dari GET (saat pertama kali link diklik) atau POST (saat form disubmit)
$pesanan_id = 0;
if (isset($_GET['id_pesanan'])) {
    $pesanan_id = (int)$_GET['id_pesanan'];
} elseif (isset($_POST['id_pesanan'])) {
    $pesanan_id = (int)$_POST['id_pesanan'];
}

// Jika tidak ada ID Pesanan sama sekali, kembali ke halaman daftar pesanan
if ($pesanan_id === 0) {
    $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠️ ID Pesanan tidak valid!'];
    header('Location: pesanan.php');
    exit();
}

// ---- LOGIKA UNTUK MEMPROSES PERUBAHAN STATUS (METHOD POST) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_baru = trim($_POST['status']); // Menggunakan status dari Anda: Diproses, Selesai, Dibatalkan

    try {
        // Query UPDATE sekarang jauh lebih sederhana
        $query_update = "UPDATE pesanan SET status_pesanan = ? WHERE id_pesanan = ?";
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param("si", $status_baru, $pesanan_id);

        if ($stmt_update->execute()) {
            $_SESSION['notification'] = ['type' => 'success', 'message' => '✅ Status pesanan #' . $pesanan_id . ' berhasil diubah menjadi ' . htmlspecialchars($status_baru) . '!'];
        } else {
            throw new Exception('⚠️ Gagal mengubah status pesanan!');
        }
    } catch (Exception $e) {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => $e->getMessage()];
    }
    header('Location: pesanan.php');
    exit();
}
// ---- AKHIR DARI LOGIKA PROSES ----


// ---- LOGIKA UNTUK MENAMPILKAN DATA PESANAN DI FORM ----
// Query ini diubah untuk mengambil detail pesanan yang baru
$query_display = "
    SELECT 
        p.id_pesanan,
        p.nomor_meja,
        p.status_pesanan,
        u.username AS nama_pelanggan,
        GROUP_CONCAT(CONCAT(m.nama, ' (', dp.jumlah, 'x)') SEPARATOR ', ') AS detail_menu
    FROM 
        pesanan p
    LEFT JOIN 
        detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    LEFT JOIN 
        menu m ON dp.id_menu = m.menu_id
    LEFT JOIN
        users u ON p.user_id = u.user_id
    WHERE 
        p.id_pesanan = ?
    GROUP BY
        p.id_pesanan";

$stmt_display = $conn->prepare($query_display);
$stmt_display->bind_param("i", $pesanan_id);
$stmt_display->execute();
$result = $stmt_display->get_result();
$row = $result->fetch_assoc();

// Jika data pesanan dengan ID tersebut tidak ditemukan
if (!$row) {
    $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠️ Pesanan dengan ID ' . $pesanan_id . ' tidak ditemukan!'];
    header('Location: pesanan.php');
    exit();
}
// ---- AKHIR DARI LOGIKA MENAMPILKAN DATA ----


// Memanggil file header Anda
include('.includes/header.php');
include('.includes/toast_notification.php');
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <h1 class="my-4">Ubah Status Pesanan #<?= htmlspecialchars($row['id_pesanan']); ?></h1>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="ubah_status.php">
        <input type="hidden" name="id_pesanan" value="<?= htmlspecialchars($row['id_pesanan']); ?>">

        <div class="mb-3">
          <label class="form-label">Nomor Meja</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($row['nomor_meja']); ?>" readonly>
        </div>
        <div class="mb-3">
          <label class="form-label">Pelanggan</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($row['nama_pelanggan']); ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Detail Menu</label>
            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($row['detail_menu']); ?></textarea>
        </div>
        <div class="mb-3">
          <label for="status" class="form-label">Status Pesanan</label>
          <select class="form-select" id="status" name="status" required>
            <option value="Diproses" <?= $row['status_pesanan'] == 'Diproses' ? 'selected' : ''; ?>>Diproses</option>
            <option value="Selesai" <?= $row['status_pesanan'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
            <option value="Dibatalkan" <?= $row['status_pesanan'] == 'Dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
          </select>
        </div>
        <a href="pesanan.php" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </form>
    </div>
  </div>
</div>

<?php 
// Memanggil file footer Anda
include('.includes/footer.php'); 
?>