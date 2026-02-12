<?php
session_start();
include '../config/db.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pembeli'){
    echo json_encode(['status'=>'error']);
    exit;
}

$pembeli_id = $_SESSION['user']['id'];
$produk_id  = intval($_GET['id'] ?? 0);

if($produk_id <= 0){
    echo json_encode(['status'=>'error']);
    exit;
}

/* CEK STOK */
$p = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT stok FROM produk WHERE id=$produk_id")
);

if(!$p || $p['stok'] <= 0){
    echo json_encode(['status'=>'habis']);
    exit;
}

/* CEK CART */
$cek = mysqli_query($conn,"
    SELECT id, qty 
    FROM cart 
    WHERE pembeli_id=$pembeli_id 
    AND produk_id=$produk_id
");

if(mysqli_num_rows($cek) > 0){
    $c = mysqli_fetch_assoc($cek);
    if($c['qty'] < $p['stok']){
        mysqli_query($conn,"
            UPDATE cart SET qty = qty + 1 WHERE id={$c['id']}
        ");
    }
}else{
    mysqli_query($conn,"
        INSERT INTO cart (pembeli_id, produk_id, qty)
        VALUES ($pembeli_id, $produk_id, 1)
    ");
}

echo json_encode(['status'=>'ok']);
