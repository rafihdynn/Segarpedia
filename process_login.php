<?php
include '../config/session.php';
include '../config/db.php';

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = $_POST['password'];

$q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$user = mysqli_fetch_assoc($q);

if ($user && password_verify($password, $user['password'])) {

    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id'    => $user['id'],
        'email' => $user['email'],
        'role'  => $user['role']
    ];

    $_SESSION['LAST_ACTIVITY'] = time();

    if ($user['role'] == 'admin')   header("Location: ../admin/dashboard.php");
    if ($user['role'] == 'penjual') header("Location: ../penjual/dashboard.php");
    if ($user['role'] == 'pembeli') header("Location: ../pembeli/dashboard.php");
    exit;

} else {
    echo "<script>
        alert('Email atau password salah!');
        window.location.href = '../auth/login.php';
    </script>";
}

