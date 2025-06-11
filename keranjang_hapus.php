<?php
session_start();

if (isset($_GET['id'])) {
    $menu_id_to_delete = (int)$_GET['id'];

    if (isset($_SESSION['cart']) && isset($_SESSION['cart'][$menu_id_to_delete])) {
        unset($_SESSION['cart'][$menu_id_to_delete]);
    }
}

header("Location: keranjang_lihat.php");
exit();
?>
