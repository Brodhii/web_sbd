<?php

// Aktifkan pelaporan error untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log');

$host = "localhost";
$username = "root";
$password = "";
// GANTI NAMA DATABASE INI MENJADI "temu_bual" jika itu nama database tempat Anda mengimpor SQL
$database = "temu_buall"; // <-- Ubah dari "coba" menjadi "temu_bual"

$conn = mysqli_connect($host, $username,$password,$database);

if ($conn->connect_error) {
    die("database gagal terkoneksi:" . $conn->connect_error);
}

?>