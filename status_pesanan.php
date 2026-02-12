<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role']!='pembeli'){
    header("Location: ../auth/login.php");
    exit;
}

$pembeli = $_SESSION['user']['id'];

$q = mysqli_query($conn,"
    SELECT o.*, t.nama_toko
    FROM orders o
    JOIN toko t ON o.penjual_id = t.user_id
    WHERE o.pembeli_id = $pembeli
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Riwayat Pesanan</title>
<style>
.card{
    background:#fff;
    padding:20px;
    border-radius:14px;
    box-shadow:0 6px 16px rgba(0,0,0,.08);
    margin-bottom:15px;
}
.status{
    padding:4px 10px;
    border-radius:8px;
    font-size:12px;
    font-weight:bold;
}
.checkout{background:#e3f2fd;color:#1565c0}
.selesai{background:#e8f5e9;color:#2e7d32}
</style>
</head>
<body>

<h2>📦 Riwayat Pesanan</h2>

<?php while($o = mysqli_fetch_assoc($q)): ?>
<div class="card">
    <b><?= $o['nama_toko'] ?></b><br>
    Total: Rp <?= number_format($o['total']) ?><br>
    Tanggal: <?= $o['created_at'] ?><br>
    <span class="status <?= $o['status'] ?>">
        <?= strtoupper($o['status']) ?>
    </span><br><br>

    <a href="detail_pesanan.php?id=<?= $o['id'] ?>">Lihat Detail</a>
</div>
<?php endwhile; ?>

</body>
</html>
