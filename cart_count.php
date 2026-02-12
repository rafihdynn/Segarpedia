<?php
session_start();
include '../config/db.php';

$user_id = $_SESSION['user']['id'];

$query = mysqli_query($conn, "
SELECT COALESCE(SUM(qty), 0) AS total
FROM cart
WHERE pembeli_id = $user_id
");

$data = mysqli_fetch_assoc($query);
echo $data['total'];
