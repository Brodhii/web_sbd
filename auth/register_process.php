<?php
session_start();
require_once("../config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kontak = trim($_POST['kontak']);
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
//
    if (empty($kontak) || empty($nama) || empty($username) || empty($password)) {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => 'Semua kolom harus diisi!'];
        header("Location: register.php");
        exit();
    }

    $query = "SELECT user_id FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => 'Username sudah digunakan!'];
        header("Location: register.php");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert_query = "INSERT INTO users (kontak, nama, username, password, role) VALUES (?, ?, ?, ?, 'pelanggan')";
    $stmt_insert = $conn->prepare($insert_query);
    $stmt_insert->bind_param("ssss", $kontak, $nama, $username, $hashed_password);

    if ($stmt_insert->execute()) {
        $_SESSION['notification'] = ['type' => 'success', 'message' => 'Pendaftaran berhasil! Silakan login.'];
        header("Location: ./login.php");
        exit();
    } else {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => 'Terjadi kesalahan saat mendaftar!'];
        header("Location: register.php");
        exit();
    }
}
?>