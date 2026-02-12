<?php
session_start();
include '../config/db.php';

$penjual = $_SESSION['user']['id'];
$nama = $_POST['nama'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];

$gambar = $_FILES['gambar']['name'];
$tmp = $_FILES['gambar']['tmp_name'];
move_uploaded_file($tmp,"../assets/img/".$gambar);

mysqli_query($conn,"
INSERT INTO produk (nama_produk,harga,stok,gambar,penjual_id)
VALUES ('$nama',$harga,$stok,'$gambar',$penjual)
");

header("Location: dashboard.php");
