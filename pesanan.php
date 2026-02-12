<?php
require '../config/session.php';
require '../config/db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role']!='penjual'){
    header("Location: ../auth/login.php");
    exit;
}

$penjual_id = $_SESSION['user']['id'];

$q = mysqli_query($conn,"
    SELECT o.*, u.nama
    FROM orders o
    JOIN users u ON o.pembeli_id = u.id
    WHERE o.penjual_id = $penjual_id
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pesanan Masuk</title>

<style>
body{
    font-family:'Segoe UI',sans-serif;
    background:#fffde7;
    margin:0;
    padding:25px;
}
h2{
    text-align:center;
    color:#f57f17;
    margin-bottom:25px;
}
.card{
    background:#fff;
    padding:18px 20px;
    border-radius:16px;
    margin-bottom:15px;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
}
.kode{
    font-size:15px;
    font-weight:700;
    color:#333;
}
.info{
    margin-top:6px;
    font-size:14px;
    color:#555;
}
.status{
    display:inline-block;
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:700;
    margin:10px 0;
}
.status.menunggu{
    background:#fff3e0;
    color:#ef6c00;
}
.status.diproses{
    background:#e3f2fd;
    color:#1565c0;
}
.status.selesai{
    background:#e8f5e9;
    color:#2e7d32;
}
.btn{
    display:inline-block;
    padding:8px 14px;
    border-radius:10px;
    font-weight:700;
    text-decoration:none;
    margin-right:6px;
    transition:.3s;
}
.detail{
    background:#607d8b;
    color:#fff;
}
.proses{
    background:#2196f3;
    color:#fff;
}
.selesai-btn{
    background:#4caf50;
    color:#fff;
}
.btn:hover{
    opacity:.85;
}
</style>
</head>

<body>

<h2>📦 Pesanan Masuk</h2>

<?php if(mysqli_num_rows($q)==0): ?>
    <div class="card">Belum ada pesanan masuk.</div>
<?php endif; ?>

<?php while($o=mysqli_fetch_assoc($q)): ?>
<div class="card">
    <div class="kode"><?= $o['kode_order'] ?></div>

    <div class="info">👤 Pembeli: <?= htmlspecialchars($o['nama']) ?></div>
    <div class="info">💰 Rp <?= number_format($o['total']) ?></div>
    <div class="info">📅 <?= date('d M Y H:i',strtotime($o['created_at'])) ?></div>

    <div class="status <?= $o['status'] ?>">
        <?= ucfirst($o['status']) ?>
    </div><br>

    <a href="../shared/detail_order.php?id=<?= $o['id'] ?>" class="btn detail">Detail</a>

    <?php if($o['status']=='menunggu'): ?>
        <a href="update_status.php?id=<?= $o['id'] ?>&s=diproses" class="btn proses">
            Proses
        </a>
    <?php elseif($o['status']=='diproses'): ?>
        <a href="update_status.php?id=<?= $o['id'] ?>&s=selesai" class="btn selesai-btn">
            Selesai
        </a>
    <?php endif; ?>
</div>
<?php endwhile; ?>

</body>
</html>
