<?php
session_start();
require_once('config.php');
include('.includes/toast_notification.php'); // Menampilkan notifikasi toast

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Cek apakah user terlibat dalam pesanan aktif
    $query_check = "SELECT COUNT(*) AS total FROM pesanan WHERE user_id = ?";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();

    if ($row_check['total'] > 0) {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠️ Pengguna ini masih memiliki pesanan aktif! Tidak dapat dihapus.'];
    } else {
        $query_delete = "DELETE FROM users WHERE user_id = ?";
        $stmt_delete = $conn->prepare($query_delete);
        $stmt_delete->bind_param("i", $id);
        if ($stmt_delete->execute()) {
            $_SESSION['notification'] = ['type' => 'success', 'message' => '✅ User berhasil dihapus!'];
        } else {
            $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠️ Gagal menghapus user!'];
        }
    }

    header("Location: users.php");
    exit();
} else {
    $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠️ User ID tidak ditemukan!'];
    header("Location: users.php");
    exit();
}
?>