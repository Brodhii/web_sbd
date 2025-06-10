<?php
require_once('.includes/init_session.php');
require_once('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $harga = trim($_POST['harga']);
    $kategori = trim($_POST['kategori']);

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $gambar = $_FILES['gambar']['name'];
        $gambar_tmp = $_FILES['gambar']['tmp_name'];
        $gambar_path = "uploads/" . basename($gambar);

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠️ Format gambar tidak valid!'];
            header('Location: tambah_menu.php');
            exit();
        }

        if (move_uploaded_file($gambar_tmp, $gambar_path)) {
            try {
                $query = "INSERT INTO menu (nama, harga, kategori, gambar) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssss", $nama, $harga, $kategori, $gambar);

                if ($stmt->execute()) {
                    $_SESSION['notification'] = ['type' => 'success', 'message' => 'hore,menu selesai ditambahkan!'];
                    header('Location: menu.php');
                    exit();
                } else {
                    throw new Exception('⚠️ yah gagal menambahkan menu! Kesalahan database.');
                }
            } catch (Exception $e) {
                $_SESSION['notification'] = ['type' => 'danger', 'message' => $e->getMessage()];
            }
        } else {
            $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠️ gagal mengupload gambar!'];
        }
    } else {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => '⚠️ gambar menu tidak valid!'];
    }
}

include('.includes/header.php');
$title = "Tambah Menu";
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="my-4">Tambah Menu</h1>

    <?php if (isset($_SESSION['notification'])): ?>
        <div class="alert alert-<?= $_SESSION['notification']['type']; ?>" role="alert">
            <?= $_SESSION['notification']['message']; ?>
        </div>
        <?php unset($_SESSION['notification']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Menu</label>
                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama Menu" required>
                </div>
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="number" class="form-control" id="harga" name="harga" placeholder="Masukkan Harga Menu" required>
                </div>
                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select class="form-control" id="kategori" name="kategori" required>
                        <option value="MAKANAN">Makanan</option>
                        <option value="MINUMAN">Minuman</option>
                        <option value="SNACK">Snack</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="gambar" class="form-label">Gambar Menu</label>
                    <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Tambah Menu</button>
            </form>
        </div>
    </div>
</div>

<?php include('.includes/footer.php'); ?>