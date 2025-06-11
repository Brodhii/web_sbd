<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_id'])) {
    $menu_id = (int)$_POST['menu_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($quantity > 0) {
        // Inisialisasi keranjang jika belum ada
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Jika item sudah ada di keranjang, tambahkan jumlahnya. Jika belum, tambahkan item baru.
        if (isset($_SESSION['cart'][$menu_id])) {
            $_SESSION['cart'][$menu_id] += $quantity;
        } else {
            $_SESSION['cart'][$menu_id] = $quantity;
        }
    }
}

// Setelah selesai, langsung kembali ke halaman menu
header("Location: menu_pelanggan.php");
exit();
?>