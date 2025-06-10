<?php
require_once('.includes/init_session.php');
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama     = trim($_POST['nama']);
    $kontak   = trim($_POST['kontak']);
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role     = trim($_POST['role']);

    $query = "INSERT INTO users (nama, kontak, username, password, role) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("sssss", $nama, $kontak, $username, $password, $role);
    
        $redirectUrl = '';

        try {
            $stmt->execute();
            $_SESSION['notification'] = [
                'type'    => 'success',
                'message' => "User successfully added!"
            ];
            $redirectUrl = "users.php";
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // Duplicate entry
                $_SESSION['notification'] = [
                    'type'    => 'danger',
                    'message' => "Error: Username '$username' already exists. Please choose another."
                ];
            } else {
                $_SESSION['notification'] = [
                    'type'    => 'danger',
                    'message' => "Error: " . $e->getMessage()
                ];
            }
            $redirectUrl = "tambah_users.php";
        }
        
        // Tutup statement sebelum redirect
        $stmt->close();
        
        header("Location: $redirectUrl");
        exit;
    } else {
        $_SESSION['notification'] = [
            'type'    => 'danger',
            'message' => "Error preparing statement: " . $conn->error
        ];
        header("Location: tambah_users.php");
        exit;
    }
}

include('.includes/header.php');
include('.includes/toast_notification.php');
?>

<!-- Konten HTML halaman (setelah proses POST selesai) -->
<div class="container-xxl flex-grow-1 my-4">
    <h1>Add New User</h1>
    <form method="POST" action="tambah_users.php">
        <div class="mb-3">
            <label for="nama" class="form-label">Name</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
        </div>

        <div class="mb-3">
            <label for="kontak" class="form-label">Contact</label>
            <input type="text" class="form-control" id="kontak" name="kontak" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" required>
                <option value="admin">Admin</option>
                <option value="pelanggan">Customer</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Add User</button>
    </form>
</div>

<?php 
include('.includes/footer.php');
?>