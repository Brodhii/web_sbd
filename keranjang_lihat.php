<?php
session_start();
require_once('config.php');

// Keamanan: Pastikan yang login adalah pelanggan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: auth/login.php");
    exit();
}

$title = "Keranjang Pesanan";
$cart_items_session = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$menu_details = [];
$total_harga_keseluruhan = 0;

if (!empty($cart_items_session)) {
    $menu_ids = array_keys($cart_items_session);
    $id_placeholders = implode(',', array_fill(0, count($menu_ids), '?'));
    $stmt = $conn->prepare("SELECT menu_id, nama, harga, gambar FROM menu WHERE menu_id IN ($id_placeholders)");
    $stmt->bind_param(str_repeat('i', count($menu_ids)), ...$menu_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $menu_details[$row['menu_id']] = $row;
    }
    $stmt->close();
}
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
            <?php include "sidemenu_pelanggan.php"; ?>
            <div class="layout-page">
                <?php include "navbar_pelanggan.php"; ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pesanan /</span> Keranjang Anda</h4>
                        <div class="card">
                            <h5 class="card-header">Daftar Item di Keranjang</h5>
                            <div class="card-body">
                                <?php if (!empty($cart_items_session) && !empty($menu_details)): ?>
                                    <form action="pesanan_proses.php" method="POST">
                                        <div class="table-responsive text-nowrap">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th>Aksi</th></tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($cart_items_session as $menu_id => $jumlah):
                                                        if (isset($menu_details[$menu_id])):
                                                            $menu = $menu_details[$menu_id];
                                                            $subtotal = $menu['harga'] * $jumlah;
                                                            $total_harga_keseluruhan += $subtotal;
                                                    ?>
                                                    <tr>
                                                        <td><div class="d-flex align-items-center"><img src="uploads/<?= htmlspecialchars($menu['gambar']) ?>" alt="" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;"> <strong><?= htmlspecialchars($menu['nama']); ?></strong></div></td>
                                                        <td>Rp <?= number_format($menu['harga']); ?></td>
                                                        <td><?= $jumlah; ?></td>
                                                        <td><strong>Rp <?= number_format($subtotal); ?></strong></td>
                                                        <td><a href="keranjang_hapus.php?id=<?= $menu_id; ?>" class="btn btn-icon btn-outline-danger" onclick="return confirm('Yakin ingin menghapus item ini?')"><i class="bx bx-trash"></i></a></td>
                                                    </tr>
                                                    <?php endif; endforeach; ?>
                                                </tbody>
                                                <tfoot class="table-border-bottom-0">
                                                    <tr><td colspan="3" class="text-end"><h4>Total Harga:</h4></td><td colspan="2"><h4>Rp <?= number_format($total_harga_keseluruhan); ?></h4></td></tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <hr class="my-4">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="nomor_meja" class="form-label"><h5>Nomor Meja</h5></label>
                                                <input type="text" name="nomor_meja" id="nomor_meja" class="form-control form-control-lg" placeholder="Contoh: Meja 5" required>
                                            </div>
                                            <div class="col-md-6 d-flex align-items-end justify-content-end mb-3">
                                                <div>
                                                    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg">Kembali</a>
                                                    <button type="submit" class="btn btn-primary btn-lg ms-2">Buat Pesanan</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form> <?php else: ?>
                                    <div class="alert alert-warning text-center">Keranjang Anda kosong. Silakan <a href="menu_pelanggan.php" class="alert-link">pilih menu</a>.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">© <script>document.write(new Date().getFullYear());</script> Temu Bual Coffee</div>
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