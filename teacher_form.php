<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$id = "";
$name = "";
$position = "";
$photo = "";
$is_edit = false;

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $is_edit = true;
    $row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM guru_staf WHERE id='$id'"));
    $name = $row['name'];
    $position = $row['position'];
    $photo = $row['photo'];
}
$page_title = $is_edit ? 'Edit Data Guru' : 'Tambah Guru Baru';
$unread_messages = 0;
$hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$today = $hari[date('w')] . ', ' . date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="icon" type="image/png" href="img/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> — Admin SDN Laladon 03</title>
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
                            &rsaquo; <a href="admin_guru.php" style="color:#94a3b8;text-decoration:none;">Guru &amp;
                                Staf</a>
                            &rsaquo; <span><?php echo $is_edit ? 'Edit' : 'Tambah'; ?></span>
                        </p>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-7">
                        <div class="form-card">
                            <div class="form-card-header">
                                <div class="form-card-icon">
                                    <i data-lucide="<?php echo $is_edit ? 'pencil' : 'user-plus'; ?>"
                                        style="width:22px;height:22px;"></i>
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
                                                <div class="img-preview-wrap"
                                                    style="display:inline-block;padding:1rem 2rem;">
                                                    <img id="photoPreview" src="<?php echo htmlspecialchars($photo); ?>"
                                                        class="img-preview-circle" alt="Foto">
                                                    <div style="font-size:.75rem;color:#94a3b8;margin-top:.5rem;">Foto saat
                                                        ini</div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="col-md-6">
                                            <label class="form-label-admin">Nama Lengkap <span
                                                    style="color:#ef4444;">*</span></label>
                                            <input type="text" name="name" class="form-control-admin"
                                                value="<?php echo htmlspecialchars($name); ?>"
                                                placeholder="Nama lengkap guru..." required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-admin">Jabatan / Mata Pelajaran <span
                                                    style="color:#ef4444;">*</span></label>
                                            <input type="text" name="position" class="form-control-admin"
                                                value="<?php echo htmlspecialchars($position); ?>"
                                                placeholder="Contoh: Guru Kelas 1A" required>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label-admin">Foto Guru</label>
                                            <input type="file" name="photo" class="form-control-admin" accept="image/*"
                                                style="padding:.5rem .75rem;"
                                                onchange="previewImg(this,'photoPreview','photoWrap')">
                                            <div style="font-size:.73rem;color:#94a3b8;margin-top:.4rem;">Format: JPG,
                                                PNG. Maks. 3MB. Biarkan kosong jika tidak ingin mengubah foto.</div>
                                            <?php if (!$is_edit): ?>
                                                <div class="img-preview-wrap mt-3" id="photoWrap" style="display:none;">
                                                    <img id="photoPreview" src="" class="img-preview-circle" alt="Preview">
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-12 d-flex align-items-center gap-3 pt-2">
                                            <button type="submit" name="<?php echo $is_edit ? 'update' : 'save'; ?>"
                                                class="btn-admin btn-admin-primary py-3 px-4">
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
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (toggle) toggle.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); });
        if (overlay) overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); });

        function previewImg(input, imgId, wrapId) {
            const wrap = document.getElementById(wrapId);
            const img = document.getElementById(imgId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => { img.src = e.target.result; if (wrap) wrap.style.display = 'block'; };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>
