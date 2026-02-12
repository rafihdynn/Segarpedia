<?php
session_start();
include '../config/db.php';

$user_id = $_SESSION['user']['id'];
$nama = $_POST['nama_toko'];
$lokasi = $_POST['lokasi'];
$desk = $_POST['deskripsi'];

$foto = $_FILES['foto']['name'];
$tmp = $_FILES['foto']['tmp_name'];
move_uploaded_file($tmp,"../assets/img/".$foto);

mysqli_query($conn,"
INSERT INTO toko (user_id,nama_toko,lokasi,deskripsi,foto)
VALUES ($user_id,'$nama','$lokasi','$desk','$foto')
");

header("Location: dashboard.php");
