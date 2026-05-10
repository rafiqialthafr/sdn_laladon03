<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// Handle submit kepsek profil
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'kepsek') {
    $name = mysqli_real_escape_string($koneksi, $_POST['name']);
    $position = mysqli_real_escape_string($koneksi, $_POST['position']);
    $education = mysqli_real_escape_string($koneksi, $_POST['education']);
    $period = mysqli_real_escape_string($koneksi, $_POST['period']);
    $vision_mission = mysqli_real_escape_string($koneksi, $_POST['vision_mission']);
    $quote = mysqli_real_escape_string($koneksi, $_POST['quote']);

    $photo = mysqli_real_escape_string($koneksi, $_POST['existing_photo']);

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['photo']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        if (!in_array($ext, $allowed)) {
            $success = 'Format file tidak didukung! Hanya JPG, JPEG, dan PNG.';
        } elseif ($_FILES['photo']['size'] > 3 * 1024 * 1024) {
            $success = 'Ukuran file terlalu besar! Maksimal 3MB.';
        } else {
            $new_name = 'kepsek_' . time() . '.' . $ext;
            $dest = 'img/' . $new_name;
            if (move_uploaded_file($tmp, $dest)) {
                $photo = 'img/' . $new_name;
            }
        }
    }

    $check = mysqli_query($koneksi, "SELECT id FROM kepsek LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $id = $row['id'];
        $query = "UPDATE kepsek SET name='$name', position='$position', education='$education', period='$period', vision_mission='$vision_mission', quote='$quote', photo='$photo' WHERE id=$id";
    } else {
        $query = "INSERT INTO kepsek (name, position, education, period, vision_mission, quote, photo) VALUES ('$name', '$position', '$education', '$period', '$vision_mission', '$quote', '$photo')";
    }

    if (mysqli_query($koneksi, $query)) {
        $success = 'Data Kepala Sekolah berhasil diperbarui.';
    } else {
        $success = 'Gagal memperbarui data: ' . mysqli_error($koneksi);
    }
}

// Handle CRUD Riwayat/Timeline
$riwayat_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'riwayat') {
    $tahun  = mysqli_real_escape_string($koneksi, trim($_POST['tahun'] ?? ''));
    $judul  = mysqli_real_escape_string($koneksi, trim($_POST['judul'] ?? ''));
    $desk   = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi'] ?? ''));
    $urutan = (int)($_POST['urutan'] ?? 0);
    $rid    = (int)($_POST['riwayat_id'] ?? 0);

    if ($tahun && $judul) {
        if ($rid > 0) {
            mysqli_query($koneksi, "UPDATE kepsek_riwayat SET tahun='$tahun', judul='$judul', deskripsi='$desk', urutan=$urutan WHERE id=$rid");
            $riwayat_msg = 'success:Riwayat berhasil diperbarui!';
        } else {
            mysqli_query($koneksi, "INSERT INTO kepsek_riwayat (tahun, judul, deskripsi, urutan) VALUES ('$tahun', '$judul', '$desk', $urutan)");
            $riwayat_msg = 'success:Riwayat berhasil ditambahkan!';
        }
    } else {
        $riwayat_msg = 'error:Tahun dan judul wajib diisi.';
    }
}
if (isset($_GET['delete_riwayat'])) {
    mysqli_query($koneksi, "DELETE FROM kepsek_riwayat WHERE id=" . (int)$_GET['delete_riwayat']);
    header('Location: admin_kepsek.php?riwayat_deleted=1');
    exit;
}
$edit_riwayat = null;
if (isset($_GET['edit_riwayat'])) {
    $edit_riwayat = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kepsek_riwayat WHERE id=" . (int)$_GET['edit_riwayat']));
}

$query_kepsek = "SELECT * FROM kepsek ORDER BY id DESC LIMIT 1";
$result_kepsek = mysqli_query($koneksi, $query_kepsek);
$kepsek = mysqli_fetch_assoc($result_kepsek);

if (!$kepsek) {
    // Fallback empty data if somehow table is empty
    $kepsek = [
        'name' => '',
        'position' => '',
        'photo' => '',
        'education' => '',
        'period' => '',
        'vision_mission' => '',
        'quote' => ''
    ];
}

$page_title = 'Profil Kepala Sekolah';

$hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$today = $hari[date('w')] . ', ' . date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="icon" type="image/png" href="img/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Kepsek — Admin SDN Laladon 03</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>

<body>
    <div class="admin-wrapper">
        <?php include 'admin_sidebar.php'; ?>
        <div id="admin-content" class="admin-content">
            <div class="admin-topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle"><i data-lucide="menu"
                            style="width:18px;height:18px;"></i></button>
                    <div>
                        <p class="topbar-title"><?php echo $page_title; ?></p>
                        <p class="topbar-subtitle"><?php echo $today; ?></p>
                    </div>
                </div>
                <div class="topbar-right">

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
                    <div
                        class="alert-admin <?php echo strpos($success, 'berhasil') !== false ? 'alert-success' : 'alert-error'; ?> mb-4">
                        <i
                            data-lucide="<?php echo strpos($success, 'berhasil') !== false ? 'check-circle' : 'alert-circle'; ?>"></i>
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-8">
                        <div class="form-card">
                            <div class="form-card-header">
                                <div class="form-card-icon"><i data-lucide="user-check"
                                        style="width:22px;height:22px;"></i></div>
                                <h5 class="form-card-title">Edit Data Kepala Sekolah</h5>
                            </div>
                            <div class="form-card-body">
                                <form action="admin_kepsek.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="form_type" value="kepsek">
                                    <input type="hidden" name="existing_photo" value="<?php echo htmlspecialchars($kepsek['photo'] ?? ''); ?>">

                                    <div class="row g-4">
                                        <div class="col-12 text-center">
                                            <div class="img-preview-wrap"
                                                style="display:inline-block;padding:1rem 2rem;">
                                                <img id="photoPreview"
                                                    src="<?php echo !empty($kepsek['photo']) ? (strpos($kepsek['photo'], 'img/') === 0 ? htmlspecialchars($kepsek['photo']) : 'img/' . htmlspecialchars($kepsek['photo'])) : 'https://via.placeholder.com/150'; ?>"
                                                    class="img-preview-circle" alt="Foto">
                                                <div style="font-size:.75rem;color:#94a3b8;margin-top:.5rem;">Foto saat
                                                    ini</div>
                                                <input type="file" name="photo" class="form-control-admin mt-3"
                                                    accept="image/*"
                                                    style="padding:.5rem .75rem; max-width: 250px; margin: 0 auto;"
                                                    onchange="previewImg(this,'photoPreview')">
                                                <div style="font-size:.73rem;color:#94a3b8;margin-top:.4rem;">Format:
                                                    JPG, JPEG, PNG. Maks. 3MB.</div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label-admin">Nama Lengkap <span
                                                    style="color:#ef4444;">*</span></label>
                                            <input type="text" name="name" class="form-control-admin"
                                                value="<?php echo htmlspecialchars($kepsek['name'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-admin">Jabatan <span
                                                    style="color:#ef4444;">*</span></label>
                                            <input type="text" name="position" class="form-control-admin"
                                                value="<?php echo htmlspecialchars($kepsek['position'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-admin">Pendidikan</label>
                                            <input type="text" name="education" class="form-control-admin"
                                                value="<?php echo htmlspecialchars($kepsek['education'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-admin">Jabatan Sejak (Tahun)</label>
                                            <input type="text" name="period" class="form-control-admin"
                                                value="<?php echo htmlspecialchars($kepsek['period'] ?? ''); ?>">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label-admin">Visi & Misi Kepemimpinan</label>
                                            <textarea name="vision_mission" class="form-control-admin"
                                                rows="4"><?php echo htmlspecialchars($kepsek['vision_mission'] ?? ''); ?></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label-admin">Kutipan (Quote)</label>
                                            <textarea name="quote" class="form-control-admin"
                                                rows="3"><?php echo htmlspecialchars($kepsek['quote'] ?? ''); ?></textarea>
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

        <!-- ========= RIWAYAT / TIMELINE ========= -->
        <div class="row justify-content-center mt-4">
            <div class="col-lg-10 col-xl-8">
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon"><i data-lucide="clock" style="width:22px;height:22px;"></i></div>
                        <h5 class="form-card-title">Riwayat Pendidikan & Karir</h5>
                    </div>
                    <div class="form-card-body">

                        <?php
                        [$rmtype, $rmmsg] = $riwayat_msg ? explode(':', $riwayat_msg, 2) : ['', ''];
                        if ($rmmsg): ?>
                        <div class="alert-admin <?php echo $rmtype === 'success' ? 'alert-success' : 'alert-error'; ?> mb-3">
                            <i data-lucide="<?php echo $rmtype === 'success' ? 'check-circle' : 'alert-circle'; ?>"></i>
                            <?php echo htmlspecialchars($rmmsg); ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($_GET['riwayat_deleted'])): ?>
                        <div class="alert-admin alert-error mb-3"><i data-lucide="trash-2"></i> Riwayat berhasil dihapus.</div>
                        <?php endif; ?>

                        <!-- Form tambah / edit riwayat -->
                        <form method="POST" class="mb-4">
                            <input type="hidden" name="form_type" value="riwayat">
                            <input type="hidden" name="riwayat_id" value="<?php echo $edit_riwayat ? $edit_riwayat['id'] : 0; ?>">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label-admin">Tahun / Periode <span style="color:#ef4444">*</span></label>
                                    <input type="text" name="tahun" class="form-control-admin" placeholder="cth: 2004 – 2006"
                                        value="<?php echo $edit_riwayat ? htmlspecialchars($edit_riwayat['tahun']) : ''; ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-admin">Judul <span style="color:#ef4444">*</span></label>
                                    <input type="text" name="judul" class="form-control-admin" placeholder="Nama institusi / jabatan"
                                        value="<?php echo $edit_riwayat ? htmlspecialchars($edit_riwayat['judul']) : ''; ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-admin">Deskripsi</label>
                                    <input type="text" name="deskripsi" class="form-control-admin" placeholder="Keterangan singkat"
                                        value="<?php echo $edit_riwayat ? htmlspecialchars($edit_riwayat['deskripsi']) : ''; ?>">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label-admin">Urutan</label>
                                    <input type="number" name="urutan" class="form-control-admin" min="1"
                                        value="<?php echo $edit_riwayat ? $edit_riwayat['urutan'] : ''; ?>" placeholder="#">
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <button type="submit" class="btn-admin btn-admin-primary">
                                    <i data-lucide="<?php echo $edit_riwayat ? 'save' : 'plus'; ?>"></i>
                                    <?php echo $edit_riwayat ? 'Simpan Perubahan' : 'Tambah Riwayat'; ?>
                                </button>
                                <?php if ($edit_riwayat): ?>
                                <a href="admin_kepsek.php" class="btn-admin btn-admin-secondary"><i data-lucide="x"></i> Batal</a>
                                <?php endif; ?>
                            </div>
                        </form>

                        <!-- Tabel daftar riwayat -->
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th style="width:30px;">#</th>
                                        <th style="width:120px;">Tahun</th>
                                        <th>Judul</th>
                                        <th class="d-none d-md-table-cell">Deskripsi</th>
                                        <th class="text-center" style="width:90px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $res_r = mysqli_query($koneksi, "SELECT * FROM kepsek_riwayat ORDER BY urutan ASC");
                                if (mysqli_num_rows($res_r) > 0):
                                    $no = 1;
                                    while ($r = mysqli_fetch_assoc($res_r)):
                                ?>
                                    <tr>
                                        <td style="color:#94a3b8;font-weight:600;"><?php echo $no++; ?></td>
                                        <td><span class="timeline-year-badge" style="font-size:.72rem;"><?php echo htmlspecialchars($r['tahun']); ?></span></td>
                                        <td class="tbl-name"><?php echo htmlspecialchars($r['judul']); ?></td>
                                        <td class="d-none d-md-table-cell" style="font-size:.8rem;color:#64748b;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($r['deskripsi']); ?></td>
                                        <td>
                                            <div class="action-wrap justify-content-center">
                                                <a href="admin_kepsek.php?edit_riwayat=<?php echo $r['id']; ?>" class="btn-tbl btn-tbl-edit" title="Edit"><i data-lucide="pencil"></i></a>
                                                <a href="admin_kepsek.php?delete_riwayat=<?php echo $r['id']; ?>" class="btn-tbl btn-tbl-delete" title="Hapus" onclick="return confirm('Hapus riwayat ini?')"><i data-lucide="trash-2"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="5" class="tbl-empty">Belum ada data riwayat.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- /RIWAYAT -->

        </div><!-- /admin-page -->
    </div><!-- /admin-content -->
    </div><!-- /admin-wrapper -->

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
