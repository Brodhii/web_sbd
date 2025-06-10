<?php
session_start();
require_once('config.php');

// Proses update jika form dikirim
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil data dari form
    $nama = trim($_POST['nama']);
    $kontak = trim($_POST['kontak']);
    $username = trim($_POST['username']);
    $role = trim($_POST['role']);

    // Query untuk memperbarui data pengguna di database
    $query = "UPDATE users SET nama=?, kontak=?, username=?, role=? WHERE user_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $nama, $kontak, $username, $role, $id);
    $stmt->execute();

    // Jika update berhasil, set notifikasi dan redirect
    if ($stmt->affected_rows > 0) {
        $_SESSION['notification'] = ['type' => 'success', 'message' => '✅ User berhasil diperbarui!'];
    } else {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠ Tidak ada perubahan data yang dilakukan!'];
    }

    $stmt->close();
    $conn->close();
    header("Location: users.php");
    exit();
}

// Pastikan ID pengguna tersedia dan valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠ ID pengguna tidak valid!'];
    header("Location: users.php");
    exit();
}

$id = intval($_GET['id']);
$query = "SELECT * FROM users WHERE user_id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Jika pengguna tidak ditemukan, redirect dengan notifikasi error
if (!$user) {
    $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠ User ID tidak ditemukan!'];
    header("Location: users.php");
    exit();
}

$stmt->close();
$conn->close();

include('.includes/header.php');
include('.includes/toast_notification.php');
?>

<div class="container mt-5">
    <div class="card p-4">
        <h2 class="text-center">Edit User</h2>
        <form action="edit_users.php?id=<?= htmlspecialchars($user['user_id']); ?>" method="POST">
            
            <!-- Field Nama -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']); ?>" required>
                <label for="nama">Nama</label>
            </div>

            <!-- Field Kontak -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="kontak" name="kontak" value="<?= htmlspecialchars($user['kontak']); ?>" required>
                <label for="kontak">Kontak</label>
            </div>

            <!-- Field Username -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
                <label for="username">Username</label>
            </div>

            <!-- Field Role -->
            <div class="form-floating mb-3">
                <select class="form-select" id="role" name="role" required>
                    <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="pelanggan" <?= ($user['role'] === 'pelanggan') ? 'selected' : ''; ?>>Pelanggan</option>
                </select>
                <label for="role">Role</label>
            </div>

            <!-- Tombol -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success mx-2">Update</button>
                <a href="users.php" class="btn btn-secondary mx-2">Kembali</a>
            </div>

        </form>
    </div>
</div>

<?php include('.includes/footer.php'); ?>