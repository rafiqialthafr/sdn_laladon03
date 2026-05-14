<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

header('Content-Type: text/html; charset=UTF-8');

include 'koneksi.php';

/** @var mysqli $koneksi Database connection from koneksi.php */

$success_msg = '';
$error_msg = '';

// PROSES CREATE (Tambah Pendaftar Baru)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $jk = $_POST['jenis_kelamin'] ?? '';
    $tmp_lahir = trim($_POST['tempat_lahir'] ?? '');
    $tgl_lahir = $_POST['tanggal_lahir'] ?? '';
    $agama = $_POST['agama'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $nama_ayah = trim($_POST['nama_ayah'] ?? '');
    $nama_ibu = trim($_POST['nama_ibu'] ?? '');
    $pekerjaan = trim($_POST['pekerjaan_ortu'] ?? '');
    $no_hp = trim($_POST['no_hp_ortu'] ?? '');
    $asal_tk = trim($_POST['asal_tk'] ?? '');

    if (!empty($nama) && !empty($tmp_lahir) && !empty($tgl_lahir) && !empty($agama) && !empty($alamat) && !empty($nama_ayah) && !empty($no_hp)) {
        $tahun = date('Y');
        $rand = strtoupper(substr(md5(uniqid()), 0, 6));
        $no_pendaftaran = "PPDB-{$tahun}-{$rand}";

        $stmt = mysqli_prepare($koneksi, "INSERT INTO ppdb (no_pendaftaran, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, alamat, nama_ayah, nama_ibu, pekerjaan_ortu, no_hp_ortu, asal_tk, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu')");
        mysqli_stmt_bind_param($stmt, 'sssssssssssss', $no_pendaftaran, $nama, $jk, $tmp_lahir, $tgl_lahir, $agama, $alamat, $nama_ayah, $nama_ibu, $pekerjaan, $no_hp, $asal_tk);

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = '✓ Data pendaftar berhasil ditambahkan.';
        } else {
            $error_msg = '✗ Gagal menambahkan data.';
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = '✗ Harap isi semua field yang wajib.';
    }
}

// PROSES UPDATE (Edit Data & Status)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $jk = $_POST['jenis_kelamin'] ?? '';
    $tmp_lahir = trim($_POST['tempat_lahir'] ?? '');
    $tgl_lahir = $_POST['tanggal_lahir'] ?? '';
    $agama = $_POST['agama'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $nama_ayah = trim($_POST['nama_ayah'] ?? '');
    $nama_ibu = trim($_POST['nama_ibu'] ?? '');
    $pekerjaan = trim($_POST['pekerjaan_ortu'] ?? '');
    $no_hp = trim($_POST['no_hp_ortu'] ?? '');
    $asal_tk = trim($_POST['asal_tk'] ?? '');
    $status = $_POST['status'] ?? 'Menunggu';

    $allowed = ['Menunggu', 'Diterima', 'Ditolak'];
    if (!in_array($status, $allowed)) $status = 'Menunggu';

    if (!empty($nama) && !empty($tmp_lahir) && !empty($tgl_lahir) && !empty($agama) && !empty($alamat) && !empty($nama_ayah) && !empty($no_hp)) {
        $stmt = mysqli_prepare($koneksi, "UPDATE ppdb SET nama_lengkap=?, jenis_kelamin=?, tempat_lahir=?, tanggal_lahir=?, agama=?, alamat=?, nama_ayah=?, nama_ibu=?, pekerjaan_ortu=?, no_hp_ortu=?, asal_tk=?, status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'ssssssssssssi', $nama, $jk, $tmp_lahir, $tgl_lahir, $agama, $alamat, $nama_ayah, $nama_ibu, $pekerjaan, $no_hp, $asal_tk, $status, $id);

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = '✓ Data pendaftar berhasil diperbarui.';
        } else {
            $error_msg = '✗ Gagal memperbarui data.';
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = '✗ Harap isi semua field yang wajib.';
    }
}

// PROSES DELETE (Hapus Data)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];
    $stmt = mysqli_prepare($koneksi, "DELETE FROM ppdb WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);

    if (mysqli_stmt_execute($stmt)) {
        $success_msg = '✓ Data pendaftar berhasil dihapus.';
    } else {
        $error_msg = '✗ Gagal menghapus data.';
    }
    mysqli_stmt_close($stmt);
}

// Filter & pagination
$status_filter = isset($_GET['status']) ? (string)$_GET['status'] : '';
$per_page = 15;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

// Query total dengan filter yang aman
if ($status_filter && in_array($status_filter, ['Menunggu', 'Diterima', 'Ditolak'])) {
    $stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) as total FROM ppdb WHERE status = ?");
    mysqli_stmt_bind_param($stmt, 's', $status_filter);
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) as total FROM ppdb");
}
mysqli_stmt_execute($stmt);
$total_res = mysqli_stmt_get_result($stmt);
$total_row = mysqli_fetch_assoc($total_res);
$total = (int)$total_row['total'];
$total_pages = ceil($total / $per_page);
mysqli_stmt_close($stmt);

// Query data dengan filter
if ($status_filter && in_array($status_filter, ['Menunggu', 'Diterima', 'Ditolak'])) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM ppdb WHERE status = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, 'sii', $status_filter, $per_page, $offset);
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM ppdb ORDER BY created_at DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, 'ii', $per_page, $offset);
}
mysqli_stmt_execute($stmt);
$rows = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// Statistik dengan prepared statement
$stat = [];
foreach (['Menunggu', 'Diterima', 'Ditolak'] as $s) {
    $stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) as c FROM ppdb WHERE status = ?");
    mysqli_stmt_bind_param($stmt, 's', $s);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    $stat[$s] = mysqli_fetch_assoc($r)['c'];
    mysqli_stmt_close($stmt);
}
$stat['Total'] = array_sum($stat);

// Jika ada request edit, ambil data berdasarkan ID
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM ppdb WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $edit_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $edit_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
?>
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data PPDB — Admin SDN Laladon 03</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
    <style>
        .stat-card-ppdb { background: var(--card-bg, #fff); border-radius: 14px; padding: 20px 24px; box-shadow: var(--shadow, 0 2px 12px rgba(0,0,0,.07)); border-top: 4px solid #e5e7eb; }
        .stat-card-ppdb .num { font-size: 2rem; font-weight: 800; color: var(--primary, #1a1a2e); }
        .stat-card-ppdb .lbl { font-size: 0.8rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-total    { border-top-color: #6366f1 !important; }
        .stat-menunggu { border-top-color: #fbbf24 !important; }
        .stat-diterima { border-top-color: #22c55e !important; }
        .stat-ditolak  { border-top-color: #ef4444 !important; }
        .badge-menunggu { background:#fffbeb; color:#92400e; border:1px solid #fde68a; border-radius:50px; padding:4px 12px; font-size:.78rem; font-weight:700; display:inline-block; }
        .badge-diterima { background:#f0fdf4; color:#15803d; border:1px solid #86efac; border-radius:50px; padding:4px 12px; font-size:.78rem; font-weight:700; display:inline-block; }
        .badge-ditolak  { background:#fef2f2; color:#b91c1c; border:1px solid #fca5a5; border-radius:50px; padding:4px 12px; font-size:.78rem; font-weight:700; display:inline-block; }
        .btn-edit   { border:none; border-radius:6px; padding:4px 10px; font-size:.75rem; font-weight:600; cursor:pointer; background:#3b82f6; color:#fff; }
        .btn-edit:hover { background:#2563eb; }
        .btn-hapus  { border:none; border-radius:6px; padding:4px 10px; font-size:.75rem; font-weight:600; cursor:pointer; background:#ef4444; color:#fff; }
.btn-hapus:hover { background:#dc2626; }
        .btn-acc { border:none; border-radius:6px; padding:4px 10px; font-size:.75rem; font-weight:700; cursor:pointer; background:#22c55e; color:#fff; display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; }
        .btn-acc:hover { background:#16a34a; }
        .btn-tolak { border:none; border-radius:6px; padding:4px 10px; font-size:.75rem; font-weight:700; cursor:pointer; background:#ef4444; color:#fff; display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; }
        .btn-tolak:hover { background:#dc2626; }
        .btn-edit { margin-right:6px; }
        .filter-btn { border:1.5px solid #e5e7eb; background:#fff; border-radius:8px; padding:6px 16px; font-size:.85rem; font-weight:600; text-decoration:none; color:#374151; transition:all .2s; }
        .filter-btn:hover { border-color:#1a1a2e; color:#1a1a2e; }
        .filter-btn.active { background:#1a1a2e; color:#fff; border-color:#1a1a2e; }
        .alert-ok  { background:#f0fdf4; border:1.5px solid #86efac; color:#15803d; border-radius:10px; padding:12px 18px; font-size:.9rem; margin-bottom:16px; }
        .alert-err { background:#fef2f2; border:1.5px solid #fca5a5; color:#991b1b; border-radius:10px; padding:12px 18px; font-size:.9rem; margin-bottom:16px; }
        .tbl-ppdb thead th { background:#f8f9fa; font-size:.8rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; border:none; padding:14px 16px; }
        .tbl-ppdb tbody td { padding:14px 16px; font-size:.88rem; vertical-align:middle; border-bottom:1px solid #f3f4f6; }
    </style>
</head>
<body>
<div class="admin-wrapper">

    <?php include 'admin_sidebar.php'; ?>

    <div id="admin-content">

        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i data-lucide="menu" style="width:18px;height:18px;"></i>
                </button>
                <div>
                    <p class="topbar-title">Data PPDB</p>
                    <p class="topbar-subtitle">Kelola pendaftaran peserta didik baru</p>
                </div>
            </div>
            <div class="topbar-right">
                <div class="d-none d-md-block text-end">
                    <p class="topbar-user-name">Administrator</p>
                    <p class="topbar-user-role">Super Admin</p>
                </div>
                <div class="topbar-avatar">A</div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="admin-page">

            <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                <div>
                    <h1 class="page-title">📋 Data PPDB</h1>
                    <p class="page-breadcrumb">Penerimaan Peserta Didik Baru — SDN Laladon 03</p>
                </div>
                <button class="btn-admin btn-admin-add" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i data-lucide="plus" style="width:16px;height:16px;"></i> Tambah Pendaftar
                </button>
            </div>

            <?php if (!empty($success_msg)): ?>
                <div class="alert-ok"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            <?php if (!empty($error_msg)): ?>
                <div class="alert-err"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <!-- STATISTIK -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card-ppdb stat-total">
                        <div class="num"><?php echo $stat['Total']; ?></div>
                        <div class="lbl">Total Pendaftar</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card-ppdb stat-menunggu">
                        <div class="num"><?php echo $stat['Menunggu']; ?></div>
                        <div class="lbl">Menunggu</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card-ppdb stat-diterima">
                        <div class="num"><?php echo $stat['Diterima']; ?></div>
                        <div class="lbl">Diterima</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card-ppdb stat-ditolak">
                        <div class="num"><?php echo $stat['Ditolak']; ?></div>
                        <div class="lbl">Tidak Diterima</div>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="admin_ppdb.php" class="filter-btn <?php echo !$status_filter ? 'active' : ''; ?>">Semua</a>
                        <a href="admin_ppdb.php?status=Menunggu" class="filter-btn <?php echo $status_filter==='Menunggu' ? 'active' : ''; ?>">Menunggu</a>
                        <a href="admin_ppdb.php?status=Diterima" class="filter-btn <?php echo $status_filter==='Diterima' ? 'active' : ''; ?>">Diterima</a>
                        <a href="admin_ppdb.php?status=Ditolak"  class="filter-btn <?php echo $status_filter==='Ditolak'  ? 'active' : ''; ?>">Ditolak</a>
                    </div>
                    <small style="color:#9ca3af;"><?php echo $total; ?> data</small>
                </div>
                <div class="admin-card-body p-0">
                    <div class="table-responsive">
                        <table class="tbl-ppdb table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>No. Pendaftaran</th>
                                    <th>Nama Siswa</th>
                                    <th>NISN</th>

                                    <th>L/P</th>
                                    <th>Tgl Lahir</th>
                                    <th>Asal TK</th>
                                    <th>HP Ortu</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($rows) > 0):
                                    $no = $offset + 1;
                                    while ($row = mysqli_fetch_assoc($rows)): ?>
                                <tr>
                                    <td style="color:#9ca3af;"><?php echo $no++; ?></td>
                                    <td style="font-family:monospace;font-weight:700;color:#d4af37;font-size:.82rem;">
                                        <?php echo htmlspecialchars($row['no_pendaftaran']); ?>
                                    </td>
                                    <td style="font-weight:600;">
                                        <?php echo htmlspecialchars($row['nama_lengkap'] ?? ''); ?>
                                    </td>

                                    <td style="font-weight:600;">
                                        <?php echo htmlspecialchars($row['nisn'] ?? ''); ?>
                                    </td>
                                    <td>
                                        <?php echo $row['jenis_kelamin']==='Laki-laki' ? '♂️ L' : '♀️ P'; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_lahir'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['asal_tk'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($row['no_hp_ortu']); ?></td>

                                    <td>

                                        <?php $sc = strtolower($row['status']); ?>
                                        <span class="badge-<?php echo $sc; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                                    </td>
<td style="white-space:nowrap;">
                                        <a href="proses_status.php?id=<?php echo (int)$row['id']; ?>&status=Diterima" class="btn-acc" title="ACC" aria-label="ACC">
                                            <i data-lucide="check" style="width:16px;height:16px;"></i>
                                        </a>
                                        <a href="proses_status.php?id=<?php echo (int)$row['id']; ?>&status=Ditolak" class="btn-tolak" title="TOLAK" aria-label="TOLAK" onclick="return confirm('Tolak pendaftaran ini?')">
                                            <i data-lucide="x" style="width:16px;height:16px;"></i>
                                        </a>
                                        <a href="edit_ppdb.php?id=<?php echo (int)$row['id']; ?>" class="btn-edit text-decoration-none">Edit</a>

                                        <form method="POST" action="admin_ppdb.php" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                                            <button type="submit" class="btn-hapus" onclick="return confirm('Yakin hapus data ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                    <?php endwhile;
                                else: ?>
                                <tr><td colspan="9" class="text-center text-muted py-5">Belum ada data pendaftaran.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_pages > 1): ?>
                    <div class="p-3 border-top d-flex justify-content-center gap-2 flex-wrap">
                        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                        <a href="admin_ppdb.php?page=<?php echo $p; ?>&status=<?php echo urlencode($status_filter); ?>"
                           class="filter-btn <?php echo $p===$page ? 'active' : ''; ?>">
                            <?php echo $p; ?>
                        </a>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div><!-- /admin-page -->
    </div><!-- /admin-content -->
</div><!-- /admin-wrapper -->

<!-- MODAL CREATE -->
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
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap *</label>
                            <input type="text" class="form-control" name="nama_lengkap" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Kelamin *</label>
                            <select class="form-control" name="jenis_kelamin" required>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tempat Lahir *</label>
                            <input type="text" class="form-control" name="tempat_lahir" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Lahir *</label>
                            <input type="date" class="form-control" name="tanggal_lahir" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Agama *</label>
                            <select class="form-control" name="agama" required>
                                <option value="">-- Pilih --</option>
                                <option>Islam</option><option>Kristen</option><option>Katolik</option>
                                <option>Hindu</option><option>Buddha</option><option>Konghucu</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asal TK</label>
                            <input type="text" class="form-control" name="asal_tk">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat *</label>
                            <textarea class="form-control" name="alamat" rows="2" required></textarea>
                        </div>
                        <div class="col-12"><hr class="my-1"><small class="fw-bold text-muted">DATA ORANG TUA</small></div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Ayah *</label>
                            <input type="text" class="form-control" name="nama_ayah" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Ibu</label>
                            <input type="text" class="form-control" name="nama_ibu">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pekerjaan Orang Tua *</label>
                            <input type="text" class="form-control" name="pekerjaan_ortu" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP Orang Tua *</label>
                            <input type="text" class="form-control" name="no_hp_ortu" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background:#1a1a2e;border:none;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a1a2e;">
                <h5 class="modal-title text-white">Edit Data Pendaftar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="admin_ppdb.php">
                <div class="modal-body" id="editFormContent">
                    <p class="text-muted text-center py-3">Memuat data...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background:#1a1a2e;border:none;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
lucide.createIcons();

// Sidebar toggle (sama seperti dashboard)
const toggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('admin-sidebar');
const overlay = document.getElementById('sidebarOverlay');
if (toggle && sidebar) {
    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        if (overlay) overlay.classList.toggle('show');
    });
}
if (overlay) {
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    });
}

// Edit modal
function editPendaftar(id) {
    fetch('admin_ppdb.php?edit=' + id)
        .then(r => r.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const tpl = doc.getElementById('editFormTemplate');
            if (tpl) {
                document.getElementById('editFormContent').innerHTML = tpl.innerHTML;
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            }
        })
        .catch(() => alert('Gagal memuat data'));
}
</script>

<?php if ($edit_data): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('editFormContent').innerHTML = `
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?php echo (int)$edit_data['id']; ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama Lengkap *</label>
                <input type="text" class="form-control" name="nama_lengkap" value="<?php echo htmlspecialchars($edit_data['nama_lengkap']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Jenis Kelamin *</label>
                <select class="form-control" name="jenis_kelamin" required>
                    <option value="Laki-laki" <?php if($edit_data['jenis_kelamin']=='Laki-laki') echo 'selected'; ?>>Laki-laki</option>
                    <option value="Perempuan" <?php if($edit_data['jenis_kelamin']=='Perempuan') echo 'selected'; ?>>Perempuan</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Tempat Lahir *</label>
                <input type="text" class="form-control" name="tempat_lahir" value="<?php echo htmlspecialchars($edit_data['tempat_lahir']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Tanggal Lahir *</label>
                <input type="date" class="form-control" name="tanggal_lahir" value="<?php echo $edit_data['tanggal_lahir']; ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Agama *</label>
                <select class="form-control" name="agama" required>
                    <option value="Islam" <?php if($edit_data['agama']=='Islam') echo 'selected'; ?>>Islam</option>
                    <option value="Kristen" <?php if($edit_data['agama']=='Kristen') echo 'selected'; ?>>Kristen</option>
                    <option value="Katolik" <?php if($edit_data['agama']=='Katolik') echo 'selected'; ?>>Katolik</option>
                    <option value="Hindu" <?php if($edit_data['agama']=='Hindu') echo 'selected'; ?>>Hindu</option>
                    <option value="Buddha" <?php if($edit_data['agama']=='Buddha') echo 'selected'; ?>>Buddha</option>
                    <option value="Konghucu" <?php if($edit_data['agama']=='Konghucu') echo 'selected'; ?>>Konghucu</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Asal TK</label>
                <input type="text" class="form-control" name="asal_tk" value="<?php echo htmlspecialchars($edit_data['asal_tk'] ?? ''); ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Alamat *</label>
                <textarea class="form-control" name="alamat" rows="2" required><?php echo htmlspecialchars($edit_data['alamat']); ?></textarea>
            </div>
            <div class="col-12"><hr class="my-1"><small class="fw-bold text-muted">DATA ORANG TUA</small></div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama Ayah *</label>
                <input type="text" class="form-control" name="nama_ayah" value="<?php echo htmlspecialchars($edit_data['nama_ayah']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama Ibu</label>
                <input type="text" class="form-control" name="nama_ibu" value="<?php echo htmlspecialchars($edit_data['nama_ibu'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Pekerjaan Orang Tua *</label>
                <input type="text" class="form-control" name="pekerjaan_ortu" value="<?php echo htmlspecialchars($edit_data['pekerjaan_ortu']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">No. HP Orang Tua *</label>
                <input type="text" class="form-control" name="no_hp_ortu" value="<?php echo htmlspecialchars($edit_data['no_hp_ortu']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status *</label>
                <select class="form-control" name="status" required>
                    <option value="Menunggu" <?php if($edit_data['status']=='Menunggu') echo 'selected'; ?>>Menunggu</option>
                    <option value="Diterima" <?php if($edit_data['status']=='Diterima') echo 'selected'; ?>>Diterima</option>
                    <option value="Ditolak"  <?php if($edit_data['status']=='Ditolak')  echo 'selected'; ?>>Ditolak</option>
                </select>
            </div>
        </div>
    `;
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
});
</script>
<?php endif; ?>

</body>
</html>