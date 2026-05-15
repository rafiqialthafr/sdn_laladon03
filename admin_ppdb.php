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


$hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$today = $hari[date('w')] . ', ' . date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="img/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data PPDB — Admin SDN Laladon 03</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
    <style>
        .tab-btn { padding:.4rem 1rem; border:1.5px solid #e2e8f0; border-radius:8px; background:#fff; font-size:.78rem; font-weight:600; color:#64748b; text-decoration:none; transition:all .2s; }
        .tab-btn:hover { border-color:#fbbf24; color:#d97706; }
        .tab-btn.active { background:#1a1a2e; color:#fff; border-color:#1a1a2e; }
    </style>
</head>
<body>
<div class="admin-wrapper">

    <?php include 'admin_sidebar.php'; ?>

    <div class="admin-content">

        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i data-lucide="menu" style="width:18px;height:18px;"></i>
                </button>
                <div>
                    <p class="topbar-title">Data PPDB</p>
                    <p class="topbar-subtitle"><?php echo $today; ?></p>
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

            <div class="page-header">
                    <div>
                        <h1 class="page-title">Data PPDB</h1>
                        <p class="page-breadcrumb">
                            <a href="admin_dashboard.php" style="color:#94a3b8;text-decoration:none;">Dashboard</a>
                            &rsaquo; <span>Data PPDB</span>
                        </p>
                    </div>
                </div>

            <?php if (!empty($success_msg)): ?>
                <div class="alert-admin alert-success"><i data-lucide="check-circle" style="width:18px;height:18px;"></i> <?php echo $success_msg; ?></div>
            <?php endif; ?>
            <?php if (!empty($error_msg)): ?>
                <div class="alert-admin alert-error"><i data-lucide="alert-circle" style="width:18px;height:18px;"></i> <?php echo $error_msg; ?></div>
            <?php endif; ?>

            <!-- STATISTIK -->
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

            <!-- TABLE -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="admin_ppdb.php" class="tab-btn <?php echo !$status_filter ? 'active' : ''; ?>">Semua</a>
                        <a href="admin_ppdb.php?status=Menunggu" class="tab-btn <?php echo $status_filter==='Menunggu' ? 'active' : ''; ?>">Menunggu</a>
                        <a href="admin_ppdb.php?status=Diterima" class="tab-btn <?php echo $status_filter==='Diterima' ? 'active' : ''; ?>">Diterima</a>
                        <a href="admin_ppdb.php?status=Ditolak"  class="tab-btn <?php echo $status_filter==='Ditolak'  ? 'active' : ''; ?>">Ditolak</a>
                    </div>
                    <small style="color:#94a3b8;"><?php echo $total; ?> data</small>
                </div>
                <div class="admin-card-body p-0">
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>No. Pendaftaran</th>
                                    <th>Nama Siswa</th>
                                    <th>NISN</th>

                                    <th>L/P</th>
                                    <th>Tgl Lahir</th>
                                    <th>AGAMA</th>
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
                                    <td style="color:#94a3b8;"><?php echo $no++; ?></td>
                                    <td style="font-family:monospace;font-weight:700;color:#d97706;font-size:.82rem;"><?php echo htmlspecialchars($row['no_pendaftaran']); ?></td>
                                    <td><div class="tbl-name"><?php echo htmlspecialchars($row['nama_lengkap'] ?? ''); ?></div></td>
                                    <td><?php echo htmlspecialchars($row['nisn'] ?? '-'); ?></td>
                                    <td><?php echo $row['jenis_kelamin']==='Laki-laki' ? 'L' : 'P'; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_lahir'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['agama'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($row['asal_tk'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($row['no_hp_ortu']); ?></td>
                                    <td><?php
                                        $badge = match($row['status']) {
                                            'Diterima' => 'badge-published',
                                            'Ditolak'  => 'badge-rejected',
                                            default    => 'badge-new',
                                        };
                                    ?><span class="badge-status <?php echo $badge; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                    <td>
                                        <div class="action-wrap">
                                            <a href="proses_status.php?id=<?php echo (int)$row['id']; ?>&status=Diterima" class="btn-tbl btn-tbl-view" title="Terima"><i data-lucide="check" style="width:13px;height:13px;"></i></a>
                                            <a href="proses_status.php?id=<?php echo (int)$row['id']; ?>&status=Ditolak" class="btn-tbl btn-tbl-delete" title="Tolak" onclick="return confirm('Tolak pendaftaran ini?')"><i data-lucide="x" style="width:13px;height:13px;"></i></a>

                                            <form method="POST" action="admin_ppdb.php" style="display:inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                                                <button type="submit" class="btn-tbl btn-tbl-delete" title="Hapus" onclick="return confirm('Yakin hapus data ini?')"><i data-lucide="trash-2" style="width:13px;height:13px;"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                    <?php endwhile;
                                else: ?>
                                <tr><td colspan="11"><div class="tbl-empty"><div class="tbl-empty-icon"><i data-lucide="inbox" style="width:28px;height:28px;"></i></div>Belum ada data pendaftaran.</div></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_pages > 1): ?>
                    <div class="p-3 border-top d-flex justify-content-center gap-2 flex-wrap">
                        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                        <a href="admin_ppdb.php?page=<?php echo $p; ?>&status=<?php echo urlencode($status_filter); ?>" class="tab-btn <?php echo $p===$page ? 'active' : ''; ?>"><?php echo $p; ?></a>
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
                    <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-admin btn-admin-primary"><i data-lucide="save" style="width:15px;height:15px;"></i> Simpan</button>
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
</script>
</body>
</html>