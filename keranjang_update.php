<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['cart'])) {
    foreach ($_POST['quantities'] as $menu_id => $quantity) {
        $quantity = (int)$quantity;
        if (isset($_SESSION['cart'][$menu_id])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$menu_id]['quantity'] = $quantity;
            } else {
                // Hapus item jika jumlah 0 atau kurang
                unset($_SESSION['cart'][$menu_id]);
            }
        }
    }
}

header("Location: keranjang_lihat.php");
exit();