<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login – SegarPedia</title>
<link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="login-container">

  <!-- KIRI -->
  <div class="login-left">
    <h1>🥬 SegarPedia</h1>
    <p>Selamat datang kembali!<br>Masuk untuk belanja atau jualan 🍃</p>
    <img src="../assets/img/login.png" alt="Login">
  </div>

  <!-- KANAN -->
  <div class="login-right">
    <form action="process_login.php" method="POST" class="card">
      <h2>Masuk Akun</h2>

      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>

      <button type="submit">Login</button>

      <p class="register-link">
        Belum punya akun?
        <a href="register.php">Daftar</a>
      </p>
    </form>
  </div>

</div>

</body>
</html>
