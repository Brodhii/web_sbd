<?php
session_start();
ob_start();
require_once('config.php');
include('.includes/header.php');
$title = "Manajemen Menu";

// Tampilkan notifikasi jika ada
if (isset($_SESSION['notification'])) {
    echo '<div class="alert alert-' . $_SESSION['notification']['type'] . ' text-center">'
         . $_SESSION['notification']['message'] . '</div>';
    unset($_SESSION['notification']);
}

include('./.includes/toast_notification.php');

// Ambil filter kategori dari URL (jika ada)
$filterCat = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Pagination untuk daftar menu
$limit = 7; // Jumlah menu per halaman
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Query untuk mengambil kategori unik dari tabel menu
$cat_query = "SELECT DISTINCT kategori FROM menu";
$cat_stmt = $conn->prepare($cat_query);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();

// Query untuk mengambil menu berdasarkan filter & pagination
$query = !empty($filterCat) ? "SELECT * FROM menu WHERE kategori = ? LIMIT $start, $limit" : "SELECT * FROM menu LIMIT $start, $limit";
$stmt = $conn->prepare($query);
if (!empty($filterCat)) {
    $stmt->bind_param("s", $filterCat);
}
$stmt->execute();
$result = $stmt->get_result();

// Hitung total menu untuk pagination
$total_query = !empty($filterCat) ? "SELECT COUNT(*) FROM menu WHERE kategori = ?" : "SELECT COUNT(*) FROM menu";
$total_stmt = $conn->prepare($total_query);
if (!empty($filterCat)) {
    $total_stmt->bind_param("s", $filterCat);
}
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_menu = $total_result->fetch_array()[0];
$total_pages = ceil($total_menu / $limit);

// Proses update stok jika ada input dari admin
if (isset($_POST['update_stok'])) {
    $menu_id = $_POST['menu_id'];
    $stok_baru = $_POST['stok'];

    $query = "UPDATE menu SET stok = ? WHERE menu_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $stok_baru, $menu_id);

    if ($stmt->execute()) {
        $_SESSION['notification'] = ['type' => 'success', 'message' => 'Stok berhasil diperbarui!'];
        ob_end_clean();
        header("Location: menu.php?page=" . $page);
        exit;
    } else {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => 'Gagal memperbarui stok.'];
    }
}
?>

<div class="container-fluid flex-grow-1 px-4 mt-3">
  <!-- Baris Filter Kategori -->
  <div class="row mb-4">
    <div class="col">
      <div class="btn-group" role="group" aria-label="Filter berdasarkan kategori">
        <a href="menu.php" class="btn btn-outline-primary <?= empty($filterCat) ? 'active' : '' ?>">All</a>
        <?php while ($cat = $cat_result->fetch_assoc()): ?>
          <a href="menu.php?kategori=<?= urlencode($cat['kategori']) ?>" 
             class="btn btn-outline-primary <?= ($filterCat == $cat['kategori']) ? 'active' : '' ?>">
            <?= $cat['kategori'] ?>
          </a>
        <?php endwhile; ?>
      </div>
    </div>
  </div>

  <!-- Grid Card Menu -->
  <div class="row g-4">
    <!-- Card Tambah Menu -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <a href="tambah_menu.php" style="text-decoration: none; color: inherit;">
        <div class="card shadow-md h-100 d-flex justify-content-center align-items-center border" 
             style="border-radius: 15px; overflow: hidden;">
          <div class="d-flex flex-column align-items-center justify-content-center" style="height:200px;">
            <i class="bx bx-plus" style="font-size: 2rem;"></i>
            <h5 class="mt-2 card-tambah-menu">Tambah Menu</h5>
          </div>
        </div>
      </a>
    </div>

    <?php while ($menu = $result->fetch_assoc()): ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card shadow-md h-100 border" 
             style="border-radius: 15px; overflow: hidden; padding: 15px; text-align: center; position: relative;">
          
          <!-- Titik tiga dropdown CRUD di atas -->
          <div class="position-absolute top-0 end-0 p-2">
            <button class="btn btn-sm btn-icon py-0" data-bs-toggle="dropdown">
              <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="edit_menu.php?id=<?= $menu['menu_id']; ?>">
                <i class="bx bx-edit-alt me-2"></i> Edit
              </a></li>
              <li><a class="dropdown-item text-danger" href="hapus_menu.php?id=<?= $menu['menu_id']; ?>">
                <i class="bx bx-trash me-2"></i> Hapus
              </a></li>
            </ul>
          </div>

          <img src="uploads/<?= $menu['gambar']; ?>" class="card-img-top img-fluid" 
               alt="<?= $menu['nama']; ?>" style="height: 220px; object-fit: cover; border-radius: 10px;">
          
          <div class="card-body">
            <h5 class="card-title"><?= $menu['nama']; ?></h5>
            <p class="card-text text-muted small"><?= $menu['kategori']; ?></p>
            <p class="card-text text-muted small">Rp <?= number_format($menu['harga'], 0, ',', '.'); ?></p>
            <p class="card-text <?= ($menu['stok'] > 0) ? 'text-warning' : 'text-danger'; ?>">
                <?= ($menu['stok'] > 0) ? '⭐ Stok: ' . $menu['stok'] . ' tersedia' : '❌ Stok: Habis'; ?>
            </p>

            <!-- Form update stok -->
            <form method="POST" class="text-center">
                <input type="hidden" name="menu_id" value="<?= $menu['menu_id']; ?>">
                <input type="number" name="stok" value="<?= $menu['stok']; ?>" min="0" class="form-control text-center mb-2">
                <button type="submit" name="update_stok" class="btn btn-primary btn-custom w-100">
                  <i class="bx bx-refresh me-2"></i> Update Stok
                </button>
            </form>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

  <!-- Pagination -->
  <div class="d-flex justify-content-center mt-4">
    <?php if ($page > 1): ?>
      <a href="menu.php?page=<?= $page - 1 ?>" class="btn btn-outline-primary">❮ Previous</a>
    <?php endif; ?>
    <?php if ($page < $total_pages): ?>
      <a href="menu.php?page=<?= $page + 1 ?>" class="btn btn-outline-primary ms-2">Next ❯</a>
    <?php endif; ?>
  </div>
</div>

<?php include('.includes/footer.php'); ?>