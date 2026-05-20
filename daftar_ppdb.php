<?php
include 'koneksi.php';

// Reset pendaftaran jika klik "Daftar Lagi"
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    setcookie('sukses_ppdb', '', time() - 3600, '/');
    setcookie('no_pendaftaran', '', time() - 3600, '/');
    header("Location: daftar_ppdb.php");
    exit;
}

$success = false;
$error = '';
$no_pendaftaran = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitasi input
    $nama = trim(mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']));
    $nisn = trim(mysqli_real_escape_string($koneksi, $_POST['nisn'] ?? ''));
    $jk = $_POST['jenis_kelamin'];
    $tmp_lahir = trim(mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']));

    $tgl_lahir = $_POST['tanggal_lahir'];
    $agama = trim(mysqli_real_escape_string($koneksi, $_POST['agama']));
    $alamat = trim(mysqli_real_escape_string($koneksi, $_POST['alamat']));
    $nama_ayah = trim(mysqli_real_escape_string($koneksi, $_POST['nama_ayah']));
    $nama_ibu = trim(mysqli_real_escape_string($koneksi, $_POST['nama_ibu']));
    $pekerjaan = trim(mysqli_real_escape_string($koneksi, $_POST['pekerjaan'] ?? ''));
    $no_hp = trim(mysqli_real_escape_string($koneksi, $_POST['no_hp_ortu']));
    $asal_tk = trim(mysqli_real_escape_string($koneksi, $_POST['asal_tk'] ?? ''));

    // Validasi dasar
    if (
        empty($nama) || empty($tmp_lahir) || empty($tgl_lahir) || empty($agama) || empty($alamat)
        || empty($nama_ayah) || empty($nama_ibu) || empty($pekerjaan) || empty($no_hp)
    ) {
        $error = 'Harap isi semua kolom yang wajib diisi (bertanda *).';
    } else {
        // Generate nomor pendaftaran unik: PPDB-TAHUN-XXXX
        $tahun = date('Y');
        $rand = strtoupper(substr(md5(uniqid()), 0, 6));
        $no_pendaftaran = "PPDB-{$tahun}-{$rand}";

        $sql = "INSERT INTO ppdb 
                (no_pendaftaran, nisn, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, alamat,
                 nama_ayah, nama_ibu, pekerjaan_ortu, no_hp_ortu, asal_tk)
                VALUES 
                ('$no_pendaftaran','$nisn','$nama','$jk','$tmp_lahir','$tgl_lahir','$agama','$alamat',
                 '$nama_ayah','$nama_ibu','$pekerjaan','$no_hp','$asal_tk')";

        if (mysqli_query($koneksi, $sql)) {
            setcookie('sukses_ppdb', '1', time() + (10 * 365 * 24 * 60 * 60), '/'); // 10 thn
            setcookie('no_pendaftaran', $no_pendaftaran, time() + (10 * 365 * 24 * 60 * 60), '/');
            header("Location: daftar_ppdb.php");
            exit;
        } else {
            $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    }
}

// Cek status
if (isset($_COOKIE['sukses_ppdb']) && $_COOKIE['sukses_ppdb'] == '1') {
    $success = true;
    $no_pendaftaran = $_COOKIE['no_pendaftaran'] ?? '';
}

include 'header.php';
?>



<!-- Page Header -->
<section class="page-header">
    <div class="geom-shape shape-1"></div>
    <div class="geom-shape shape-2"></div>
    <div class="geom-shape shape-3"></div>

    <div class="container">
        <div class="row page-header-inner">
            <div class="col-12 text-center">
                <h1 class="page-header-title"><i data-lucide="clipboard-edit"></i>Pendaftaran PPDB</h1>
                <p class="page-header-subtitle">SDN Laladon 03 - Tahun Ajaran
                    <?php echo date('Y') . '/' . (date('Y') + 1); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <?php if ($success): ?>
                    <!-- SUCCESS STATE -->
                    <div class="content-card daftar-ppdb-card ppdb-success-wrap p-4 p-md-5">
                        <div class="icon-ok-wrap">
                            <i data-lucide="check" class="success-heading-icon"></i>
                        </div>
                        <h3 class="success-heading">Pendaftaran Berhasil!</h3>
                        <p class="text-muted mb-0">Pendaftaran PPDB Anda telah kami terima. Simpan nomor pendaftaran berikut:</p>
                        <div class="no-daftar-box">
                            <div class="label">NOMOR PENDAFTARAN</div>
                            <div class="nomor"><?php echo htmlspecialchars($no_pendaftaran); ?></div>
                        </div>
                        <p class="text-muted success-message-text">
                            Nomor ini digunakan untuk mengecek status pendaftaran Anda. Pihak sekolah akan menghubungi Anda melalui nomor HP yang didaftarkan.
                        </p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="daftar_ppdb.php?action=reset" class="btn btn-gold-primary">
                                <i data-lucide="rotate-ccw"></i>Daftar Lagi
                            </a>
                            <a href="cek_ppdb.php" class="btn btn-gold-outline">
                                <i data-lucide="search"></i>Cek Status Pendaftaran
                            </a>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- FORM STATE -->
                    <div class="content-card daftar-ppdb-card p-4 p-md-5">

                        <?php if ($error): ?>
                            <div class="alert-ppdb-error">
                                <i data-lucide="alert-triangle"></i>
                                <span><?php echo htmlspecialchars($error); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="info-ppdb">
                            <div class="info-ppdb-title-wrap">
                                <i data-lucide="info"></i>Petunjuk Pengisian:
                            </div>
                            <ul>
                                <li>Isi semua kolom yang bertanda <span class="required">*</span> dengan lengkap dan benar.</li>
                                <li>Pastikan nomor HP orang tua aktif &mdash; akan dihubungi oleh pihak sekolah.</li>
                                <li>Simpan nomor pendaftaran yang muncul setelah mendaftar.</li>
                            </ul>
                        </div>

                        <form method="POST" action="daftar_ppdb.php" novalidate>

                            <!-- DATA CALON SISWA -->
                            <div class="ppdb-section-title">👦 Data Calon Siswa</div>

                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                                <input type="text" name="nama_lengkap" class="form-control"
                                    placeholder="Sesuai akta kelahiran"
                                    value="<?php echo htmlspecialchars($_POST['nama_lengkap'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">NISN</label>
                                <input type="text" name="nisn" class="form-control" placeholder="Masukkan NISN"
                                    value="<?php echo htmlspecialchars($_POST['nisn'] ?? ''); ?>" maxlength="10">
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Kelamin <span class="required">*</span></label>
                                    <select name="jenis_kelamin" class="form-select" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="Laki-laki" <?php if (($_POST['jenis_kelamin'] ?? '') == 'Laki-laki')
                                            echo 'selected'; ?>>Laki-laki</option>
                                        <option value="Perempuan" <?php if (($_POST['jenis_kelamin'] ?? '') == 'Perempuan')
                                            echo 'selected'; ?>>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Agama <span class="required">*</span></label>
                                    <select name="agama" class="form-select" required>
                                        <option value="">-- Pilih --</option>
                                        <?php
                                        $agama_list = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
                                        foreach ($agama_list as $ag):
                                            $sel = (($_POST['agama'] ?? '') == $ag) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo $ag; ?>" <?php echo $sel; ?>><?php echo $ag; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tempat Lahir <span class="required">*</span></label>
                                    <input type="text" name="tempat_lahir" class="form-control" placeholder="Kota kelahiran"
                                        value="<?php echo htmlspecialchars($_POST['tempat_lahir'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Lahir <span class="required">*</span></label>
                                    <input type="date" name="tanggal_lahir" class="form-control"
                                        max="<?php echo date('Y-m-d', strtotime('+0 years')); ?>"
                                        value="<?php echo htmlspecialchars($_POST['tanggal_lahir'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat Lengkap <span class="required">*</span></label>
                                <textarea name="alamat" class="form-control" rows="3"
                                    placeholder="Masukkan alamat lengkap (Nama Jalan, Blok/No. Rumah, atau RT/RW jika ada) sesuai Kartu Keluarga"
                                    required><?php echo htmlspecialchars($_POST['alamat'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Asal TK / RA (jika ada)</label>
                                <input type="text" name="asal_tk" class="form-control"
                                    placeholder="Kosongkan jika tidak pernah TK"
                                    value="<?php echo htmlspecialchars($_POST['asal_tk'] ?? ''); ?>">
                            </div>

                            <!-- DATA ORANG TUA -->
                            <div class="ppdb-section-title mt-5">👨‍👩‍👦 Data Orang Tua / Wali</div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Ayah <span class="required">*</span></label>
                                    <input type="text" name="nama_ayah" class="form-control"
                                        value="<?php echo htmlspecialchars($_POST['nama_ayah'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nama Ibu <span class="required">*</span></label>
                                    <input type="text" name="nama_ibu" class="form-control"
                                        value="<?php echo htmlspecialchars($_POST['nama_ibu'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Pekerjaan Orang Tua / Wali<span
                                            class="required">*</span></label>
                                    <input type="text" name="pekerjaan" class="form-control"
                                        placeholder="Contoh: Karyawan, Buruh"
                                        value="<?php echo htmlspecialchars($_POST['pekerjaan'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No. HP Orang Tua / Wali <span
                                            class="required">*</span></label>
                                    <input type="tel" name="no_hp_ortu" class="form-control"
                                        placeholder="Contoh: 08123456789"
                                        value="<?php echo htmlspecialchars($_POST['no_hp_ortu'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-5">
                                <button type="submit" class="btn btn-gold-primary border-0 w-100 w-sm-auto">
                                    <i data-lucide="send"></i>Kirim Pendaftaran
                                </button>
                            </div>

                        </form>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>