<?php
ini_set('session.gc_maxlifetime', 3600); // 1 jam
session_set_cookie_params(3600);
session_start();

include '../config/db.php';

/* ===== VALIDASI LOGIN PENJUAL ===== */
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'penjual'){
    header("Location: ../auth/login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

// Ambil data produk
$data = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT * FROM produk WHERE id=$id")
);

if(!$data){
    echo "Produk tidak ditemukan";
    exit;
}

/* ===== PROSES UPDATE ===== */
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = intval($_POST['harga']);
    $stok  = intval($_POST['stok']);

    // Jika upload gambar baru
    if(!empty($_FILES['gambar']['name'])){
        $file_name = time().'_'.basename($_FILES['gambar']['name']);
        $tmp_name  = $_FILES['gambar']['tmp_name'];

        move_uploaded_file($tmp_name, "../assets/img/$file_name");

        mysqli_query($conn,"
            UPDATE produk SET
                nama_produk='$nama',
                harga='$harga',
                stok='$stok',
                gambar='$file_name'
            WHERE id=$id
        ");
    } else {
        mysqli_query($conn,"
            UPDATE produk SET
                nama_produk='$nama',
                harga='$harga',
                stok='$stok'
            WHERE id=$id
        ");
    }

    header("Location: dashboard.php?status=updated");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Produk</title>

<style>
body{
    font-family:'Segoe UI',sans-serif;
    background:#e8f5e9;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}

.card{
    background:#fff;
    padding:30px;
    border-radius:15px;
    box-shadow:0 8px 25px rgba(0,0,0,.15);
    width:100%;
    max-width:420px;
}

h2{
    text-align:center;
    color:#2e7d32;
    margin-bottom:20px;
}

img.preview{
    width:100%;
    height:160px;
    object-fit:cover;
    border-radius:10px;
    margin-bottom:15px;
    border:2px solid #4caf50;
}

input{
    width:100%;
    padding:12px 15px;
    margin-bottom:15px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:14px;
}

input:focus{
    outline:none;
    border-color:#66bb6a;
    box-shadow:0 0 5px rgba(102,187,106,.5);
}

button{
    width:100%;
    padding:12px;
    background:#4caf50;
    color:#fff;
    font-size:16px;
    font-weight:bold;
    border:none;
    border-radius:8px;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:#43a047;
    transform:scale(1.03);
}

.back{
    display:block;
    margin-top:15px;
    text-align:center;
    text-decoration:none;
    color:#2e7d32;
    font-weight:bold;
}
</style>

<script>
// ===============================
// PREVIEW GAMBAR
// ===============================
function previewImage(input){
    const img = document.getElementById('preview');
    if(input.files && input.files[0]){
        const reader = new FileReader();
        reader.onload = e => img.src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}

// ===============================
// CONFIRM SIMPAN
// ===============================
function confirmSubmit(){
    return confirm("Yakin ingin menyimpan perubahan produk ini?");
}
</script>
</head>

<body>

<form method="POST"
      enctype="multipart/form-data"
      class="card"
      onsubmit="return confirmSubmit()">

    <h2>✏️ Edit Produk</h2>

    <img id="preview"
         src="../assets/img/<?= htmlspecialchars($data['gambar']) ?>"
         class="preview"
         alt="Preview">

    <input type="text"
           name="nama"
           value="<?= htmlspecialchars($data['nama_produk']) ?>"
           placeholder="Nama Produk"
           required>

    <input type="number"
           name="harga"
           value="<?= $data['harga'] ?>"
           placeholder="Harga"
           required>

    <input type="number"
           name="stok"
           value="<?= $data['stok'] ?>"
           placeholder="Stok"
           required>

    <input type="file"
           name="gambar"
           accept="image/*"
           onchange="previewImage(this)">

    <button type="submit">💾 Simpan Perubahan</button>

    <a href="dashboard.php" class="back">← Kembali ke Dashboard</a>
</form>

</body>
</html>
