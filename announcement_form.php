<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$id = "";
$title = "";
$content = "";
$category = "pengumuman";
$image = "";
$is_published = 1;
$is_edit = false;

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $is_edit = true;
    $row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM announcements WHERE id='$id'"));
    $title = $row['title'];
    $content = $row['content'];
    $category = $row['category'];
    $image = $row['image'];
    $is_published = $row['is_published'];
}
$page_title = $is_edit ? 'Edit Berita' : 'Tambah Berita';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> — Admin SDN Laladon 03</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
    <style>
        /* Form card */
        .form-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
            overflow: hidden;
        }

        .form-card-header {
            padding: 1.25rem 1.75rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .form-card-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: linear-gradient(135deg, #FFF3CD, #FFE082);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #c8890a;
        }

        .form-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0;
        }

        .form-card-body {
            padding: 1.75rem;
        }

        /* Form elements */
        .form-label {
            font-size: .82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: .4rem;
        }

        .form-control,
        .form-select {
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: .65rem 1rem;
            font-size: .875rem;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #FFD700;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, .15);
            outline: none;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 180px;
        }

        /* Image preview */
        .img-preview-wrap {
            border: 2px dashed #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            margin-top: .6rem;
            text-align: center;
            background: #fafafa;
        }

        .img-preview-wrap img {
            max-height: 180px;
            border-radius: 8px;
            object-fit: cover;
        }

        /* Status select */
        .select-published {
            border-color: #bbf7d0 !important;
            background: #f0fdf4;
        }

        .select-draft {
            border-color: #e5e7eb !important;
        }

        .topbar-back {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: .85rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: color .2s;
        }

        .topbar-back:hover {
            color: #d97706;
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
                        <p class="topbar-title"><?php echo $page_title; ?></p>
                        <p class="topbar-subtitle">Kelola Berita & Pengumuman</p>
                    </div>
                </div>
                <div class="topbar-right">
                    <a href="admin_berita.php" class="topbar-back">
                        <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
                        Kembali ke Daftar Berita
                    </a>
                    <div class="topbar-avatar">A</div>
                </div>
            </div>

            <div class="admin-page">
                <div class="page-header">
                    <div>
                        <h1 class="page-title"><?php echo $page_title; ?></h1>
                        <p class="page-breadcrumb">
                            <a href="admin_dashboard.php" style="color:#94a3b8;text-decoration:none;">Dashboard</a>
                            &rsaquo; <a href="admin_berita.php" style="color:#94a3b8;text-decoration:none;">Berita</a>
                            &rsaquo; <span><?php echo $page_title; ?></span>
                        </p>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-7">
                        <!-- Form Card -->
                        <div class="form-card">
                            <div class="form-card-header">
                                <div class="form-card-icon">
                                    <i data-lucide="<?php echo $is_edit ? 'file-pen' : 'file-plus'; ?>"
                                        style="width:22px;height:22px;"></i>
                                </div>
                                <h5 class="form-card-title"><?php echo $page_title; ?></h5>
                            </div>
                            <div class="form-card-body">
                                <form action="announcement_process.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                                    <input type="hidden" name="existing_image" value="<?php echo $image; ?>">

                                    <div class="row g-4">
                                        <!-- Judul -->
                                        <div class="col-12">
                                            <label class="form-label-admin">Judul Berita / Pengumuman <span
                                                    style="color:#ef4444;">*</span></label>
                                            <input type="text" name="title" class="form-control-admin"
                                                value="<?php echo htmlspecialchars($title); ?>"
                                                placeholder="Masukkan judul berita..." required>
                                        </div>

                                        <!-- Kategori & Status -->
                                        <div class="col-md-6">
                                            <label class="form-label-admin">Kategori <span
                                                    style="color:#ef4444;">*</span></label>
                                            <select name="category" class="form-control-admin" required>
                                                <option value="pengumuman" <?php echo $category == 'pengumuman' ? 'selected' : ''; ?>>📢 Pengumuman</option>
                                                <option value="berita" <?php echo $category == 'berita' ? 'selected' : ''; ?>>
                                                    📰 Berita</option>
                                                <option value="event" <?php echo $category == 'event' ? 'selected' : ''; ?>>🎉
                                                    Event</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-admin">Status Publikasi</label>
                                            <select name="is_published" class="form-control-admin"
                                                style="<?php echo $is_published ? 'border-color:#bbf7d0;background:#f0fdf4;' : ''; ?>"
                                                onchange="this.style.borderColor=this.value=='1'?'#bbf7d0':'#e2e8f0'; this.style.backgroundColor=this.value=='1'?'#f0fdf4':'#fff';">
                                                <option value="1" <?php echo $is_published == 1 ? 'selected' : ''; ?>>✅
                                                    Published</option>
                                                <option value="0" <?php echo $is_published == 0 ? 'selected' : ''; ?>>📝
                                                    Draft
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Gambar -->
                                        <div class="col-12">
                                            <label class="form-label-admin">Gambar Artikel</label>
                                            <input type="file" name="image" class="form-control-admin" accept="image/*"
                                                style="padding:.5rem .75rem;" onchange="previewImg(this,'imgPreview')">
                                            <small class="text-muted d-block mt-1">Format: JPG, PNG, WebP. Maks. 2MB.
                                                Biarkan kosong jika tidak ingin mengubah gambar.</small>
                                            <?php if ($is_edit && $image): ?>
                                                <div class="img-preview-wrap mt-2">
                                                    <img id="imgPreview" src="<?php echo htmlspecialchars($image); ?>"
                                                        alt="Preview"
                                                        style="max-height:180px;border-radius:8px;object-fit:cover;">
                                                </div>
                                            <?php else: ?>
                                                <div class="img-preview-wrap mt-2" id="imgPreviewWrap"
                                                    style="display:none;">
                                                    <img id="imgPreview" src="" alt="Preview"
                                                        style="max-height:180px;border-radius:8px;object-fit:cover;">
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Isi -->
                                        <div class="col-12">
                                            <label class="form-label-admin">Isi Berita / Pengumuman <span
                                                    style="color:#ef4444;">*</span></label>
                                            <textarea name="content" class="form-control-admin" rows="10"
                                                placeholder="Tulis isi berita di sini..."
                                                required><?php echo htmlspecialchars($content); ?></textarea>
                                        </div>

                                        <!-- Actions -->
                                        <div class="col-12 d-flex align-items-center gap-3 pt-2">
                                            <button type="submit" name="<?php echo $is_edit ? 'update' : 'save'; ?>"
                                                class="btn-admin btn-admin-primary py-3 px-4">
                                                <i data-lucide="save" style="width:16px;height:16px;"></i>
                                                <?php echo $is_edit ? 'Simpan Perubahan' : 'Publikasikan'; ?>
                                            </button>
                                            <a href="admin_berita.php" class="btn-admin btn-admin-secondary py-3 px-4">
                                                <i data-lucide="x" style="width:16px;height:16px;"></i>
                                                Batal
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
        function previewImg(input, imgId) {
            const wrap = document.getElementById('imgPreviewWrap');
            const img = document.getElementById(imgId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => { img.src = e.target.result; if (wrap) wrap.style.display = 'block'; };
                reader.readAsDataURL(input.files[0]);
            }
        }
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (toggle) toggle.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); });
        if (overlay) overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); });
    </script>
</body>

</html>