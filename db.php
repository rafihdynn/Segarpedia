<?php
$conn = mysqli_connect("localhost", "root", "", "segarpedia");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
