<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// Handle submit
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($koneksi, $_POST['name']);
    $position = mysqli_real_escape_string($koneksi, $_POST['position']);
    $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $education = mysqli_real_escape_string($koneksi, $_POST['education']);
    $period = mysqli_real_escape_string($koneksi, $_POST['period']);
    $vision_mission = mysqli_real_escape_string($koneksi, $_POST['vision_mission']);
    $quote = mysqli_real_escape_string($koneksi, $_POST['quote']);
    
    $photo = mysqli_real_escape_string($koneksi, $_POST['existing_photo']);

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['photo']['tmp_name'];
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $new_name = 'kepsek_' . time() . '.' . $ext;
        $dest = 'img/' . $new_name;
        if (move_uploaded_file($tmp, $dest)) {
            $photo = 'img/' . $new_name;
        }
    }

    // Check if record exists
    $check = mysqli_query($koneksi, "SELECT id FROM kepsek LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $id = $row['id'];
        $query = "UPDATE kepsek SET name='$name', position='$position', nip='$nip', education='$education', period='$period', vision_mission='$vision_mission', quote='$quote', photo='$photo' WHERE id=$id";
    } else {
        $query = "INSERT INTO kepsek (name, position, nip, education, period, vision_mission, quote, photo) VALUES ('$name', '$position', '$nip', '$education', '$period', '$vision_mission', '$quote', '$photo')";
    }

    if (mysqli_query($koneksi, $query)) {
        $success = 'Data Kepala Sekolah berhasil diperbarui.';
    } else {
        $success = 'Gagal memperbarui data: ' . mysqli_error($koneksi);
    }
}

$query_kepsek = "SELECT * FROM kepsek ORDER BY id DESC LIMIT 1";
$result_kepsek = mysqli_query($koneksi, $query_kepsek);
$kepsek = mysqli_fetch_assoc($result_kepsek);

if(!$kepsek) {
    // Fallback empty data if somehow table is empty
    $kepsek = [
        'name' => '',
        'position' => '',
        'photo' => '',
        'nip' => '',
        'education' => '',
        'period' => '',
        'vision_mission' => '',
        'quote' => ''
    ];
}

$page_title = 'Profil Kepala Sekolah';
$unread_messages = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM contact_messages WHERE is_read=0"))['t'] ?? 0;
$hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$today = $hari[date('w')] . ', ' . date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Kepsek — Admin SDN Laladon 03</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
    <style>
        .form-card { background: #fff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 4px 16px rgba(0,0,0,.03); border: 1px solid #f1f5f9; overflow: hidden; }
        .form-card-header { padding: 1.25rem 1.75rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: .75rem; }
        .form-card-icon { width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg, #fef3c7, #fde68a); display: flex; align-items: center; justify-content: center; color: #d97706; }
        .form-card-title { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; }
        .form-card-body { padding: 1.75rem; }
        .img-preview-wrap { border: 2px dashed #e2e8f0; border-radius: 12px; padding: 1.25rem; text-align: center; background: #f8fafc; transition: border-color .2s; }
        .img-preview-wrap:hover { border-color: #fcd34d; }
        .img-preview-circle { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #fcd34d; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'admin_sidebar.php'; ?>
        <div id="admin-content">
            <div class="admin-topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle"><i data-lucide="menu" style="width:18px;height:18px;"></i></button>
                    <div>
                        <p class="topbar-title"><?php echo $page_title; ?></p>
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
                    <div class="topbar-avatar">A</div>
                </div>
            </div>

            <div class="admin-page">
                <div class="page-header">
                    <div>
                        <h1 class="page-title"><?php echo $page_title; ?></h1>
                        <p class="page-breadcrumb">
                            <a href="admin_dashboard.php" style="color:#94a3b8;text-decoration:none;">Dashboard</a>
                            &rsaquo; <span>Profil Kepala Sekolah</span>
                        </p>
                    </div>
                </div>

                <?php if ($success): ?>
                    <div class="alert-admin <?php echo strpos($success, 'berhasil') !== false ? 'alert-success' : 'alert-error'; ?> mb-4">
                        <i data-lucide="<?php echo strpos($success, 'berhasil') !== false ? 'check-circle' : 'alert-circle'; ?>"></i>
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-8">
                        <div class="form-card">
                            <div class="form-card-header">
                                <div class="form-card-icon"><i data-lucide="user-check" style="width:22px;height:22px;"></i></div>
                                <h5 class="form-card-title">Edit Data Kepala Sekolah</h5>
                            </div>
                            <div class="form-card-body">
                                <form action="admin_kepsek.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="existing_photo" value="<?php echo $kepsek['photo']; ?>">
                                    
                                    <div class="row g-4">
                                        <div class="col-12 text-center">
                                            <div class="img-preview-wrap" style="display:inline-block;padding:1rem 2rem;">
                                                <img id="photoPreview" src="<?php echo $kepsek['photo'] ? htmlspecialchars($kepsek['photo']) : 'https://via.placeholder.com/150'; ?>" class="img-preview-circle" alt="Foto">
                                                <div style="font-size:.75rem;color:#94a3b8;margin-top:.5rem;">Foto saat ini</div>
                                                <input type="file" name="photo" class="form-control-admin mt-3" accept="image/*" style="padding:.5rem .75rem; max-width: 250px; margin: 0 auto;" onchange="previewImg(this,'photoPreview')">
                                                <div style="font-size:.73rem;color:#94a3b8;margin-top:.4rem;">Format: JPG, PNG. Maks. 2MB.</div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label-admin">Nama Lengkap <span style="color:#ef4444;">*</span></label>
                                            <input type="text" name="name" class="form-control-admin" value="<?php echo htmlspecialchars($kepsek['name']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-admin">Jabatan <span style="color:#ef4444;">*</span></label>
                                            <input type="text" name="position" class="form-control-admin" value="<?php echo htmlspecialchars($kepsek['position']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-admin">NIP</label>
                                            <input type="text" name="nip" class="form-control-admin" value="<?php echo htmlspecialchars($kepsek['nip']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-admin">Pendidikan</label>
                                            <input type="text" name="education" class="form-control-admin" value="<?php echo htmlspecialchars($kepsek['education']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-admin">Jabatan Sejak (Tahun)</label>
                                            <input type="text" name="period" class="form-control-admin" value="<?php echo htmlspecialchars($kepsek['period']); ?>">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label-admin">Visi & Misi Kepemimpinan</label>
                                            <textarea name="vision_mission" class="form-control-admin" rows="4"><?php echo htmlspecialchars($kepsek['vision_mission']); ?></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label-admin">Kutipan (Quote)</label>
                                            <textarea name="quote" class="form-control-admin" rows="3"><?php echo htmlspecialchars($kepsek['quote']); ?></textarea>
                                        </div>

                                        <div class="col-12 d-flex align-items-center gap-3 pt-2">
                                            <button type="submit" class="btn-admin btn-admin-primary py-3 px-4">
                                                <i data-lucide="save"></i> Simpan Perubahan
                                            </button>
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

        function previewImg(input, imgId) {
            const img = document.getElementById(imgId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => { img.src = e.target.result; };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
