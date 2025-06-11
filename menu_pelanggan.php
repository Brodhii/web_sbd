<?php
session_start();
require_once('config.php');

// Keamanan: Pastikan yang login adalah pelanggan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: auth/login.php");
    exit();
}

// Logika untuk mengambil data menu
$title = "Pilih Menu";
$filterCat = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$cat_query = "SELECT DISTINCT kategori FROM menu ORDER BY kategori ASC";
$cat_stmt = $conn->prepare($cat_query);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();

$query = !empty($filterCat) ? "SELECT * FROM menu WHERE kategori = ? LIMIT ?, ?" : "SELECT * FROM menu LIMIT ?, ?";
$stmt = $conn->prepare($query);
if (!empty($filterCat)) {
    $stmt->bind_param("sii", $filterCat, $start, $limit);
} else {
    $stmt->bind_param("ii", $start, $limit);
}
$stmt->execute();
$result = $stmt->get_result();

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
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Temu Bual Coffee &mdash; <?= htmlspecialchars($title); ?></title>
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.png" />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/styles.css" />
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/js/config.js"></script>
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include "sidemenu_pelanggan.php"; // Panggil sidebar pelanggan ?>
            <div class="layout-page">
                <?php include "navbar_pelanggan.php"; // Panggil navbar pelanggan ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card mb-4">
                            <div class="card-header"><h5 class="mb-0">Pilih Kategori</h5></div>
                            <div class="card-body">
                                <div class="btn-group flex-wrap" role="group">
                                    <a href="menu_pelanggan.php" class="btn btn-outline-primary <?= empty($filterCat) ? 'active' : '' ?>">Semua</a>
                                    <?php while ($cat = $cat_result->fetch_assoc()): ?>
                                        <a href="menu_pelanggan.php?kategori=<?= urlencode($cat['kategori']) ?>"
                                           class="btn btn-outline-primary <?= ($filterCat == $cat['kategori']) ? 'active' : '' ?>">
                                            <?= htmlspecialchars($cat['kategori']) ?>
                                        </a>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <div class="col-sm-6 col-lg-3 mb-4">
                                    <div class="card h-100">
                                        <img class="card-img-top" src="uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama']); ?>" style="height: 200px; object-fit: cover;">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?= htmlspecialchars($row['nama']); ?></h5>
                                            <p class="card-text text-muted">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                                            <p class="card-text small <?= ($row['stok'] > 0) ? 'text-success' : 'text-danger'; ?>">
                                                <?= ($row['stok'] > 0) ? 'Stok: ' . $row['stok'] : 'Stok Habis'; ?>
                                            </p>
                                            <form action="keranjang_tambah.php" method="POST" class="mt-auto">
                                                <input type="hidden" name="menu_id" value="<?= $row['menu_id'] ?>">
                                                <div class="input-group">
                                                    <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?= $row['stok'] ?>" <?= ($row['stok'] <= 0) ? 'disabled' : '' ?>>
                                                    <button type="submit" class="btn btn-primary" <?= ($row['stok'] <= 0) ? 'disabled' : '' ?>>
                                                        <i class="bx bx-cart-add"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    <?php if ($page > 1): ?><li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&kategori=<?= urlencode($filterCat) ?>">Previous</a></li><?php endif; ?>
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?><li class="page-item <?= ($i == $page) ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&kategori=<?= urlencode($filterCat) ?>"><?= $i ?></a></li><?php endfor; ?>
                                    <?php if ($page < $total_pages): ?><li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&kategori=<?= urlencode($filterCat) ?>">Next</a></li><?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                        </div>
                    <footer class="content-footer footer bg-footer-theme">
                      <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                        <div class="mb-2 mb-md-0">
                            © <script>document.write(new Date().getFullYear());</script> Temu Bual Coffee
                        </div>
                      </div>
                    </footer>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/js/menu.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>