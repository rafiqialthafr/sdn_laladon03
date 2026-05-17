<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: masuk-admin-03.php"); exit; }
header('Content-Type: text/html; charset=UTF-8');
include 'koneksi.php';

$success_msg = $error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $nama=$_POST['nama_lengkap']??''; $jk=$_POST['jenis_kelamin']??'';
        $tmp=$_POST['tempat_lahir']??''; $tgl=$_POST['tanggal_lahir']??'';
        $agama=$_POST['agama']??''; $alamat=$_POST['alamat']??'';
        $ayah=$_POST['nama_ayah']??''; $ibu=$_POST['nama_ibu']??'';
        $kerja=$_POST['pekerjaan_ortu']??''; $hp=$_POST['no_hp_ortu']??'';
        $tk=$_POST['asal_tk']??''; $nisn=$_POST['nisn']??'';
        if ($nama&&$tmp&&$tgl&&$agama&&$alamat&&$ayah&&$hp) {
            $no="PPDB-".date('Y')."-".strtoupper(substr(md5(uniqid()),0,6));
            $st=mysqli_prepare($koneksi,"INSERT INTO ppdb (no_pendaftaran,nisn,nama_lengkap,jenis_kelamin,tempat_lahir,tanggal_lahir,agama,alamat,nama_ayah,nama_ibu,pekerjaan_ortu,no_hp_ortu,asal_tk,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,'Menunggu')");
            mysqli_stmt_bind_param($st,'sssssssssssss',$no,$nisn,$nama,$jk,$tmp,$tgl,$agama,$alamat,$ayah,$ibu,$kerja,$hp,$tk);
            $success_msg = mysqli_stmt_execute($st) ? '✓ Data berhasil ditambahkan.' : '✗ Gagal menambahkan.';
            mysqli_stmt_close($st);
        } else { $error_msg='✗ Harap isi semua field wajib.'; }
    }
    if ($_POST['action'] === 'delete') {
        $id=(int)$_POST['id'];
        $st=mysqli_prepare($koneksi,"DELETE FROM ppdb WHERE id=?");
        mysqli_stmt_bind_param($st,'i',$id);
        $success_msg = mysqli_stmt_execute($st) ? '✓ Data dihapus.' : '✗ Gagal hapus.';
        mysqli_stmt_close($st);
    }
    if ($_POST['action'] === 'status') {
        $id=(int)$_POST['id']; $s=$_POST['status']??'';
        if (in_array($s,['Menunggu','Diterima','Ditolak'])) {
            $st=mysqli_prepare($koneksi,"UPDATE ppdb SET status=? WHERE id=?");
            mysqli_stmt_bind_param($st,'si',$s,$id);
            $success_msg = mysqli_stmt_execute($st) ? '✓ Status diperbarui.' : '✗ Gagal update.';
            mysqli_stmt_close($st);
        }
    }
}

$status_filter = $_GET['status']??'';
$per_page=10; $page=max(1,(int)($_GET['page']??1)); $offset=($page-1)*$per_page;

if ($status_filter && in_array($status_filter,['Menunggu','Diterima','Ditolak'])) {
    $st=mysqli_prepare($koneksi,"SELECT COUNT(*) as t FROM ppdb WHERE status=?");
    mysqli_stmt_bind_param($st,'s',$status_filter);
} else { $st=mysqli_prepare($koneksi,"SELECT COUNT(*) as t FROM ppdb"); }
mysqli_stmt_execute($st); $total=(int)mysqli_fetch_assoc(mysqli_stmt_get_result($st))['t'];
$total_pages=ceil($total/$per_page); mysqli_stmt_close($st);

if ($status_filter && in_array($status_filter,['Menunggu','Diterima','Ditolak'])) {
    $st=mysqli_prepare($koneksi,"SELECT * FROM ppdb WHERE status=? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($st,'sii',$status_filter,$per_page,$offset);
} else {
    $st=mysqli_prepare($koneksi,"SELECT * FROM ppdb ORDER BY created_at DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($st,'ii',$per_page,$offset);
}
mysqli_stmt_execute($st); $rows=mysqli_stmt_get_result($st); mysqli_stmt_close($st);

$stat=[]; foreach(['Menunggu','Diterima','Ditolak'] as $s) {
    $st=mysqli_prepare($koneksi,"SELECT COUNT(*) as c FROM ppdb WHERE status=?");
    mysqli_stmt_bind_param($st,'s',$s); mysqli_stmt_execute($st);
    $stat[$s]=mysqli_fetch_assoc(mysqli_stmt_get_result($st))['c']; mysqli_stmt_close($st);
}
$stat['Total']=array_sum($stat);
$hari=['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$today=$hari[date('w')].', '.date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<link rel="icon" type="image/png" href="img/logo.png">
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data PPDB — Admin SDN Laladon 03</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/admin.css" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.tab-btn{padding:.4rem 1rem;border:1.5px solid #e2e8f0;border-radius:8px;background:#fff;font-size:.78rem;font-weight:600;color:#64748b;text-decoration:none;transition:all .2s}
.tab-btn:hover{border-color:#fbbf24;color:#d97706}
.tab-btn.active{background:#1a1a2e;color:#fff;border-color:#1a1a2e}

/* Pendaftar Card */
.ppdb-card{background:#fff;border-radius:16px;border:1px solid #f1f5f9;box-shadow:0 2px 12px rgba(0,0,0,.05);overflow:hidden;margin-bottom:1.25rem;transition:box-shadow .2s}
.ppdb-card:hover{box-shadow:0 4px 24px rgba(0,0,0,.09)}
.ppdb-card-header{display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;background:linear-gradient(135deg,#1a1a2e,#16213e);flex-wrap:wrap;gap:.5rem}
.ppdb-card-no{font-family:monospace;font-weight:800;color:#fcd34d;font-size:.9rem;letter-spacing:1px}
.ppdb-card-name{font-size:1rem;font-weight:700;color:#fff;margin-top:.15rem}
.ppdb-card-body{padding:1.25rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem}
@media(max-width:640px){.ppdb-card-body{grid-template-columns:1fr}}
/* Card Footer — action buttons, separated from status */
.ppdb-card-footer{display:flex;align-items:center;justify-content:space-between;padding:.85rem 1.25rem;border-top:1px solid #f1f5f9;background:#fafafa;flex-wrap:wrap;gap:.5rem}
.ppdb-card-footer-label{font-size:.75rem;color:#94a3b8;font-weight:500;display:flex;align-items:center;gap:.35rem}
.ppdb-card-footer-actions{display:flex;gap:.5rem;flex-wrap:wrap}
.ppdb-section{background:#f8fafc;border-radius:10px;padding:1rem}
.ppdb-section-title{font-size:.75rem;font-weight:700;color:#64748b;letter-spacing:.8px;text-transform:uppercase;margin-bottom:.75rem;display:flex;align-items:center;gap:.4rem}
.ppdb-field{display:flex;flex-direction:column;margin-bottom:.6rem}
.ppdb-field:last-child{margin-bottom:0}
.ppdb-field-label{font-size:.7rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px}
.ppdb-field-value{font-size:.88rem;font-weight:600;color:#0f172a;margin-top:.1rem}
.ppdb-field-value.empty{color:#cbd5e1;font-weight:400;font-style:italic}
.ppdb-alamat{grid-column:1/-1;background:#f8fafc;border-radius:10px;padding:1rem}

/* Status badges */
.badge-menunggu{background:#fef3c7;color:#d97706;border:1px solid #fcd34d}
.badge-diterima{background:#dcfce7;color:#16a34a;border:1px solid #86efac}
.badge-ditolak{background:#fee2e2;color:#dc2626;border:1px solid #fca5a5}
.ppdb-badge{padding:.25rem .75rem;border-radius:20px;font-size:.75rem;font-weight:700;white-space:nowrap}

/* Actions */
.btn-ppdb{display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .75rem;border-radius:7px;font-size:.75rem;font-weight:600;border:none;cursor:pointer;text-decoration:none;transition:all .2s}
.btn-ppdb-terima{background:#dcfce7;color:#15803d}.btn-ppdb-terima:hover{background:#bbf7d0;color:#15803d}
.btn-ppdb-tolak{background:#fee2e2;color:#b91c1c}.btn-ppdb-tolak:hover{background:#fecaca;color:#b91c1c}
.btn-ppdb-delete{background:#fff0f0;color:#ef4444;border:1px solid #fecaca}.btn-ppdb-delete:hover{background:#fee2e2}

/* Empty */
.ppdb-empty{text-align:center;padding:4rem 2rem;color:#94a3b8}
.ppdb-empty-icon{width:64px;height:64px;background:#f1f5f9;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem}
</style>
</head>
<body>
<div class="admin-wrapper">
<?php include 'admin_sidebar.php'; ?>
<div class="admin-content">

<!-- Topbar -->
<div class="admin-topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle"><i data-lucide="menu" style="width:18px;height:18px;"></i></button>
        <div><p class="topbar-title">Data PPDB</p><p class="topbar-subtitle"><?php echo $today; ?></p></div>
    </div>
    <div class="topbar-right">
        <div class="d-none d-md-block text-end"><p class="topbar-user-name">Administrator</p><p class="topbar-user-role">Super Admin</p></div>
        <div class="topbar-avatar">A</div>
    </div>
</div>

<div class="admin-page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Data PPDB</h1>
            <p class="page-breadcrumb"><a href="admin_dashboard.php" style="color:#94a3b8;text-decoration:none;">Dashboard</a> &rsaquo; <span>Data PPDB</span></p>
        </div>
        <button class="btn-admin btn-admin-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i data-lucide="plus" style="width:15px;height:15px;"></i> Tambah Pendaftar
        </button>
    </div>

    <?php if ($success_msg): ?><div class="alert-admin alert-success"><i data-lucide="check-circle" style="width:18px;height:18px;"></i> <?php echo $success_msg; ?></div><?php endif; ?>
    <?php if ($error_msg): ?><div class="alert-admin alert-error"><i data-lucide="alert-circle" style="width:18px;height:18px;"></i> <?php echo $error_msg; ?></div><?php endif; ?>

    <!-- Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="stat-card">
            <div class="stat-icon purple"><i data-lucide="users" style="width:22px;height:22px;"></i></div>
            <div class="stat-info"><div class="stat-value"><?php echo $stat['Total']; ?></div><div class="stat-label">Total Pendaftar</div></div>
        </div></div>
        <div class="col-6 col-md-3"><div class="stat-card">
            <div class="stat-icon amber"><i data-lucide="clock" style="width:22px;height:22px;"></i></div>
            <div class="stat-info"><div class="stat-value"><?php echo $stat['Menunggu']; ?></div><div class="stat-label">Menunggu</div></div>
        </div></div>
        <div class="col-6 col-md-3"><div class="stat-card">
            <div class="stat-icon green"><i data-lucide="check-circle" style="width:22px;height:22px;"></i></div>
            <div class="stat-info"><div class="stat-value"><?php echo $stat['Diterima']; ?></div><div class="stat-label">Diterima</div></div>
        </div></div>
        <div class="col-6 col-md-3"><div class="stat-card">
            <div class="stat-icon rose"><i data-lucide="x-circle" style="width:22px;height:22px;"></i></div>
            <div class="stat-info"><div class="stat-value"><?php echo $stat['Ditolak']; ?></div><div class="stat-label">Tidak Diterima</div></div>
        </div></div>
    </div>

    <!-- Filter + jumlah -->
    <div class="admin-card mb-4">
        <div class="admin-card-header">
            <div class="d-flex gap-2 flex-wrap">
                <a href="admin_ppdb.php" class="tab-btn <?php echo !$status_filter?'active':''; ?>">Semua</a>
                <a href="admin_ppdb.php?status=Menunggu" class="tab-btn <?php echo $status_filter==='Menunggu'?'active':''; ?>">Menunggu</a>
                <a href="admin_ppdb.php?status=Diterima" class="tab-btn <?php echo $status_filter==='Diterima'?'active':''; ?>">Diterima</a>
                <a href="admin_ppdb.php?status=Ditolak" class="tab-btn <?php echo $status_filter==='Ditolak'?'active':''; ?>">Ditolak</a>
            </div>
            <small style="color:#94a3b8;"><?php echo $total; ?> pendaftar</small>
        </div>
    </div>

    <!-- Cards -->
    <?php if (mysqli_num_rows($rows) > 0):
        $no = $offset + 1;
        while ($row = mysqli_fetch_assoc($rows)):
            $badge_class = match($row['status']) {
                'Diterima' => 'badge-diterima',
                'Ditolak'  => 'badge-ditolak',
                default    => 'badge-menunggu',
            };
            $v = fn($f) => htmlspecialchars($row[$f] ?? '');
            $vd = fn($f) => !empty($row[$f]) ? htmlspecialchars($row[$f]) : '<span class="empty">—</span>';
    ?>
    <div class="ppdb-card">
        <!-- Card Header: Identitas + Status saja -->
        <div class="ppdb-card-header">
            <div>
                <div class="ppdb-card-no"><?php echo $v('no_pendaftaran'); ?></div>
                <div class="ppdb-card-name"><?php echo $no ?>. <?php echo $v('nama_lengkap'); ?></div>
            </div>
            <span class="ppdb-badge <?php echo $badge_class; ?>"><?php echo $v('status'); ?></span>
        </div>

        <!-- Card Body -->
        <div class="ppdb-card-body">
            <!-- Data Siswa -->
            <div class="ppdb-section">
                <div class="ppdb-section-title"><i data-lucide="user" style="width:14px;height:14px;"></i> Data Calon Siswa</div>
                <div class="ppdb-field"><span class="ppdb-field-label">NISN</span><span class="ppdb-field-value"><?php echo $vd('nisn'); ?></span></div>
                <div class="ppdb-field"><span class="ppdb-field-label">Jenis Kelamin</span><span class="ppdb-field-value"><?php echo $v('jenis_kelamin'); ?></span></div>
                <div class="ppdb-field"><span class="ppdb-field-label">Tempat, Tgl Lahir</span><span class="ppdb-field-value"><?php echo $v('tempat_lahir').', '.date('d F Y', strtotime($row['tanggal_lahir'])); ?></span></div>
                <div class="ppdb-field"><span class="ppdb-field-label">Agama</span><span class="ppdb-field-value"><?php echo $v('agama'); ?></span></div>
                <div class="ppdb-field"><span class="ppdb-field-label">Asal TK / RA</span><span class="ppdb-field-value"><?php echo $vd('asal_tk'); ?></span></div>
                <?php if (!empty($row['created_at'])): ?>
                <div class="ppdb-field"><span class="ppdb-field-label">Tanggal Daftar</span><span class="ppdb-field-value" style="color:#64748b;font-size:.78rem;"><?php echo date('d F Y, H:i', strtotime($row['created_at'])); ?></span></div>
                <?php endif; ?>
            </div>

            <!-- Data Ortu -->
            <div class="ppdb-section">
                <div class="ppdb-section-title"><i data-lucide="users" style="width:14px;height:14px;"></i> Data Orang Tua / Wali</div>
                <div class="ppdb-field"><span class="ppdb-field-label">Nama Ayah</span><span class="ppdb-field-value"><?php echo $v('nama_ayah'); ?></span></div>
                <div class="ppdb-field"><span class="ppdb-field-label">Nama Ibu</span><span class="ppdb-field-value"><?php echo $vd('nama_ibu'); ?></span></div>
                <div class="ppdb-field"><span class="ppdb-field-label">Pekerjaan</span><span class="ppdb-field-value"><?php echo $vd('pekerjaan_ortu'); ?></span></div>
                <div class="ppdb-field"><span class="ppdb-field-label">No. HP</span>
                    <span class="ppdb-field-value">
                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/','',$row['no_hp_ortu']??''); ?>" target="_blank" style="color:#16a34a;text-decoration:none;">
                            <?php echo $v('no_hp_ortu'); ?>
                        </a>
                    </span>
                </div>
            </div>

            <!-- Alamat — full width -->
            <div class="ppdb-alamat">
                <div class="ppdb-section-title"><i data-lucide="map-pin" style="width:14px;height:14px;"></i> Alamat Lengkap</div>
                <div class="ppdb-field-value" style="line-height:1.6;"><?php echo $vd('alamat'); ?></div>
            </div>
        </div>

        <!-- Card Footer: Tombol Aksi (terpisah dari status) -->
        <div class="ppdb-card-footer">
            <div class="ppdb-card-footer-label">
                <i data-lucide="settings-2" style="width:13px;height:13px;"></i>
                Ubah status atau hapus data pendaftar ini:
            </div>
            <div class="ppdb-card-footer-actions">
                <form method="POST" action="admin_ppdb.php" style="display:inline;">
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                    <input type="hidden" name="status" value="Diterima">
                    <button type="submit" class="btn-ppdb btn-ppdb-terima" onclick="return confirm('Terima pendaftar ini?')">
                        <i data-lucide="check" style="width:13px;height:13px;"></i> Terima
                    </button>
                </form>
                <form method="POST" action="admin_ppdb.php" style="display:inline;">
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                    <input type="hidden" name="status" value="Ditolak">
                    <button type="submit" class="btn-ppdb btn-ppdb-tolak" onclick="return confirm('Tolak pendaftar ini?')">
                        <i data-lucide="x" style="width:13px;height:13px;"></i> Tolak
                    </button>
                </form>
                <form method="POST" action="admin_ppdb.php" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                    <button type="submit" class="btn-ppdb btn-ppdb-delete" onclick="return confirm('Hapus data ini permanen?')">
                        <i data-lucide="trash-2" style="width:13px;height:13px;"></i> Hapus Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php $no++; endwhile;
    else: ?>
    <div class="ppdb-empty">
        <div class="ppdb-empty-icon"><i data-lucide="inbox" style="width:28px;height:28px;"></i></div>
        <p style="font-weight:600;">Belum ada data pendaftaran</p>
        <p style="font-size:.85rem;">Data akan muncul setelah ada yang mendaftar melalui halaman PPDB.</p>
    </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="d-flex justify-content-center gap-2 flex-wrap mt-3 mb-4">
        <?php for ($p=1; $p<=$total_pages; $p++): ?>
        <a href="admin_ppdb.php?page=<?php echo $p; ?>&status=<?php echo urlencode($status_filter); ?>" class="tab-btn <?php echo $p===$page?'active':''; ?>"><?php echo $p; ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

</div><!-- /admin-page -->
</div><!-- /admin-content -->
</div><!-- /admin-wrapper -->

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a1a2e;">
                <h5 class="modal-title text-white">Tambah Pendaftar PPDB</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="admin_ppdb.php">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold">Nama Lengkap *</label><input type="text" class="form-control" name="nama_lengkap" required></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">NISN</label><input type="text" class="form-control" name="nisn" maxlength="10"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Jenis Kelamin *</label>
                            <select class="form-control" name="jenis_kelamin" required><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select>
                        </div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Agama *</label>
                            <select class="form-control" name="agama" required><option value="">-- Pilih --</option><?php foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $ag) echo "<option>$ag</option>"; ?></select>
                        </div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Tempat Lahir *</label><input type="text" class="form-control" name="tempat_lahir" required></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Tanggal Lahir *</label><input type="date" class="form-control" name="tanggal_lahir" required></div>
                        <div class="col-12"><label class="form-label fw-semibold">Alamat Lengkap *</label><textarea class="form-control" name="alamat" rows="2" required></textarea></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Asal TK / RA</label><input type="text" class="form-control" name="asal_tk"></div>
                        <div class="col-12"><hr class="my-1"><small class="fw-bold text-muted">DATA ORANG TUA / WALI</small></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Nama Ayah *</label><input type="text" class="form-control" name="nama_ayah" required></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Nama Ibu</label><input type="text" class="form-control" name="nama_ibu"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Pekerjaan Ortu *</label><input type="text" class="form-control" name="pekerjaan_ortu" required></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">No. HP Ortu *</label><input type="text" class="form-control" name="no_hp_ortu" required></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-admin btn-admin-primary"><i data-lucide="save" style="width:15px;height:15px;"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
lucide.createIcons();
const toggle=document.getElementById('sidebarToggle');
const sidebar=document.getElementById('admin-sidebar');
const overlay=document.getElementById('sidebarOverlay');
if(toggle&&sidebar){toggle.addEventListener('click',()=>{sidebar.classList.toggle('open');if(overlay)overlay.classList.toggle('show');})}
if(overlay){overlay.addEventListener('click',()=>{sidebar.classList.remove('open');overlay.classList.remove('show');})}
</script>
</body>
</html>