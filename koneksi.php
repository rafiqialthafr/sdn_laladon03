<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "laladon";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Pastikan koneksi menggunakan charset UTF-8 untuk mencegah karakter rusak di hosting
mysqli_set_charset($koneksi, 'utf8mb4');
?>