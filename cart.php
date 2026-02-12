<?php
include '../config/session.php';
include '../config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$pembeli_id = $_SESSION['user']['id'];

/* ===============================
   QUERY BARU (TANPA HAPUS YANG LAMA)
   Ambil cart + produk + toko
================================ */

$pembeli_id = $_SESSION['user']['id']; 

$q = mysqli_query($conn,"
    SELECT 
        c.id AS cart_id,
        c.qty,
        p.nama_produk,
        p.harga,
        p.penjual_id,
        t.nama_toko
    FROM cart c
    JOIN produk p ON c.produk_id = p.id
    JOIN toko t ON p.penjual_id = t.user_id
    WHERE c.pembeli_id = $pembeli_id
    ORDER BY t.nama_toko
") or die(mysqli_error($conn));


/* ===============================
   KELOMPOKKAN CART PER TOKO
================================ */
$cartToko = [];

while($d = mysqli_fetch_assoc($q)){
    $cartToko[$d['penjual_id']]['nama_toko'] = $d['nama_toko'];
    $cartToko[$d['penjual_id']]['items'][] = $d;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Keranjang Belanja</title>

<style>
body{
    font-family:Arial, sans-serif;
    background:#f4f6f8;
    margin:0;
    padding:30px;
}
.container{
    max-width:900px;
    margin:auto;
}
h2{
    margin-bottom:20px;
}
.card{
    background:#fff;
    padding:20px;
    border-radius:16px;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
    margin-bottom:15px;
}
.item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
}
.info{
    flex:1;
}
.qty{
    font-weight:bold;
}
.btn{
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-weight:700;
    color:#fff;
    margin-right:5px;
}
.plus{background:#4caf50}
.minus{background:#ff9800}
.del{background:#f44336}
.back{background:#607d8b}
.checkout{
    display:inline-block;
    margin-top:15px;
    background:#2196f3;
    padding:12px 20px;
    border-radius:12px;
    color:#fff;
    font-weight:700;
    text-decoration:none;
}
.toko{
    font-size:18px;
    font-weight:bold;
    margin-bottom:10px;
}
.total{
    font-size:18px;
    font-weight:bold;
    margin-top:10px;
    text-align:right;
}
</style>
</head>

<body>
<div class="container">

<h2>🛒 Keranjang Belanja</h2>

<?php if(empty($cartToko)): ?>
    <div class="card">
        <h3>Keranjang kamu kosong</h3>
        <a href="dashboard.php" class="btn back">← Kembali Belanja</a>
    </div>
<?php else: ?>

<?php foreach($cartToko as $penjual_id => $data): ?>
    <div class="card">
        <div class="toko">🏪 <?= htmlspecialchars($data['nama_toko']) ?></div>

        <?php
        $totalToko = 0;
        foreach($data['items'] as $d):
            $sub = $d['qty'] * $d['harga'];
            $totalToko += $sub;
        ?>
        <div class="item">
            <div class="info">
                <b><?= $d['nama_produk'] ?></b><br>
                Harga: Rp <?= number_format($d['harga']) ?><br>
                Qty: <span class="qty"><?= $d['qty'] ?></span><br>
                Subtotal: Rp <?= number_format($sub) ?>
            </div>

            <div>
                <a href="update_cart.php?id=<?= $d['cart_id'] ?>&aksi=kurang" class="btn minus">−</a>
                <a href="update_cart.php?id=<?= $d['cart_id'] ?>&aksi=tambah" class="btn plus">+</a>
                <a href="remove_cart.php?id=<?= $d['cart_id'] ?>" class="btn del">Hapus</a>
            </div>
        </div>
        <hr>
        <?php endforeach; ?>

        <div class="total">
            Total Toko: Rp <?= number_format($totalToko) ?>
        </div>

        <!-- CHECKOUT PER TOKO -->
        <a href="checkout.php?toko=<?= $penjual_id ?>" class="checkout">
            Checkout Toko Ini
        </a>
    </div>
<?php endforeach; ?>

<a href="dashboard.php" class="btn back">← Kembali Belanja</a>

<?php endif; ?>

</div>
</body>
</html>
