<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['user'])){
    exit;
}

$id = (int)$_GET['id'];
mysqli_query($conn,"DELETE FROM cart WHERE id=$id");

header("Location: cart.php");
