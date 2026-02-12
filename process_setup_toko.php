<?php
session_start();
include '../config/db.php';

$id = $_SESSION['user']['id'];
$nama = $_POST['nama_toko'];
$lokasi = $_POST['lokasi'];
$desk = $_POST['deskripsi'];

$foto = $_FILES['foto']['name'];
$tmp  = $_FILES['foto']['tmp_name'];

move_uploaded_file($tmp,"../assets/img/$foto");

/* ================= KODE LAMA (TETAP) ================= */
mysqli_query($conn,"INSERT INTO toko 
(user_id,nama_toko,lokasi,deskripsi,foto)
VALUES ($id,'$nama','$lokasi','$desk','$foto')");
/* ==================================================== */


/* ============ TAMBAHAN NOMOR WHATSAPP ============ */
$no_wa = $_POST['no_wa'] ?? '';

if (empty($no_wa)) {
    die("Nomor WhatsApp wajib diisi!");
}

// validasi format WA Indonesia
if (!preg_match('/^08[0-9]{8,11}$/', $no_wa)) {
    die("Nomor WhatsApp tidak valid!");
}

// normalisasi: 08xxx -> 62xxx
$no_wa = preg_replace('/[^0-9]/', '', $no_wa);
if (substr($no_wa, 0, 2) === '08') {
    $no_wa = '62' . substr($no_wa, 1);
}

// simpan WA ke toko (UPDATE, tidak mengubah INSERT lama)
mysqli_query($conn,"
    UPDATE toko 
    SET no_wa = '$no_wa'
    WHERE user_id = $id
");
/* ================================================ */

header("Location: dashboard.php");
