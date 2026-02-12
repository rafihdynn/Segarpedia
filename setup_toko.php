<?php
ini_set('session.gc_maxlifetime', 3600); // 1 jam
session_set_cookie_params(3600);
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['role']!='penjual'){
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftarkan Toko</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(135deg,#1b5e20,#66bb6a);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.card{
    background:#fff;
    width:420px;
    padding:35px;
    border-radius:18px;
    box-shadow:0 25px 50px rgba(0,0,0,.25);
    animation:slide .6s ease;
}

h2{
    text-align:center;
    color:#2e7d32;
    margin-bottom:20px;
}

input, textarea{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:1px solid #ccc;
    margin-bottom:12px;
    font-size:14px;
    transition:.3s;
}

input:focus, textarea:focus{
    outline:none;
    border-color:#2e7d32;
    box-shadow:0 0 0 2px rgba(46,125,50,.15);
}

textarea{
    resize:none;
    height:80px;
}

label{
    font-size:13px;
    color:#555;
}

button{
    width:100%;
    padding:13px;
    border:none;
    border-radius:25px;
    background:#2e7d32;
    color:#fff;
    font-weight:bold;
    font-size:15px;
    cursor:pointer;
    transition:.3s;
    margin-top:10px;
}

button:hover{
    background:#1b5e20;
}

.preview{
    display:flex;
    justify-content:center;
    margin-bottom:15px;
}

.preview img{
    width:90px;
    height:90px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #2e7d32;
    display:none;
}

@keyframes slide{
    from{opacity:0; transform:translateY(25px);}
    to{opacity:1; transform:translateY(0);}
}
</style>
</head>

<body>

<form action="process_setup_toko.php" method="POST" enctype="multipart/form-data" class="card">

<h2>🌿 Daftarkan Toko</h2>

<div class="preview">
    <img id="previewImg">
</div>

<input name="nama_toko" placeholder="Nama Toko" required>
<input name="lokasi" placeholder="Lokasi Toko" required>

<!-- NOMOR WHATSAPP (WAJIB) -->
<input 
    name="no_wa" 
    placeholder="Nomor WhatsApp (contoh: 81234567890) Wajib Diisi!" 
    required
    pattern="08[0-9]{8,11}"
    title="Masukkan nomor WhatsApp yang valid (contoh: 081234567890)">

<textarea name="deskripsi" placeholder="Deskripsi toko"></textarea>

<label>Foto Toko</label>
<input type="file" name="foto" accept="image/*" onchange="preview(this)" required>

<button>Daftarkan Toko</button>

</form>

<script>
function preview(input){
    const img = document.getElementById('previewImg');
    img.src = URL.createObjectURL(input.files[0]);
    img.style.display='block';
}
</script>

</body>
</html>
