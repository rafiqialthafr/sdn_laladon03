<?php
include 'koneksi.php';
include 'header.php';

$success = false;
$error = '';
$no_pendaftaran = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitasi input
    $nama        = trim(mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']));
    $nisn         = trim(mysqli_real_escape_string($koneksi, $_POST['nisn'] ?? ''));
    $jk          = $_POST['jenis_kelamin'];
    $tmp_lahir   = trim(mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']));

    $tgl_lahir   = $_POST['tanggal_lahir'];
    $agama       = trim(mysqli_real_escape_string($koneksi, $_POST['agama']));
    $alamat      = trim(mysqli_real_escape_string($koneksi, $_POST['alamat']));
    $nama_ayah   = trim(mysqli_real_escape_string($koneksi, $_POST['nama_ayah']));
    $nama_ibu    = trim(mysqli_real_escape_string($koneksi, $_POST['nama_ibu']));
    $pekerjaan   = trim(mysqli_real_escape_string($koneksi, $_POST['pekerjaan_ortu']));
    $no_hp       = trim(mysqli_real_escape_string($koneksi, $_POST['no_hp_ortu']));
    $asal_tk     = trim(mysqli_real_escape_string($koneksi, $_POST['asal_tk'] ?? ''));

    // Validasi dasar
    if (empty($nama) || empty($tmp_lahir) || empty($tgl_lahir) || empty($agama) || empty($alamat)
    || empty($nama_ayah) || empty($nama_ibu) || empty($pekerjaan) || empty($no_hp)) {
        $error = 'Harap isi semua kolom yang wajib diisi (bertanda *).';
    } else {
        // Generate nomor pendaftaran unik: PPDB-TAHUN-XXXX
        $tahun = date('Y');
        $rand  = strtoupper(substr(md5(uniqid()), 0, 6));
        $no_pendaftaran = "PPDB-{$tahun}-{$rand}";

        $sql = "INSERT INTO ppdb 
                (no_pendaftaran, nisn, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, alamat,
                 nama_ayah, nama_ibu, pekerjaan_ortu, no_hp_ortu, asal_tk)
                VALUES 
                ('$no_pendaftaran','$nisn','$nama','$jk','$tmp_lahir','$tgl_lahir','$agama','$alamat',
                 '$nama_ayah','$nama_ibu','$pekerjaan','$no_hp','$asal_tk')";


        if (mysqli_query($koneksi, $sql)) {
            $success = true;
        } else {
            $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    }
}
?>

<style>
/* ── PPDB PAGE STYLES ── */
.ppdb-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
    padding: 80px 0 60px;
    text-align: center;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.ppdb-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('img/foto4.jpeg') center/cover no-repeat;
    opacity: .08;
}
.ppdb-hero .badge-ppdb {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(212,175,55,.15);
    border: 1px solid rgba(212,175,55,.4);
    color: #d4af37;
    border-radius: 50px;
    padding: 6px 18px;
    font-size: .82rem;
    font-weight: 600;
    letter-spacing: .5px;
    margin-bottom: 16px;
}
.ppdb-hero h1 {
    font-size: 2.4rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 12px;
}
.ppdb-hero h1 span { color: #d4af37; }
.ppdb-hero p {
    color: rgba(255,255,255,.75);
    max-width: 560px;
    margin: 0 auto;
    font-size: 1rem;
}

/* Form Card */
.ppdb-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 8px 40px rgba(0,0,0,.10);
    padding: 40px;
    margin-top: -30px;
    position: relative;
    z-index: 2;
}
@media(max-width:576px){ .ppdb-card{ padding:24px 16px; } }

.ppdb-section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1a1a2e;
    border-left: 4px solid #d4af37;
    padding-left: 12px;
    margin: 28px 0 16px;
}
.ppdb-section-title:first-child { margin-top: 0; }

.form-label { font-weight: 600; font-size: .88rem; color: #374151; }
.form-control, .form-select {
    border-radius: 10px;
    border: 1.5px solid #e5e7eb;
    padding: 10px 14px;
    font-size: .92rem;
    transition: border-color .2s, box-shadow .2s;
}
.form-control:focus, .form-select:focus {
    border-color: #d4af37;
    box-shadow: 0 0 0 3px rgba(212,175,55,.15);
    outline: none;
}
.required { color: #e74c3c; }

.btn-ppdb-submit {
    background: linear-gradient(135deg, #d4af37, #b8960c);
    color: #1a1a2e;
    font-weight: 700;
    border: none;
    border-radius: 12px;
    padding: 13px 36px;
    font-size: 1rem;
    cursor: pointer;
    transition: transform .2s, box-shadow .2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-ppdb-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(212,175,55,.35);
}

/* Alert */
.alert-ppdb-error {
    background: #fef2f2;
    border: 1.5px solid #fca5a5;
    color: #991b1b;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 20px;
    font-size: .92rem;
}

/* Success Card */
.ppdb-success {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border: 2px solid #86efac;
    border-radius: 20px;
    padding: 50px 30px;
    text-align: center;
}
.ppdb-success .icon-ok {
    width: 70px; height: 70px;
    background: #16a34a;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    font-size: 2rem;
    color: #fff;
}
.ppdb-success h3 { color: #15803d; font-weight: 800; font-size: 1.6rem; }
.no-daftar-box {
    background: #fff;
    border: 2px dashed #86efac;
    border-radius: 14px;
    padding: 18px 28px;
    display: inline-block;
    margin: 18px 0;
}
.no-daftar-box .label { font-size: .78rem; color: #6b7280; font-weight: 600; letter-spacing: .5px; }
.no-daftar-box .nomor { font-size: 1.6rem; font-weight: 800; color: #15803d; letter-spacing: 2px; }

/* Info box */
.info-ppdb {
    background: #fffbeb;
    border: 1.5px solid #fde68a;
    border-radius: 14px;
    padding: 16px 20px;
    font-size: .88rem;
    color: #92400e;
    margin-bottom: 28px;
}
.info-ppdb ul { margin: 6px 0 0; padding-left: 18px; }
.info-ppdb li { margin-bottom: 4px; }
</style>

<!-- HERO -->
<section class="ppdb-hero">
    <div class="container position-relative">
        <div class="badge-ppdb">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            Penerimaan Peserta Didik Baru
        </div>
        <h1>Pendaftaran <span>PPDB Online</span></h1>
        <p>SDN Laladon 03 &mdash; Tahun Ajaran <?php echo date('Y') . '/' . (date('Y')+1); ?></p>
    </div>
</section>

<section class="py-5" style="background:#f8f9fa;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <?php if ($success): ?>
                <!-- SUCCESS STATE -->
                <div class="ppdb-success">
                    <div class="icon-ok">✓</div>
                    <h3>Pendaftaran Berhasil!</h3>
                    <p class="text-muted mb-0">Pendaftaran PPDB Anda telah kami terima. Simpan nomor pendaftaran berikut:</p>
                    <div class="no-daftar-box">
                        <div class="label">NOMOR PENDAFTARAN</div>
                        <div class="nomor"><?php echo htmlspecialchars($no_pendaftaran); ?></div>
                    </div>
                    <p class="text-muted" style="font-size:.9rem;max-width:440px;margin:0 auto 24px;">
                        Nomor ini digunakan untuk mengecek status pendaftaran Anda. Pihak sekolah akan menghubungi Anda melalui nomor HP yang didaftarkan.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="daftar_ppdb.php" class="btn-ppdb-submit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12l7-7 7 7"/></svg>
                            Daftar Lagi
                        </a>
                        <a href="cek_ppdb.php" style="display:inline-flex;align-items:center;gap:8px;border:2px solid #d4af37;color:#92400e;background:#fff;border-radius:12px;padding:13px 28px;font-weight:700;text-decoration:none;font-size:1rem;">
                            Cek Status Pendaftaran
                        </a>
                    </div>
                </div>

                <?php else: ?>
                <!-- FORM STATE -->
                <div class="ppdb-card">

                    <?php if ($error): ?>
                    <div class="alert-ppdb-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <div class="info-ppdb">
                        📋 <strong>Petunjuk Pengisian:</strong>
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
                                   value="<?php echo htmlspecialchars($_POST['nama_lengkap'] ?? ''); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn" class="form-control"
                                   placeholder="Masukkan NISN"
                                   value="<?php echo htmlspecialchars($_POST['nisn'] ?? ''); ?>"
                                   maxlength="10">
                        </div>


                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kelamin <span class="required">*</span></label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki"  <?php if(($_POST['jenis_kelamin']??'')=='Laki-laki') echo 'selected'; ?>>Laki-laki</option>
                                    <option value="Perempuan"  <?php if(($_POST['jenis_kelamin']??'')=='Perempuan') echo 'selected'; ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Agama <span class="required">*</span></label>
                                <select name="agama" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <?php
                                    $agama_list = ['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'];
                                    foreach ($agama_list as $ag):
                                        $sel = (($_POST['agama']??'') == $ag) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $ag; ?>" <?php echo $sel; ?>><?php echo $ag; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tempat Lahir <span class="required">*</span></label>
                                <input type="text" name="tempat_lahir" class="form-control"
                                       placeholder="Kota kelahiran"
                                       value="<?php echo htmlspecialchars($_POST['tempat_lahir'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Lahir <span class="required">*</span></label>
                                <input type="date" name="tanggal_lahir" class="form-control"
                                       max="<?php echo date('Y-m-d', strtotime('-5 years')); ?>"
                                       value="<?php echo htmlspecialchars($_POST['tanggal_lahir'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap <span class="required">*</span></label>
                            <textarea name="alamat" class="form-control" rows="3"
                                      placeholder="Jalan, RT/RW, Desa/Kelurahan, Kecamatan, Kota"
                                      required><?php echo htmlspecialchars($_POST['alamat'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Asal TK / RA (jika ada)</label>
                            <input type="text" name="asal_tk" class="form-control"
                                   placeholder="Kosongkan jika tidak pernah TK"
                                   value="<?php echo htmlspecialchars($_POST['asal_tk'] ?? ''); ?>">
                        </div>

                        <!-- DATA ORANG TUA -->
                        <div class="ppdb-section-title">👨‍👩‍👦 Data Orang Tua / Wali</div>

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
                                <label class="form-label">Pekerjaan Orang Tua <span class="required">*</span></label>
                                <select name="pekerjaan_ortu" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <?php
                                    $pekerjaan_list = ['PNS','TNI/Polri','Swasta','Wiraswasta','Petani','Buruh','Lainnya'];
                                    foreach ($pekerjaan_list as $pk):
                                        $sel = (($_POST['pekerjaan_ortu']??'') == $pk) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $pk; ?>" <?php echo $sel; ?>><?php echo $pk; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. HP Orang Tua / Wali <span class="required">*</span></label>
                                <input type="tel" name="no_hp_ortu" class="form-control"
                                       placeholder="Contoh: 08123456789"
                                       value="<?php echo htmlspecialchars($_POST['no_hp_ortu'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn-ppdb-submit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                Kirim Pendaftaran
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