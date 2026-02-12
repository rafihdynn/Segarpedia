<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit;
}

$order_id = intval($_GET['id']);
$pembeli  = $_SESSION['user']['id'];

/* Ambil order */
$qOrder = mysqli_query($conn,"
SELECT o.*, t.nama_toko, t.no_wa
FROM orders o
JOIN toko t ON o.penjual_id = t.user_id
WHERE o.id = $order_id
AND o.pembeli_id = $pembeli
");

$order = mysqli_fetch_assoc($qOrder);
if(!$order){
    echo "Order tidak ditemukan";
    exit;
}

/* Ambil item */
$qItems = mysqli_query($conn,"
SELECT * FROM order_items
WHERE order_id = $order_id
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Pesanan</title>
<style>
body{
    font-family:'Segoe UI',sans-serif;
    background:#e8f5e9;
    padding:20px;
}
.container{
    max-width:700px;
    margin:auto;
}
.card{
    background:#fff;
    padding:15px;
    border-radius:12px;
    margin-bottom:10px;
    box-shadow:0 5px 15px rgba(0,0,0,.1);
}
.status{
    padding:6px 10px;
    border-radius:8px;
    color:#fff;
    font-weight:bold;
}
.pending{background:#ff9800}
.dibayar{background:#4caf50}
.dikirim{background:#2196f3}
.btn{
    display:block;
    margin-top:15px;
    padding:12px;
    text-align:center;
    background:#25D366;
    color:#fff;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
}
</style>
</head>

<body>
<div class="container">

<h2>Order #<?= $order['id'] ?></h2>

<div class="card">
    <p><b>Toko:</b> <?= htmlspecialchars($order['nama_toko']) ?></p>
    <p><b>Status:</b> 
        <span class="status <?= $order['status'] ?>">
            <?= ucfirst($order['status']) ?>
        </span>
    </p>
</div>

<?php while($i = mysqli_fetch_assoc($qItems)): ?>
<div class="card">
    <?= htmlspecialchars($i['produk_nama']) ?> (<?= $i['qty'] ?>)
    <div style="float:right">
        Rp <?= number_format($i['subtotal']) ?>
    </div>
</div>
<?php endwhile; ?>

<div class="card" style="font-weight:bold;text-align:right">
    Total: Rp <?= number_format($order['total']) ?>
</div>

<a class="btn" href="https://wa.me/<?= preg_replace('/[^0-9]/','',$order['no_wa']) ?>">
    💬 Hubungi Penjual
</a>

</div>
</body>
</html>
