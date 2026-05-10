<?php
include 'koneksi.php';
include 'header.php';

// Ambil ID dengan aman (hindari SQL Injection)
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Gunakan prepared statement untuk keamanan
$stmt = mysqli_prepare($koneksi, "SELECT * FROM berita WHERE id = ? AND is_published = 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "<script>window.location.href='berita.php';</script>";
    exit;
}

// Definisi kategori lengkap (termasuk bg & color untuk badge)
$catMap = [
    'pengumuman' => ['label' => 'Pengumuman', 'bg' => '#fef3c7', 'color' => '#92400e'],
    'berita' => ['label' => 'Berita', 'bg' => '#dbeafe', 'color' => '#1e40af'],
    'event' => ['label' => 'Event', 'bg' => '#dcfce7', 'color' => '#166534'],
];
$cat = $catMap[$row['category']] ?? $catMap['berita'];
?>

<!-- Reading Progress Bar -->
<div id="rd-bar"
    style="position:fixed;top:0;left:0;height:3px;background:#c8890a;width:0;z-index:9999;transition:width .1s linear;">
</div>

<!-- Breadcrumb -->
<section class="py-3 bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size:0.875rem;">
                <li class="breadcrumb-item">
                    <a href="index.php" style="color:#c8890a;text-decoration:none;">Beranda</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="berita.php" style="color:#c8890a;text-decoration:none;">Berita &amp; Pengumuman</a>
                </li>
                <li class="breadcrumb-item active text-muted" aria-current="page"
                    style="max-width:300px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
                    <?php echo htmlspecialchars($row['title']); ?>
                </li>
            </ol>
        </nav>
    </div>
</section>

<!-- Artikel + Sidebar -->
<section class="py-5" style="background-color:#f8f9fc;">
    <div class="container">
        <div class="row g-4">

            <!-- ── Artikel ── -->
            <div class="col-lg-8">
                <article class="article-container">

                    <!-- Hero Image -->
                    <img class="article-hero-img" src="<?php echo htmlspecialchars($row['image']); ?>"
                        alt="<?php echo htmlspecialchars($row['title']); ?>">

                    <div class="article-inner">

                        <!-- Badge Kategori -->
                        <span class="art-cat-badge"
                            style="background:<?php echo $cat['bg']; ?>;color:<?php echo $cat['color']; ?>;">
                            <?php echo $cat['label']; ?>
                        </span>

                        <!-- Judul -->
                        <h1 class="art-title"><?php echo htmlspecialchars($row['title']); ?></h1>

                        <!-- Meta -->
                        <div class="art-meta-bar">
                            <span class="art-meta-item">
                                <i data-lucide="calendar" style="width:13px;height:13px;"></i>
                                <?php echo date('d F Y', strtotime($row['created_at'])); ?>
                            </span>
                            <span class="art-meta-item">
                                <i data-lucide="clock" style="width:13px;height:13px;"></i>
                                <?php echo date('H:i', strtotime($row['created_at'])); ?> WIB
                            </span>
                            <span class="art-meta-item">
                                <i data-lucide="school" style="width:13px;height:13px;"></i>
                                SDN Laladon 03
                            </span>
                        </div>

                        <!-- Konten Artikel -->
                        <div class="art-content">
                            <?php echo nl2br(htmlspecialchars($row['content'])); ?>
                        </div>

                        <hr class="art-hr">

                        <!-- Share & Kembali -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-3">
                            <a href="berita.php" class="art-back-link">
                                <i data-lucide="arrow-left" style="width:14px;height:14px;"></i>
                                Kembali ke Berita
                            </a>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted small">Bagikan:</span>
                                <a href="https://wa.me/?text=<?php echo urlencode($row['title'] . ' ' . (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : '')); ?>"
                                    target="_blank" rel="noopener" title="Bagikan ke WhatsApp"
                                    style="width:36px;height:36px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;color:#15803d;text-decoration:none;">
                                    <i data-lucide="message-circle" style="width:16px;height:16px;"></i>
                                </a>
                                <a href="#" title="Salin tautan"
                                    style="width:36px;height:36px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#374151;text-decoration:none;"
                                    onclick="navigator.clipboard.writeText(window.location.href);this.title='Tersalin!';return false;">
                                    <i data-lucide="link" style="width:16px;height:16px;"></i>
                                </a>
                            </div>
                        </div>

                    </div><!-- .article-inner -->
                </article>
            </div><!-- col-lg-8 -->

            <!-- ── Sidebar ── -->
            <div class="col-lg-4">
                <div style="position:sticky;top:90px;">

                    <!-- Berita Terbaru -->
                    <div class="sb-card">
                        <div class="sb-card-head">
                            <i data-lucide="zap" style="width:13px;height:13px;"></i>
                            Berita Terbaru
                        </div>
                        <?php
                        $lr = mysqli_prepare($koneksi, "SELECT * FROM berita WHERE is_published = 1 AND id != ? ORDER BY created_at DESC LIMIT 5");
                        mysqli_stmt_bind_param($lr, 'i', $id);
                        mysqli_stmt_execute($lr);
                        $lr_result = mysqli_stmt_get_result($lr);
                        while ($l = mysqli_fetch_assoc($lr_result)):
                            ?>
                            <a href="berita_detail.php?id=<?php echo (int) $l['id']; ?>" class="sb-news-item">
                                <img src="<?php echo htmlspecialchars($l['image']); ?>"
                                    alt="<?php echo htmlspecialchars($l['title']); ?>">
                                <div>
                                    <p class="sb-news-title"><?php echo htmlspecialchars($l['title']); ?></p>
                                    <span class="sb-news-date">
                                        <i data-lucide="calendar" style="width:10px;height:10px;"></i>
                                        <?php echo date('d M Y', strtotime($l['created_at'])); ?>
                                    </span>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    </div>

                    <!-- Kategori -->
                    <div class="sb-card">
                        <div class="sb-card-head">
                            <i data-lucide="tag" style="width:13px;height:13px;"></i>
                            Kategori
                        </div>
                        <?php
                        $cats = [
                            ['slug' => 'pengumuman', 'label' => 'Pengumuman'],
                            ['slug' => 'berita', 'label' => 'Berita'],
                            ['slug' => 'event', 'label' => 'Event'],
                        ];
                        foreach ($cats as $c):
                            $cnt_stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM berita WHERE is_published = 1 AND category = ?");
                            mysqli_stmt_bind_param($cnt_stmt, 's', $c['slug']);
                            mysqli_stmt_execute($cnt_stmt);
                            $cnt = mysqli_fetch_assoc(mysqli_stmt_get_result($cnt_stmt))['total'];
                            ?>
                            <a href="berita.php?category=<?php echo urlencode($c['slug']); ?>" class="sb-cat-item">
                                <?php echo $c['label']; ?>
                                <span><?php echo (int) $cnt; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                </div><!-- sticky -->
            </div><!-- col-lg-4 -->

        </div><!-- row -->
    </div><!-- container -->
</section>
<script>
    (function () {
        var bar = document.getElementById('rd-bar');
        if (!bar) return;
        window.addEventListener('scroll', function () {
            var h = document.documentElement.scrollHeight - window.innerHeight;
            bar.style.width = (h > 0 ? Math.min(window.scrollY / h * 100, 100) : 0) + '%';
        }, { passive: true });
    })();
</script>

<?php include 'footer.php'; ?>