<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';
require_once 'src/Exception.php';
require_once 'src/PHPMailer.php';
require_once 'src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;

// Handle reply pesan
$success_msg = '';
$error_msg   = '';
if (isset($_POST['reply_msg'])) {
    $reply_to   = trim($_POST['reply_email']);
    $reply_subj = trim($_POST['reply_subjek']);
    $reply_body = trim($_POST['reply_isi']);
    $msg_id     = (int)$_POST['msg_id'];

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'rafiqialthaf8@gmail.com';
        $mail->Password   = 'rjxa hoot wvfq kvda';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('rafiqialthaf8@gmail.com', 'Admin SDN Laladon 03');
        $mail->addAddress($reply_to);
        $mail->Subject = 'Re: ' . $reply_subj;
        $mail->isHTML(false);
        $mail->Body    = $reply_body;
        $mail->send();

        mysqli_query($koneksi, "UPDATE contact_messages SET is_read=1 WHERE id=$msg_id");
        $success_msg = 'Email berhasil dikirim ke ' . htmlspecialchars($reply_to) . '!';
    } catch (Exception $e) {
        $error_msg = 'Gagal kirim email: ' . $mail->ErrorInfo;

    }
}

// Stats
$total_teachers  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM teachers"))['total'];
$total_news      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM announcements WHERE is_published=1"))['total'];
$total_draft     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM announcements WHERE is_published=0"))['total'];
$total_all_news  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM announcements"))['total'];
$total_messages  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM contact_messages"))['total'];
$unread_messages = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM contact_messages WHERE is_read=0"))['total'];

// Handle read/delete actions
if (isset($_GET['read_msg'])) {
    $mid = (int)$_GET['read_msg'];
    mysqli_query($koneksi, "UPDATE contact_messages SET is_read=1 WHERE id=$mid");
    header('Location: admin_dashboard.php#messages-section');
    exit;
}
if (isset($_GET['delete_msg'])) {
    $mid = (int)$_GET['delete_msg'];
    mysqli_query($koneksi, "DELETE FROM contact_messages WHERE id=$mid");
    header('Location: admin_dashboard.php#messages-section');
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — SDN Laladon 03</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
        }

        /* ── LAYOUT ── */
        .admin-wrapper { display: flex; min-height: 100vh; }

        /* ── SIDEBAR ── */
        #admin-sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(175deg, #0f0f1a 0%, #1a1a2e 40%, #16213e 100%);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform .3s ease;
        }
        .sidebar-brand {
            padding: 1.5rem 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex;
            align-items: center;
            gap: .85rem;
        }
        .sidebar-brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #FFD700, #FFA500);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #3d1f00;
            flex-shrink: 0;
        }
        .sidebar-brand-text { font-size: .95rem; font-weight: 700; color: #fff; line-height: 1.2; }
        .sidebar-brand-sub  { font-size: .72rem; color: rgba(255,255,255,.45); font-weight: 400; }

        .sidebar-section-label {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,.3);
            padding: 1.25rem 1.5rem .4rem;
        }
        .sidebar-nav { list-style: none; padding: 0 .75rem; margin: 0; }
        .sidebar-nav li { margin-bottom: .3rem; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .72rem 1.1rem;
            color: rgba(255,255,255,.65);
            text-decoration: none;
            font-size: .875rem;
            font-weight: 500;
            border-radius: 10px;
            border-left: 3px solid transparent;
            transition: all .2s ease;
        }
        .sidebar-nav a:hover {
            color: #fff;
            background: rgba(255,255,255,.07);
            border-left-color: rgba(255,215,0,.5);
        }
        .sidebar-nav a.active {
            color: #FFD700;
            background: rgba(255,215,0,.10);
            border-left-color: #FFD700;
            font-weight: 600;
        }
        .sidebar-nav a svg { flex-shrink: 0; opacity: .8; }
        .sidebar-nav a.active svg { opacity: 1; }

        .sidebar-footer {
            margin-top: auto;
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .825rem;
            color: rgba(255,255,255,.45);
            text-decoration: none;
            transition: color .2s;
        }
        .sidebar-footer a:hover { color: #ff6b6b; }

        /* ── CONTENT ── */
        #admin-content {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── TOP BAR ── */
        .admin-topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: .875rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .topbar-title { font-size: 1rem; font-weight: 700; color: #1a1a2e; margin: 0; }
        .topbar-subtitle { font-size: .78rem; color: #9ca3af; margin: 0; }
        .topbar-user {
            display: flex;
            align-items: center;
            gap: .85rem;
        }
        .topbar-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FFD700, #FFA500);
            display: flex; align-items: center; justify-content: center;
            color: #3d1f00;
            font-weight: 700;
            font-size: .875rem;
        }
        .topbar-user-info { text-align: right; }
        .topbar-user-name { font-size: .875rem; font-weight: 600; color: #1a1a2e; margin: 0; }
        .topbar-user-role { font-size: .72rem; color: #9ca3af; margin: 0; }

        /* ── STATS CARDS ── */
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .stat-icon {
            width: 56px; height: 56px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-icon.gold   { background: linear-gradient(135deg, #FFF3CD, #FFE082); color: #c8890a; }
        .stat-icon.blue   { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1d4ed8; }
        .stat-icon.green  { background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #15803d; }
        .stat-icon.purple { background: linear-gradient(135deg, #f3e8ff, #e9d5ff); color: #7c3aed; }
        .stat-value { font-size: 1.9rem; font-weight: 800; color: #1a1a2e; line-height: 1; margin-bottom: .2rem; }
        .stat-label { font-size: .8rem; color: #6b7280; font-weight: 500; }

        /* ── ADMIN CARD (TABLE WRAPPER) ── */
        .admin-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
            overflow: hidden;
        }
        .admin-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0;
            display: flex;
            align-items: center;
            gap: .6rem;
        }
        .admin-card-title svg { color: #c8890a; }

        /* Add Button */
        .btn-admin-add {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .55rem 1.1rem;
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: #3d1f00;
            border: none;
            border-radius: 8px;
            font-size: .825rem;
            font-weight: 700;
            text-decoration: none;
            transition: all .2s;
            cursor: pointer;
        }
        .btn-admin-add:hover {
            background: linear-gradient(135deg, #FFA500, #e6900a);
            color: #3d1f00;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255,165,0,.35);
        }

        /* Table */
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table thead tr {
            background: #f8f9fa;
            border-bottom: 1px solid #e5e7eb;
        }
        .admin-table thead th {
            padding: .85rem 1.25rem;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6b7280;
        }
        .admin-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: background .15s;
        }
        .admin-table tbody tr:last-child { border-bottom: none; }
        .admin-table tbody tr:hover { background: #fafafa; }
        .admin-table td {
            padding: .9rem 1.25rem;
            font-size: .875rem;
            color: #374151;
            vertical-align: middle;
        }

        /* Thumbnail */
        .tbl-thumb {
            width: 52px; height: 38px;
            object-fit: cover;
            border-radius: 7px;
            flex-shrink: 0;
        }
        .tbl-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f3f4f6;
            flex-shrink: 0;
        }
        .tbl-name { font-weight: 600; color: #1a1a2e; }
        .tbl-sub  { font-size: .78rem; color: #6b7280; margin-top: .15rem; }

        /* Status badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-size: .72rem;
            font-weight: 700;
            padding: .25rem .7rem;
            border-radius: 50px;
        }
        .status-badge.published { background: #dcfce7; color: #166534; }
        .status-badge.draft     { background: #f3f4f6; color: #6b7280; }
        .status-badge::before   { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

        /* Action buttons */
        .action-wrap { display: flex; align-items: center; gap: .4rem; }
        .btn-tbl {
            width: 32px; height: 32px;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-tbl-edit   { background: #dbeafe; color: #1d4ed8; }
        .btn-tbl-edit:hover   { background: #1d4ed8; color: #fff; }
        .btn-tbl-delete { background: #fee2e2; color: #dc2626; }
        .btn-tbl-delete:hover { background: #dc2626; color: #fff; }

        /* Category badge */
        .cat-badge {
            display: inline-block;
            font-size: .7rem; font-weight: 700;
            padding: .2rem .65rem;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .cat-badge.pengumuman { background: #fee2e2; color: #991b1b; }
        .cat-badge.berita     { background: #dcfce7; color: #166534; }
        .cat-badge.event      { background: #fef9c3; color: #854d0e; }

        /* Empty state */
        .tbl-empty {
            text-align: center;
            padding: 3rem;
            color: #9ca3af;
        }

        /* Responsive */
        @media (max-width: 991px) {
            #admin-sidebar { transform: translateX(-260px); }
            #admin-sidebar.open { transform: translateX(0); }
            #admin-content { margin-left: 0; }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">

    <!-- ── SIDEBAR ── -->
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
            <li>
                <a href="admin_dashboard.php" class="active">
                    <i data-lucide="layout-dashboard" style="width:18px;height:18px;"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="#teachers-section">
                    <i data-lucide="users" style="width:18px;height:18px;"></i>
                    Kelola Guru
                </a>
            </li>
            <li>
                <a href="#news-section">
                    <i data-lucide="newspaper" style="width:18px;height:18px;"></i>
                    Kelola Berita
                </a>
            </li>
            <li>
                <a href="#messages-section" style="position:relative;">
                    <i data-lucide="mail" style="width:18px;height:18px;"></i>
                    Pesan Masuk
                    <?php if ($unread_messages > 0): ?>
                    <span style="margin-left:auto;background:#ef4444;color:#fff;font-size:.65rem;font-weight:700;padding:.15rem .5rem;border-radius:50px;"><?php echo $unread_messages; ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Konten</div>
        <ul class="sidebar-nav">
            <li>
                <a href="teacher_form.php">
                    <i data-lucide="user-plus" style="width:18px;height:18px;"></i>
                    Tambah Guru
                </a>
            </li>
            <li>
                <a href="announcement_form.php">
                    <i data-lucide="file-plus" style="width:18px;height:18px;"></i>
                    Tambah Berita
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">System</div>
        <ul class="sidebar-nav">
            <li>
                <a href="index.php" target="_blank">
                    <i data-lucide="external-link" style="width:18px;height:18px;"></i>
                    Lihat Website
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <a href="logout.php">
                <i data-lucide="log-out" style="width:16px;height:16px;"></i>
                Logout
            </a>
        </div>
    </nav>

    <!-- ── CONTENT ── -->
    <div id="admin-content">

        <!-- Top Bar -->
        <div class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none" id="sidebarToggle">
                    <i data-lucide="menu" style="width:18px;height:18px;"></i>
                </button>
                <div>
                    <p class="topbar-title">Dashboard</p>
                    <p class="topbar-subtitle"><?php
                        $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                        echo $hari[date('w')] . ', ' . date('d F Y');
                    ?></p>
                </div>
            </div>
            <div class="topbar-user">
                <div class="topbar-user-info d-none d-md-block">
                    <p class="topbar-user-name">Administrator</p>
                    <p class="topbar-user-role">Super Admin</p>
                </div>
                <div class="topbar-avatar">A</div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-4" style="flex:1;">

            <!-- Notifikasi reply -->
            <?php if ($success_msg): ?>
            <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:.9rem 1.25rem;border-radius:10px;margin-bottom:1.25rem;display:flex;align-items:center;gap:.6rem;font-weight:600;">
                <i data-lucide="check-circle" style="width:18px;height:18px;"></i> <?php echo $success_msg; ?>
            </div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
            <div style="background:#fef2f2;border:1px solid #fca5a5;color:#dc2626;padding:.9rem 1.25rem;border-radius:10px;margin-bottom:1.25rem;display:flex;align-items:center;gap:.6rem;font-weight:600;font-size:.875rem;">
                <i data-lucide="alert-circle" style="width:18px;height:18px;"></i> <?php echo htmlspecialchars($error_msg); ?>
            </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon gold">
                            <i data-lucide="users" style="width:26px;height:26px;"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?php echo $total_teachers; ?></div>
                            <div class="stat-label">Total Guru & Staf</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i data-lucide="file-check" style="width:26px;height:26px;"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?php echo $total_news; ?></div>
                            <div class="stat-label">Berita Published</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i data-lucide="file-clock" style="width:26px;height:26px;"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?php echo $total_draft; ?></div>
                            <div class="stat-label">Draft Berita</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i data-lucide="newspaper" style="width:26px;height:26px;"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?php echo $total_all_news; ?></div>
                            <div class="stat-label">Total Semua Berita</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:linear-gradient(135deg,#fce7f3,#fbcfe8);color:#be185d;width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i data-lucide="mail" style="width:26px;height:26px;"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?php echo $total_messages; ?></div>
                            <div class="stat-label">Pesan Masuk <?php if($unread_messages>0): ?><span style="font-size:.7rem;background:#ef4444;color:#fff;padding:.1rem .45rem;border-radius:50px;margin-left:.3rem;"><?php echo $unread_messages; ?> baru</span><?php endif; ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teachers Table -->
            <div class="admin-card mb-4" id="teachers-section">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">
                        <i data-lucide="users" style="width:18px;height:18px;"></i>
                        Data Guru & Staf
                    </h5>
                    <a href="teacher_form.php" class="btn-admin-add">
                        <i data-lucide="plus" style="width:15px;height:15px;"></i>
                        Tambah Guru
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Guru</th>
                                <th>Jabatan</th>
                                <th>Bio</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = mysqli_query($koneksi, "SELECT * FROM teachers ORDER BY id ASC");
                            $no = 1;
                            if (mysqli_num_rows($res) > 0):
                                while ($r = mysqli_fetch_assoc($res)):
                            ?>
                            <tr>
                                <td style="color:#9ca3af;font-weight:600;width:50px;"><?php echo $no++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?php echo htmlspecialchars($r['photo']); ?>" class="tbl-avatar" alt="">
                                        <div>
                                            <div class="tbl-name"><?php echo htmlspecialchars($r['name']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span style="font-size:.82rem;color:#374151;"><?php echo htmlspecialchars($r['position']); ?></span></td>
                                <td><span class="tbl-sub"><?php echo htmlspecialchars(substr($r['bio'], 0, 60)); ?>...</span></td>
                                <td>
                                    <div class="action-wrap justify-content-center">
                                        <a href="teacher_form.php?id=<?php echo $r['id']; ?>" class="btn-tbl btn-tbl-edit" title="Edit">
                                            <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                                        </a>
                                        <a href="teacher_process.php?delete=<?php echo $r['id']; ?>" class="btn-tbl btn-tbl-delete" title="Hapus"
                                           onclick="return confirm('Yakin ingin menghapus guru ini?')">
                                            <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="5" class="tbl-empty">
                                <i data-lucide="inbox" style="width:40px;height:40px;color:#d1d5db;display:block;margin:0 auto .75rem;"></i>
                                Belum ada data guru.<br>
                                <a href="teacher_form.php" class="btn-admin-add mt-3" style="display:inline-flex;">Tambah Sekarang</a>
                            </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- News Table -->
            <div class="admin-card mb-4" id="news-section">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">
                        <i data-lucide="newspaper" style="width:18px;height:18px;"></i>
                        Data Berita & Pengumuman
                    </h5>
                    <a href="announcement_form.php" class="btn-admin-add">
                        <i data-lucide="plus" style="width:15px;height:15px;"></i>
                        Tambah Berita
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res2 = mysqli_query($koneksi, "SELECT * FROM announcements ORDER BY created_at DESC");
                            $no2  = 1;
                            if (mysqli_num_rows($res2) > 0):
                                while ($r2 = mysqli_fetch_assoc($res2)):
                                    $cat_map   = ['pengumuman'=>'pengumuman','berita'=>'berita','event'=>'event'];
                                    $cat_label = ['pengumuman'=>'Pengumuman','berita'=>'Berita','event'=>'Event'];
                                    $cat_cls   = $cat_map[$r2['category']] ?? 'berita';
                                    $cat_lbl   = $cat_label[$r2['category']] ?? 'Berita';
                            ?>
                            <tr>
                                <td style="color:#9ca3af;font-weight:600;width:50px;"><?php echo $no2++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?php echo htmlspecialchars($r2['image']); ?>" class="tbl-thumb" alt="">
                                        <div class="tbl-name" style="max-width:280px;line-height:1.4;"><?php echo htmlspecialchars($r2['title']); ?></div>
                                    </div>
                                </td>
                                <td><span class="cat-badge <?php echo $cat_cls; ?>"><?php echo $cat_lbl; ?></span></td>
                                <td style="font-size:.82rem;color:#6b7280;"><?php echo date('d M Y', strtotime($r2['created_at'])); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $r2['is_published'] ? 'published' : 'draft'; ?>">
                                        <?php echo $r2['is_published'] ? 'Published' : 'Draft'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-wrap justify-content-center">
                                        <a href="announcement_form.php?id=<?php echo $r2['id']; ?>" class="btn-tbl btn-tbl-edit" title="Edit">
                                            <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                                        </a>
                                        <a href="announcement_process.php?delete=<?php echo $r2['id']; ?>" class="btn-tbl btn-tbl-delete" title="Hapus"
                                           onclick="return confirm('Yakin ingin menghapus berita ini?')">
                                            <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="6" class="tbl-empty">
                                <i data-lucide="inbox" style="width:40px;height:40px;color:#d1d5db;display:block;margin:0 auto .75rem;"></i>
                                Belum ada berita.<br>
                                <a href="announcement_form.php" class="btn-admin-add mt-3" style="display:inline-flex;">Tambah Sekarang</a>
                            </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Messages Table -->
            <div class="admin-card mb-4" id="messages-section">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">
                        <i data-lucide="mail" style="width:18px;height:18px;"></i>
                        Pesan Masuk
                        <?php if ($unread_messages > 0): ?>
                        <span style="background:#ef4444;color:#fff;font-size:.65rem;font-weight:700;padding:.2rem .6rem;border-radius:50px;"><?php echo $unread_messages; ?> belum dibaca</span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pengirim</th>
                                <th>Subjek</th>
                                <th>Pesan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res3 = mysqli_query($koneksi, "SELECT * FROM contact_messages ORDER BY created_at DESC");
                            $no3  = 1;
                            if (mysqli_num_rows($res3) > 0):
                                while ($msg = mysqli_fetch_assoc($res3)):
                            ?>
                            <tr style="<?php echo !$msg['is_read'] ? 'background:#fffbeb;' : ''; ?>">
                                <td style="color:#9ca3af;font-weight:600;width:50px;"><?php echo $no3++; ?></td>
                                <td>
                                    <div class="tbl-name"><?php echo htmlspecialchars($msg['nama']); ?></div>
                                    <div class="tbl-sub"><?php echo htmlspecialchars($msg['email']); ?></div>
                                </td>
                                <td style="font-weight:600;color:#1a1a2e;font-size:.875rem;"><?php echo htmlspecialchars($msg['subjek']); ?></td>
                                <td style="max-width:260px;">
                                    <span class="tbl-sub"><?php echo htmlspecialchars(substr($msg['pesan'], 0, 80)); ?>...</span>
                                </td>
                                <td style="font-size:.82rem;color:#6b7280;white-space:nowrap;"><?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?></td>
                                <td>
                                    <?php if (!$msg['is_read']): ?>
                                    <span class="status-badge" style="background:#fef9c3;color:#854d0e;">⬤ Baru</span>
                                    <?php else: ?>
                                    <span class="status-badge" style="background:#f3f4f6;color:#6b7280;">Dibaca</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-wrap justify-content-center">
                                        <?php if (!$msg['is_read']): ?>
                                        <a href="admin_dashboard.php?read_msg=<?php echo $msg['id']; ?>" class="btn-tbl" style="background:#dcfce7;color:#15803d;" title="Tandai Sudah Dibaca">
                                            <i data-lucide="check" style="width:14px;height:14px;"></i>
                                        </a>
                                        <?php endif; ?>
                                        <a href="#" class="btn-tbl" style="background:#dbeafe;color:#1d4ed8;" title="Balas"
                                           onclick="bukaBalasan(<?php echo $msg['id']; ?>, '<?php echo htmlspecialchars($msg['email']); ?>', '<?php echo htmlspecialchars($msg['subjek']); ?>')">
                                            <i data-lucide="reply" style="width:14px;height:14px;"></i>
                                        </a>
                                        <a href="admin_dashboard.php?delete_msg=<?php echo $msg['id']; ?>" class="btn-tbl btn-tbl-delete" title="Hapus"
                                           onclick="return confirm('Yakin ingin menghapus pesan ini?')">
                                            <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="7" class="tbl-empty">
                                <i data-lucide="inbox" style="width:40px;height:40px;color:#d1d5db;display:block;margin:0 auto .75rem;"></i>
                                Belum ada pesan masuk.
                            </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /p-4 -->
    </div><!-- /admin-content -->
</div><!-- /admin-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('admin-sidebar').classList.toggle('open');
    });
</script>
<!-- Modal Balas -->
<div id="modalBalas" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;">
    <div style="background:#fff;max-width:500px;margin:80px auto;border-radius:12px;padding:24px;">
        <h5 style="margin-bottom:16px;">Balas Pesan</h5>
        <form method="POST">
            <input type="hidden" name="msg_id" id="modal_id">
            <input type="hidden" name="reply_email" id="modal_email">
            <input type="hidden" name="reply_subjek" id="modal_subjek">
            <div style="margin-bottom:12px;">
                <label>Kepada:</label>
                <input type="text" id="modal_email_show" readonly style="width:100%;padding:8px;border:1px solid #e5e7eb;border-radius:6px;">
            </div>
            <div style="margin-bottom:12px;">
                <label>Isi Balasan:</label>
                <textarea name="reply_isi" rows="5" required style="width:100%;padding:8px;border:1px solid #e5e7eb;border-radius:6px;"></textarea>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('modalBalas').style.display='none'" style="padding:8px 16px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;">Batal</button>
                <button type="submit" name="reply_msg" style="padding:8px 16px;background:#1d4ed8;color:#fff;border:none;border-radius:6px;">Kirim</button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaBalasan(id, email, subjek) {
    document.getElementById('modal_id').value = id;
    document.getElementById('modal_email').value = email;
    document.getElementById('modal_subjek').value = subjek;
    document.getElementById('modal_email_show').value = email;
    document.getElementById('modalBalas').style.display = 'block';
}
</script>
</body>
</html>
