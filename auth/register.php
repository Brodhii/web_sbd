<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$notification = $_SESSION['notification'] ?? null;
unset($_SESSION['notification']); 

include("./.layouts/header.php"); 
?>

<!-- Register Card -->
<div class="card">
  <div class="card-body">
    <!-- Logo -->
    <div class="app-brand justify-content-center">
      <a href="../index.php" class="app-brand-link gap-2">
        <span class="app-brand-logo demo"></span>
        <span class="app-brand-text demo text-uppercase fw-bolder">BiteNest 🍖</span>
      </a>
    </div>
    
    <h4 class="mb-2">Daftar untuk membuat akun baru</h4>

    

    <!-- Form Register -->
    <form action="register_process.php" method="POST">
      <div class="mb-3">
        <label for="kontak" class="form-label">Kontak</label>
        <input type="text" class="form-control" name="kontak" placeholder="Kontak" required />
      </div>

      <div class="mb-3">
        <label for="nama" class="form-label">Nama</label>
        <input type="text" class="form-control" name="nama" placeholder="Nama" required />
      </div>

      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" name="username" placeholder="Username" required />
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" name="password" placeholder="Password" required />
      </div>

      <button type="submit" class="btn btn-primary d-grid w-100">Daftar</button>
    </form>

    <p class="text-center">
      <span>Sudah punya akun?</span>
      <a href="login.php"><span> Mari masuk</span></a>
    </p>

  </div>
</div>
<!-- /Register Card -->

<?php include("./.layouts/footer.php"); // Pastikan path benar ?>