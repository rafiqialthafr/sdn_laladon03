<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

$id = ""; $name = ""; $position = ""; $bio = ""; $photo = ""; $is_edit = false;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $is_edit = true;
    $row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM teachers WHERE id='$id'"));
    $name = $row['name']; $position = $row['position']; $bio = $row['bio']; $photo = $row['photo'];
}
$page_title = $is_edit ? 'Edit Data Guru' : 'Tambah Guru Baru';
$unread_messages = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as t FROM contact_messages WHERE is_read=0"))['t'];
$hari  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$today = $hari[date('w')] . ', ' . date('d F Y');
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
        .form-card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,.04),0 4px 16px rgba(0,0,0,.03); border:1px solid #f1f5f9; overflow:hidden; }
        .form-card-header { padding:1.25rem 1.75rem; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:.75rem; }
        .form-card-icon { width:42px;height:42px; border-radius:10px; background:linear-gradient(135deg,#fef3c7,#fde68a); display:flex;align-items:center;justify-content:center; color:#d97706; }
        .form-card-title { font-size:1rem; font-weight:700; color:#0f172a; margin:0; }
        .form-card-body { padding:1.75rem; }
        .img-preview-wrap { border:2px dashed #e2e8f0; border-radius:12px; padding:1.25rem; text-align:center; background:#f8fafc; transition:border-color .2s; }
        .img-preview-wrap:hover { border-color:#fcd34d; }
        .img-preview-circle { width:90px; height:90px; border-radius:50%; object-fit:cover; border:3px solid #fcd34d; }
        .topbar-back { display:inline-flex;align-items:center;gap:.5rem;font-size:.85rem;font-weight:600;color:#64748b;text-decoration:none;transition:color .2s; }
        .topbar-back:hover { color:#d97706; }
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
                <p class="topbar-title"><?php echo $page_title; ?></p>
                <p class="topbar-subtitle"><?php echo $today; ?></p>
            </div>
        </div>
        <div class="topbar-right">
            <a href="admin_guru.php" class="topbar-back">
                <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
                Kembali ke Daftar Guru
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
                    &rsaquo; <a href="admin_guru.php" style="color:#94a3b8;text-decoration:none;">Guru &amp; Staf</a>
                    &rsaquo; <span><?php echo $is_edit ? 'Edit' : 'Tambah'; ?></span>
                </p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon">
                            <i data-lucide="<?php echo $is_edit ? 'pencil' : 'user-plus'; ?>" style="width:22px;height:22px;"></i>
                        </div>
                        <h5 class="form-card-title"><?php echo $page_title; ?></h5>
                    </div>
                    <div class="form-card-body">
                        <form action="teacher_process.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <input type="hidden" name="existing_photo" value="<?php echo $photo; ?>">

                            <div class="row g-4">
                                <!-- Preview foto saat edit -->
                                <?php if ($is_edit && $photo): ?>
                                <div class="col-12 text-center">
                                    <div class="img-preview-wrap" style="display:inline-block;padding:1rem 2rem;">
                                        <img id="photoPreview" src="<?php echo htmlspecialchars($photo); ?>" class="img-preview-circle" alt="Foto">
                                        <div style="font-size:.75rem;color:#94a3b8;margin-top:.5rem;">Foto saat ini</div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="col-md-6">
                                    <label class="form-label-admin">Nama Lengkap <span style="color:#ef4444;">*</span></label>
                                    <input type="text" name="name" class="form-control-admin"
                                           value="<?php echo htmlspecialchars($name); ?>"
                                           placeholder="Nama lengkap guru..." required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-admin">Jabatan / Mata Pelajaran <span style="color:#ef4444;">*</span></label>
                                    <input type="text" name="position" class="form-control-admin"
                                           value="<?php echo htmlspecialchars($position); ?>"
                                           placeholder="Contoh: Guru Kelas 1A" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label-admin">Foto Guru</label>
                                    <input type="file" name="photo" class="form-control-admin" accept="image/*"
                                           style="padding:.5rem .75rem;"
                                           onchange="previewImg(this,'photoPreview','photoWrap')">
                                    <div style="font-size:.73rem;color:#94a3b8;margin-top:.4rem;">Format: JPG, PNG. Maks. 2MB. Biarkan kosong jika tidak ingin mengubah foto.</div>
                                    <?php if (!$is_edit): ?>
                                    <div class="img-preview-wrap mt-3" id="photoWrap" style="display:none;">
                                        <img id="photoPreview" src="" class="img-preview-circle" alt="Preview">
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-12">
                                    <label class="form-label-admin">Bio Singkat <span style="color:#ef4444;">*</span></label>
                                    <textarea name="bio" class="form-control-admin" rows="5"
                                              placeholder="Tuliskan bio singkat guru..." required><?php echo htmlspecialchars($bio); ?></textarea>
                                </div>

                                <div class="col-12 d-flex align-items-center gap-3 pt-2">
                                    <button type="submit" name="<?php echo $is_edit ? 'update' : 'save'; ?>" class="btn-admin btn-admin-primary py-3 px-4">
                                        <i data-lucide="save"></i>
                                        <?php echo $is_edit ? 'Simpan Perubahan' : 'Tambah Guru'; ?>
                                    </button>
                                    <a href="admin_guru.php" class="btn-admin btn-admin-secondary py-3 px-4">
                                        <i data-lucide="x"></i>
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
const toggle  = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('admin-sidebar');
const overlay = document.getElementById('sidebarOverlay');
if(toggle)  toggle.addEventListener('click',  ()=>{ sidebar.classList.toggle('open'); overlay.classList.toggle('show'); });
if(overlay) overlay.addEventListener('click', ()=>{ sidebar.classList.remove('open'); overlay.classList.remove('show'); });

function previewImg(input, imgId, wrapId) {
    const wrap = document.getElementById(wrapId);
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
