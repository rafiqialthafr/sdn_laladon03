<?php
include 'koneksi.php';
include 'header.php';

// Get category filter if set
$category_filter = isset($_GET['category']) ? mysqli_real_escape_string($koneksi, $_GET['category']) : '';

// Build query
if ($category_filter) {
    $query = "SELECT * FROM announcements WHERE is_published=1 AND category='$category_filter' ORDER BY created_at DESC";
} else {
    $query = "SELECT * FROM announcements WHERE is_published=1 ORDER BY created_at DESC";
}
$result = mysqli_query($koneksi, $query);

// Category helper
function getCatClass($cat) {
    return match($cat) {
        'pengumuman' => 'pengumuman',
        'berita'     => 'berita',
        'event'      => 'event',
        default      => 'berita'
    };
}
function getCatLabel($cat) {
    return match($cat) {
        'pengumuman' => 'Pengumuman',
        'berita'     => 'Berita',
        'event'      => 'Event',
        default      => 'Berita'
    };
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row py-5">
            <div class="col-12 text-center">
                <h1 class="page-header-title">Berita & Pengumuman</h1>
                <p class="page-header-subtitle">Informasi terbaru dari SDN Laladon 03</p>
            </div>
        </div>
    </div>
</section>

<!-- Filter Bar -->
<section class="py-4 bg-white">
    <div class="container">
        <div class="text-center">
            <div class="news-filter-bar">
                <a href="berita.php" class="news-filter-btn <?php echo !$category_filter ? 'active' : ''; ?>">
                    <i data-lucide="layout-grid" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:4px;"></i> Semua
                </a>
                <a href="berita.php?category=pengumuman" class="news-filter-btn <?php echo $category_filter == 'pengumuman' ? 'active' : ''; ?>">
                    📢 Pengumuman
                </a>
                <a href="berita.php?category=berita" class="news-filter-btn <?php echo $category_filter == 'berita' ? 'active' : ''; ?>">
                    📰 Berita
                </a>
                <a href="berita.php?category=event" class="news-filter-btn <?php echo $category_filter == 'event' ? 'active' : ''; ?>">
                    🎉 Event
                </a>
            </div>
        </div>
    </div>
</section>

<!-- News Grid -->
<section class="py-5" style="background-color:#f8f9fc;">
    <div class="container">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="row g-4">
                <?php
                $delay = 1;
                while ($row = mysqli_fetch_assoc($result)):
                    $cat_class = getCatClass($row['category']);
                    $cat_label = getCatLabel($row['category']);
                ?>
                <div class="col-md-6 col-lg-4 fade-delay-<?php echo min($delay, 4); ?>">
                    <div class="news-card-modern">
                        <div class="img-wrap">
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            <span class="news-cat-badge <?php echo $cat_class; ?>"><?php echo $cat_label; ?></span>
                        </div>
                        <div class="news-card-content">
                            <div class="news-card-date">
                                <i data-lucide="calendar" style="width:13px;height:13px;"></i>
                                <?php echo date('d F Y', strtotime($row['created_at'])); ?>
                            </div>
                            <h5 class="news-card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="news-card-excerpt"><?php echo htmlspecialchars(substr(strip_tags($row['content']), 0, 120)); ?>...</p>
                            <a href="berita_detail.php?id=<?php echo $row['id']; ?>" class="news-read-more">
                                Baca Selengkapnya
                                <i data-lucide="arrow-right" style="width:15px;height:15px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php
                    $delay++;
                endwhile;
                ?>
            </div>
        <?php else: ?>
            <div class="news-empty-state">
                <div class="empty-icon">
                    <i data-lucide="inbox" style="width:36px;height:36px;color:#d1d5db;"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color:#6b7280;">Belum Ada Berita</h5>
                <p class="text-muted">Saat ini belum ada berita atau pengumuman yang tersedia.</p>
                <?php if ($category_filter): ?>
                    <a href="berita.php" class="news-filter-btn active mt-3" style="display:inline-flex;align-items:center;gap:0.4rem;">
                        Lihat Semua Berita
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
