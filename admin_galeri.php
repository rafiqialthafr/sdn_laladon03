<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: masuk-admin-03.php");
    exit;
}
include 'koneksi.php';

$success_msg = '';
$error_msg = '';

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM galeri WHERE id=$id");
    header("Location: admin_galeri.php?success=deleted");
    exit;
}

// Handle ADD / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $kategori = trim($_POST['kategori'] ?? 'kegiatan');
    $edit_id = (int) ($_POST['edit_id'] ?? 0);

    $foto_url = '';
    // Upload file jika ada
    if (!empty($_FILES['foto']['name'])) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($ext, $allowed)) {
            if ($_FILES['foto']['size'] > 3 * 1024 * 1024) {
                $error_msg = 'Ukuran file terlalu besar! Maksimal 3MB.';
            } else {
                $dir = 'uploads/galeri/';
                if (!is_dir($dir))
                    mkdir($dir, 0755, true);
                $fname = 'galeri_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $fname);
                $foto_url = $dir . $fname;
            }
        } else {
            $error_msg = 'Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau WEBP.';
        }
    } elseif (!empty($_POST['foto_url'])) {
        $foto_url = trim($_POST['foto_url']);
    }

    if (!$error_msg) {
        if ($edit_id > 0) {
            // Update
            $q = $foto_url
                ? "UPDATE galeri SET judul='" . mysqli_real_escape_string($koneksi, $judul) . "', kategori='" . mysqli_real_escape_string($koneksi, $kategori) . "', foto='" . mysqli_real_escape_string($koneksi, $foto_url) . "' WHERE id=$edit_id"
                : "UPDATE galeri SET judul='" . mysqli_real_escape_string($koneksi, $judul) . "', kategori='" . mysqli_real_escape_string($koneksi, $kategori) . "' WHERE id=$edit_id";
            mysqli_query($koneksi, $q);
            header("Location: admin_galeri.php?success=updated");
            exit;
        } else {
            // Insert
            if ($judul && $foto_url) {
                mysqli_query($koneksi, "INSERT INTO galeri (judul,foto,kategori) VALUES ('" . mysqli_real_escape_string($koneksi, $judul) . "','" . mysqli_real_escape_string($koneksi, $foto_url) . "','" . mysqli_real_escape_string($koneksi, $kategori) . "')");
                header("Location: admin_galeri.php?success=added");
                exit;
            } else {
                $error_msg = 'Judul dan foto wajib diisi.';
            }
        }
    }
}


// Fetch edit data
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM galeri WHERE id=" . (int) $_GET['edit']));
}

$success = $_GET['success'] ?? '';
$filter_kat = $_GET['kat'] ?? '';
$search = trim($_GET['q'] ?? '');
$conditions = [];
if ($filter_kat)
    $conditions[] = "kategori='" . mysqli_real_escape_string($koneksi, $filter_kat) . "'";
if ($search)
    $conditions[] = "judul LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%'";
$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : '';

$res = mysqli_query($koneksi, "SELECT * FROM galeri $where ORDER BY id DESC");
$total = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM galeri $where"))['t'];

// Stats per kategori
$stats_kat = [];
$rk = mysqli_query($koneksi, "SELECT kategori,COUNT(*) as cnt FROM galeri GROUP BY kategori");
while ($krow = mysqli_fetch_assoc($rk))
    $stats_kat[$krow['kategori']] = $krow['cnt'];
$total_all = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM galeri"))['t'];

$unread_messages = 0;
$hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$today = $hari[date('w')] . ', ' . date('d F Y');

$show_modal = $edit_data || $error_msg;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="icon" type="image/png" href="img/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri — Admin SDN Laladon 03</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>

<body>
    <div class="admin-wrapper">
        <?php include 'admin_sidebar.php'; ?>

        <div id="admin-content" class="admin-content">
            <div class="admin-topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i data-lucide="menu" style="width:18px;height:18px;"></i>
                    </button>
                    <div>
                        <p class="topbar-title">Galeri Foto</p>
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

            <div class="admin-page">
                <!-- Page Header with Add Button -->
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Galeri Foto</h1>
                        <p class="page-breadcrumb">
                            <a href="admin_dashboard.php" style="color:#94a3b8;text-decoration:none;">Dashboard</a>
                            &rsaquo; <span>Galeri Foto</span>
                        </p>
                    </div>
                    <button type="button" class="btn-admin btn-admin-primary" onclick="openModal()">
                        <i data-lucide="image-plus"></i>
                        Tambah Foto
                    </button>
                </div>

                <!-- Alerts -->
                <?php if ($success === 'deleted'): ?>
                    <div class="alert-admin alert-error mb-4"><i data-lucide="trash-2"></i> Foto berhasil dihapus.</div>
                <?php endif; ?>
                <?php if ($success === 'added'): ?>
                    <div class="alert-admin alert-success mb-4"><i data-lucide="check-circle"></i> Foto berhasil ditambahkan!</div>
                <?php endif; ?>
                <?php if ($success === 'updated'): ?>
                    <div class="alert-admin alert-success mb-4"><i data-lucide="check-circle"></i> Foto berhasil diperbarui!</div>
                <?php endif; ?>
                <?php if ($error_msg): ?>
                    <div class="alert-admin alert-error mb-4"><i data-lucide="alert-circle"></i>
                        <?php echo htmlspecialchars($error_msg); ?></div>
                <?php endif; ?>

                <!-- Stats mini -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon cyan"><i data-lucide="image" style="width:22px;height:22px;"></i></div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $total_all; ?></div>
                                <div class="stat-label">Total Foto</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon blue"><i data-lucide="calendar" style="width:22px;height:22px;"></i></div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $stats_kat['kegiatan'] ?? 0; ?></div>
                                <div class="stat-label">Kegiatan</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon green"><i data-lucide="trophy" style="width:22px;height:22px;"></i></div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $stats_kat['prestasi'] ?? 0; ?></div>
                                <div class="stat-label">Prestasi</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon purple"><i data-lucide="graduation-cap" style="width:22px;height:22px;"></i></div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $stats_kat['wisuda'] ?? 0; ?></div>
                                <div class="stat-label">Wisuda</div>
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
                                <input type="text" name="q" class="search-input" placeholder="Cari judul foto..."
                                    value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <button type="submit" class="btn-admin btn-admin-secondary">Cari</button>
                            <?php if ($search || $filter_kat): ?>
                                <a href="admin_galeri.php" class="btn-admin btn-admin-secondary">Reset</a>
                            <?php endif; ?>
                            <span style="margin-left:auto;font-size:.8rem;color:#94a3b8;"><?php echo $total; ?> foto
                                ditemukan</span>
                        </form>
                    </div>

                    <!-- Category Filter -->
                    <div style="padding: .75rem 1.25rem; border-bottom: 1px solid #f1f5f9;">
                        <div class="filter-bar">
                            <a href="admin_galeri.php" class="filter-pill <?php echo !$filter_kat ? 'active' : ''; ?>">
                                Semua (<?php echo $total_all; ?>)
                            </a>
                            <?php foreach (['kegiatan' => 'Kegiatan', 'prestasi' => 'Prestasi', 'wisuda' => 'Wisuda', 'lainnya' => 'Lainnya'] as $kv => $kl): ?>
                                <a href="admin_galeri.php?kat=<?php echo $kv; ?>"
                                    class="filter-pill <?php echo $filter_kat === $kv ? 'active' : ''; ?>">
                                    <?php echo $kl; ?> (<?php echo $stats_kat[$kv] ?? 0; ?>)
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width:50px;">#</th>
                                    <th>Foto</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Tanggal</th>
                                    <th class="text-center" style="width:100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($res) > 0):
                                    $no = 1;
                                    while ($g = mysqli_fetch_assoc($res)):
                                        $cat_cls_map = ['kegiatan' => 'cat-kegiatan', 'prestasi' => 'cat-prestasi', 'wisuda' => 'cat-wisuda', 'lainnya' => 'cat-lainnya'];
                                        $cat_lbl_map = ['kegiatan' => 'Kegiatan', 'prestasi' => 'Prestasi', 'wisuda' => 'Wisuda', 'lainnya' => 'Lainnya'];
                                        $kat_c = $cat_cls_map[$g['kategori']] ?? 'cat-lainnya';
                                        $kat_l = $cat_lbl_map[$g['kategori']] ?? 'Lainnya';
                                        ?>
                                        <tr>
                                            <td style="color:#94a3b8;font-weight:600;"><?php echo $no++; ?></td>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($g['foto']); ?>" class="galeri-thumb"
                                                    alt="<?php echo htmlspecialchars($g['judul']); ?>" loading="lazy">
                                            </td>
                                            <td>
                                                <div class="tbl-name"><?php echo htmlspecialchars($g['judul']); ?></div>
                                            </td>
                                            <td>
                                                <span class="cat-pill <?php echo $kat_c; ?>"><?php echo $kat_l; ?></span>
                                            </td>
                                            <td style="font-size:.8rem;color:#64748b;">
                                                <?php echo date('d M Y', strtotime($g['created_at'])); ?>
                                            </td>
                                            <td>
                                                <div class="action-wrap justify-content-center">
                                                    <a href="admin_galeri.php?edit=<?php echo $g['id']; ?>"
                                                        class="btn-tbl btn-tbl-edit" title="Edit">
                                                        <i data-lucide="pencil"></i>
                                                    </a>
                                                    <a href="admin_galeri.php?delete=<?php echo $g['id']; ?>"
                                                        class="btn-tbl btn-tbl-delete" title="Hapus"
                                                        onclick="return confirm('Yakin hapus foto ini?')">
                                                        <i data-lucide="trash-2"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; else: ?>
                                    <tr>
                                        <td colspan="6">
                                            <div class="tbl-empty">
                                                <div class="tbl-empty-icon">
                                                    <i data-lucide="image" style="width:28px;height:28px;"></i>
                                                </div>
                                                <?php echo $filter_kat || $search ? 'Tidak ada foto yang ditemukan.' : 'Belum ada foto di galeri.'; ?>
                                                <?php if (!$filter_kat && !$search): ?>
                                                    <div class="mt-3">
                                                        <button type="button" class="btn-admin btn-admin-primary" onclick="openModal()">
                                                            <i data-lucide="image-plus"></i> Tambah Foto Pertama
                                                        </button>
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

    <!-- Modal Tambah/Edit Foto -->
    <div class="modal-overlay" id="galeriModal">
        <div class="modal-panel">
            <div class="modal-header-custom">
                <h5>
                    <i data-lucide="<?php echo $edit_data ? 'pencil' : 'image-plus'; ?>"
                        style="width:18px;height:18px;color:#d97706;"></i>
                    <?php echo $edit_data ? 'Edit Foto' : 'Tambah Foto Baru'; ?>
                </h5>
                <button class="modal-close" onclick="closeModal()">
                    <i data-lucide="x" style="width:16px;height:16px;"></i>
                </button>
            </div>

            <?php if ($edit_data): ?>
                <img src="<?php echo htmlspecialchars($edit_data['foto']); ?>" class="edit-preview-img" alt="">
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit_id"
                    value="<?php echo $edit_data ? $edit_data['id'] : 0; ?>">
                <div class="mb-3">
                    <label class="form-label-admin">Judul Foto <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="judul" class="form-control-admin"
                        placeholder="Contoh: Upacara Hari Senin"
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['judul']) : ''; ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label class="form-label-admin">Kategori</label>
                    <select name="kategori" class="form-control-admin">
                        <?php
                        $cats = ['kegiatan' => 'Kegiatan', 'prestasi' => 'Prestasi', 'wisuda' => 'Wisuda', 'lainnya' => 'Lainnya'];
                        foreach ($cats as $val => $lbl):
                            $sel = ($edit_data && $edit_data['kategori'] === $val) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $val; ?>" <?php echo $sel; ?>><?php echo $lbl; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label-admin">Upload Foto</label>
                    <input type="file" name="foto" class="form-control-admin" accept="image/*"
                        style="padding:.5rem .75rem;" id="modalFotoInput">
                    <div style="font-size:.73rem;color:#94a3b8;margin-top:.4rem;">Format: JPG, JPEG, PNG, WEBP. Maks. 3MB</div>
                    <img id="modalPreviewImg" style="display:none;width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:12px;margin-top:.75rem;border:1.5px solid #e2e8f0;" alt="">
                </div>
                <div class="mb-4">
                    <label class="form-label-admin">Atau URL Gambar</label>
                    <input type="text" name="foto_url" class="form-control-admin"
                        placeholder="https://..."
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['foto']) : ''; ?>">
                    <div style="font-size:.73rem;color:#94a3b8;margin-top:.4rem;">Isi salah satu saja (upload atau URL)</div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit"
                        class="btn-admin btn-admin-primary flex-fill justify-content-center py-3">
                        <i data-lucide="<?php echo $edit_data ? 'save' : 'plus'; ?>"></i>
                        <?php echo $edit_data ? 'Simpan Perubahan' : 'Tambah Foto'; ?>
                    </button>
                    <?php if ($edit_data): ?>
                        <a href="admin_galeri.php" class="btn-admin btn-admin-secondary py-3">
                            <i data-lucide="x"></i>
                        </a>
                    <?php else: ?>
                        <button type="button" class="btn-admin btn-admin-secondary py-3" onclick="closeModal()">
                            <i data-lucide="x"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (toggle) toggle.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); });
        if (overlay) overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); });

        // Modal functions
        const modal = document.getElementById('galeriModal');
        function openModal() {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
        // Close on overlay click
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });
        // Close on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });

        // Auto-open modal if editing or error
        <?php if ($show_modal): ?>
            openModal();
        <?php endif; ?>

        // Preview uploaded image in modal
        document.getElementById('modalFotoInput')?.addEventListener('change', function () {
            const file = this.files[0];
            const preview = document.getElementById('modalPreviewImg');
            if (!file) { preview.style.display = 'none'; return; }
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    </script>
</body>

</html>
