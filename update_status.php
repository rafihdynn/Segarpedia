<?php
session_start();
include '../config/db.php';

$id = intval($_GET['id']);
$status = $_GET['s'];

mysqli_query($conn,"
    UPDATE orders SET status='$status' WHERE id=$id
");

$_SESSION['notif']=[
    'type'=>'success',
    'message'=>'Status pesanan diperbarui'
];

header("Location: pesanan.php");
