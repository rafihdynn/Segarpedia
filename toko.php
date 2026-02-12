<?php
// ================= SESSION & LOGIN =================
include '../config/session.php';
include '../config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// ================= AMBIL ID TOKO =================
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$toko_id = intval($_GET['id']);

// Ambil info toko
$toko_query = mysqli_query($conn, "SELECT * FROM toko WHERE user_id=$toko_id");
if (mysqli_num_rows($toko_query) == 0) {
    die("Toko tidak ditemukan");
}
$toko = mysqli_fetch_assoc($toko_query);

// Ambil produk aktif toko
$produk_query = mysqli_query($conn, "SELECT * FROM produk WHERE penjual_id=$toko_id AND status='aktif'");

// Ambil jumlah item di keranjang
$cart_query = mysqli_query($conn, "
SELECT COALESCE(SUM(qty),0) AS total
FROM cart
WHERE pembeli_id = $user_id
");
$cart_count = mysqli_fetch_assoc($cart_query)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($toko['nama_toko']) ?> - Pasar</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { font-family: 'Segoe UI', sans-serif; background: #f7f7f7; margin:0; padding:20px; }
h2 { text-align:center; color:#333; margin-bottom:20px; }

/* Keranjang */
.cart-link {
    display: inline-block;
    background: #ff5722;
    color: #fff;
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s;
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
}
.cart-link:hover { background: #e64a19; }
.cart-count {
    background: #fff;
    color: #ff5722;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 50%;
    font-size: 12px;
    position: absolute;
    top: -5px;
    right: -5px;
}

/* Logout */
.logout-link {
    position: fixed;
    top: 70px;
    right: 20px;
    background: #f44336;
    color: white;
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s;
    z-index: 1000;
}
.logout-link:hover { background: #d32f2f; }

/* Grid produk */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 100px;
}

.card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    padding: 15px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }

.card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
}

.card h3 { margin:0 0 8px; font-size:18px; color:#333; }
.card p { margin:0 0 6px; color:#555; font-size:14px; }
.store-info { font-style: italic; color:#555; font-size:13px; margin-bottom:10px; }

.btn {
    display: inline-block;
    background: #4caf50;
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s, transform 0.2s;
}
.btn:hover { background: #43a047; transform: scale(1.05); }

.btn-disabled {
    background: #bdbdbd;
    color: #666;
    cursor: not-allowed;
    pointer-events: none;
}

/* Toast */
#toast {
    visibility: hidden;
    min-width: 200px;
    background-color: #333;
    color: #fff;
    text-align:center;
    border-radius: 25px;
    padding:12px;
    position:fixed;
    z-index:999;
    left:50%;
    bottom:30px;
    font-size:14px;
    transform:translateX(-50%);
}
#toast.show { visibility:visible; animation:fadein 0.5s, fadeout 0.5s 2.5s; }
@keyframes fadein { from {bottom:0;opacity:0;} to {bottom:30px;opacity:1;} }
@keyframes fadeout { from {bottom:30px;opacity:1;} to {bottom:0;opacity:0;} }
</style>
</head>
<body>

<h2><?= htmlspecialchars($toko['nama_toko']) ?> 🛒</h2>
<p style="text-align:center; color:#555;"><?= htmlspecialchars($toko['lokasi']) ?></p>

<!-- Keranjang -->
<a href="cart.php" class="cart-link">
  🛒 Keranjang
  <span class="cart-count"><?= $cart_count ?></span>
</a>

<!-- Logout -->
<a href="../auth/logout.php" class="logout-link" onclick="return confirm('Yakin mau logout?')">
  🚪 Logout
</a>

<!-- Grid Produk -->
<div class="grid">
<?php while($p = mysqli_fetch_assoc($produk_query)) { ?>
  <div class="card">
    <img src="../assets/img/<?= htmlspecialchars($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>">
    <h3><?= htmlspecialchars($p['nama_produk']) ?></h3>
    <p>Rp <?= number_format($p['harga']) ?></p>
    <p>Stok: <?= $p['stok'] ?></p>

    <?php if($p['stok'] > 0) { ?>
      <a href="add_cart.php?id=<?= $p['id'] ?>" class="btn" onclick="addToCart(event,'<?= htmlspecialchars($p['nama_produk']) ?>')">
        🛒 Tambah ke Keranjang
      </a>
    <?php } else { ?>
      <span class="btn btn-disabled">❌ Habis</span>
    <?php } ?>
  </div>
<?php } ?>
</div>

<div id="toast"></div>

<script>
function addToCart(event, productName){
    event.preventDefault();
    const url = event.target.href;

    fetch(url)
    .then(res => res.text())
    .then(data => {
        showToast(productName + " berhasil ditambahkan ke keranjang!");
        updateCartCount();
    })
    .catch(err => console.error(err));
}

function showToast(message){
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'show';
    setTimeout(()=>{ toast.className = toast.className.replace('show',''); },3000);
}

function updateCartCount(){
    fetch('cart_count.php')
    .then(res=>res.text())
    .then(count=>{
        let span=document.querySelector('.cart-count');
        if(!span){
            span=document.createElement('span');
            span.className='cart-count';
            document.querySelector('.cart-link').appendChild(span);
        }
        span.textContent=count;
    });
}
</script>

</body>
</html>
