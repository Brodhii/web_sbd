<?php
session_start(); 
require_once('config.php');
include('.includes/header.php');

// Query database
$index = 1;
$query = "SELECT * FROM users ORDER BY user_id ASC";
$result = mysqli_query($conn, $query);

// **Menambahkan pengecekan jika query gagal**
if (!$result) {
    die("<div class='alert alert-danger'>⚠️ Kesalahan database: " . mysqli_error($conn) . "</div>");
}
?>

<?php include('.includes/toast_notification.php'); ?>

<div class="container-xxl flex-grow-1 my-4">
    <h1 class="mb-4">Daftar Pengguna</h1>
    <a href="tambah_users.php" class="btn btn-primary mb-3">➕ Tambah Pengguna</a>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $index++;?></td>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td><?= htmlspecialchars($row['kontak']); ?></td>
                        <td><?= htmlspecialchars($row['username']); ?></td>
                        <td><?= htmlspecialchars($row['role']); ?></td>
                        <td>
                            <a href="edit_users.php?id=<?= $row['user_id']; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="hapus_users.php?id=<?= $row['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('.includes/footer.php'); ?>