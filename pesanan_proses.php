<?php
// Koneksi database
$host = "localhost";
$user = "root";
$password = "";
$db = "temu_buall";
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari session
session_start();
$user_id = $_SESSION['user_id'];
$nomor_meja = $_POST['nomor_meja'];
$cart = $_SESSION['cart'];

if (empty($cart)) {
    // Jika keranjang kosong ➜ redirect ke halaman keranjang
    header("Location: keranjang_lihat.php");
    exit();
}

// 1️⃣ Insert ke tabel pesanan
$tanggal_pesanan = date("Y-m-d H:i:s");
$status_pesanan = "Diproses";

$stmt_pesanan = $conn->prepare("INSERT INTO pesanan (user_id, nomor_meja, tanggal_pesanan, status_pesanan) VALUES (?, ?, ?, ?)");
$stmt_pesanan->bind_param("isss", $user_id, $nomor_meja, $tanggal_pesanan, $status_pesanan);
$stmt_pesanan->execute();
$id_pesanan_baru = $stmt_pesanan->insert_id;
$stmt_pesanan->close();

// 2️⃣ Insert ke detail_pesanan & update stok menu
$stmt_detail = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, harga_saat_pesan) VALUES (?, ?, ?, ?)");
$stmt_update_stok = $conn->prepare("UPDATE menu SET stok = stok - ? WHERE menu_id = ?");

foreach ($cart as $id_menu => $jumlah) {
    // Ambil harga saat ini
    $stmt_harga = $conn->prepare("SELECT harga FROM menu WHERE menu_id = ?");
    $stmt_harga->bind_param("i", $id_menu);
    $stmt_harga->execute();
    $stmt_harga->bind_result($harga);
    $stmt_harga->fetch();
    $stmt_harga->close();

    // Insert detail pesanan
    $stmt_detail->bind_param("iiid", $id_pesanan_baru, $id_menu, $jumlah, $harga);
    $stmt_detail->execute();

    // Update stok menu
    $stmt_update_stok->bind_param("ii", $jumlah, $id_menu);
    $stmt_update_stok->execute();
}

$stmt_detail->close();
$stmt_update_stok->close();

// Bersihkan keranjang
unset($_SESSION['cart']);

// Redirect ke halaman pesanan saya
header("Location: beranda_pelanggan.php");
exit();
?>
