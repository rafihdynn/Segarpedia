<?php
ini_set('session.gc_maxlifetime', 3600);

session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../config/db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'pembeli'){
    header("Location: ../auth/login.php");
    exit;
}

$pembeli = $_SESSION['user']['id'];
$penjual = intval($_GET['toko']); // ← ID toko / penjual dari cart
/* =========================================================
   AMBIL CART BERDASARKAN TOKO (PER TOKO)
========================================================= */
$q = mysqli_query($conn,"
SELECT 
    c.qty,
    p.nama_produk,
    p.harga,
    t.nama_toko,
    t.no_wa
FROM cart c
JOIN produk p ON c.produk_id = p.id
JOIN toko t ON p.penjual_id = t.user_id
WHERE c.pembeli_id = $pembeli
AND p.penjual_id = $penjual
");



$items = [];
$total = 0;
$nama_toko = '';
$wa_penjual = '';

while($d = mysqli_fetch_assoc($q)){
    $sub = $d['qty'] * $d['harga'];
    $total += $sub;

    $items[] = [
        'nama' => $d['nama_produk'],
        'qty' => $d['qty'],
        'subtotal' => $sub
    ];

    $nama_toko = $d['nama_toko'];
    $wa_penjual = $d['no_wa'];
}

/* =========================================================
   PESAN WHATSAPP
========================================================= */
$pesan = "Halo $nama_toko, saya mau pesan:%0A";
foreach($items as $i){
    $pesan .= "- {$i['nama']} ({$i['qty']} pcs) : Rp ".number_format($i['subtotal'])."%0A";
}
$pesan .= "%0ATotal: Rp ".number_format($total);
$pesan .= "%0A%0ATerima kasih 🙏";

/* Normalisasi WA */
$wa = preg_replace('/[^0-9]/', '', $wa_penjual);
if(substr($wa,0,1)=='0'){
    $wa = '62'.substr($wa,1);
}elseif(substr($wa,0,2)!='62'){
    $wa = '62'.$wa;
}

$link_wa = "https://wa.me/$wa?text=$pesan";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Checkout</title>

<style>
body{
    font-family:'Segoe UI',sans-serif;
    background:#e8f5e9;
    padding:20px;
}
h2{
    text-align:center;
    color:#2e7d32;
}
.checkout-container{
    max-width:700px;
    margin:auto;
}
.card{
    background:#fff;
    padding:15px;
    border-radius:12px;
    margin-bottom:12px;
    display:flex;
    justify-content:space-between;
    box-shadow:0 5px 15px rgba(0,0,0,.1);
}
.total{
    background:#4caf50;
    color:#fff;
    padding:15px;
    border-radius:12px;
    font-size:18px;
    font-weight:bold;
    text-align:right;
}
.btn{
    display:block;
    width:100%;
    margin-top:20px;
    padding:12px;
    text-align:center;
    background:#25D366;
    color:#fff;
    font-weight:bold;
    border-radius:10px;
    text-decoration:none;
}
.back{
    background:#607d8b;
}
</style>
</head>

<body>

<h2>Checkout – <?= htmlspecialchars($nama_toko) ?></h2>

<div class="checkout-container">

<?php if(count($items)>0): ?>
    <?php foreach($items as $i): ?>
        <div class="card">
            <div><?= $i['nama'] ?> (<?= $i['qty'] ?>)</div>
            <div>Rp <?= number_format($i['subtotal']) ?></div>
        </div>
    <?php endforeach; ?>

    <div class="total">
        Total: Rp <?= number_format($total) ?>
    </div>

    <button onclick="checkoutWA()" class="btn">
    💬 Checkout via WhatsApp
</button>


    <a href="cart.php" class="btn back">← Kembali ke Keranjang</a>

<?php else: ?>
    <div class="card">Keranjang kosong</div>
<?php endif; ?>

</div>

<script>
function checkoutWA(){
    // 1. buka WhatsApp di tab baru
    window.open("<?= $link_wa ?>", "_blank");

    // 2. panggil server buat hapus cart
    fetch("checkout_ajax.php?toko=<?= (int)$penjual ?>")
        .then(res => res.text())
        .then(() => {
            // 3. redirect ke dashboard
            window.location.href = "dashboard.php";
        });
}
</script>

</body>
</html>
