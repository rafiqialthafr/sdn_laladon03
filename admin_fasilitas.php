<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$error_msg = '';

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM fasilitas WHERE id=$id");
    header("Location: admin_fasilitas.php?success=deleted");
    exit;
}

// Handle ADD / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $edit_id = (int) ($_POST['edit_id'] ?? 0);

    $image_url = '';
    // Upload file jika ada
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($ext, $allowed)) {
            if ($_FILES['image']['size'] > 3 * 1024 * 1024) {
                $error_msg = 'Ukuran file terlalu besar! Maksimal 3MB.';
            } else {
                $dir = 'uploads/fasilitas/';
                if (!is_dir($dir))
                    mkdir($dir, 0755, true);
                $fname = 'fasilitas_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
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
                ? "UPDATE fasilitas SET name='" . mysqli_real_escape_string($koneksi, $name) . "', description='" . mysqli_real_escape_string($koneksi, $description) . "', image='" . mysqli_real_escape_string($koneksi, $image_url) . "' WHERE id=$edit_id"
                : "UPDATE fasilitas SET name='" . mysqli_real_escape_string($koneksi, $name) . "', description='" . mysqli_real_escape_string($koneksi, $description) . "' WHERE id=$edit_id";
            mysqli_query($koneksi, $q);
            header("Location: admin_fasilitas.php?success=updated");
            exit;
        } else {
            // Insert
            if ($name && $image_url) {
                mysqli_query($koneksi, "INSERT INTO fasilitas (name, description, image) VALUES ('" . mysqli_real_escape_string($koneksi, $name) . "','" . mysqli_real_escape_string($koneksi, $description) . "','" . mysqli_real_escape_string($koneksi, $image_url) . "')");
                header("Location: admin_fasilitas.php?success=added");
                exit;
            } else {
                $error_msg = 'Nama dan gambar fasilitas wajib diisi.';
            }
        }
    }
}

// Fetch edit data
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM fasilitas WHERE id=" . (int) $_GET['edit']));
}

$success = $_GET['success'] ?? '';
$search = trim($_GET['q'] ?? '');
$conditions = [];
if ($search)
    $conditions[] = "name LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%'";
$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : '';

$res = mysqli_query($koneksi, "SELECT * FROM fasilitas $where ORDER BY id DESC");
$total = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM fasilitas $where"))['t'];

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
    <title>Kelola Fasilitas — Admin SDN Laladon 03</title>
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
                        <p class="topbar-title">Fasilitas</p>
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
                        <h1 class="page-title">Fasilitas</h1>
                        <p class="page-breadcrumb">
                            <a href="admin_dashboard.php" style="color:#94a3b8;text-decoration:none;">Dashboard</a>
                            &rsaquo; <span>Fasilitas</span>
                        </p>
                    </div>
                    <button type="button" class="btn-admin btn-admin-primary" onclick="openModal()">
                        <i data-lucide="building-2"></i>
                        Tambah Fasilitas
                    </button>
                </div>

                <!-- Alerts -->
                <?php if ($success === 'deleted'): ?>
                    <div class="alert-admin alert-error mb-4"><i data-lucide="trash-2"></i> Fasilitas berhasil dihapus.
                    </div>
                <?php endif; ?>
                <?php if ($success === 'added'): ?>
                    <div class="alert-admin alert-success mb-4"><i data-lucide="check-circle"></i> Fasilitas berhasil
                        ditambahkan!</div>
                <?php endif; ?>
                <?php if ($success === 'updated'): ?>
                    <div class="alert-admin alert-success mb-4"><i data-lucide="check-circle"></i> Fasilitas berhasil
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
                                <input type="text" name="q" class="search-input" placeholder="Cari nama fasilitas..."
                                    value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <button type="submit" class="btn-admin btn-admin-secondary">Cari</button>
                            <?php if ($search): ?>
                                <a href="admin_fasilitas.php" class="btn-admin btn-admin-secondary">Reset</a>
                            <?php endif; ?>
                            <span style="margin-left:auto;font-size:.8rem;color:#94a3b8;"><?php echo $total; ?>
                                fasilitas
                                ditemukan</span>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width:50px;">#</th>
                                    <th>Gambar</th>
                                    <th>Nama Fasilitas</th>
                                    <th class="text-center" style="width:100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($res) > 0):
                                    $no = 1;
                                    while ($f = mysqli_fetch_assoc($res)):
                                        ?>
                                        <tr>
                                            <td style="color:#94a3b8;font-weight:600;"><?php echo $no++; ?></td>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($f['image']); ?>" class="fasilitas-thumb"
                                                    alt="<?php echo htmlspecialchars($f['name']); ?>" loading="lazy">
                                            </td>
                                            <td>
                                                <div class="tbl-name"><?php echo htmlspecialchars($f['name']); ?></div>
                                                <div
                                                    style="font-size:.8rem;color:#64748b;max-width:250px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                    <?php echo htmlspecialchars($f['description']); ?>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="action-wrap justify-content-center">
                                                    <a href="admin_fasilitas.php?edit=<?php echo $f['id']; ?>"
                                                        class="btn-tbl btn-tbl-edit" title="Edit">
                                                        <i data-lucide="pencil"></i>
                                                    </a>
                                                    <a href="admin_fasilitas.php?delete=<?php echo $f['id']; ?>"
                                                        class="btn-tbl btn-tbl-delete" title="Hapus"
                                                        onclick="return confirm('Yakin hapus fasilitas ini?')">
                                                        <i data-lucide="trash-2"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; else: ?>
                                    <tr>
                                        <td colspan="4">
                                            <div class="tbl-empty">
                                                <div class="tbl-empty-icon">
                                                    <i data-lucide="building-2" style="width:28px;height:28px;"></i>
                                                </div>
                                                <?php echo $search ? 'Tidak ada fasilitas yang ditemukan.' : 'Belum ada fasilitas.'; ?>
                                                <?php if (!$search): ?>
                                                    <div class="mt-3">
                                                        <button type="button" class="btn-admin btn-admin-primary"
                                                            onclick="openModal()">
                                                            <i data-lucide="plus"></i> Tambah Fasilitas Pertama
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

    <!-- Modal Tambah/Edit Fasilitas -->
    <div class="modal-overlay" id="fasilitasModal">
        <div class="modal-panel">
            <div class="modal-header-custom">
                <h5>
                    <i data-lucide="<?php echo $edit_data ? 'pencil' : 'plus'; ?>"
                        style="width:18px;height:18px;color:#d97706;"></i>
                    <?php echo $edit_data ? 'Edit Fasilitas' : 'Tambah Fasilitas Baru'; ?>
                </h5>
                <button class="modal-close" onclick="closeModal()">
                    <i data-lucide="x" style="width:16px;height:16px;"></i>
                </button>
            </div>

            <?php if ($edit_data): ?>
                <img src="<?php echo htmlspecialchars($edit_data['image']); ?>" class="edit-preview-img" alt="">
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" value="<?php echo $edit_data ? $edit_data['id'] : 0; ?>">

                <div class="mb-3">
                    <label class="form-label-admin">Nama Fasilitas <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" class="form-control-admin" placeholder="Contoh: Perpustakaan"
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['name']) : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label-admin">Deskripsi</label>
                    <textarea name="description" class="form-control-admin" rows="3"
                        placeholder="Deskripsi singkat fasilitas..."><?php echo $edit_data ? htmlspecialchars($edit_data['description']) : ''; ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label-admin">Upload Gambar Fasilitas</label>
                    <input type="file" name="image" class="form-control-admin" accept="image/*"
                        style="padding:.5rem .75rem;" id="modalFotoInput">
                    <div style="font-size:.73rem;color:#94a3b8;margin-top:.4rem;">Format: JPG, JPEG, PNG, WEBP. Maks.
                        3MB</div>
                    <img id="modalPreviewImg"
                        style="display:none;width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:12px;margin-top:.75rem;border:1.5px solid #e2e8f0;"
                        alt="">
                </div>

                <div class="mb-4">
                    <label class="form-label-admin">Atau URL Gambar</label>
                    <input type="text" name="image_url" class="form-control-admin" placeholder="https://..."
                        value="<?php echo $edit_data ? htmlspecialchars($edit_data['image']) : ''; ?>">
                    <div style="font-size:.73rem;color:#94a3b8;margin-top:.4rem;">Isi salah satu (upload file atau URL)
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-admin btn-admin-primary flex-fill justify-content-center py-3">
                        <i data-lucide="<?php echo $edit_data ? 'save' : 'plus'; ?>"></i>
                        <?php echo $edit_data ? 'Simpan Perubahan' : 'Tambah Fasilitas'; ?>
                    </button>
                    <?php if ($edit_data): ?>
                        <a href="admin_fasilitas.php" class="btn-admin btn-admin-secondary py-3">
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
        const modal = document.getElementById('fasilitasModal');
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
