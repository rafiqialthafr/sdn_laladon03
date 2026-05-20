<?php
include 'koneksi.php';
include 'header.php';

$result_ppdb = null;
$searched = false;
$no_input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_input = trim(mysqli_real_escape_string($koneksi, $_POST['no_pendaftaran'] ?? ''));
    $searched = true;

    if (!empty($no_input)) {
        $sql = "SELECT * FROM ppdb WHERE no_pendaftaran = '$no_input' LIMIT 1";
        $res = mysqli_query($koneksi, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            $result_ppdb = mysqli_fetch_assoc($res);
        }
    }
}

function statusBadge($status)
{
    return match ($status) {
        'Diterima' => '<span class="status-badge diterima"><i data-lucide="check-circle-2"></i>Diterima</span>',
        'Ditolak' => '<span class="status-badge ditolak"><i data-lucide="x-circle"></i>Tidak Diterima</span>',
        default => '<span class="status-badge menunggu"><i data-lucide="clock"></i>Menunggu Verifikasi</span>',
    };
}
?>



<!-- Page Header -->
<section class="page-header">
    <div class="geom-shape shape-1"></div>
    <div class="geom-shape shape-2"></div>
    <div class="geom-shape shape-3"></div>

    <div class="container">
        <div class="row page-header-inner">
            <div class="col-12 text-center">
                <h1 class="page-header-title"><i data-lucide="search"></i>Cek Status PPDB</h1>
                <p class="page-header-subtitle">Pantau perkembangan pendaftaran Anda</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="content-card cek-ppdb-card p-4 p-md-5">

                    <!-- FORM CEK -->
                    <form method="POST" action="cek_ppdb.php">
                        <label class="form-label fw-bold mb-3">
                            Nomor Pendaftaran
                        </label>
                        <div class="d-flex gap-2 flex-column flex-sm-row">
                            <input type="text" name="no_pendaftaran" class="form-control"
                                placeholder="Contoh: PPDB-2025-AB12CD"
                                value="<?php echo htmlspecialchars($no_input); ?>"
                                required>
                            <button type="submit" class="btn btn-gold-primary border-0">
                                Cek
                            </button>
                        </div>
                    </form>

                    <!-- HASIL -->
                    <?php if ($searched): ?>
                        <hr>

                        <?php if ($result_ppdb): ?>
                            <div class="status-title-wrap">
                                <span class="status-label-title">STATUS PENDAFTARAN</span><br>
                                <div class="status-badge-container">
                                    <?php echo statusBadge($result_ppdb['status']); ?>
                                </div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">No. Pendaftaran</div>
                                <div class="detail-value registration-no">
                                    <?php echo htmlspecialchars($result_ppdb['no_pendaftaran']); ?>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Nama Lengkap</div>
                                <div class="detail-value"><?php echo htmlspecialchars($result_ppdb['nama_lengkap']); ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Jenis Kelamin</div>
                                <div class="detail-value"><?php echo htmlspecialchars($result_ppdb['jenis_kelamin']); ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Tempat, Tanggal Lahir</div>
                                <div class="detail-value">
                                    <?php echo htmlspecialchars($result_ppdb['tempat_lahir']) . ', '
                                        . date('d F Y', strtotime($result_ppdb['tanggal_lahir'])); ?>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Nama Ayah</div>
                                <div class="detail-value"><?php echo htmlspecialchars($result_ppdb['nama_ayah']); ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Nama Ibu</div>
                                <div class="detail-value"><?php echo htmlspecialchars($result_ppdb['nama_ibu']); ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">No. HP Orang Tua</div>
                                <div class="detail-value"><?php echo htmlspecialchars($result_ppdb['no_hp_ortu']); ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Tanggal Daftar</div>
                                <div class="detail-value">
                                    <?php echo date('d F Y, H:i', strtotime($result_ppdb['created_at'])); ?> WIB
                                </div>
                            </div>

                            <?php if ($result_ppdb['status'] === 'Menunggu'): ?>
                                <div class="info-alert-box status-waiting">
                                    <i data-lucide="clock"></i>
                                    <div>Pendaftaran Anda sedang dalam proses verifikasi oleh pihak sekolah. Kami akan menghubungi Anda melalui nomor HP yang didaftarkan.</div>
                                </div>
                            <?php elseif ($result_ppdb['status'] === 'Diterima'): ?>
                                <div class="info-alert-box status-approved">
                                    <i data-lucide="party-popper"></i>
                                    <div>Selamat! Calon siswa <strong><?php echo htmlspecialchars($result_ppdb['nama_lengkap']); ?></strong> diterima di SDN Laladon 03. Silakan datang ke sekolah untuk proses administrasi selanjutnya.</div>
                                </div>
                            <?php elseif ($result_ppdb['status'] === 'Ditolak'): ?>
                                <div class="info-alert-box status-rejected">
                                    <i data-lucide="x-circle"></i>
                                    <div>Mohon maaf, calon siswa <strong><?php echo htmlspecialchars($result_ppdb['nama_lengkap']); ?></strong> belum dapat diterima di SDN Laladon 03 pada tahun ajaran ini.</div>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="not-found-box">
                                <i data-lucide="file-warning"></i>
                                <p>Nomor pendaftaran <strong style="color:var(--navy);"><?php echo htmlspecialchars($no_input); ?></strong> tidak ditemukan.<br>
                                    <span class="hint-text">Pastikan nomor yang dimasukkan benar.</span>
                                </p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="card-footer-note">
                        Belum mendaftar?
                        <a href="daftar_ppdb.php">Daftar PPDB Sekarang <i data-lucide="arrow-right"></i></a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>