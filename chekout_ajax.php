<?php
session_start();
include '../config/db.php';

/* =====================
   VALIDASI LOGIN
===================== */
if(!isset($_SESSION['user'])){
    http_response_code(401);
    echo "NOT_LOGIN";
    exit;
}

$pembeli = intval($_SESSION['user']['id']);
$penjual = isset($_GET['toko']) ? intval($_GET['toko']) : 0;

if($penjual <= 0){
    http_response_code(400);
    echo "TOKO_INVALID";
    exit;
}

/* =====================
   AMBIL CART
===================== */
$cart = mysqli_query($conn,"
    SELECT 
        c.*, 
        p.nama_produk, 
        p.harga 
    FROM cart c
    JOIN produk p ON c.produk_id = p.id
    WHERE c.pembeli_id = $pembeli
    AND p.penjual_id = $penjual
");

if(mysqli_num_rows($cart) == 0){
    echo "CART_EMPTY";
    exit;
}

$kode  = 'ORD'.date('YmdHis').rand(100,999);
$total = 0;
$data  = [];

while($c = mysqli_fetch_assoc($cart)){
    $sub = $c['harga'] * $c['qty'];
    $total += $sub;
    $data[] = $c;
}

/* =====================
   TRANSAKSI DATABASE
===================== */
mysqli_begin_transaction($conn);

try {

    /* SIMPAN ORDER */
    mysqli_query($conn,"
        INSERT INTO orders 
        (kode_order, pembeli_id, penjual_id, total, status, created_at)
        VALUES
        ('$kode', $pembeli, $penjual, $total, 'menunggu', NOW())
    ");

    $order_id = mysqli_insert_id($conn);

    /* DETAIL ORDER */
    foreach($data as $d){
        mysqli_query($conn,"
            INSERT INTO order_items
            (order_id, produk_id, nama_produk, harga, qty)
            VALUES
            (
                $order_id,
                {$d['produk_id']},
                '".mysqli_real_escape_string($conn,$d['nama_produk'])."',
                {$d['harga']},
                {$d['qty']}
            )
        ");
    }

    /* HAPUS CART */
    mysqli_query($conn,"
        DELETE c FROM cart c
        JOIN produk p ON c.produk_id = p.id
        WHERE c.pembeli_id = $pembeli
        AND p.penjual_id = $penjual
    ");

    mysqli_commit($conn);

    /* NOTIFIKASI */
    $_SESSION['notif'] = [
        'type'    => 'success',
        'message' => 'Pesanan berhasil dibuat. Silakan lanjutkan via WhatsApp.'
    ];

    echo "OK";

} catch(Exception $e){
    mysqli_rollback($conn);
    http_response_code(500);
    echo "FAILED";
}
