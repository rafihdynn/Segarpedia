<?php
session_start();
include '../config/db.php';

$id = $_SESSION['user']['id'];
$nama = $_POST['nama'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];

$gambar = $_FILES['gambar']['name'];
$tmp = $_FILES['gambar']['tmp_name'];

move_uploaded_file($tmp,"../assets/img/$gambar");

mysqli_query($conn,"INSERT INTO produk 
(penjual_id,nama_produk,harga,stok,gambar)
VALUES ($id,'$nama',$harga,$stok,'$gambar')");

header("Location: dashboard.php");
