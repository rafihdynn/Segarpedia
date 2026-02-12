<?php
session_start();
include '../config/db.php';

$pembeli = $_SESSION['user']['id'];
$penjual = intval($_GET['toko']);

/* Ambil cart */
$q = mysqli_query($conn,"
SELECT 
    c.qty,
    p.nama_produk,
    p.harga
FROM cart c
JOIN produk p ON c.produk_id = p.id
WHERE c.pembeli_id = $pembeli
AND p.penjual_id = $penjual
");

$total = 0;
$items = [];

while($d = mysqli_fetch_assoc($q)){
    $sub = $d['qty'] * $d['harga'];
    $total += $sub;

    $items[] = [
        'nama' => $d['nama_produk'],
        'qty' => $d['qty'],
        'subtotal' => $sub
    ];
}

/* Simpan ke orders */
mysqli_query($conn,"
INSERT INTO orders (pembeli_id, penjual_id, total, status)
VALUES ($pembeli, $penjual, $total, 'pending')
");

$order_id = mysqli_insert_id($conn);

/* Simpan order items */
foreach($items as $i){
    mysqli_query($conn,"
    INSERT INTO order_items (order_id, produk_nama, qty, subtotal)
    VALUES (
        $order_id,
        '{$i['nama']}',
        {$i['qty']},
        {$i['subtotal']}
    )
    ");
}

/* Hapus cart */
mysqli_query($conn,"
DELETE c FROM cart c
JOIN produk p ON c.produk_id = p.id
WHERE c.pembeli_id = $pembeli
AND p.penjual_id = $penjual
");

echo "OK";
