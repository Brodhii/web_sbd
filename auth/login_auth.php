<?php
// Pastikan sesi hanya dimulai jika belum aktif
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('../config.php'); // Pastikan koneksi ke database benar

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Query untuk memverifikasi pengguna
    $query = "SELECT user_id, nama, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session setelah login sukses
            $_SESSION['user_id'] = $user['user_id'];  
            $_SESSION['username'] = $user['username']; 
            $_SESSION['nama'] = $user['nama']; 
            $_SESSION['role'] = $user['role']; 

            // Redirect ke dashboard setelah login berhasil
            header('Location: ../dashboard.php');
            exit();
        } else {
            $_SESSION['notification'] = ['type' => 'danger', 'message' => 'Username atau password salah!'];
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => 'Username tidak ditemukan!'];
        header('Location: login.php');
        exit();
    }
}
?>