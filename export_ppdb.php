<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: masuk-admin-03.php");
    exit;
}
include 'koneksi.php';

// Set headers for excel download
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=data_ppdb_sdn_laladon03_" . date('Ymd_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$status_filter = $_GET['status'] ?? '';
$query = "SELECT * FROM ppdb";
if ($status_filter && in_array($status_filter, ['Menunggu', 'Diterima', 'Ditolak'])) {
    $query .= " WHERE status = '" . mysqli_real_escape_string($koneksi, $status_filter) . "'";
}
$query .= " ORDER BY created_at DESC";
$res = mysqli_query($koneksi, $query);
?>
<table border="1">
    <thead>
        <tr style="background-color: #1a1a2e; color: #ffffff; font-weight: bold; height: 35px;">
            <th>#</th>
            <th>No. Pendaftaran</th>
            <th>NISN</th>
            <th>Nama Lengkap</th>
            <th>Jenis Kelamin</th>
            <th>Tempat Lahir</th>
            <th>Tanggal Lahir</th>
            <th>Agama</th>
            <th>Alamat Lengkap</th>
            <th>Nama Ayah</th>
            <th>Nama Ibu</th>
            <th>Pekerjaan Ortu</th>
            <th>No. HP Ortu</th>
            <th>Asal TK / RA</th>
            <th>Status Kelulusan</th>
            <th>Tanggal Daftar</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        while ($row = mysqli_fetch_assoc($res)): 
        ?>
        <tr style="height: 25px;">
            <td align="center"><?php echo $no++; ?></td>
            <!-- Style '@' forces Excel to treat numbers as text, preserving leading zeros -->
            <td style="vnd.ms-excel.numberformat:@"><?php echo htmlspecialchars($row['no_pendaftaran']); ?></td>
            <td style="vnd.ms-excel.numberformat:@"><?php echo htmlspecialchars($row['nisn'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
            <td><?php echo htmlspecialchars($row['jenis_kelamin']); ?></td>
            <td><?php echo htmlspecialchars($row['tempat_lahir']); ?></td>
            <td align="center"><?php echo date('d-m-Y', strtotime($row['tanggal_lahir'])); ?></td>
            <td><?php echo htmlspecialchars($row['agama']); ?></td>
            <td><?php echo htmlspecialchars($row['alamat']); ?></td>
            <td><?php echo htmlspecialchars($row['nama_ayah']); ?></td>
            <td><?php echo htmlspecialchars($row['nama_ibu'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($row['pekerjaan_ortu'] ?? ''); ?></td>
            <td style="vnd.ms-excel.numberformat:@"><?php echo htmlspecialchars($row['no_hp_ortu'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($row['asal_tk'] ?? ''); ?></td>
            <td align="center" style="font-weight: bold;"><?php echo htmlspecialchars($row['status']); ?></td>
            <td align="center"><?php echo date('d-m-Y H:i', strtotime($row['created_at'])); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
