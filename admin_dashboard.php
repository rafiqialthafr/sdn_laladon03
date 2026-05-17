<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: masuk-admin-03.php");
    exit;
}
include 'koneksi.php';

// Stats
$total_teachers = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM guru_staf"))['t'];
$total_published = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM berita WHERE is_published=1"))['t'];
$total_galeri = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM galeri"))['t'];
$total_fasilitas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM fasilitas"))['t'];

// Recent teachers
$recent_teachers = mysqli_query($koneksi, "SELECT * FROM guru_staf ORDER BY id DESC LIMIT 4");
// Recent news
$recent_news = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY created_at DESC LIMIT 5");


// Chart data: news per category
$cat_data = [];
$res_cat = mysqli_query($koneksi, "SELECT category, COUNT(*) as cnt FROM berita GROUP BY category");
while ($row = mysqli_fetch_assoc($res_cat)) {
    $cat_data[$row['category']] = $row['cnt'];
}

$hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$today = $hari[date('w')] . ', ' . date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="icon" type="image/png" href="img/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Admin SDN Laladon 03</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>

<body>
    <div class="admin-wrapper">

        <?php include 'admin_sidebar.php'; ?>

        <div id="admin-content" class="admin-content">

            <!-- Topbar -->
            <div class="admin-topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i data-lucide="menu" style="width:18px;height:18px;"></i>
                    </button>
                    <div>
                        <p class="topbar-title">Dashboard</p>
                        <p class="topbar-subtitle"><?php echo $today; ?></p>
                    </div>
                </div>
                <div class="topbar-right">
                    <div class="topbar-user-text text-end">
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
                        <h1 class="page-title">Selamat Datang! 👋</h1>
                        <p class="page-breadcrumb">Ringkasan kondisi website SDN Laladon 03 hari ini</p>
                    </div>
                </div>

                <!-- Stat Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-xl-3">
                        <div class="stat-card">
                            <div class="stat-icon amber">
                                <i data-lucide="users" style="width:24px;height:24px;"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $total_teachers; ?></div>
                                <div class="stat-label">Guru &amp; Staf</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="stat-card">
                            <div class="stat-icon green">
                                <i data-lucide="file-check" style="width:24px;height:24px;"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $total_published; ?></div>
                                <div class="stat-label">Berita Tayang</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="stat-card">
                            <div class="stat-icon purple">
                                <i data-lucide="building-2" style="width:24px;height:24px;"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $total_fasilitas; ?></div>
                                <div class="stat-label">Fasilitas</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="stat-card">
                            <div class="stat-icon cyan">
                                <i data-lucide="image" style="width:24px;height:24px;"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $total_galeri; ?></div>
                                <div class="stat-label">Foto Galeri</div>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- Charts + Activity Row -->
                <div class="row g-3 mb-4">
                    <!-- Donut Chart -->
                    <div class="col-lg-4">
                        <div class="admin-card h-100">
                            <div class="admin-card-header">
                                <h5 class="admin-card-title">
                                    <i data-lucide="pie-chart"></i>
                                    Distribusi Berita
                                </h5>
                            </div>
                            <div class="admin-card-body text-center">
                                <div class="chart-container">
                                    <canvas id="catChart"></canvas>
                                </div>
                                <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
                                    <div
                                        style="display:flex;align-items:center;gap:.4rem;font-size:.75rem;color:#374151;">
                                        <span
                                            style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                                        Pengumuman
                                    </div>
                                    <div
                                        style="display:flex;align-items:center;gap:.4rem;font-size:.75rem;color:#374151;">
                                        <span
                                            style="width:10px;height:10px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                                        Berita
                                    </div>
                                    <div
                                        style="display:flex;align-items:center;gap:.4rem;font-size:.75rem;color:#374151;">
                                        <span
                                            style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                                        Event
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="col-lg-8">
                        <div class="admin-card h-100">
                            <div class="admin-card-header">
                                <h5 class="admin-card-title">
                                    <i data-lucide="activity"></i>
                                    Aktivitas Terbaru
                                </h5>
                                <a href="admin_berita.php" class="btn-admin btn-admin-secondary"
                                    style="font-size:.75rem;padding:.4rem .8rem;">Lihat Semua</a>
                            </div>
                            <div class="admin-card-body p-0">
                                <?php
                                $no_act = 0;
                                $res_act = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY created_at DESC LIMIT 6");
                                while ($act = mysqli_fetch_assoc($res_act)):
                                    $colors = ['pengumuman' => 'rose', 'berita' => 'green', 'event' => 'amber'];
                                    $icons = ['pengumuman' => 'megaphone', 'berita' => 'newspaper', 'event' => 'calendar'];
                                    $col = $colors[$act['category']] ?? 'blue';
                                    $ico = $icons[$act['category']] ?? 'file-text';
                                    ?>
                                    <div class="activity-item px-4">
                                        <div class="activity-dot <?php echo $col; ?>">
                                            <i data-lucide="<?php echo $ico; ?>" style="width:15px;height:15px;"></i>
                                        </div>
                                        <div style="flex:1;min-width:0;">
                                            <div class="activity-text"
                                                style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                <strong><?php echo htmlspecialchars($act['title']); ?></strong>
                                            </div>
                                            <div class="activity-time">
                                                <?php echo ucfirst($act['category']); ?> &middot;
                                                <?php echo date('d M Y H:i', strtotime($act['created_at'])); ?>
                                                &middot; <span
                                                    class="badge-status <?php echo $act['is_published'] ? 'badge-published' : 'badge-draft'; ?>"
                                                    style="font-size:.65rem;"><?php echo $act['is_published'] ? 'Published' : 'Draft'; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent teachers -->
                <div class="row g-3">
                    <!-- Recent Teachers -->
                    <div class="col-12">
                        <div class="admin-card">
                            <div class="admin-card-header">
                                <h5 class="admin-card-title">
                                    <i data-lucide="users"></i>
                                    Guru &amp; Staf Terbaru
                                </h5>
                                <a href="admin_guru.php" class="btn-admin btn-admin-secondary"
                                    style="font-size:.75rem;padding:.4rem .8rem;">Kelola Semua</a>
                            </div>
                            <div class="table-responsive">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($recent_teachers) > 0):
                                            while ($t = mysqli_fetch_assoc($recent_teachers)): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <img src="<?php echo htmlspecialchars($t['photo']); ?>"
                                                                class="tbl-avatar" alt="">
                                                            <div class="tbl-name"><?php echo htmlspecialchars($t['name']); ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td style="font-size:.8rem;color:#64748b;">
                                                        <?php echo htmlspecialchars($t['position']); ?>
                                                    </td>
                                                    <td>
                                                        <div class="action-wrap justify-content-center">
                                                            <a href="teacher_form.php?id=<?php echo $t['id']; ?>"
                                                                class="btn-tbl btn-tbl-edit" title="Edit">
                                                                <i data-lucide="pencil"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; else: ?>
                                            <tr>
                                                <td colspan="3" class="tbl-empty">
                                                    <div class="tbl-empty-icon"><i data-lucide="inbox"
                                                            style="width:26px;height:26px;"></i></div>
                                                    Belum ada data guru.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /row -->

            <!-- Galeri + Fasilitas Row -->
            <div class="row g-3 mt-1 mb-4">

                <!-- Recent Gallery -->
                <div class="col-lg-6">
                    <div class="admin-card h-100">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">
                                <i data-lucide="image"></i>
                                Galeri Terbaru
                            </h5>
                            <a href="admin_galeri.php" class="btn-admin btn-admin-secondary"
                                style="font-size:.75rem;padding:.4rem .8rem;">Kelola Galeri</a>
                        </div>
                        <div class="admin-card-body">
                            <div class="row g-2">
                                <?php
                                $recent_galeri = mysqli_query($koneksi, "SELECT * FROM galeri ORDER BY id DESC LIMIT 4");
                                if (mysqli_num_rows($recent_galeri) > 0):
                                    while ($g = mysqli_fetch_assoc($recent_galeri)):
                                        ?>
                                        <div class="col-6">
                                            <div
                                                style="border-radius:10px;overflow:hidden;position:relative;aspect-ratio:4/3;background:#f8fafc;border:1px solid #f1f5f9;box-shadow:0 2px 4px rgba(0,0,0,0.04);">
                                                <img src="<?php echo htmlspecialchars($g['foto']); ?>"
                                                    style="width:100%;height:100%;object-fit:cover;" alt="">
                                                <div
                                                    style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent, rgba(0,0,0,0.8));padding:1.5rem .5rem .5rem;color:#fff;font-size:.75rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                    <?php echo htmlspecialchars($g['judul']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; else: ?>
                                    <div class="col-12 text-center text-muted" style="font-size:.85rem;padding:2rem 0;">
                                        Belum ada foto galeri.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Fasilitas -->
                <div class="col-lg-6">
                    <div class="admin-card h-100">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">
                                <i data-lucide="building-2"></i>
                                Fasilitas
                            </h5>
                            <a href="admin_fasilitas.php" class="btn-admin btn-admin-secondary"
                                style="font-size:.75rem;padding:.4rem .8rem;">Kelola Fasilitas</a>
                        </div>
                        <div class="admin-card-body p-0">
                            <?php
                            $recent_fasilitas = mysqli_query($koneksi, "SELECT * FROM fasilitas ORDER BY id DESC LIMIT 5");
                            if (mysqli_num_rows($recent_fasilitas) > 0):
                                while ($f = mysqli_fetch_assoc($recent_fasilitas)):
                            ?>
                                <div class="activity-item px-4">
                                    <div style="width:40px;height:40px;border-radius:10px;overflow:hidden;flex-shrink:0;background:#f1f5f9;">
                                        <img src="<?php echo htmlspecialchars($f['image']); ?>"
                                            style="width:100%;height:100%;object-fit:cover;" alt="">
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div class="activity-text" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            <strong><?php echo htmlspecialchars($f['name']); ?></strong>
                                        </div>
                                        <div class="activity-time" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            <?php echo htmlspecialchars($f['description'] ?: '—'); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; else: ?>
                                <div class="p-4 text-center text-muted" style="font-size:.85rem;">Belum ada fasilitas.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div><!-- /row -->
        </div><!-- /admin-page -->
    </div><!-- /admin-content -->
    </div><!-- /admin-wrapper -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        lucide.createIcons();

        // Sidebar toggle
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (toggle) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            });
        }
        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });
        }

        // Donut chart
        const ctx = document.getElementById('catChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pengumuman', 'Berita', 'Event'],
                datasets: [{
                    data: [
                        <?php echo $cat_data['pengumuman'] ?? 0; ?>,
                        <?php echo $cat_data['berita'] ?? 0; ?>,
                        <?php echo $cat_data['event'] ?? 0; ?>
                    ],
                    backgroundColor: ['#ef4444', '#22c55e', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                cutout: '70%',
                plugins: { legend: { display: false } },
                responsive: true,
                maintainAspectRatio: true
            }
        });
    </script>
</body>

</html>
