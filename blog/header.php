<?php
// session_start() harus sudah dipanggil di file utama sebelum include header ini
// Contoh: di menu_pelanggan.php

// Menghitung jumlah item di keranjang
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Temu Bual Coffee</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <link href="blog/assets/img/favicon.png" rel="icon">
  <link href="blog/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600;1,700&family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Cardo:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

  <link href="blog/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="blog/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="blog/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="blog/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="blog/assets/vendor/aos/aos.css" rel="stylesheet">

  <link href="blog/assets/css/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid d-flex align-items-center justify-content-between">

      <a href="index.php" class="logo d-flex align-items-center  me-auto me-lg-0">
        <i class="bi bi-camera"></i>
        <h1>Temu Bual</h1>
      </a>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="menu_pelanggan.php">Menu</a></li>
          <li><a href="riwayat_pesanan.php">Riwayat</a></li>
          </ul>
      </nav><div class="header-social-links d-flex align-items-center">
        
        <a href="keranjang_lihat.php" class="btn btn-outline-light position-relative ms-3">
            <i class="fas fa-shopping-cart"></i>
            <?php if ($cart_count > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7em;">
                    <?= $cart_count ?>
                </span>
            <?php endif; ?>
        </a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="auth/logout.php" class="btn btn-danger ms-3">Logout</a>
        <?php else: ?>
            <a href="auth/login.php" class="btn btn-success ms-3">Login</a>
        <?php endif; ?>

      </div>
      <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
      <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>

    </div>
  </header>```

### Penjelasan Perubahan

1.  **Logika Hitung Keranjang**: Kode PHP di bagian atas (`$cart_count = ...`) akan menghitung berapa jenis menu yang ada di dalam keranjang belanja (`$_SESSION['cart']`).
2.  **Tombol Keranjang Dinamis**: Saya telah menambahkan sebuah tombol keranjang di bagian kanan atas header.
    * `href="keranjang_lihat.php"`: Tombol ini akan mengarahkan pelanggan ke halaman keranjang.
    * **Badge Angka**: Jika ada item di dalam keranjang (`$cart_count > 0`), sebuah lingkaran merah kecil (badge) akan muncul di atas ikon keranjang, menunjukkan jumlah item yang ada. Jika keranjang kosong, badge ini tidak akan tampil.
3.  **Tombol Login/Logout**: Saya juga menambahkan logika sederhana untuk menampilkan tombol "Login" jika pengguna belum login, dan tombol "Logout" jika sudah login.
4.  **Ikon**: Saya menambahkan link ke Font Awesome untuk menampilkan ikon keranjang belanja (`<i class="fas fa-shopping-cart"></i>`).

### Langkah Selanjutnya

Pastikan semua file halaman pelanggan Anda (seperti `menu_pelanggan.php`, `riwayat_pesanan.php`, dll.) memanggil header ini dengan benar.

Contoh di `menu_pelanggan.php`:
```php
<?php
session_start(); // Pastikan session_start() ada di baris pertama
require_once('config.php');
include('blog/header.php'); // Pastikan path-nya benar
// ...sisa kode Anda...
?>