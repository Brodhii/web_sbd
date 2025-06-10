<?php
require_once('config.php');
include('./.includes/header.php');
$title = "Menu untuk Pelanggan";

$filterCat = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Pagination untuk daftar menu
$limit = 8; // Jumlah menu per halaman
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Query untuk mengambil kategori dari tabel menu
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
?>

<div class="container-fluid flex-grow-1 px-4 mt-3">
  <!-- Baris Filter Kategori -->
  <div class="row mb-4">
    <div class="col">
      <div class="btn-group" role="group" aria-label="Filter berdasarkan kategori">
        <a href="menu_pelanggan.php" class="btn btn-outline-primary <?= empty($filterCat) ? 'active' : '' ?>">All</a>
        <?php while ($cat = $cat_result->fetch_assoc()): ?>
          <a href="menu_pelanggan.php?kategori=<?= urlencode($cat['kategori']) ?>" 
             class="btn btn-outline-primary <?= ($filterCat == $cat['kategori']) ? 'active' : '' ?>">
            <?= $cat['kategori'] ?>
          </a>
        <?php endwhile; ?>
      </div>
    </div>
  </div>

  <!-- Grid Card Menu -->
  <div class="row g-4">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card shadow-md h-100 border" 
             style="border-radius: 15px; overflow: hidden; padding: 15px; text-align: center;">
          <img src="uploads/<?= $row['gambar']; ?>" class="card-img-top img-fluid" 
               alt="<?= $row['nama']; ?>" style="height: 200px; object-fit: cover; border-radius: 10px;">
          <div class="card-body">
            <h5 class="card-title"><?= $row['nama']; ?></h5>
            <p class="card-text text-muted small">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
            <p class="card-text <?= ($row['stok'] > 0) ? 'text-warning' : 'text-danger'; ?>">
                <?= ($row['stok'] > 0) ? '⭐ Stok: ' . $row['stok'] . ' tersedia' : '❌ Stok: Habis'; ?>
            </p>
            <a href="pesan_menu.php?menu_id=<?= $row['menu_id']; ?>" 
               class="btn btn-primary btn-sm <?= ($row['stok'] > 0) ? '' : 'disabled'; ?>">
                Pesan Sekarang
            </a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

  <!-- Pagination -->
  <div class="d-flex justify-content-center mt-4">
    <?php if ($page > 1): ?>
      <a href="menu_pelanggan.php?page=<?= $page - 1 ?>" class="btn btn-outline-primary">❮ Previous</a>
    <?php endif; ?>
    <?php if ($page < $total_pages): ?>
      <a href="menu_pelanggan.php?page=<?= $page + 1 ?>" class="btn btn-outline-primary ms-2">Next ❯</a>
    <?php endif; ?>
  </div>
</div>

<?php include('./.includes/footer.php'); ?>