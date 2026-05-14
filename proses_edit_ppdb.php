<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

header('Content-Type: text/html; charset=UTF-8');

include 'koneksi.php';

/** @var mysqli $koneksi */

$id = isset($_POST['id_pendaftar']) ? (int)$_POST['id_pendaftar'] : 0;
if ($id <= 0) {
    header("Location: admin_ppdb.php");
    exit;
}

$nisn         = trim($_POST['nisn'] ?? '');
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
$asal_tk      = trim($_POST['asal_tk'] ?? '');

// Auto-update: jika field "jenis_kelamin" dikirim kosong,
// ambil jenis_kelamin lama dari database supaya tidak berubah karena hanya edit nama.
$stmt_old = mysqli_prepare($koneksi, "SELECT jenis_kelamin FROM ppdb WHERE id=? LIMIT 1");
if ($stmt_old) {
    mysqli_stmt_bind_param($stmt_old, 'i', $id);
    mysqli_stmt_execute($stmt_old);
    $res_old = mysqli_stmt_get_result($stmt_old);
    $row_old = mysqli_fetch_assoc($res_old);
    $old_jenis_kelamin = $row_old['jenis_kelamin'] ?? '';
    mysqli_stmt_close($stmt_old);
} else {
    $old_jenis_kelamin = '';
}

// Jika nilai dari form kosong, pertahankan nilai lama.
// (Di form saat ini, jenis_kelamin memang tidak ada inputnya, jadi biasanya kosong.)
if (trim($jenis_kelamin) === '' && $old_jenis_kelamin !== '') {
    $jenis_kelamin = $old_jenis_kelamin;
}


$no_hp_ortu   = trim($_POST['no_hp_ortu'] ?? '');
$status       = $_POST['status'] ?? 'Menunggu';

$allowed_status = ['Menunggu', 'Diterima', 'Ditolak'];
if (!in_array($status, $allowed_status, true)) {
    $status = 'Menunggu';
}

// Update kolom sesuai tabel ppdb (kolom nisn bisa sudah ada / ditambahkan manual)
$sql = "UPDATE ppdb SET 
            nisn=?,
            nama_lengkap=?,
            jenis_kelamin=?,
            asal_tk=?,
            no_hp_ortu=?,
            status=?
        WHERE id=?";

$stmt = mysqli_prepare($koneksi, $sql);
if (!$stmt) {
    header("Location: admin_ppdb.php");
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    'ssssssi',
    $nisn,
    $nama_lengkap,
    $jenis_kelamin,
    $asal_tk,
    $no_hp_ortu,
    $status,
    $id
);

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header('Location: admin_ppdb.php');
exit;

