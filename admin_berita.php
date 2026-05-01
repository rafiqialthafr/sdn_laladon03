<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM announcements WHERE id=$id");
    header("Location: admin_berita.php?success=deleted");
    exit;
}
// Handle toggle publish
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $cur = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT is_published FROM announcements WHERE id=$id"));
    $new = $cur['is_published'] ? 0 : 1;
    mysqli_query($koneksi, "UPDATE announcements SET is_published=$new WHERE id=$id");
    header("Location: admin_berita.php?success=toggled");
    exit;
}

$success  = $_GET['success'] ?? '';
$search   = trim($_GET['q'] ?? '');
$cat_f    = $_GET['cat'] ?? '';
$status_f = $_GET['status'] ?? '';

$conditions = [];
if ($search)   $conditions[] = "(title LIKE '%" . mysqli_real_escape_string($koneksi,$search) . "%')";
if ($cat_f)    $conditions[] = "category='" . mysqli_real_escape_string($koneksi,$cat_f) . "'";
if ($status_f !== '') $conditions[] = "is_published=" . (int)$status_f;
$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : '';

$res   = mysqli_query($koneksi, "SELECT * FROM announcements $where ORDER BY created_at DESC");
$total = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM announcements $where"))['t'];

$unread_messages = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as t FROM contact_messages WHERE is_read=0"))['t'];
$hari  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$today = $hari[date('w')] . ', ' . date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Berita — Admin SDN Laladon 03</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/admin.css" rel="stylesheet">
</head>
<body>
<div class="admin-wrapper">
<?php include 'admin_sidebar.php'; ?>

<div id="admin-content">
    <div class="admin-topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i data-lucide="menu" style="width:18px;height:18px;"></i>
            </button>
            <div>
                <p class="topbar-title">Berita &amp; Pengumuman</p>
                <p class="topbar-subtitle"><?php echo $today; ?></p>
            </div>
        </div>
        <div class="topbar-right">
            <?php if ($unread_messages > 0): ?>
            <a href="admin_pesan.php" class="topbar-notif" style="text-decoration:none;" title="Pesan baru">
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
        <div class="page-header">
            <div>
                <h1 class="page-title">Berita &amp; Pengumuman</h1>
                <p class="page-breadcrumb">
                    <a href="admin_dashboard.php" style="color:#94a3b8;text-decoration:none;">Dashboard</a>
                    &rsaquo; <span>Berita &amp; Pengumuman</span>
                </p>
            </div>
            <a href="announcement_form.php" class="btn-admin btn-admin-primary">
                <i data-lucide="file-plus"></i>
                Tambah Berita
            </a>
        </div>

        <!-- Alerts -->
        <?php if ($success === 'deleted'): ?>
        <div class="alert-admin alert-error mb-4"><i data-lucide="trash-2"></i> Berita berhasil dihapus.</div>
        <?php elseif ($success === 'toggled'): ?>
        <div class="alert-admin alert-success mb-4"><i data-lucide="check-circle"></i> Status berita berhasil diubah.</div>
        <?php elseif ($success === 'saved'): ?>
        <div class="alert-admin alert-success mb-4"><i data-lucide="check-circle"></i> Berita berhasil disimpan.</div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <?php
            $s_all  = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as t FROM announcements"))['t'];
            $s_pub  = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as t FROM announcements WHERE is_published=1"))['t'];
            $s_drft = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as t FROM announcements WHERE is_published=0"))['t'];
            $s_peng = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as t FROM announcements WHERE category='pengumuman'"))['t'];
            $s_ber  = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as t FROM announcements WHERE category='berita'"))['t'];
            $s_evt  = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as t FROM announcements WHERE category='event'"))['t'];
            ?>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon blue"><i data-lucide="newspaper" style="width:22px;height:22px;"></i></div>
                    <div class="stat-info"><div class="stat-value"><?php echo $s_all; ?></div><div class="stat-label">Total Semua</div></div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon green"><i data-lucide="check-circle" style="width:22px;height:22px;"></i></div>
                    <div class="stat-info"><div class="stat-value"><?php echo $s_pub; ?></div><div class="stat-label">Published</div></div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon purple"><i data-lucide="file-clock" style="width:22px;height:22px;"></i></div>
                    <div class="stat-info"><div class="stat-value"><?php echo $s_drft; ?></div><div class="stat-label">Draft</div></div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="admin-card">
            <div class="table-toolbar">
                <form method="GET" class="d-flex align-items-center gap-2 flex-wrap w-100">
                    <div class="search-wrap">
                        <i data-lucide="search"></i>
                        <input type="text" name="q" class="search-input" placeholder="Cari judul berita..."
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <select name="cat" class="filter-select">
                        <option value="">Semua Kategori</option>
                        <option value="pengumuman" <?php echo $cat_f==='pengumuman'?'selected':''; ?>>Pengumuman</option>
                        <option value="berita"     <?php echo $cat_f==='berita'?'selected':''; ?>>Berita</option>
                        <option value="event"      <?php echo $cat_f==='event'?'selected':''; ?>>Event</option>
                    </select>
                    <select name="status" class="filter-select">
                        <option value="">Semua Status</option>
                        <option value="1" <?php echo $status_f==='1'?'selected':''; ?>>Published</option>
                        <option value="0" <?php echo $status_f==='0'?'selected':''; ?>>Draft</option>
                    </select>
                    <button type="submit" class="btn-admin btn-admin-secondary">Filter</button>
                    <?php if ($search||$cat_f||$status_f!==''): ?>
                    <a href="admin_berita.php" class="btn-admin btn-admin-secondary">Reset</a>
                    <?php endif; ?>
                    <span style="margin-left:auto;font-size:.8rem;color:#94a3b8;"><?php echo $total; ?> berita</span>
                </form>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:45px;">#</th>
                            <th>Judul Berita</th>
                            <th style="width:120px;">Kategori</th>
                            <th style="width:110px;">Tanggal</th>
                            <th style="width:110px;">Status</th>
                            <th class="text-center" style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($res) > 0):
                              $no = 1;
                              while ($r = mysqli_fetch_assoc($res)):
                            $cat_labels = ['pengumuman'=>'Pengumuman','berita'=>'Berita','event'=>'Event'];
                            $cat_cls    = ['pengumuman'=>'cat-pengumuman','berita'=>'cat-berita','event'=>'cat-event'];
                        ?>
                        <tr>
                            <td style="color:#94a3b8;font-weight:600;"><?php echo $no++; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?php echo htmlspecialchars($r['image']); ?>" class="tbl-thumb" alt="">
                                    <div class="tbl-name" style="max-width:320px;line-height:1.4;">
                                        <?php echo htmlspecialchars($r['title']); ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="cat-badge <?php echo $cat_cls[$r['category']] ?? 'cat-berita'; ?>">
                                    <?php echo $cat_labels[$r['category']] ?? 'Berita'; ?>
                                </span>
                            </td>
                            <td style="font-size:.78rem;color:#64748b;white-space:nowrap;">
                                <?php echo date('d M Y', strtotime($r['created_at'])); ?>
                            </td>
                            <td>
                                <span class="badge-status <?php echo $r['is_published'] ? 'badge-published' : 'badge-draft'; ?>">
                                    <?php echo $r['is_published'] ? 'Published' : 'Draft'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-wrap justify-content-center">
                                    <!-- Toggle publish -->
                                    <a href="admin_berita.php?toggle=<?php echo $r['id']; ?>"
                                       class="btn-tbl" title="<?php echo $r['is_published'] ? 'Jadikan Draft' : 'Publish'; ?>"
                                       style="background:<?php echo $r['is_published'] ? '#f1f5f9' : '#dcfce7'; ?>;color:<?php echo $r['is_published'] ? '#64748b' : '#15803d'; ?>;">
                                        <i data-lucide="<?php echo $r['is_published'] ? 'eye-off' : 'eye'; ?>"></i>
                                    </a>
                                    <a href="announcement_form.php?id=<?php echo $r['id']; ?>"
                                       class="btn-tbl btn-tbl-edit" title="Edit">
                                        <i data-lucide="pencil"></i>
                                    </a>
                                    <a href="admin_berita.php?delete=<?php echo $r['id']; ?>"
                                       class="btn-tbl btn-tbl-delete" title="Hapus"
                                       onclick="return confirm('Yakin hapus berita ini?')">
                                        <i data-lucide="trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="tbl-empty">
                                    <div class="tbl-empty-icon"><i data-lucide="newspaper" style="width:28px;height:28px;"></i></div>
                                    <?php echo ($search||$cat_f||$status_f!=='') ? 'Tidak ada berita sesuai filter.' : 'Belum ada berita.'; ?>
                                    <?php if (!$search && !$cat_f && $status_f===''): ?>
                                    <div class="mt-3">
                                        <a href="announcement_form.php" class="btn-admin btn-admin-primary">
                                            <i data-lucide="file-plus"></i> Tulis Berita Pertama
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
        </div>
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
