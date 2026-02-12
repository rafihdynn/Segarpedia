<?php
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();

include '../config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user']['id'];

$user = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT nama, email, telp, tgl_lahir, jenis_kelamin, foto
    FROM users WHERE id=$id
"));

/* ================= AMBIL TELP DARI TOKO JIKA KOSONG ================= */
if (empty($user['telp'])) {
    $qToko = mysqli_query($conn, "
        SELECT no_wa 
        FROM toko 
        WHERE user_id = $id 
        LIMIT 1
    ");
    if ($toko = mysqli_fetch_assoc($qToko)) {
        $user['telp'] = $toko['no_wa'];
    }
}
/* ================================================================== */

/* ================= AMBIL FOTO DARI TOKO JIKA USER BELUM ADA ================= */
if (empty($user['foto'])) {
    $qFoto = mysqli_query($conn, "
        SELECT foto 
        FROM toko 
        WHERE user_id = $id 
        LIMIT 1
    ");
    if ($tokoFoto = mysqli_fetch_assoc($qFoto)) {
        $user['foto'] = $tokoFoto['foto'];
    }
}
/* ======================================================================== */

/* ================= VALIDASI TELEPON ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['telp'])) {
        if (!preg_match('/^08[0-9]{8,11}$/', $_POST['telp'])) {
            echo "
            <script>
                alert('Nomor telepon tidak valid! Gunakan format 08xxxxxxxxxx');
                history.back();
            </script>";
            exit;
        }
    }
}
/* =================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = $_POST['nama'];
    $email = $_POST['email'];
    $telp  = $_POST['telp'];
    $tgl   = $_POST['tgl_lahir'];
    $jk    = $_POST['jenis_kelamin'];

    /* ================= PROSES FOTO ================= */
    $foto_sql = "";
    if (!empty($_FILES['foto']['name'])) {
        $foto = time().'_'.$_FILES['foto']['name'];
        $tmp  = $_FILES['foto']['tmp_name'];

        move_uploaded_file($tmp, "../assets/img/$foto");

        // update foto user
        $foto_sql = ", foto='$foto'";

        // 🔥 SINKRON FOTO KE TOKO
        mysqli_query($conn, "
            UPDATE toko SET foto='$foto'
            WHERE user_id=$id
        ");
    }
    /* =============================================== */

    mysqli_query($conn, "
        UPDATE users SET
        nama='$nama',
        email='$email',
        telp='$telp',
        tgl_lahir='$tgl',
        jenis_kelamin='$jk'
        $foto_sql
        WHERE id=$id
    ");

    $_SESSION['success'] = "Data berhasil di update";
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Biodata Diri</title>
<style>
*{box-sizing:border-box;font-family:Arial}
body{background:#f6f6f6;padding:30px}
.container{background:#fff;max-width:850px;margin:auto;padding:30px;border-radius:10px;display:flex;gap:40px}
.left{text-align:center;width:260px}
.avatar{
    width:200px;height:200px;
    border:1px solid #ccc;
    border-radius:6px;
    margin:auto 0 10px;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
}
.avatar img{width:100%;height:100%;object-fit:cover}
.right{flex:1}
.row{display:grid;grid-template-columns:160px 1fr;margin-bottom:15px;align-items:center}
input,select{padding:8px;border-radius:6px;border:1px solid #ccc;width:100%}
button{background:#2e7d32;color:#fff;border:none;padding:10px 20px;border-radius:6px;cursor:pointer}
</style>
</head>
<body>

<form method="POST" class="container" enctype="multipart/form-data">
    <div class="left">
        <div class="avatar">
            <?php if(!empty($user['foto'])): ?>
                <img src="../assets/img/<?= htmlspecialchars($user['foto']) ?>">
            <?php else: ?>
                🙂
            <?php endif; ?>
        </div>
        <input type="file" name="foto" accept="image/*">
    </div>

    <div class="right">
        <h3>Biodata Diri</h3>

        <div class="row">
            <label>Nama</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
        </div>

        <div class="row">
            <label>Tanggal Lahir</label>
            <input type="date" name="tgl_lahir" value="<?= $user['tgl_lahir'] ?>">
        </div>

        <div class="row">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin">
                <option value="">-- Pilih --</option>
                <option value="Laki-laki" <?= $user['jenis_kelamin']=='Laki-laki'?'selected':'' ?>>Laki-laki</option>
                <option value="Perempuan" <?= $user['jenis_kelamin']=='Perempuan'?'selected':'' ?>>Perempuan</option>
            </select>
        </div>

        <h3>Kontak</h3>

        <div class="row">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="row">
            <label>No Telepon / WA</label>
            <input type="text" name="telp" value="<?= $user['telp'] ?>">
        </div>

        <button>Simpan Perubahan</button>
    </div>
</form>

</body>
</html>
