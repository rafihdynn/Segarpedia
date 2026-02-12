<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Produk</title>
<style>
body{
    font-family:'Segoe UI';
    background:#e8f5e9;
    display:flex;justify-content:center;
}
.card{
    background:#fff;padding:30px 40px;
    border-radius:15px;
    box-shadow:0 8px 25px rgba(0,0,0,.15);
    width:100%;max-width:450px;
}
.card input,.card select,.card textarea,.card button{
    width:100%;padding:12px;margin-bottom:15px;
    border-radius:8px;border:1px solid #ccc;
}
.card button{
    background:#4caf50;color:white;
    border:none;font-weight:bold;
}
</style>
</head>
<body>

<form action="process_tambah_produk.php" method="POST" enctype="multipart/form-data" class="card">
<h2 style="text-align:center;color:#2e7d32">Tambah Produk</h2>

<input name="nama" placeholder="Nama Produk" required>
<input type="number" name="harga" placeholder="Harga" required>
<input type="number" name="stok" placeholder="Stok" required>

<select name="kategori" required>
    <option value="">-- Pilih Kategori --</option>
    <option value="Sayur">Sayur</option>
    <option value="Buah">Buah</option>
    <option value="Daging">Daging</option>
    <option value="Minuman">Minuman</option>
</select>

<textarea name="keterangan" placeholder="Deskripsi produk" required></textarea>
<input type="file" name="gambar" required>

<button>Simpan</button>
<a href="dashboard.php" style="text-align:center;display:block">⬅ Kembali</a>
</form>

</body>
</html>
