<?php
// Determine current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<nav id="admin-sidebar" class="admin-sidebar">
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
            <a href="admin_dashboard.php"
                class="<?php echo $current_page === 'admin_dashboard.php' ? 'active' : ''; ?>">
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
            <a href="admin_fasilitas.php"
                class="<?php echo $current_page === 'admin_fasilitas.php' ? 'active' : ''; ?>">
                <i data-lucide="building-2" style="width:17px;height:17px;"></i>
                Fasilitas
            </a>
        </li>
        <li>
            <a href="admin_ekskul.php"
                class="<?php echo $current_page === 'admin_ekskul.php' ? 'active' : ''; ?>">
                <i data-lucide="compass" style="width:17px;height:17px;"></i>
                Kelola Ekskul
            </a>
        </li>

        <li>
            <a href="admin_kepsek.php" class="<?php echo $current_page === 'admin_kepsek.php' ? 'active' : ''; ?>">
                <i data-lucide="user-round-cog" style="width:17px;height:17px;"></i>
                Profil Kepsek
            </a>
        </li>
        <li>
            <a href="admin_ppdb.php" class="<?php echo $current_page === 'admin_ppdb.php' ? 'active' : ''; ?>">
                <i data-lucide="clipboard-list" style="width:17px;height:17px;"></i>
                Data PPDB
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

<!-- Auto-Hide Alerts Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-admin');
    
    // Hapus parameter URL agar saat di-refresh alert tidak muncul lagi
    if (alerts.length > 0 && window.history.replaceState) {
        const url = new URL(window.location);
        url.searchParams.delete('success');
        url.searchParams.delete('error');
        url.searchParams.delete('msg'); // jaga-jaga kalau ada parameter msg
        window.history.replaceState(null, null, url.toString());
    }

    alerts.forEach(function(alert) {
        // Biarkan alert tampil selama 3 detik, lalu mulai animasi fade out
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            
            // Hapus dari DOM setelah animasi selesai
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        }, 3000);
    });
});
</script>
