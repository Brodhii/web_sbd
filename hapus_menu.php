<?php
require_once('.includes/init_session.php');
require_once('config.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠️ ID menu tidak ditemukan!'];
    header('Location: menu.php');
    exit();
}

$id_menu = intval($_GET['id']);

try {
    $check_query = "SELECT COUNT(*) AS count FROM pesanan WHERE menu_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $id_menu);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $row = $check_result->fetch_assoc();

    if ($row['count'] > 0) {
        throw new Exception('yah,menunya gabisa dihapus nih karena terlibat dalam pemesanan!');
    }

    $query = "DELETE FROM menu WHERE menu_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_menu);

    if ($stmt->execute()) {
        $_SESSION['notification'] = ['type' => 'success', 'message' => 'yey, menu berhasil dihapus!'];
    } else {
        throw new Exception('⚠️ gagal menghapus menu! error: ' . $stmt->error);
    }
} catch (Exception $e) {
    $_SESSION['notification'] = ['type' => 'danger', 'message' => $e->getMessage()];
}

header("Location: menu.php");
exit();