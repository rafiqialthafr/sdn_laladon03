<?php
include 'koneksi.php';
include 'header.php';

$result_ppdb = null;
$searched    = false;
$no_input    = '';

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

function statusBadge($status) {
    return match($status) {
        'Diterima' => '<span style="background:#dcfce7;color:#15803d;border:1.5px solid #86efac;border-radius:50px;padding:5px 16px;font-weight:700;font-size:.88rem;">✓ Diterima</span>',
        'Ditolak'  => '<span style="background:#fef2f2;color:#b91c1c;border:1.5px solid #fca5a5;border-radius:50px;padding:5px 16px;font-weight:700;font-size:.88rem;">✗ Tidak Diterima</span>',
        default    => '<span style="background:#fffbeb;color:#92400e;border:1.5px solid #fde68a;border-radius:50px;padding:5px 16px;font-weight:700;font-size:.88rem;">⏳ Menunggu Verifikasi</span>',
    };
}
?>

<style>
.ppdb-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
    padding: 80px 0 60px; text-align: center; color: #fff; position: relative; overflow: hidden;
}
.ppdb-hero::before {
    content:''; position:absolute; inset:0;
    background: url('img/foto4.jpeg') center/cover no-repeat; opacity:.08;
}
.ppdb-hero .badge-ppdb {
    display:inline-flex; align-items:center; gap:8px;
    background:rgba(212,175,55,.15); border:1px solid rgba(212,175,55,.4);
    color:#d4af37; border-radius:50px; padding:6px 18px;
    font-size:.82rem; font-weight:600; margin-bottom:16px;
}
.ppdb-hero h1 { font-size:2.2rem; font-weight:800; color:#fff; margin-bottom:8px; }
.ppdb-hero h1 span { color:#d4af37; }
.ppdb-hero p { color:rgba(255,255,255,.75); font-size:1rem; }

.cek-card {
    background:#fff; border-radius:20px;
    box-shadow:0 8px 40px rgba(0,0,0,.10);
    padding:40px; margin-top:-30px; position:relative; z-index:2;
}
@media(max-width:576px){ .cek-card{ padding:24px 16px; } }

.form-control { border-radius:10px; border:1.5px solid #e5e7eb; padding:12px 16px; font-size:1rem; }
.form-control:focus { border-color:#d4af37; box-shadow:0 0 0 3px rgba(212,175,55,.15); outline:none; }

.btn-cek {
    background:linear-gradient(135deg,#d4af37,#b8960c); color:#1a1a2e;
    font-weight:700; border:none; border-radius:10px;
    padding:12px 28px; font-size:1rem; cursor:pointer;
    transition:transform .2s, box-shadow .2s; white-space:nowrap;
}
.btn-cek:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(212,175,55,.35); }

.detail-row {
    display:flex; border-bottom:1px solid #f3f4f6; padding:12px 0;
}
.detail-row:last-child { border-bottom:none; }
.detail-label { width:200px; min-width:160px; font-size:.85rem; font-weight:600; color:#6b7280; }
.detail-value { flex:1; font-size:.92rem; color:#1a1a2e; font-weight:500; }

.not-found-box {
    text-align:center; padding:40px 20px;
    background:#fef2f2; border:1.5px solid #fca5a5; border-radius:16px;
}
.not-found-box p { color:#991b1b; font-weight:600; margin:12px 0 0; }
</style>

<!-- HERO -->
<section class="ppdb-hero">
    <div class="container position-relative">
        <div class="badge-ppdb">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            Cek Status Pendaftaran
        </div>
        <h1>Cek <span>Status PPDB</span></h1>
        <p>Masukkan nomor pendaftaran yang Anda terima setelah mendaftar</p>
    </div>
</section>

<section class="py-5" style="background:#f8f9fa;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="cek-card">

                    <!-- FORM CEK -->
                    <form method="POST" action="cek_ppdb.php">
                        <label class="form-label fw-bold mb-2" style="font-size:.9rem;color:#374151;">
                            Nomor Pendaftaran
                        </label>
                        <div class="d-flex gap-2">
                            <input type="text" name="no_pendaftaran" class="form-control"
                                   placeholder="Contoh: PPDB-2025-AB12CD"
                                   value="<?php echo htmlspecialchars($no_input); ?>"
                                   style="text-transform:uppercase;" required>
                            <button type="submit" class="btn-cek">Cek</button>
                        </div>
                    </form>

                    <!-- HASIL -->
                    <?php if ($searched): ?>
                    <hr style="margin:28px 0; border-color:#f3f4f6;">

                    <?php if ($result_ppdb): ?>
                        <div style="margin-bottom:16px;">
                            <span style="font-size:.85rem;color:#6b7280;font-weight:600;">STATUS PENDAFTARAN</span><br>
                            <div style="margin-top:8px;">
                                <?php echo statusBadge($result_ppdb['status']); ?>
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">No. Pendaftaran</div>
                            <div class="detail-value" style="font-family:monospace;color:#d4af37;font-weight:800;">
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
                        <div style="margin-top:20px;background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;padding:14px 18px;font-size:.88rem;color:#92400e;">
                            ℹ️ Pendaftaran Anda sedang dalam proses verifikasi oleh pihak sekolah. Kami akan menghubungi Anda melalui nomor HP yang didaftarkan.
                        </div>
                        <?php elseif ($result_ppdb['status'] === 'Diterima'): ?>
                        <div style="margin-top:20px;background:#f0fdf4;border:1.5px solid #86efac;border-radius:12px;padding:14px 18px;font-size:.88rem;color:#15803d;">
                            🎉 Selamat! Calon siswa <strong><?php echo htmlspecialchars($result_ppdb['nama_lengkap']); ?></strong> diterima di SDN Laladon 03. Silakan datang ke sekolah untuk proses administrasi selanjutnya.
                        </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="not-found-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" stroke="#fca5a5" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <p>Nomor pendaftaran <strong><?php echo htmlspecialchars($no_input); ?></strong> tidak ditemukan.<br>
                            <span style="font-weight:400;color:#b91c1c;font-size:.88rem;">Pastikan nomor yang dimasukkan benar.</span></p>
                        </div>
                    <?php endif; ?>
                    <?php endif; ?>

                    <div class="mt-4 text-center" style="font-size:.88rem;color:#9ca3af;">
                        Belum mendaftar? 
                        <a href="daftar_ppdb.php" style="color:#d4af37;font-weight:700;text-decoration:none;">Daftar PPDB Sekarang →</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>