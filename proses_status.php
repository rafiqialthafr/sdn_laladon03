<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

/** @var mysqli $koneksi */

$redirect = 'admin_ppdb.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) ? (string)$_GET['status'] : '';

$allowed = ['Diterima', 'Ditolak'];
if ($id <= 0 || !in_array($status, $allowed, true)) {
    header("Location: {$redirect}");
    exit;
}

$stmt = mysqli_prepare($koneksi, "UPDATE ppdb SET status=? WHERE id=?");
mysqli_stmt_bind_param($stmt, 'si', $status, $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: {$redirect}");
exit;

