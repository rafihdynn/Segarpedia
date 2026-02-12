<?php
// ================= SESSION & LOGIN =================
include '../config/session.php';
include '../config/db.php';

// Pastikan pembeli login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}
$user_id = $_SESSION['user']['id'];

// ================= FILTER PENCARIAN =================
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// ================= AMBIL TOKO =================
$toko_sql = "SELECT t.*, COUNT(DISTINCT p.id) AS total_produk 
             FROM toko t 
             LEFT JOIN produk p 
             ON p.penjual_id = t.user_id 
             AND p.status='aktif' 
             AND p.stok > 0";

$where = [];
if ($search !== '') {
    $where[] = "(t.nama_toko LIKE '%$search%' OR p.nama_produk LIKE '%$search%')";
}

if (!empty($where)) {
    $toko_sql .= " WHERE " . implode(" AND ", $where);
}

$toko_sql .= " GROUP BY t.user_id ORDER BY t.nama_toko ASC";

$toko_query = mysqli_query($conn, $toko_sql);

// Ambil jumlah item di keranjang
$cart_query = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(qty),0) AS total 
     FROM cart 
     WHERE pembeli_id = $user_id"
);
$cart_count = mysqli_fetch_assoc($cart_query)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pasar Pembeli</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f7f7f7;
    margin: 0;
    padding: 20px;
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

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

.cart-link:hover {
    background: #e64a19;
}

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
    color: #fff;
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s;
    z-index: 1000;
}

.logout-link:hover {
    background: #d32f2f;
}

/* Grid */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    padding: 15px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
}

.card h3 {
    margin: 0 0 8px;
    font-size: 18px;
    color: #333;
}

.card p {
    margin: 0 0 10px;
    color: #555;
    font-size: 14px;
}

.store-info {
    font-style: italic;
    color: #555;
    font-size: 13px;
    margin-bottom: 10px;
}

/* Tombol */
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

.btn:hover {
    background: #43a047;
    transform: scale(1.05);
}

/* Form pencarian */
form.search-form {
    text-align: center;
    margin-bottom: 20px;
}

form.search-form input,
form.search-form button {
    padding: 8px;
    border-radius: 8px;
    border: 1px solid #ccc;
    margin-right: 5px;
}

form.search-form button {
    background: #4caf50;
    color: white;
    border: none;
    font-weight: bold;
    cursor: pointer;
}

form.search-form button:hover {
    background: #43a047;
}

.notif{
    padding:15px 20px;
    margin-bottom:20px;
    border-radius:12px;
    font-weight:600;
    animation: fadeIn .4s ease;
}
.notif.success{
    background:#e8f5e9;
    color:#2e7d32;
    border-left:6px solid #4caf50;
}
.notif.error{
    background:#ffebee;
    color:#c62828;
    border-left:6px solid #f44336;
}

.menu-link{
    display:inline-block;
    background:#4caf50;
    color:#fff;
    padding:12px 20px;
    border-radius:12px;
    font-weight:700;
    text-decoration:none;
    transition:.3s;
}

.menu-link:hover{
    background:#43a047;
}

.menu-link:visited{
    color:#fff;
}


@keyframes fadeIn{
    from{opacity:0; transform:translateY(-5px)}
    to{opacity:1; transform:translateY(0)}
}

</style>
</head>

<body>

<?php if(isset($_SESSION['notif'])): ?>
    <div class="notif <?= $_SESSION['notif']['type'] ?>">
        <?= $_SESSION['notif']['message'] ?>
    </div>
<?php unset($_SESSION['notif']); endif; ?>

<h2>PILIH TOKO SESUKAMU, KAMU MAU BELANJA APA HARI INI?</h2>

<!-- Keranjang -->
<a href="cart.php" class="cart-link">
    🛒 Keranjang <span class="cart-count"><?= $cart_count ?></span>
</a>

<!-- Logout -->
<a href="../auth/logout.php" class="logout-link"
   onclick="return confirm('Yakin mau logout?')">
   🚪 Logout
</a>

<!-- Form Pencarian -->
<form method="GET" class="search-form">
    <input
        type="text"
        name="search"
        placeholder="Cari toko atau produk..."
        value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Cari</button>
</form>

<!-- Grid Toko -->
<div class="grid">
<?php while($t = mysqli_fetch_assoc($toko_query)) { ?>
    <div class="card">
        <img src="../assets/img/<?= htmlspecialchars($t['foto']) ?>"
             alt="<?= htmlspecialchars($t['nama_toko']) ?>">
        <h3><?= htmlspecialchars($t['nama_toko']) ?></h3>
        <div class="store-info">
            📍 <?= htmlspecialchars($t['lokasi']) ?>
        </div>
        <p>Total Produk: <?= $t['total_produk'] ?></p>
        <a href="toko.php?id=<?= $t['user_id'] ?>" class="btn">
            🛒 Buka Toko
        </a>
    </div>
<?php } ?>
</div>

</body>
</html>