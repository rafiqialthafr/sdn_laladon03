<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM teachers WHERE id=$id");
    header("Location: admin_guru.php?success=deleted");
    exit;
}

$success = $_GET['success'] ?? '';
$search  = trim($_GET['q'] ?? '');
$where   = $search ? "WHERE name LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%' OR position LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%'" : '';
$res     = mysqli_query($koneksi, "SELECT * FROM teachers $where ORDER BY id ASC");
$total   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM teachers $where"))['t'];

$unread_messages = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM contact_messages WHERE is_read=0"))['t'];
$hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$today = $hari[date('w')] . ', ' . date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Guru — Admin SDN Laladon 03</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/admin.css" rel="stylesheet">
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
                <p class="topbar-title">Data Guru &amp; Staf</p>
                <p class="topbar-subtitle"><?php echo $today; ?></p>
            </div>
        </div>
        <div class="topbar-right">
            <?php if ($unread_messages > 0): ?>
            <a href="admin_pesan.php" class="topbar-notif" title="Pesan baru" style="text-decoration:none;">
                <i data-lucide="bell" style="width:17px;height:17px;"></i>
                <span class="notif-dot"></span>
            </a>
            <?php endif; ?>
            <div class="d-none d-md-block text-end">
                <p class="topbar-user-name">Administrator</p>
                <p class="topbar-user-role">Super Admin</p>
            </div>
            <div class="topbar-avatar">A</div>
        </div>
    </div>

    <div class="admin-page">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Guru &amp; Staf</h1>
                <p class="page-breadcrumb">
                    <a href="admin_dashboard.php" style="color:#94a3b8;text-decoration:none;">Dashboard</a>
                    &rsaquo; <span>Data Guru &amp; Staf</span>
                </p>
            </div>
            <a href="teacher_form.php" class="btn-admin btn-admin-primary">
                <i data-lucide="user-plus"></i>
                Tambah Guru
            </a>
        </div>

        <!-- Alerts -->
        <?php if ($success === 'deleted'): ?>
        <div class="alert-admin alert-error mb-4">
            <i data-lucide="check-circle"></i>
            Data guru berhasil dihapus.
        </div>
        <?php endif; ?>
        <?php if ($success === 'saved'): ?>
        <div class="alert-admin alert-success mb-4">
            <i data-lucide="check-circle"></i>
            Data guru berhasil disimpan.
        </div>
        <?php endif; ?>

        <!-- Stats mini -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon amber"><i data-lucide="users" style="width:22px;height:22px;"></i></div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as t FROM teachers"))['t']; ?></div>
                        <div class="stat-label">Total Guru &amp; Staf</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon green"><i data-lucide="user-check" style="width:22px;height:22px;"></i></div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as t FROM teachers WHERE position LIKE '%Guru%'"))['t']; ?></div>
                        <div class="stat-label">Guru Kelas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon blue"><i data-lucide="briefcase" style="width:22px;height:22px;"></i></div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as t FROM teachers WHERE position LIKE '%Kepala%'"))['t']; ?></div>
                        <div class="stat-label">Kepala Sekolah</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon purple"><i data-lucide="user-cog" style="width:22px;height:22px;"></i></div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as t FROM teachers WHERE position NOT LIKE '%Guru%' AND position NOT LIKE '%Kepala%'"))['t']; ?></div>
                        <div class="stat-label">Staf Lainnya</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="admin-card">
            <div class="table-toolbar">
                <form method="GET" class="d-flex align-items-center gap-2 flex-wrap w-100">
                    <div class="search-wrap">
                        <i data-lucide="search"></i>
                        <input type="text" name="q" class="search-input" placeholder="Cari nama atau jabatan..."
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <button type="submit" class="btn-admin btn-admin-secondary">Cari</button>
                    <?php if ($search): ?>
                    <a href="admin_guru.php" class="btn-admin btn-admin-secondary">Reset</a>
                    <?php endif; ?>
                    <span style="margin-left:auto;font-size:.8rem;color:#94a3b8;"><?php echo $total; ?> data ditemukan</span>
                </form>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Nama Guru / Staf</th>
                            <th>Jabatan</th>
                            <th>Bio Singkat</th>
                            <th class="text-center" style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($res) > 0):
                              $no = 1;
                              while ($r = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td style="color:#94a3b8;font-weight:600;"><?php echo $no++; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?php echo htmlspecialchars($r['photo']); ?>" class="tbl-avatar" alt="">
                                    <div>
                                        <div class="tbl-name"><?php echo htmlspecialchars($r['name']); ?></div>
                                        <div class="tbl-sub">ID #<?php echo $r['id']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="display:inline-flex;align-items:center;gap:.35rem;font-size:.8rem;background:#f1f5f9;color:#475569;padding:.3rem .75rem;border-radius:50px;font-weight:600;">
                                    <i data-lucide="briefcase" style="width:12px;height:12px;"></i>
                                    <?php echo htmlspecialchars($r['position']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="tbl-sub" style="max-width:300px;display:block;">
                                    <?php echo htmlspecialchars(substr($r['bio'] ?? '', 0, 80)); ?>...
                                </span>
                            </td>
                            <td>
                                <div class="action-wrap justify-content-center">
                                    <a href="teacher_form.php?id=<?php echo $r['id']; ?>" class="btn-tbl btn-tbl-edit" title="Edit">
                                        <i data-lucide="pencil"></i>
                                    </a>
                                    <a href="admin_guru.php?delete=<?php echo $r['id']; ?>"
                                       class="btn-tbl btn-tbl-delete" title="Hapus"
                                       onclick="return confirm('Yakin ingin menghapus guru ini?')">
                                        <i data-lucide="trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="tbl-empty">
                                    <div class="tbl-empty-icon">
                                        <i data-lucide="users" style="width:28px;height:28px;"></i>
                                    </div>
                                    <?php echo $search ? 'Tidak ada guru dengan kata kunci "<strong>' . htmlspecialchars($search) . '</strong>".' : 'Belum ada data guru &amp; staf.'; ?>
                                    <?php if (!$search): ?>
                                    <div class="mt-3">
                                        <a href="teacher_form.php" class="btn-admin btn-admin-primary">
                                            <i data-lucide="user-plus"></i> Tambah Guru Pertama
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div><!-- /admin-card -->
    </div><!-- /admin-page -->
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
lucide.createIcons();
const toggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('admin-sidebar');
const overlay = document.getElementById('sidebarOverlay');
if(toggle) toggle.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); });
if(overlay) overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); });
</script>
</body>
</html>
