<?php
include '../config/db.php';

$nama  = trim($_POST['nama']);
$email = trim($_POST['email']);
$pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role  = $_POST['role'];

$query = "
INSERT INTO users (nama, email, password, role)
VALUES ('$nama', '$email', '$pass', '$role')
";

if (mysqli_query($conn, $query)) {
    header("Location: login.php?register=success");
    exit;
} else {
    die(mysqli_error($conn));
}
