<?php
// Pastikan sesi hanya dimulai jika belum aktif
    session_start();


// Cek apakah ada notifikasi yang dikirim dari loginauth.php
$notification = $_SESSION['notification'] ?? null;
unset($_SESSION['notification']); // Hapus notifikasi setelah ditampilkan

include("./.layouts/header.php"); // Pastikan path benar
?>

<!-- Login Card -->
<div class="card">
  <div class="card-body">
    <!-- Logo -->
    <div class="app-brand justify-content-center">
      <a href="../index.php" class="app-brand-link gap-2">
        <span class="app-brand-logo demo"></span>
        <span class="app-brand-text demo text-uppercase fw-bolder">BiteNest 🍖</span>
      </a>
    </div>
    
    <h4 class="mb-2">Selamat datang kembali! Masukkan informasi login Anda.</h4>

    <!-- Form Login -->
    <form action="login_auth.php" method="POST">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required />
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required />
      </div>

      <button type="submit" class="btn btn-primary d-grid w-100">Login</button>
    </form>

    <p class="mt-3 text-center">
      <span>Belum punya akun?</span>
      <a href="register.php"><span> Daftar di sini</span></a>
    </p>

  </div>
</div>
<!-- /Login Card -->

<?php include("./.layouts/footer.php"); // Pastikan path benar ?>