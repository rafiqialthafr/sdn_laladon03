<?php
include 'koneksi.php';
include 'header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT * FROM announcements WHERE id='$id' AND is_published=1";
$result = mysqli_query($koneksi, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "<script>window.location.href='berita.php';</script>";
    exit;
}

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

<!-- Breadcrumb -->
<section class="py-3 bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb" class="fade-in-up">
            <ol class="breadcrumb mb-0" style="font-size:0.875rem;">
                <li class="breadcrumb-item"><a href="index.php" style="color:#c8890a;text-decoration:none;">Beranda</a></li>
                <li class="breadcrumb-item"><a href="berita.php" style="color:#c8890a;text-decoration:none;">Berita & Pengumuman</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page" style="max-width:300px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
                    <?php echo htmlspecialchars($row['title']); ?>
                </li>
            </ol>
        </nav>
    </div>
</section>

<!-- Article + Sidebar -->
<section class="py-5" style="background-color:#f8f9fc;">
    <div class="container">
        <div class="row g-5">

            <!-- Article -->
            <div class="col-lg-8">
                <article style="background:#fff;border-radius:20px;padding:2.5rem;box-shadow:0 4px 20px rgba(0,0,0,0.07);">
                    <!-- Meta -->
                    <div class="article-meta">
                        <span class="news-cat-badge <?php echo getCatClass($row['category']); ?>" style="position:static;font-size:0.75rem;">
                            <?php echo getCatLabel($row['category']); ?>
                        </span>
                        <span class="article-meta-item">
                            <i data-lucide="calendar" style="width:14px;height:14px;"></i>
                            <?php echo date('d F Y', strtotime($row['created_at'])); ?>
                        </span>
                        <span class="article-meta-item">
                            <i data-lucide="clock" style="width:14px;height:14px;"></i>
                            <?php echo date('H:i', strtotime($row['created_at'])); ?> WIB
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="article-title mb-4"><?php echo htmlspecialchars($row['title']); ?></h1>

                    <!-- Hero Image -->
                    <img src="<?php echo htmlspecialchars($row['image']); ?>"
                         alt="<?php echo htmlspecialchars($row['title']); ?>"
                         class="article-hero">

                    <!-- Body -->
                    <div class="article-body">
                        <?php echo nl2br(htmlspecialchars($row['content'])); ?>
                    </div>

                    <!-- Footer Actions -->
                    <div class="mt-5 pt-4 d-flex align-items-center justify-content-between flex-wrap gap-3"
                         style="border-top:1px solid #f3f4f6;">
                        <a href="berita.php" class="article-back-btn">
                            <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
                            Kembali ke Berita
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Bagikan:</span>
                            <a href="#" title="Bagikan ke WhatsApp"
                               style="width:36px;height:36px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;color:#15803d;text-decoration:none;">
                                <i data-lucide="message-circle" style="width:16px;height:16px;"></i>
                            </a>
                            <a href="#" title="Salin tautan"
                               style="width:36px;height:36px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#374151;text-decoration:none;"
                               onclick="navigator.clipboard.writeText(window.location.href);return false;">
                                <i data-lucide="link" style="width:16px;height:16px;"></i>
                            </a>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">

                <!-- Berita Terbaru -->
                <div class="news-sidebar-card">
                    <div class="news-sidebar-header">
                        <i data-lucide="zap" style="width:16px;height:16px;"></i>
                        Berita Terbaru
                    </div>
                    <?php
                    $latest_query = "SELECT * FROM announcements WHERE is_published=1 ORDER BY created_at DESC LIMIT 5";
                    $latest_result = mysqli_query($koneksi, $latest_query);
                    while ($latest = mysqli_fetch_assoc($latest_result)):
                    ?>
                    <a href="berita_detail.php?id=<?php echo $latest['id']; ?>" class="sidebar-news-item">
                        <img src="<?php echo htmlspecialchars($latest['image']); ?>" alt="<?php echo htmlspecialchars($latest['title']); ?>">
                        <div>
                            <h6><?php echo htmlspecialchars($latest['title']); ?></h6>
                            <small><i data-lucide="calendar" style="width:11px;height:11px;display:inline;"></i> <?php echo date('d M Y', strtotime($latest['created_at'])); ?></small>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>

                <!-- Kategori -->
                <div class="news-sidebar-card">
                    <div class="news-sidebar-header">
                        <i data-lucide="tag" style="width:16px;height:16px;"></i>
                        Kategori
                    </div>
                    <?php
                    $cats = [
                        ['slug' => 'pengumuman', 'label' => '📢 Pengumuman'],
                        ['slug' => 'berita',     'label' => '📰 Berita'],
                        ['slug' => 'event',      'label' => '🎉 Event'],
                    ];
                    foreach ($cats as $cat):
                        $cnt_q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM announcements WHERE is_published=1 AND category='{$cat['slug']}'");
                        $cnt = mysqli_fetch_assoc($cnt_q)['total'];
                    ?>
                    <a href="berita.php?category=<?php echo $cat['slug']; ?>" class="sidebar-cat-btn">
                        <?php echo $cat['label']; ?>
                        <span><?php echo $cnt; ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
