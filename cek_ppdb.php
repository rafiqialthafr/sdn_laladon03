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
        'Diterima' => '<span class="status-badge diterima"><i data-lucide="check-circle-2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"></i>Diterima</span>',
        'Ditolak' => '<span class="status-badge ditolak"><i data-lucide="x-circle" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"></i>Tidak Diterima</span>',
        default => '<span class="status-badge menunggu"><i data-lucide="clock" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"></i>Menunggu Verifikasi</span>',
    };
}
?>

<style>
    .form-control {
        border-radius: 12px;
        border: 1.5px solid var(--gray-border);
        padding: 14px 20px;
        font-size: 1rem;
        transition: all var(--transition-base);
    }
    .form-control:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.15);
    }
    
    .detail-row {
        display: flex;
        border-bottom: 1px solid #f3f4f6;
        padding: 16px 0;
    }
    .detail-row:last-child { border-bottom: none; }
    
    .detail-label {
        width: 200px;
        min-width: 160px;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--gray-mid);
    }
    .detail-value {
        flex: 1;
        font-size: 0.95rem;
        color: var(--navy);
        font-weight: 500;
    }
    
    .not-found-box {
        text-align: center;
        padding: 40px 20px;
        background: #fef2f2;
        border: 1px solid #fca5a5;
        border-radius: 16px;
    }
    .not-found-box p {
        color: #991b1b;
        font-weight: 600;
        margin: 16px 0 0;
    }
    
    .status-badge {
        display: inline-block;
        border-radius: 50px;
        padding: 6px 20px;
        font-weight: 700;
        font-size: 0.9rem;
    }
    .status-badge.diterima { background: #dcfce7; color: #15803d; border: 1.5px solid #86efac; }
    .status-badge.ditolak { background: #fef2f2; color: #b91c1c; border: 1.5px solid #fca5a5; }
    .status-badge.menunggu { background: #fffbeb; color: #92400e; border: 1.5px solid #fde68a; }

    /* Tombol PPDB — override kuning terang */
    .btn-gold-primary {
        background: #FFD700;
        box-shadow: 0 4px 16px rgba(255, 215, 0, 0.45);
    }
    .btn-gold-primary:hover {
        background: #FFC107;
        box-shadow: 0 8px 24px rgba(255, 215, 0, 0.55);
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="geom-shape shape-1"></div>
    <div class="geom-shape shape-2"></div>
    <div class="geom-shape shape-3"></div>

    <div class="container">
        <div class="row page-header-inner">
            <div class="col-12 text-center">
                <h1 class="page-header-title"><i data-lucide="search"
                        style="width:36px;height:36px;vertical-align:middle;margin-right:10px;margin-bottom:6px;"></i>Cek Status PPDB</h1>
                <p class="page-header-subtitle">Pantau perkembangan pendaftaran Anda</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="content-card p-4 p-md-5">

                    <!-- FORM CEK -->
                    <form method="POST" action="cek_ppdb.php">
                        <label class="form-label fw-bold mb-3" style="font-size:0.95rem;color:var(--navy-mid);">
                            Nomor Pendaftaran
                        </label>
                        <div class="d-flex gap-2 flex-column flex-sm-row">
                            <input type="text" name="no_pendaftaran" class="form-control"
                                placeholder="Contoh: PPDB-2025-AB12CD"
                                value="<?php echo htmlspecialchars($no_input); ?>" style="text-transform:uppercase;"
                                required>
                            <button type="submit" class="btn btn-gold-primary border-0" style="padding:14px 28px;">
                                Cek
                            </button>
                        </div>
                    </form>

                    <!-- HASIL -->
                    <?php if ($searched): ?>
                        <hr style="margin:40px 0; border-color:#e5e7eb;">

                        <?php if ($result_ppdb): ?>
                            <div style="margin-bottom:24px;">
                                <span style="font-size:0.85rem;color:var(--gray-mid);font-weight:700;letter-spacing:1px;text-transform:uppercase;">STATUS PENDAFTARAN</span><br>
                                <div style="margin-top:12px;">
                                    <?php echo statusBadge($result_ppdb['status']); ?>
                                </div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">No. Pendaftaran</div>
                                <div class="detail-value" style="font-family:monospace;color:var(--gold-amber);font-weight:800;font-size:1.1rem;">
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
                                <div
                                    style="margin-top:32px;background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;padding:16px 20px;font-size:0.95rem;color:#92400e;display:flex;gap:12px;">
                                    <i data-lucide="clock" style="width:24px;height:24px;flex-shrink:0;"></i>
                                    <div>Pendaftaran Anda sedang dalam proses verifikasi oleh pihak sekolah. Kami akan menghubungi Anda melalui nomor HP yang didaftarkan.</div>
                                </div>
                            <?php elseif ($result_ppdb['status'] === 'Diterima'): ?>
                                <div
                                    style="margin-top:32px;background:#f0fdf4;border:1.5px solid #86efac;border-radius:12px;padding:16px 20px;font-size:0.95rem;color:#15803d;display:flex;gap:12px;">
                                    <i data-lucide="party-popper" style="width:24px;height:24px;flex-shrink:0;"></i>
                                    <div>Selamat! Calon siswa <strong><?php echo htmlspecialchars($result_ppdb['nama_lengkap']); ?></strong> diterima di SDN Laladon 03. Silakan datang ke sekolah untuk proses administrasi selanjutnya.</div>
                                </div>
                            <?php elseif ($result_ppdb['status'] === 'Ditolak'): ?>
                                <div
                                    style="margin-top:32px;background:#fef2f2;border:1.5px solid #fca5a5;border-radius:12px;padding:16px 20px;font-size:0.95rem;color:#b91c1c;display:flex;gap:12px;">
                                    <i data-lucide="x-circle" style="width:24px;height:24px;flex-shrink:0;"></i>
                                    <div>Mohon maaf, calon siswa <strong><?php echo htmlspecialchars($result_ppdb['nama_lengkap']); ?></strong> belum dapat diterima di SDN Laladon 03 pada tahun ajaran ini.</div>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="not-found-box">
                                <i data-lucide="file-warning" style="width:48px;height:48px;color:#fca5a5;margin:0 auto;"></i>
                                <p>Nomor pendaftaran <strong style="color:var(--navy);"><?php echo htmlspecialchars($no_input); ?></strong> tidak
                                    ditemukan.<br>
                                    <span style="font-weight:400;color:#b91c1c;font-size:0.9rem;display:inline-block;margin-top:8px;">Pastikan nomor yang dimasukkan
                                        benar.</span>
                                </p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="mt-5 text-center" style="font-size:0.95rem;color:var(--gray-mid);">
                        Belum mendaftar?
                        <a href="daftar_ppdb.php" style="color:var(--gold-amber);font-weight:700;text-decoration:none;margin-left:4px;">Daftar
                            PPDB Sekarang <i data-lucide="arrow-right" style="width:16px;height:16px;display:inline-block;vertical-align:middle;"></i></a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>