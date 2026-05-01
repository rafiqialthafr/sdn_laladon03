<?php
// Determine current page for active state
$current_page = basename($_SERVER['PHP_SELF']);

// Count unread messages for badge
if (!isset($unread_messages)) {
    include_once 'koneksi.php';
    $res_unread = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM contact_messages WHERE is_read=0");
    $unread_messages = mysqli_fetch_assoc($res_unread)['total'] ?? 0;
}
?>
<!-- Overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

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

    <div class="sidebar-section-label">Overview</div>
    <ul class="sidebar-nav">
        <li>
            <a href="admin_dashboard.php" class="<?php echo $current_page === 'admin_dashboard.php' ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard" style="width:17px;height:17px;"></i>
                Dashboard
            </a>
        </li>
    </ul>

    <div class="sidebar-section-label">Manajemen</div>
    <ul class="sidebar-nav">
        <li>
            <a href="admin_guru.php" class="<?php echo $current_page === 'admin_guru.php' ? 'active' : ''; ?>">
                <i data-lucide="users" style="width:17px;height:17px;"></i>
                Data Guru &amp; Staf
            </a>
        </li>
        <li>
            <a href="admin_berita.php" class="<?php echo $current_page === 'admin_berita.php' ? 'active' : ''; ?>">
                <i data-lucide="newspaper" style="width:17px;height:17px;"></i>
                Berita &amp; Pengumuman
            </a>
        </li>
        <li>
            <a href="admin_galeri.php" class="<?php echo $current_page === 'admin_galeri.php' ? 'active' : ''; ?>">
                <i data-lucide="image" style="width:17px;height:17px;"></i>
                Galeri Foto
            </a>
        </li>
        <li>
            <a href="admin_pesan.php" class="<?php echo $current_page === 'admin_pesan.php' ? 'active' : ''; ?>">
                <i data-lucide="mail" style="width:17px;height:17px;"></i>
                Pesan Masuk
                <?php if ($unread_messages > 0): ?>
                <span class="sidebar-badge"><?php echo $unread_messages; ?></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>


    <div class="sidebar-section-label">System</div>
    <ul class="sidebar-nav">
        <li>
            <a href="index.php" target="_blank">
                <i data-lucide="external-link" style="width:17px;height:17px;"></i>
                Lihat Website
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="logout.php" onclick="return confirm('Yakin ingin logout?')">
            <i data-lucide="log-out" style="width:15px;height:15px;"></i>
            Logout
        </a>
    </div>
</nav>
