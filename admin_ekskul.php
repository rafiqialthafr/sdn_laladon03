<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: masuk-admin-03.php");
    exit;
}
include 'koneksi.php';

// Check and create table 'ekskul' if it does not exist
$table_check = mysqli_query($koneksi, "SHOW TABLES LIKE 'ekskul'");
if (mysqli_num_rows($table_check) == 0) {
    $create_table = "CREATE TABLE ekskul (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(50) NOT NULL DEFAULT 'pilihan',
        description TEXT,
        image VARCHAR(255) NOT NULL,
        schedule VARCHAR(255),
        instructor VARCHAR(255),
        location VARCHAR(255),
        icon VARCHAR(50) DEFAULT 'compass'
    )";
    mysqli_query($koneksi, $create_table);
    
    // Seed with initial Pramuka data
    $seed = "INSERT INTO ekskul (name, category, description, image, schedule, instructor, location, icon) VALUES 
    ('Gerakan Pramuka', 'wajib', 'Pramuka merupakan ekstrakurikuler wajib di SDN Laladon 03 untuk siswa kelas III, IV, dan V. Melalui gerakan kepramukaan, siswa dibimbing untuk membangun kedisiplinan, ketahanan mental, kerja sama kelompok, kepemimpinan, serta jiwa patriotisme yang berlandaskan Dasa Darma Pramuka.', 'img/slider1.jpeg', 'Sabtu, 08.00 - 10.00 WIB', 'Kak Dadan Ramdani, S.Pd.', 'Lapangan & Lingkungan Sekolah', 'compass')";
    mysqli_query($koneksi, $seed);
}

$error_msg = '';

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM ekskul WHERE id=$id");
    header("Location: admin_ekskul.php?success=deleted");
    exit;
}

// Handle ADD / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'pilihan');
    $description = trim($_POST['description'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $icon = trim($_POST['icon'] ?? 'compass');
    $edit_id = (int) ($_POST['edit_id'] ?? 0);

    $image_url = '';
    // Upload file if present
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($ext, $allowed)) {
            if ($_FILES['image']['size'] > 5 * 1024 * 1024) { // 5MB Limit per sidebar Sidney defense guidelines update
                $error_msg = 'Ukuran file terlalu besar! Maksimal 5MB.';
            } else {
                $dir = 'uploads/ekskul/';
                if (!is_dir($dir))
                    mkdir($dir, 0755, true);
                $fname = 'ekskul_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $dir . $fname);
                $image_url = $dir . $fname;
            }
        } else {
            $error_msg = 'Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau WEBP.';
        }
    } elseif (!empty($_POST['image_url'])) {
        $image_url = trim($_POST['image_url']);
    }

    if (!$error_msg) {
        if ($edit_id > 0) {
            // Update
            $q = $image_url
                ? "UPDATE ekskul SET 
                    name='" . mysqli_real_escape_string($koneksi, $name) . "', 
                    category='" . mysqli_real_escape_string($koneksi, $category) . "', 
                    description='" . mysqli_real_escape_string($koneksi, $description) . "', 
                    schedule='" . mysqli_real_escape_string($koneksi, $schedule) . "', 
                    instructor='" . mysqli_real_escape_string($koneksi, $instructor) . "', 
                    location='" . mysqli_real_escape_string($koneksi, $location) . "', 
                    icon='" . mysqli_real_escape_string($koneksi, $icon) . "', 
                    image='" . mysqli_real_escape_string($koneksi, $image_url) . "' 
                   WHERE id=$edit_id"
                : "UPDATE ekskul SET 
                    name='" . mysqli_real_escape_string($koneksi, $name) . "', 
                    category='" . mysqli_real_escape_string($koneksi, $category) . "', 
                    description='" . mysqli_real_escape_string($koneksi, $description) . "', 
                    schedule='" . mysqli_real_escape_string($koneksi, $schedule) . "', 
                    instructor='" . mysqli_real_escape_string($koneksi, $instructor) . "', 
                    location='" . mysqli_real_escape_string($koneksi, $location) . "', 
                    icon='" . mysqli_real_escape_string($koneksi, $icon) . "' 
                   WHERE id=$edit_id";
            mysqli_query($koneksi, $q);
            header("Location: admin_ekskul.php?success=updated");
            exit;
        } else {
            // Insert
            if ($name && $image_url) {
                mysqli_query($koneksi, "INSERT INTO ekskul (name, category, description, image, schedule, instructor, location, icon) VALUES (
                    '" . mysqli_real_escape_string($koneksi, $name) . "',
                    '" . mysqli_real_escape_string($koneksi, $category) . "',
                    '" . mysqli_real_escape_string($koneksi, $description) . "',
                    '" . mysqli_real_escape_string($koneksi, $image_url) . "',
                    '" . mysqli_real_escape_string($koneksi, $schedule) . "',
                    '" . mysqli_real_escape_string($koneksi, $instructor) . "',
                    '" . mysqli_real_escape_string($koneksi, $location) . "',
                    '" . mysqli_real_escape_string($koneksi, $icon) . "'
                )");
                header("Location: admin_ekskul.php?success=added");
                exit;
            } else {
                $error_msg = 'Nama ekskul dan gambar wajib diisi.';
            }
        }
    }
}

// Fetch edit data
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM ekskul WHERE id=" . (int) $_GET['edit']));
}

$success = $_GET['success'] ?? '';
$search = trim($_GET['q'] ?? '');
$conditions = [];
if ($search)
    $conditions[] = "name LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%'";
$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : '';

$res = mysqli_query($koneksi, "SELECT * FROM ekskul $where ORDER BY id DESC");
$total = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM ekskul $where"))['t'];

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
    <title>Kelola Ekskul — Admin SDN Laladon 03</title>
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
                        <p class="topbar-title">Ekskul</p>
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
                        <h1 class="page-title">Kelola Ekskul</h1>
                        <p class="page-breadcrumb">
                            <a href="admin_dashboard.php" style="color:#94a3b8;text-decoration:none;">Dashboard</a>
                            &rsaquo; <span>Ekskul</span>
                        </p>
                    </div>
                    <button type="button" class="btn-admin btn-admin-primary" onclick="openModal()">
                        <i data-lucide="compass"></i>
                        Tambah Ekskul
                    </button>
                </div>

                <!-- Alerts -->
                <?php if ($success === 'deleted'): ?>
                    <div class="alert-admin alert-error mb-4"><i data-lucide="trash-2"></i> Ekskul berhasil dihapus.
                    </div>
                <?php endif; ?>
                <?php if ($success === 'added'): ?>
                    <div class="alert-admin alert-success mb-4"><i data-lucide="check-circle"></i> Ekskul berhasil
                        ditambahkan!</div>
                <?php endif; ?>
                <?php if ($success === 'updated'): ?>
                    <div class="alert-admin alert-success mb-4"><i data-lucide="check-circle"></i> Ekskul berhasil
                        diperbarui!</div>
                <?php endif; ?>
                <?php if ($error_msg): ?>
                    <div class="alert-admin alert-error mb-4"><i data-lucide="alert-circle"></i>
                        <?php echo htmlspecialchars($error_msg); ?></div>
                <?php endif; ?>

                <!-- Table Card -->
                <div class="admin-card">
                    <div class="table-toolbar">
                        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap w-100">
                            <div class="search-wrap">
                                <i data-lucide="search"></i>
                                <input type="text" name="q" class="search-input" placeholder="Cari nama ekskul..."
                                    value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <button type="submit" class="btn-admin btn-admin-secondary">Cari</button>
                            <?php if ($search): ?>
                                <a href="admin_ekskul.php" class="btn-admin btn-admin-secondary">Reset</a>
                            <?php endif; ?>
                            <span style="margin-left:auto;font-size:.8rem;color:#94a3b8;"><?php echo $total; ?>
                                ekskul ditemukan</span>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width:50px;">#</th>
                                    <th>Gambar</th>
                                    <th>Nama Ekskul</th>
                                    <th>Kategori</th>
                                    <th>Jadwal &amp; Pembina</th>
                                    <th class="text-center" style="width:100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($res) > 0):
                                    $no = 1;
                                    while ($e = mysqli_fetch_assoc($res)):
                                        ?>
                                        <tr>
                                            <td style="color:#94a3b8;font-weight:600;"><?php echo $no++; ?></td>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($e['image']); ?>" class="fasilitas-thumb"
                                                    alt="<?php echo htmlspecialchars($e['name']); ?>" loading="lazy" style="width:70px;height:50px;object-fit:cover;border-radius:6px;">
                                            </td>
                                            <td>
                                                <div class="tbl-name d-flex align-items-center gap-2">
                                                    <i data-lucide="<?php echo htmlspecialchars($e['icon']); ?>" style="width:16px;height:16px;color:#d97706;"></i>
                                                    <?php echo htmlspecialchars($e['name']); ?>
                                                </div>
                                                <div style="font-size:.8rem;color:#64748b;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                    <?php echo htmlspecialchars($e['description']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $e['category'] === 'wajib' ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary'; ?> px-2.5 py-1 text-uppercase fw-bold" style="font-size:.7rem;border-radius:20px;">
                                                    <?php echo htmlspecialchars($e['category']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="font-size:.82rem;font-weight:600;color:#1e293b;">
                                                    <i data-lucide="calendar" style="width:13px;height:13px;vertical-align:middle;margin-right:3px;color:#64748b;"></i>
                                                    <?php echo htmlspecialchars($e['schedule']); ?>
                                                </div>
                                                <div style="font-size:.75rem;color:#64748b;">
                                                    <i data-lucide="user" style="width:13px;height:13px;vertical-align:middle;margin-right:3px;"></i>
                                                    <?php echo htmlspecialchars($e['instructor']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-wrap justify-content-center">
                                                    <a href="admin_ekskul.php?edit=<?php echo $e['id']; ?>"
                                                        class="btn-tbl btn-tbl-edit" title="Edit">
                                                        <i data-lucide="pencil"></i>
                                                    </a>
                                                    <a href="admin_ekskul.php?delete=<?php echo $e['id']; ?>"
                                                        class="btn-tbl btn-tbl-delete" title="Hapus"
                                                        onclick="return confirm('Yakin hapus ekskul ini?')">
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
                                                    <i data-lucide="compass" style="width:28px;height:28px;"></i>
                                                </div>
                                                <?php echo $search ? 'Tidak ada ekskul yang ditemukan.' : 'Belum ada ekskul.'; ?>
                                                <?php if (!$search): ?>
                                                    <div class="mt-3">
                                                        <button type="button" class="btn-admin btn-admin-primary"
                                                            onclick="openModal()">
                                                            <i data-lucide="plus"></i> Tambah Ekskul Pertama
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

    <!-- Modal Tambah/Edit Ekskul -->
    <div class="modal-overlay" id="ekskulModal">
        <div class="modal-panel" style="max-width: 600px;">
            <div class="modal-header-custom">
                <h5>
                    <i data-lucide="<?php echo $edit_data ? 'pencil' : 'plus'; ?>"
                        style="width:18px;height:18px;color:#d97706;"></i>
                    <?php echo $edit_data ? 'Edit Ekskul' : 'Tambah Ekskul Baru'; ?>
                </h5>
                <button class="modal-close" onclick="closeModal()">
                    <i data-lucide="x" style="width:16px;height:16px;"></i>
                </button>
            </div>

            <?php if ($edit_data): ?>
                <img src="<?php echo htmlspecialchars($edit_data['image']); ?>" class="edit-preview-img" alt="" style="height:150px;object-fit:cover;width:100%;border-radius:10px;margin-bottom:1rem;">
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" value="<?php echo $edit_data ? $edit_data['id'] : 0; ?>">

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label-admin">Nama Ekskul <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" class="form-control-admin" placeholder="Contoh: Gerakan Pramuka"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['name']) : ''; ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label-admin">Kategori <span style="color:#ef4444;">*</span></label>
                        <select name="category" class="form-control-admin" style="padding:.65rem .75rem;" required>
                            <option value="wajib" <?php echo ($edit_data && $edit_data['category'] === 'wajib') ? 'selected' : ''; ?>>Wajib</option>
                            <option value="pilihan" <?php echo ($edit_data && $edit_data['category'] === 'pilihan') ? 'selected' : ''; ?>>Pilihan</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-admin">Deskripsi Ekskul</label>
                    <textarea name="description" class="form-control-admin" rows="3" required
                        placeholder="Deskripsi kegiatan ekskul..."><?php echo $edit_data ? htmlspecialchars($edit_data['description']) : ''; ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label-admin">Jadwal Latihan</label>
                        <input type="text" name="schedule" class="form-control-admin" placeholder="Contoh: Sabtu, 08.00 - 10.00 WIB"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['schedule']) : ''; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label-admin">Pembina / Pelatih</label>
                        <input type="text" name="instructor" class="form-control-admin" placeholder="Contoh: Kak Dadan, S.Pd."
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['instructor']) : ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label-admin">Lokasi Latihan</label>
                        <input type="text" name="location" class="form-control-admin" placeholder="Contoh: Lapangan Sekolah"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['location']) : ''; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label-admin">Ikon (Lucide Icon Name)</label>
                        <input type="text" name="icon" class="form-control-admin" placeholder="Contoh: compass, swords, music, heart"
                            value="<?php echo $edit_data ? htmlspecialchars($edit_data['icon']) : 'compass'; ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-admin">Upload Gambar Ekskul</label>
                    <input type="file" name="image" class="form-control-admin" accept="image/*"
                        style="padding:.5rem .75rem;" id="modalFotoInput">
                    <div style="font-size:.73rem;color:#94a3b8;margin-top:.4rem;">Format: JPG, JPEG, PNG, WEBP. Maks. 5MB</div>
                    <img id="modalPreviewImg"
                        style="display:none;width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:12px;margin-top:.75rem;border:1.5px solid #e2e8f0;"
                        alt="">
                </div>

                <div class="mb-4">
                    <label class="form-label-admin">Atau URL Gambar</label>
                    <input type="text" name="image_url" class="form-control-admin" placeholder="https://..."
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['image']) : ''; ?>">
                    <div style="font-size:.73rem;color:#94a3b8;margin-top:.4rem;">Isi salah satu (upload file atau URL)</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-admin btn-admin-primary flex-fill justify-content-center py-3">
                        <i data-lucide="<?php echo $edit_data ? 'save' : 'plus'; ?>"></i>
                        <?php echo $edit_data ? 'Simpan Perubahan' : 'Tambah Ekskul'; ?>
                    </button>
                    <?php if ($edit_data): ?>
                        <a href="admin_ekskul.php" class="btn-admin btn-admin-secondary py-3">
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
        const modal = document.getElementById('ekskulModal');
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
