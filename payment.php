<?php
ini_set('session.gc_maxlifetime', 3600); // 1 jam
session_set_cookie_params(3600);
session_start();

include '../config/db.php';

$pembeli = $_SESSION['user']['id'];

/* hitung total */
$q = mysqli_query($conn,"
SELECT SUM(p.harga * c.qty) AS total
FROM cart c 
JOIN produk p ON c.produk_id=p.id
WHERE c.pembeli_id=$pembeli
");
$data = mysqli_fetch_assoc($q);
$total = $data['total'] ?? 0;

/* simpan order */
mysqli_query($conn,"
INSERT INTO orders (pembeli_id, total, status)
VALUES ($pembeli, $total, 'dibayar')
");
$order_id = mysqli_insert_id($conn);

/* simpan detail order & update stok */
$cart = mysqli_query($conn,"
SELECT * FROM cart WHERE pembeli_id=$pembeli
");
while($c=mysqli_fetch_assoc($cart)){
    $produk_id = $c['produk_id'];
    $qty = $c['qty'];

    /* simpan order item */
    mysqli_query($conn,"
        INSERT INTO order_items (order_id, produk_id, qty)
        VALUES ($order_id, $produk_id, $qty)
    ");

    /* update stok produk */
    mysqli_query($conn,"
        UPDATE produk 
        SET stok = stok - $qty 
        WHERE id = $produk_id AND stok >= $qty
    ");
}

/* kosongkan keranjang */
mysqli_query($conn,"DELETE FROM cart WHERE pembeli_id=$pembeli");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pembayaran Berhasil</title>
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #e8f5e9;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}
.card-success {
    background: #fff;
    border-radius: 15px;
    padding: 30px 40px;
    max-width: 450px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    animation: fadeIn 0.8s ease-out;
}
.card-success h2 {
    color: #2e7d32;
    margin-bottom: 20px;
}
.card-success p {
    font-size: 16px;
    margin: 8px 0;
}
.btn {
    display: inline-block;
    background: #4caf50;
    color: white;
    padding: 12px 20px;
    margin-top: 20px;
    border-radius: 10px;
    font-weight: bold;
    text-decoration: none;
    transition: background 0.3s, transform 0.2s;
}
.btn:hover {
    background: #2e7d32;
    transform: scale(1.05);
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px);}
    to { opacity: 1; transform: translateY(0);}
}
</style>
</head>
<body>

<div class="card-success">
    <h2>✅ Pembayaran Berhasil</h2>
    <p>Total Bayar: <strong>Rp <?= number_format($total) ?></strong></p>
    <p>Order ID: <strong>#<?= $order_id ?></strong></p>

    <a href="orders.php" class="btn">📄 Lihat Pesanan Saya</a>
</div>

</body>
</html>
