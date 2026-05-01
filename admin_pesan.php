<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$success_msg = '';
$error_msg = '';

// Handle read / delete
if (isset($_GET['read_msg'])) {
    $mid = (int) $_GET['read_msg'];
    mysqli_query($koneksi, "UPDATE contact_messages SET is_read=1 WHERE id=$mid");
    header("Location: admin_pesan.php");
    exit;
}
if (isset($_GET['read_all'])) {
    mysqli_query($koneksi, "UPDATE contact_messages SET is_read=1");
    header("Location: admin_pesan.php");
    exit;
}
if (isset($_GET['delete_msg'])) {
    $mid = (int) $_GET['delete_msg'];
    mysqli_query($koneksi, "DELETE FROM contact_messages WHERE id=$mid");
    header("Location: admin_pesan.php");
    exit;
}

$filter = $_GET['filter'] ?? '';
$search = trim($_GET['q'] ?? '');
$conditions = [];
if ($filter === 'unread')
    $conditions[] = "is_read=0";
if ($filter === 'read')
    $conditions[] = "is_read=1";
if ($search)
    $conditions[] = "(nama LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%' OR subjek LIKE '%" . mysqli_real_escape_string($koneksi, $search) . "%')";
$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : '';

$res = mysqli_query($koneksi, "SELECT * FROM contact_messages $where ORDER BY created_at DESC");
$total = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM contact_messages $where"))['t'];
$unread_messages = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM contact_messages WHERE is_read=0"))['t'];
$total_messages = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM contact_messages"))['t'];

$hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$today = $hari[date('w')] . ', ' . date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk — Admin SDN Laladon 03</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
    <style>
        .msg-card {
            background: #fff;
            border: 1.5px solid #f1f5f9;
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            margin-bottom: .75rem;
            transition: all .2s;
            cursor: pointer;
        }

        .msg-card:hover {
            border-color: #fcd34d;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .07);
        }

        .msg-card.unread {
            border-left: 4px solid #f59e0b;
            background: #fffdf5;
        }

        .msg-card.read {
            border-left: 4px solid #e2e8f0;
        }

        .msg-avatar {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .msg-avatar.read-av {
            background: linear-gradient(135deg, #94a3b8, #64748b);
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
                        <p class="topbar-title">Pesan Masuk <?php if ($unread_messages > 0): ?><span
                                    style="background:#ef4444;color:#fff;font-size:.65rem;padding:.15rem .5rem;border-radius:50px;margin-left:.4rem;"><?php echo $unread_messages; ?>
                                    baru</span><?php endif; ?></p>
                        <p class="topbar-subtitle"><?php echo $today; ?></p>
                    </div>
                </div>
                <div class="topbar-right">
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
                        <h1 class="page-title">Pesan Masuk</h1>
                        <p class="page-breadcrumb">
                            <a href="admin_dashboard.php" style="color:#94a3b8;text-decoration:none;">Dashboard</a>
                            &rsaquo; <span>Pesan Masuk</span>
                        </p>
                    </div>
                    <?php if ($unread_messages > 0): ?>
                        <a href="admin_pesan.php?read_all=1" class="btn-admin btn-admin-secondary"
                            onclick="return confirm('Tandai semua pesan sebagai sudah dibaca?')">
                            <i data-lucide="check-check"></i>
                            Tandai Semua Dibaca
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($success_msg): ?>
                    <div class="alert-admin alert-success mb-4"><i
                            data-lucide="check-circle"></i><?php echo $success_msg; ?></div>
                <?php endif; ?>
                <?php if ($error_msg): ?>
                    <div class="alert-admin alert-error mb-4"><i
                            data-lucide="alert-circle"></i><?php echo htmlspecialchars($error_msg); ?></div>
                <?php endif; ?>

                <!-- Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon blue"><i data-lucide="mail" style="width:22px;height:22px;"></i></div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $total_messages; ?></div>
                                <div class="stat-label">Total Pesan</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="stat-card" style="<?php echo $unread_messages > 0 ? 'border-color:#fcd34d;' : '' ?>">
                            <div class="stat-icon amber"><i data-lucide="mail-open" style="width:22px;height:22px;"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $unread_messages; ?></div>
                                <div class="stat-label">Belum Dibaca</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon green"><i data-lucide="check-circle"
                                    style="width:22px;height:22px;"></i></div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $total_messages - $unread_messages; ?></div>
                                <div class="stat-label">Sudah Dibaca</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter & Search -->
                <div class="admin-card mb-4" style="overflow:visible;">
                    <div class="table-toolbar" style="border:none;">
                        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap w-100">
                            <div class="search-wrap">
                                <i data-lucide="search"></i>
                                <input type="text" name="q" class="search-input" placeholder="Cari nama atau subjek..."
                                    value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <select name="filter" class="filter-select">
                                <option value="">Semua Pesan</option>
                                <option value="unread" <?php echo $filter === 'unread' ? 'selected' : ''; ?>>Belum Dibaca
                                </option>
                                <option value="read" <?php echo $filter === 'read' ? 'selected' : ''; ?>>Sudah Dibaca</option>
                            </select>
                            <button type="submit" class="btn-admin btn-admin-secondary">Filter</button>
                            <?php if ($search || $filter): ?>
                                <a href="admin_pesan.php" class="btn-admin btn-admin-secondary">Reset</a>
                            <?php endif; ?>
                            <span style="margin-left:auto;font-size:.8rem;color:#94a3b8;"><?php echo $total; ?>
                                pesan</span>
                        </form>
                    </div>
                </div>

                <!-- Messages List -->
                <?php if (mysqli_num_rows($res) > 0):
                    while ($msg = mysqli_fetch_assoc($res)):
                        $is_unread = !$msg['is_read'];
                        $initial = strtoupper(substr($msg['nama'], 0, 1));
                        ?>
                        <div class="msg-card <?php echo $is_unread ? 'unread' : 'read'; ?>" id="msg-<?php echo $msg['id']; ?>">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="msg-avatar <?php echo !$is_unread ? 'read-av' : ''; ?>"><?php echo $initial; ?>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div>
                                            <span class="tbl-name"><?php echo htmlspecialchars($msg['nama']); ?></span>
                                            <?php if ($is_unread): ?>
                                                <span class="badge-status badge-new ms-2" style="font-size:.65rem;">Baru</span>
                                            <?php else: ?>
                                                <span class="badge-status badge-read ms-2" style="font-size:.65rem;">Dibaca</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span style="font-size:.75rem;color:#94a3b8;">
                                                <i data-lucide="clock"
                                                    style="width:12px;height:12px;vertical-align:middle;"></i>
                                                <?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?>
                                            </span>
                                            <div class="action-wrap">
                                                <?php if ($is_unread): ?>
                                                    <a href="admin_pesan.php?read_msg=<?php echo $msg['id']; ?>"
                                                        class="btn-tbl btn-tbl-view" title="Tandai Dibaca">
                                                        <i data-lucide="check"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="admin_pesan.php?delete_msg=<?php echo $msg['id']; ?>"
                                                    class="btn-tbl btn-tbl-delete" title="Hapus"
                                                    onclick="return confirm('Yakin hapus pesan ini?')">
                                                    <i data-lucide="trash-2"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="font-size:.78rem;color:#94a3b8;margin:.2rem 0;">
                                        <i data-lucide="mail" style="width:12px;height:12px;vertical-align:middle;"></i>
                                        <?php echo htmlspecialchars($msg['email']); ?>
                                    </div>
                                    <div style="font-size:.88rem;font-weight:600;color:#0f172a;margin:.5rem 0 .35rem;">
                                        <?php echo htmlspecialchars($msg['subjek']); ?>
                                    </div>
                                    <div
                                        style="font-size:.83rem;color:#475569;line-height:1.6;background:#f8fafc;border-radius:10px;padding:.75rem 1rem;">
                                        <?php echo nl2br(htmlspecialchars($msg['pesan'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; else: ?>
                    <div class="admin-card">
                        <div class="tbl-empty py-5">
                            <div class="tbl-empty-icon"><i data-lucide="inbox" style="width:28px;height:28px;"></i></div>
                            <?php echo ($search || $filter) ? 'Tidak ada pesan sesuai filter.' : 'Belum ada pesan masuk.'; ?>
                        </div>
                    </div>
                <?php endif; ?>
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
    </script>
</body>

</html>