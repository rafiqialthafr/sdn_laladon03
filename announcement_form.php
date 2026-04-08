<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

$id = ""; $title = ""; $content = ""; $category = "pengumuman"; $image = ""; $is_published = 1; $is_edit = false;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $is_edit = true;
    $row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM announcements WHERE id='$id'"));
    $title = $row['title']; $content = $row['content'];
    $category = $row['category']; $image = $row['image']; $is_published = $row['is_published'];
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f0f2f5; margin: 0; min-height: 100vh; display: flex; }

        /* Sidebar */
        #admin-sidebar {
            width: 260px; min-height: 100vh; flex-shrink: 0;
            background: linear-gradient(175deg, #0f0f1a 0%, #1a1a2e 40%, #16213e 100%);
            display: flex; flex-direction: column; position: fixed; top:0; left:0; bottom:0; z-index:100;
        }
        .sidebar-brand {
            padding: 1.5rem 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex; align-items: center; gap: .85rem;
        }
        .sidebar-brand-icon {
            width:40px;height:40px;background:linear-gradient(135deg,#FFD700,#FFA500);
            border-radius:10px;display:flex;align-items:center;justify-content:center;color:#3d1f00;flex-shrink:0;
        }
        .sidebar-brand-text { font-size:.95rem;font-weight:700;color:#fff;line-height:1.2; }
        .sidebar-brand-sub  { font-size:.72rem;color:rgba(255,255,255,.45);font-weight:400; }
        .sidebar-section-label {
            font-size:.68rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;
            color:rgba(255,255,255,.3);padding:1.25rem 1.5rem .5rem;
        }
        .sidebar-nav { list-style:none;padding:0;margin:0; }
        .sidebar-nav a {
            display:flex;align-items:center;gap:.75rem;padding:.75rem 1.5rem;
            color:rgba(255,255,255,.65);text-decoration:none;font-size:.875rem;font-weight:500;
            border-left:3px solid transparent;transition:all .2s;
        }
        .sidebar-nav a:hover { color:#fff;background:rgba(255,255,255,.05);border-left-color:rgba(255,215,0,.5); }
        .sidebar-nav a.active { color:#FFD700;background:rgba(255,215,0,.08);border-left-color:#FFD700;font-weight:600; }
        .sidebar-footer {
            margin-top:auto;padding:1rem 1.5rem;border-top:1px solid rgba(255,255,255,.07);
        }
        .sidebar-footer a {
            display:flex;align-items:center;gap:.6rem;font-size:.825rem;
            color:rgba(255,255,255,.45);text-decoration:none;transition:color .2s;
        }
        .sidebar-footer a:hover { color:#ff6b6b; }

        /* Content */
        .admin-content { margin-left:260px;flex:1;padding:2rem; }

        /* Topbar */
        .admin-topbar {
            background:#fff;border-radius:12px;padding:1rem 1.5rem;
            display:flex;justify-content:space-between;align-items:center;
            box-shadow:0 2px 12px rgba(0,0,0,.05);margin-bottom:1.75rem;
        }
        .topbar-back {
            display:inline-flex;align-items:center;gap:.5rem;font-size:.85rem;font-weight:600;
            color:#6b7280;text-decoration:none;transition:color .2s;
        }
        .topbar-back:hover { color:#c8890a; }
        .topbar-title { font-size:1rem;font-weight:700;color:#1a1a2e;margin:0; }

        /* Form card */
        .form-card {
            background:#fff;border-radius:16px;
            box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden;
        }
        .form-card-header {
            padding:1.25rem 1.75rem;border-bottom:1px solid #f3f4f6;
            display:flex;align-items:center;gap:.75rem;
        }
        .form-card-icon {
            width:42px;height:42px;border-radius:10px;
            background:linear-gradient(135deg,#FFF3CD,#FFE082);
            display:flex;align-items:center;justify-content:center;color:#c8890a;
        }
        .form-card-title { font-size:1rem;font-weight:700;color:#1a1a2e;margin:0; }
        .form-card-body  { padding:1.75rem; }

        /* Form elements */
        .form-label { font-size:.82rem;font-weight:600;color:#374151;margin-bottom:.4rem; }
        .form-control, .form-select {
            border:1.5px solid #e5e7eb;border-radius:10px;padding:.65rem 1rem;
            font-size:.875rem;font-family:'Poppins',sans-serif;transition:border-color .2s,box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color:#FFD700;box-shadow:0 0 0 3px rgba(255,215,0,.15);outline:none;
        }
        textarea.form-control { resize:vertical;min-height:180px; }

        /* Image preview */
        .img-preview-wrap {
            border:2px dashed #e5e7eb;border-radius:12px;padding:1rem;
            margin-top:.6rem;text-align:center;background:#fafafa;
        }
        .img-preview-wrap img { max-height:180px;border-radius:8px;object-fit:cover; }

        /* Status select */
        .select-published { border-color:#bbf7d0 !important;background:#f0fdf4; }
        .select-draft      { border-color:#e5e7eb !important; }

        /* Buttons */
        .btn-save {
            background:linear-gradient(135deg,#FFD700,#FFA500);color:#3d1f00;
            border:none;border-radius:10px;padding:.7rem 2rem;font-weight:700;
            font-family:'Poppins',sans-serif;font-size:.9rem;cursor:pointer;
            transition:all .2s;display:inline-flex;align-items:center;gap:.5rem;
        }
        .btn-save:hover { transform:translateY(-1px);box-shadow:0 4px 16px rgba(255,165,0,.35); }
        .btn-cancel {
            background:#f3f4f6;color:#374151;border:none;border-radius:10px;
            padding:.7rem 1.5rem;font-weight:600;font-family:'Poppins',sans-serif;
            font-size:.9rem;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;
            transition:background .2s;
        }
        .btn-cancel:hover { background:#e5e7eb;color:#1a1a2e; }

        @media(max-width:991px) {
            #admin-sidebar { display:none; }
            .admin-content { margin-left:0; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<nav id="admin-sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i data-lucide="school" style="width:22px;height:22px;"></i>
        </div>
        <div>
            <div class="sidebar-brand-text">Admin Panel</div>
            <div class="sidebar-brand-sub">SDN Laladon 03</div>
        </div>
    </div>
    <div class="sidebar-section-label">Main Menu</div>
    <ul class="sidebar-nav">
        <li><a href="admin_dashboard.php"><i data-lucide="layout-dashboard" style="width:18px;height:18px;"></i> Dashboard</a></li>
        <li><a href="admin_dashboard.php#teachers-section"><i data-lucide="users" style="width:18px;height:18px;"></i> Kelola Guru</a></li>
        <li><a href="admin_dashboard.php#news-section" class="active"><i data-lucide="newspaper" style="width:18px;height:18px;"></i> Kelola Berita</a></li>
    </ul>
    <div class="sidebar-section-label">System</div>
    <ul class="sidebar-nav">
        <li><a href="index.php" target="_blank"><i data-lucide="external-link" style="width:18px;height:18px;"></i> Lihat Website</a></li>
    </ul>
    <div class="sidebar-footer">
        <a href="logout.php"><i data-lucide="log-out" style="width:16px;height:16px;"></i> Logout</a>
    </div>
</nav>

<!-- Content -->
<div class="admin-content">
    <!-- Topbar -->
    <div class="admin-topbar">
        <a href="admin_dashboard.php" class="topbar-back">
            <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
            Kembali ke Dashboard
        </a>
        <p class="topbar-title"><?php echo $page_title; ?></p>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon">
                <i data-lucide="<?php echo $is_edit ? 'file-pen' : 'file-plus'; ?>" style="width:22px;height:22px;"></i>
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
                        <label class="form-label">Judul Berita / Pengumuman *</label>
                        <input type="text" name="title" class="form-control"
                               value="<?php echo htmlspecialchars($title); ?>"
                               placeholder="Masukkan judul berita..." required>
                    </div>

                    <!-- Kategori & Status -->
                    <div class="col-md-6">
                        <label class="form-label">Kategori *</label>
                        <select name="category" class="form-select" required>
                            <option value="pengumuman" <?php echo $category=='pengumuman'?'selected':''; ?>>📢 Pengumuman</option>
                            <option value="berita"     <?php echo $category=='berita'?'selected':''; ?>>📰 Berita</option>
                            <option value="event"      <?php echo $category=='event'?'selected':''; ?>>🎉 Event</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status Publikasi</label>
                        <select name="is_published" class="form-select <?php echo $is_published?'select-published':'select-draft'; ?>"
                                onchange="this.className='form-select '+(this.value=='1'?'select-published':'select-draft')">
                            <option value="1" <?php echo $is_published==1?'selected':''; ?>>✅ Published</option>
                            <option value="0" <?php echo $is_published==0?'selected':''; ?>>📝 Draft</option>
                        </select>
                    </div>

                    <!-- Gambar -->
                    <div class="col-12">
                        <label class="form-label">Gambar Artikel</label>
                        <input type="file" name="image" class="form-control" accept="image/*"
                               onchange="previewImg(this,'imgPreview')">
                        <small class="text-muted d-block mt-1">Format: JPG, PNG, WebP. Maks. 2MB. Biarkan kosong jika tidak ingin mengubah gambar.</small>
                        <?php if ($is_edit && $image): ?>
                        <div class="img-preview-wrap mt-2">
                            <img id="imgPreview" src="<?php echo htmlspecialchars($image); ?>" alt="Preview">
                        </div>
                        <?php else: ?>
                        <div class="img-preview-wrap mt-2" id="imgPreviewWrap" style="display:none;">
                            <img id="imgPreview" src="" alt="Preview">
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Isi -->
                    <div class="col-12">
                        <label class="form-label">Isi Berita / Pengumuman *</label>
                        <textarea name="content" class="form-control" rows="10"
                                  placeholder="Tulis isi berita di sini..." required><?php echo htmlspecialchars($content); ?></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="col-12 d-flex align-items-center gap-3 pt-2">
                        <button type="submit" name="<?php echo $is_edit?'update':'save'; ?>" class="btn-save">
                            <i data-lucide="save" style="width:16px;height:16px;"></i>
                            <?php echo $is_edit ? 'Simpan Perubahan' : 'Publikasikan'; ?>
                        </button>
                        <a href="admin_dashboard.php" class="btn-cancel">
                            <i data-lucide="x" style="width:16px;height:16px;"></i>
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
    function previewImg(input, imgId) {
        const wrap = document.getElementById('imgPreviewWrap');
        const img  = document.getElementById(imgId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => { img.src = e.target.result; if(wrap) wrap.style.display='block'; };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
</body>
</html>
