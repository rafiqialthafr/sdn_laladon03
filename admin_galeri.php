<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
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
            $dir = 'uploads/galeri/';
            if (!is_dir($dir))
                mkdir($dir, 0755, true);
            $fname = 'galeri_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $fname);
            $foto_url = $dir . $fname;
        } else {
            $error_msg = 'Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.';
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
            $success_msg = 'Foto berhasil diperbarui!';
        } else {
            // Insert
            if ($judul && $foto_url) {
                mysqli_query($koneksi, "INSERT INTO galeri (judul,foto,kategori) VALUES ('" . mysqli_real_escape_string($koneksi, $judul) . "','" . mysqli_real_escape_string($koneksi, $foto_url) . "','" . mysqli_real_escape_string($koneksi, $kategori) . "')");
                $success_msg = 'Foto berhasil ditambahkan!';
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

$unread_messages = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM contact_messages WHERE is_read=0"))['t'];
$hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$today = $hari[date('w')] . ', ' . date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri — Admin SDN Laladon 03</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
    <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
            padding: 1.5rem;
        }

        .gallery-item {
            border-radius: 14px;
            overflow: hidden;
            border: 1.5px solid #f1f5f9;
            background: #fff;
            transition: all .25s;
            position: relative;
        }

        .gallery-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, .12);
            border-color: #fcd34d;
        }

        .gallery-img {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: cover;
            display: block;
        }

        .gallery-info {
            padding: .85rem 1rem;
        }

        .gallery-title {
            font-size: .83rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: .3rem;
            line-height: 1.4;
        }

        .gallery-meta {
            font-size: .72rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        .gallery-actions {
            position: absolute;
            top: .6rem;
            right: .6rem;
            display: flex;
            gap: .3rem;
            opacity: 0;
            transition: opacity .2s;
        }

        .gallery-item:hover .gallery-actions {
            opacity: 1;
        }

        .gallery-actions .btn-tbl {
            width: 28px;
            height: 28px;
            border-radius: 7px;
        }

        .form-panel {
            background: #fff;
            border: 1.5px solid #f1f5f9;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
        }

        .form-panel.editing {
            border-color: #fcd34d;
            box-shadow: 0 4px 20px rgba(245, 158, 11, .12);
        }

        .cat-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            font-size: .7rem;
            font-weight: 700;
            padding: .25rem .65rem;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .cat-kegiatan {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .cat-prestasi {
            background: #dcfce7;
            color: #15803d;
        }

        .cat-wisuda {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .cat-lainnya {
            background: #f1f5f9;
            color: #64748b;
        }
    </style>
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
                        <p class="topbar-title">Galeri Foto</p>
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
                        <h1 class="page-title">Galeri Foto</h1>
                        <p class="page-breadcrumb">
                            <a href="admin_dashboard.php" style="color:#94a3b8;text-decoration:none;">Dashboard</a>
                            &rsaquo; <span>Galeri Foto</span>
                        </p>
                    </div>
                </div>

                <?php if ($success === 'deleted'): ?>
                    <div class="alert-admin alert-error mb-4"><i data-lucide="trash-2"></i> Foto berhasil dihapus.</div>
                <?php endif; ?>
                <?php if ($success_msg): ?>
                    <div class="alert-admin alert-success mb-4"><i data-lucide="check-circle"></i>
                        <?php echo $success_msg; ?></div>
                <?php endif; ?>
                <?php if ($error_msg): ?>
                    <div class="alert-admin alert-error mb-4"><i data-lucide="alert-circle"></i>
                        <?php echo htmlspecialchars($error_msg); ?></div>
                <?php endif; ?>

                <div class="row g-4">
                    <!-- LEFT: Form -->
                    <div class="col-lg-4">
                        <div class="form-panel <?php echo $edit_data ? 'editing' : ''; ?>"
                            style="position:sticky;top:80px;">
                            <h5
                                style="font-size:.95rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem;display:flex;align-items:center;gap:.5rem;">
                                <i data-lucide="<?php echo $edit_data ? 'pencil' : 'image-plus'; ?>"
                                    style="width:18px;height:18px;color:#d97706;"></i>
                                <?php echo $edit_data ? 'Edit Foto' : 'Tambah Foto Baru'; ?>
                            </h5>
                            <?php if ($edit_data): ?>
                                <div
                                    style="margin-bottom:1rem;border-radius:12px;overflow:hidden;border:1.5px solid #fcd34d;">
                                    <img src="<?php echo htmlspecialchars($edit_data['foto']); ?>"
                                        style="width:100%;aspect-ratio:16/9;object-fit:cover;" alt="">
                                </div>
                            <?php endif; ?>
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="edit_id"
                                    value="<?php echo $edit_data ? $edit_data['id'] : 0; ?>">
                                <div class="mb-3">
                                    <label class="form-label-admin">Judul Foto <span
                                            style="color:#ef4444;">*</span></label>
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
                                            <option value="<?php echo $val; ?>" <?php echo $sel; ?>><?php echo $lbl; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label-admin">Upload Foto</label>
                                    <input type="file" name="foto" class="form-control-admin" accept="image/*"
                                        style="padding:.5rem .75rem;">
                                    <div style="font-size:.73rem;color:#94a3b8;margin-top:.4rem;">Format: JPG, PNG,
                                        WEBP. Maks. 5MB</div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-admin">Atau URL Gambar</label>
                                    <input type="text" name="foto_url" class="form-control-admin"
                                        placeholder="https://..."
                                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['foto']) : ''; ?>">
                                    <div style="font-size:.73rem;color:#94a3b8;margin-top:.4rem;">Isi salah satu saja
                                        (upload atau URL)</div>
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
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- RIGHT: Gallery Grid -->
                    <div class="col-lg-8">
                        <!-- Stats + Filter -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <?php
                                $stats_kat = [];
                                $rk = mysqli_query($koneksi, "SELECT kategori,COUNT(*) as cnt FROM galeri GROUP BY kategori");
                                while ($krow = mysqli_fetch_assoc($rk))
                                    $stats_kat[$krow['kategori']] = $krow['cnt'];
                                $total_all = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM galeri"))['t'];
                                ?>
                                <a href="admin_galeri.php"
                                    class="btn-admin <?php echo !$filter_kat ? 'btn-admin-primary' : 'btn-admin-secondary'; ?>"
                                    style="font-size:.78rem;padding:.4rem .85rem;">
                                    Semua <span style="opacity:.8;">(<?php echo $total_all; ?>)</span>
                                </a>
                                <?php foreach (['kegiatan' => 'Kegiatan', 'prestasi' => 'Prestasi', 'wisuda' => 'Wisuda', 'lainnya' => 'Lainnya'] as $kv => $kl): ?>
                                    <a href="admin_galeri.php?kat=<?php echo $kv; ?>"
                                        class="btn-admin <?php echo $filter_kat === $kv ? 'btn-admin-primary' : 'btn-admin-secondary'; ?>"
                                        style="font-size:.78rem;padding:.4rem .85rem;">
                                        <?php echo $kl; ?> <span
                                            style="opacity:.8;">(<?php echo $stats_kat[$kv] ?? 0; ?>)</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <span style="font-size:.8rem;color:#94a3b8;"><?php echo $total; ?> foto</span>
                        </div>

                        <div class="admin-card">
                            <?php if (mysqli_num_rows($res) > 0): ?>
                                <div class="gallery-grid">
                                    <?php while ($g = mysqli_fetch_assoc($res)):
                                        $cat_cls_map = ['kegiatan' => 'cat-kegiatan', 'prestasi' => 'cat-prestasi', 'wisuda' => 'cat-wisuda', 'lainnya' => 'cat-lainnya'];
                                        $cat_lbl_map = ['kegiatan' => 'Kegiatan', 'prestasi' => 'Prestasi', 'wisuda' => 'Wisuda', 'lainnya' => 'Lainnya'];
                                        $kat_c = $cat_cls_map[$g['kategori']] ?? 'cat-lainnya';
                                        $kat_l = $cat_lbl_map[$g['kategori']] ?? 'Lainnya';
                                        ?>
                                        <div class="gallery-item">
                                            <img src="<?php echo htmlspecialchars($g['foto']); ?>" class="gallery-img"
                                                alt="<?php echo htmlspecialchars($g['judul']); ?>" loading="lazy">
                                            <div class="gallery-actions">
                                                <a href="admin_galeri.php?edit=<?php echo $g['id']; ?>"
                                                    class="btn-tbl btn-tbl-edit" title="Edit" style="width:28px;height:28px;">
                                                    <i data-lucide="pencil"></i>
                                                </a>
                                                <a href="admin_galeri.php?delete=<?php echo $g['id']; ?>"
                                                    class="btn-tbl btn-tbl-delete" title="Hapus"
                                                    onclick="return confirm('Yakin hapus foto ini?')"
                                                    style="width:28px;height:28px;">
                                                    <i data-lucide="trash-2"></i>
                                                </a>
                                            </div>
                                            <div class="gallery-info">
                                                <div class="gallery-title"><?php echo htmlspecialchars($g['judul']); ?></div>
                                                <div class="gallery-meta">
                                                    <span class="cat-pill <?php echo $kat_c; ?>"><?php echo $kat_l; ?></span>
                                                    <span
                                                        style="margin-left:auto;"><?php echo date('d M Y', strtotime($g['created_at'])); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="tbl-empty py-5">
                                    <div class="tbl-empty-icon"><i data-lucide="image" style="width:28px;height:28px;"></i>
                                    </div>
                                    <?php echo $filter_kat ? 'Tidak ada foto dengan kategori ini.' : 'Belum ada foto di galeri.'; ?>
                                    <div style="font-size:.8rem;color:#94a3b8;margin-top:.5rem;">Tambah foto menggunakan
                                        form di sebelah kiri</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div><!-- /row -->
            </div>
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

        // Preview uploaded image
        document.querySelector('input[name="foto"]')?.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                let prev = document.getElementById('upload-preview');
                if (!prev) {
                    prev = document.createElement('img');
                    prev.id = 'upload-preview';
                    prev.style.cssText = 'width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:12px;margin-top:.75rem;border:1.5px solid #e2e8f0;';
                    this.parentElement.appendChild(prev);
                }
                prev.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    </script>
</body>

</html>