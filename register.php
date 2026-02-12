<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Akun – SegarPedia</title>
<link rel="stylesheet" href="../assets/css/register.css">
</head>
<body>

<div class="register-container">

  <!-- KIRI -->
  <div class="register-left">
    <h1>🥬 SegarPedia</h1>
    <p>Gabung dan mulai jual beli<br>buah & sayur segar 🍃</p>
    <img src="../assets/img/register.png" alt="Register">
  </div>

  <!-- KANAN -->
  <div class="register-right">
    <form action="process_register.php" method="POST" class="card">
      <h2>Buat Akun</h2>

      <input type="text" name="nama" placeholder="Nama Lengkap" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>

      <select name="role" required>
        <option value="">Pilih Peran</option>
        <option value="pembeli">🛒 Pembeli</option>
        <option value="penjual">🏪 Penjual</option>
      </select>

      <button type="submit">Daftar Sekarang</button>

      <p class="login-link">
        Sudah punya akun?
        <a href="login.php">Login</a>
      </p>
    </form>
  </div>

</div>

</body>
</html>
