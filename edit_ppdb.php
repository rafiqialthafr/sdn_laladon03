<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

header('Content-Type: text/html; charset=UTF-8');

include 'koneksi.php';

/** @var mysqli $koneksi */

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: admin_ppdb.php");
    exit;
}

$edit_data = null;
$stmt = mysqli_prepare($koneksi, "SELECT * FROM ppdb WHERE id=? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$edit_data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$edit_data) {
    header("Location: admin_ppdb.php");
    exit;
}

// Karena kolom NISN belum ada di DB, kita tetap siapkan value untuk inputnya.
$nisn_value = isset($edit_data['nisn']) ? (string)$edit_data['nisn'] : '';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit PPDB — Admin SDN Laladon 03</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">

    <style>
        .admin-page { padding: 24px 22px 60px; }
        .page-title { color:#111827; font-weight:800; }
        .card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,.06); }
        .card-header { background:#ffffff; border-bottom: 1px solid rgba(17,24,39,.08); }
        .label { font-weight: 700; color:#374151; font-size: .92rem; }
        .btn-save { background:#1a1a2e; border:none; }
        .btn-save:hover { background:#0f0f23; }
        .alert-err { background:#fef2f2; border:1.5px solid #fca5a5; color:#991b1b; border-radius:10px; padding:12px 18px; font-size:.9rem; }
    </style>
</head>
<body>
<div class="admin-wrapper">

    <?php include 'admin_sidebar.php'; ?>

    <div id="admin-content">

        <!-- Topbar (header putih) -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i data-lucide="menu" style="width:18px;height:18px;"></i>
                </button>
                <div>
                    <p class="topbar-title">Edit Data PPDB</p>
                    <p class="topbar-subtitle">Perbarui identitas calon peserta didik</p>
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

        <div class="admin-page">
            <div class="page-header mb-3">
<h1 class="page-title">📋 Edit PPDB</h1>
                <p class="page-breadcrumb mb-0">ID pendaftar: <span style="font-family:monospace;">#<?php echo (int)$edit_data['id']; ?></span></p>
            </div>

            <div class="card">
                <div class="card-header py-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0" style="font-weight:800; color:#111827;">Form Edit</h5>
                            <small style="color:#6b7280;">Otomatis terisi berdasarkan data lama</small>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="proses_edit_ppdb.php">
                        <input type="hidden" name="id_pendaftar" value="<?php echo (int)$edit_data['id']; ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="label">Nama *</label>
                                <input type="text" class="form-control" name="nama_lengkap" required
                                       value="<?php echo htmlspecialchars($edit_data['nama_lengkap'] ?? ''); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="label">Jenis Kelamin</label>
                                <select class="form-select" name="jenis_kelamin" required>
                                    <option value="Laki-laki" <?php echo (($edit_data['jenis_kelamin'] ?? '') === 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="Perempuan" <?php echo (($edit_data['jenis_kelamin'] ?? '') === 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="label">NISN</label>
                                <input type="text" class="form-control" name="nisn" 
                                       value="<?php echo htmlspecialchars($nisn_value); ?>">
                                <div class="form-text">Kolom NISN belum ada di database saat ini (akan sinkron setelah Anda menambahkan kolom).</div>
                            </div>

                            <div class="col-md-6">
                                <label class="label">Asal TK</label>
                                <input type="text" class="form-control" name="asal_tk"
                                       value="<?php echo htmlspecialchars($edit_data['asal_tk'] ?? ''); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="label">HP Ortu *</label>
                                <input type="text" class="form-control" name="no_hp_ortu" required
                                       value="<?php echo htmlspecialchars($edit_data['no_hp_ortu'] ?? ''); ?>">
                            </div>

                            <div class="col-12">
                                <label class="label">Status *</label>
                                <select class="form-select" name="status" required>
                                    <option value="Menunggu" <?php echo (($edit_data['status'] ?? '') === 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                    <option value="Diterima" <?php echo (($edit_data['status'] ?? '') === 'Diterima') ? 'selected' : ''; ?>>Diterima</option>
                                    <option value="Ditolak" <?php echo (($edit_data['status'] ?? '') === 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end flex-wrap mt-4">
                            <a href="admin_ppdb.php" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-save btn-primary px-4">Simpan</button>
                        </div>
                    </form>

                </div>
            </div>

        </div><!-- /admin-page -->

    </div><!-- /admin-content -->
</div><!-- /admin-wrapper -->

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

