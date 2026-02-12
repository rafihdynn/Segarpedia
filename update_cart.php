<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['user'])){
    exit;
}

$id = (int)$_GET['id'];
$aksi = $_GET['aksi'];

if($aksi == 'tambah'){
    mysqli_query($conn,"UPDATE cart SET qty = qty + 1 WHERE id=$id");
}

if($aksi == 'kurang'){
    mysqli_query($conn,"UPDATE cart SET qty = qty - 1 WHERE id=$id");
    mysqli_query($conn,"DELETE FROM cart WHERE id=$id AND qty <= 0");
}

header("Location: cart.php");
