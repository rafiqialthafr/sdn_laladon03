<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = mysqli_real_escape_string($koneksi, trim($_POST['nama'] ?? ''));
    $wa     = mysqli_real_escape_string($koneksi, trim($_POST['wa_pengirim'] ?? ''));
    $subjek = mysqli_real_escape_string($koneksi, trim($_POST['subjek'] ?? ''));
    $pesan  = mysqli_real_escape_string($koneksi, trim($_POST['pesan'] ?? ''));

    if (!empty($nama) && !empty($wa) && !empty($pesan)) {
        $query = "INSERT INTO contact_messages (nama, email, subjek, pesan) VALUES ('$nama', '$wa', '$subjek', '$pesan')";
        mysqli_query($koneksi, $query);
    }

    $WA_NUMBER = '6289531497117';
    $teks = "*Pesan dari Website SDN Laladon 03*%0A" .
            "--------------------------------%0A" .
            "*Nama:* " . $nama . "%0A" .
            "*Subjek:* " . $subjek . "%0A" .
            "*Pesan:* " . $pesan;

    header("Location: https://wa.me/{$WA_NUMBER}?text={$teks}");
    exit;
}
?>
