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

</head>
<body>
<div class="admin-wrapper">
<?php include 'admin_sidebar.php'; ?>
<div class="admin-content">

<!-- Topbar -->
<div class="admin-topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle"><i data-lucide="menu" class="i-lg"></i></button>
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
            <p class="page-breadcrumb"><a href="admin_dashboard.php">Dashboard</a> &rsaquo; <span>Data PPDB</span></p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="export_ppdb.php?status=<?php echo urlencode($status_filter); ?>" class="btn fw-bold text-white px-4 py-2.5 rounded-pill d-inline-flex align-items-center gap-2 shadow-sm btn-export-excel">
                <i data-lucide="file-spreadsheet" class="i-md"></i> Export Excel
            </a>
            <button class="btn-admin btn-admin-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
                <i data-lucide="plus" style="width:15px;height:15px;"></i> Tambah Pendaftar
            </button>
        </div>
    </div>

    <?php if ($success_msg): ?><div class="alert-admin alert-success"><i data-lucide="check-circle" style="width:18px;height:18px;"></i> <?php echo $success_msg; ?></div><?php endif; ?>
    <?php if ($error_msg): ?><div class="alert-admin alert-error"><i data-lucide="alert-circle" style="width:18px;height:18px;"></i> <?php echo $error_msg; ?></div><?php endif; ?>

    <!-- Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="stat-card">
            <div class="stat-icon purple"><i data-lucide="users" class="i-xl"></i></div>
            <div class="stat-info"><div class="stat-value"><?php echo $stat['Total']; ?></div><div class="stat-label">Total Pendaftar</div></div>
        </div></div>
        <div class="col-6 col-md-3"><div class="stat-card">
            <div class="stat-icon amber"><i data-lucide="clock" class="i-xl"></i></div>
            <div class="stat-info"><div class="stat-value"><?php echo $stat['Menunggu']; ?></div><div class="stat-label">Menunggu</div></div>
        </div></div>
        <div class="col-6 col-md-3"><div class="stat-card">
            <div class="stat-icon green"><i data-lucide="check-circle" class="i-xl"></i></div>
            <div class="stat-info"><div class="stat-value"><?php echo $stat['Diterima']; ?></div><div class="stat-label">Diterima</div></div>
        </div></div>
        <div class="col-6 col-md-3"><div class="stat-card">
            <div class="stat-icon rose"><i data-lucide="x-circle" class="i-xl"></i></div>
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
            <small><?php echo $total; ?> pendaftar</small>
        </div>
    </div>

    <!-- Tabular Grid (1 Baris = 1 Siswa) -->
    <div class="admin-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Daftar</th>
                        <th>Nama Lengkap</th>
                        <th>TTL (Tempat, Tanggal Lahir)</th>
                        <th>Jenis Kelamin</th>
                        <th>Status</th>
                        <th class="text-center th-action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($rows) > 0):
                        $no = $offset + 1;
                        while ($row = mysqli_fetch_assoc($rows)):
                            $badge_class = match($row['status']) {
                                'Diterima' => 'badge-diterima',
                                'Ditolak'  => 'badge-ditolak',
                                default    => 'badge-menunggu',
                            };
                            $formatted_ttl = htmlspecialchars($row['tempat_lahir']) . ', ' . date('d F Y', strtotime($row['tanggal_lahir']));
                            $clean_hp = preg_replace('/[^0-9]/', '', $row['no_hp_ortu'] ?? '');
                            $created_at_fmt = !empty($row['created_at']) ? date('d F Y, H:i', strtotime($row['created_at'])) : '—';
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td class="col-reg-no"><?php echo htmlspecialchars($row['no_pendaftaran']); ?></td>
                        <td class="col-name"><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                        <td><?php echo $formatted_ttl; ?></td>
                        <td>
                            <span class="gender-text">
                                <?php if ($row['jenis_kelamin'] === 'Laki-laki'): ?>
                                    <i data-lucide="user" class="gender-male"></i> Laki-laki
                                <?php else: ?>
                                    <i data-lucide="user" class="gender-female"></i> Perempuan
                                <?php endif; ?>
                            </span>
                        </td>
                        <td>
                            <span class="ppdb-badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                        </td>
                        <td>
                            <div class="action-wrap justify-content-center">
                                <button type="button" class="btn btn-detail-gold rounded-pill d-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#modalDetail"
                                    data-id="<?php echo (int)$row['id']; ?>"
                                    data-no="<?php echo htmlspecialchars($row['no_pendaftaran']); ?>"
                                    data-nama="<?php echo htmlspecialchars($row['nama_lengkap']); ?>"
                                    data-nisn="<?php echo htmlspecialchars($row['nisn'] ?? ''); ?>"
                                    data-jk="<?php echo htmlspecialchars($row['jenis_kelamin']); ?>"
                                    data-ttl="<?php echo $formatted_ttl; ?>"
                                    data-agama="<?php echo htmlspecialchars($row['agama']); ?>"
                                    data-tk="<?php echo htmlspecialchars($row['asal_tk'] ?? ''); ?>"
                                    data-alamat="<?php echo htmlspecialchars($row['alamat']); ?>"
                                    data-ayah="<?php echo htmlspecialchars($row['nama_ayah']); ?>"
                                    data-ibu="<?php echo htmlspecialchars($row['nama_ibu'] ?? ''); ?>"
                                    data-kerja="<?php echo htmlspecialchars($row['pekerjaan_ortu'] ?? ''); ?>"
                                    data-hp="<?php echo htmlspecialchars($row['no_hp_ortu'] ?? ''); ?>"
                                    data-status="<?php echo htmlspecialchars($row['status']); ?>"
                                    data-tgl-daftar="<?php echo $created_at_fmt; ?>">
                                    <i data-lucide="eye" class="i-xs"></i>
                                    Lihat Detail
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile;
                    else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="tbl-empty">
                                <div class="tbl-empty-icon"><i data-lucide="inbox" class="i-3xl"></i></div>
                                <p>Belum ada data pendaftaran</p>
                                <p>Data akan muncul setelah ada yang mendaftar melalui halaman PPDB.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div><!-- /admin-card -->

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="d-flex justify-content-center gap-2 flex-wrap mt-4 mb-4">
        <?php for ($p=1; $p<=$total_pages; $p++): ?>
        <a href="admin_ppdb.php?page=<?php echo $p; ?>&status=<?php echo urlencode($status_filter); ?>" class="tab-btn <?php echo $p===$page?'active':''; ?>"><?php echo $p; ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

</div><!-- /admin-page -->
</div><!-- /admin-content -->
</div><!-- /admin-wrapper -->

<!-- MODAL DETAIL PENDAFTAR -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-content-premium">
            <div class="modal-header text-white modal-header-premium">
                <div>
                    <span id="det-no"></span>
                    <h5 class="modal-title fw-bold" id="det-nama"></h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-body-premium">
                <div class="row g-4">
                    <!-- Left: Personal Student Data -->
                    <div class="col-md-6">
                        <div class="detail-box">
                            <div class="detail-section-title">
                                <i data-lucide="user"></i> Data Calon Siswa
                            </div>
                            
                            <div class="detail-label">NISN</div>
                            <div class="detail-value" id="det-nisn"></div>
                            
                            <div class="detail-label">Jenis Kelamin</div>
                            <div class="detail-value" id="det-jk"></div>
                            
                            <div class="detail-label">Tempat, Tanggal Lahir</div>
                            <div class="detail-value" id="det-ttl"></div>
                            
                            <div class="detail-label">Agama</div>
                            <div class="detail-value" id="det-agama"></div>
                            
                            <div class="detail-label">Asal TK / RA</div>
                            <div class="detail-value" id="det-tk"></div>
                            
                            <div class="detail-label">Tanggal Registrasi</div>
                            <div class="detail-value text-muted" id="det-tgl-daftar"></div>
                        </div>
                    </div>
                    
                    <!-- Right: Family Data -->
                    <div class="col-md-6">
                        <div class="detail-box">
                            <div class="detail-section-title">
                                <i data-lucide="users"></i> Data Orang Tua / Wali
                            </div>
                            
                            <div class="detail-label">Nama Ayah</div>
                            <div class="detail-value" id="det-ayah"></div>
                            
                            <div class="detail-label">Nama Ibu</div>
                            <div class="detail-value" id="det-ibu"></div>
                            
                            <div class="detail-label">Pekerjaan Orang Tua</div>
                            <div class="detail-value" id="det-kerja"></div>
                            
                            <div class="detail-label">Nomor HP / WhatsApp</div>
                            <div class="detail-value">
                                <a id="det-hp-link" href="" target="_blank" class="fw-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                      <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.949h.004c4.368 0 7.927-3.558 7.93-7.93a7.9 7.9 0 0 0-2.327-5.594ZM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.69-4.836c-.2-.1-.1.185-.596-.347-.1-.1-.3-.15-.5-.05l-.1.085c-.2.2-.4.45-.6.65-.1.1-.3.1-.5 0a5.17 5.17 0 0 1-1.39-1.06 6.19 6.19 0 0 1-.92-1.28c-.1-.2 0-.3.1-.4l.3-.35c.1-.1.1-.2.1-.3s0-.3-.1-.5l-.3-.7c-.2-.4-.3-.4-.5-.4s-.4 0-.6.2c-.15.2-.5.55-.5 1.3s.55 1.5 1.25 2.1c.7.6 1.8 1.5 2.8 1.9.4.15.75.2 1 .15.3-.05 1-.4 1.15-.8s.15-.8.1-.85c-.05-.05-.2-.15-.4-.25"/>
                                    </svg>
                                    <span id="det-hp-text"></span>
                                </a>
                            </div>
                            
                            <div class="detail-label">Status Kelulusan</div>
                            <div class="mt-1">
                                <span id="det-status" class="ppdb-badge"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Alamat Lengkap -->
                <div class="detail-alamat-box">
                    <div class="detail-section-title">
                        <i data-lucide="map-pin"></i> Alamat Tempat Tinggal
                    </div>
                    <div id="det-alamat"></div>
                </div>
            </div>
            
            <div class="modal-footer modal-footer-premium">
                <form method="POST" action="admin_ppdb.php">
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="id" class="action-id-input" value="">
                    <input type="hidden" name="status" value="Diterima">
                    <button type="submit" class="btn btn-success fw-bold px-4 py-2.5 rounded-pill btn-success-premium" onclick="return confirm('Terima pendaftar ini?')">
                        <i data-lucide="check"></i> Terima
                    </button>
                </form>
                
                <form method="POST" action="admin_ppdb.php">
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="id" class="action-id-input" value="">
                    <input type="hidden" name="status" value="Ditolak">
                    <button type="submit" class="btn btn-danger fw-bold px-4 py-2.5 rounded-pill btn-danger-premium" onclick="return confirm('Tolak pendaftar ini?')">
                        <i data-lucide="x"></i> Tolak
                    </button>
                </form>
                
                <form method="POST" action="admin_ppdb.php" class="form-delete">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" class="action-id-input" value="">
                    <button type="submit" class="btn btn-outline-danger fw-bold px-4 py-2.5 rounded-pill btn-outline-danger-premium" onclick="return confirm('Hapus data ini permanen?')">
                        <i data-lucide="trash-2"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-create">
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

// Dynamic Modal Population for Detail Viewer
const modalDetail = document.getElementById('modalDetail');
if (modalDetail) {
    modalDetail.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        
        // Extract data-attributes
        const id = button.getAttribute('data-id');
        const no = button.getAttribute('data-no');
        const nama = button.getAttribute('data-nama');
        const nisn = button.getAttribute('data-nisn');
        const jk = button.getAttribute('data-jk');
        const ttl = button.getAttribute('data-ttl');
        const agama = button.getAttribute('data-agama');
        const tk = button.getAttribute('data-tk');
        const alamat = button.getAttribute('data-alamat');
        const ayah = button.getAttribute('data-ayah');
        const ibu = button.getAttribute('data-ibu');
        const kerja = button.getAttribute('data-kerja');
        const hp = button.getAttribute('data-hp');
        const status = button.getAttribute('data-status');
        const tglDaftar = button.getAttribute('data-tgl-daftar');

        // Populate modal text content
        document.getElementById('det-no').textContent = no;
        document.getElementById('det-nama').textContent = nama;
        document.getElementById('det-nisn').textContent = nisn ? nisn : '—';
        document.getElementById('det-jk').textContent = jk;
        document.getElementById('det-ttl').textContent = ttl;
        document.getElementById('det-agama').textContent = agama;
        document.getElementById('det-tk').textContent = tk ? tk : '—';
        document.getElementById('det-tgl-daftar').textContent = tglDaftar;
        
        document.getElementById('det-ayah').textContent = ayah;
        document.getElementById('det-ibu').textContent = ibu ? ibu : '—';
        document.getElementById('det-kerja').textContent = kerja ? kerja : '—';
        
        // WhatsApp Link & text
        const hpLink = document.getElementById('det-hp-link');
        const cleanHp = hp.replace(/[^0-9]/g, '');
        hpLink.href = `https://wa.me/${cleanHp}`;
        document.getElementById('det-hp-text').textContent = hp;

        document.getElementById('det-alamat').textContent = alamat;
        
        // Status badge
        const badge = document.getElementById('det-status');
        badge.textContent = status;
        badge.className = 'ppdb-badge ' + (status === 'Diterima' ? 'badge-diterima' : (status === 'Ditolak' ? 'badge-ditolak' : 'badge-menunggu'));

        // Assign ID to inputs for post actions
        document.querySelectorAll('.action-id-input').forEach(input => {
            input.value = id;
        });
    });
}
</script>
</body>
</html>