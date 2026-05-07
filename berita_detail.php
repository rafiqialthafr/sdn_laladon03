<?php
include 'koneksi.php';
include 'header.php';

// Ambil ID dengan aman (hindari SQL Injection)
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Gunakan prepared statement untuk keamanan
$stmt = mysqli_prepare($koneksi, "SELECT * FROM announcements WHERE id = ? AND is_published = 1");
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
    'pengumuman' => ['label' => '📢 Pengumuman', 'bg' => '#fef3c7', 'color' => '#92400e'],
    'berita'     => ['label' => '📰 Berita',     'bg' => '#dbeafe', 'color' => '#1e40af'],
    'event'      => ['label' => '🎉 Event',       'bg' => '#dcfce7', 'color' => '#166534'],
];
$cat = $catMap[$row['category']] ?? $catMap['berita'];
?>

<!-- Reading Progress Bar -->
<div id="rd-bar" style="position:fixed;top:0;left:0;height:3px;background:#c8890a;width:0;z-index:9999;transition:width .1s linear;"></div>

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
                    <img class="article-hero-img"
                         src="<?php echo htmlspecialchars($row['image']); ?>"
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
                        $lr = mysqli_prepare($koneksi, "SELECT * FROM announcements WHERE is_published = 1 AND id != ? ORDER BY created_at DESC LIMIT 5");
                        mysqli_stmt_bind_param($lr, 'i', $id);
                        mysqli_stmt_execute($lr);
                        $lr_result = mysqli_stmt_get_result($lr);
                        while ($l = mysqli_fetch_assoc($lr_result)):
                        ?>
                            <a href="berita_detail.php?id=<?php echo (int)$l['id']; ?>" class="sb-news-item">
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
                            ['slug' => 'pengumuman', 'label' => '📢 Pengumuman'],
                            ['slug' => 'berita',     'label' => '📰 Berita'],
                            ['slug' => 'event',      'label' => '🎉 Event'],
                        ];
                        foreach ($cats as $c):
                            $cnt_stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM announcements WHERE is_published = 1 AND category = ?");
                            mysqli_stmt_bind_param($cnt_stmt, 's', $c['slug']);
                            mysqli_stmt_execute($cnt_stmt);
                            $cnt = mysqli_fetch_assoc(mysqli_stmt_get_result($cnt_stmt))['total'];
                        ?>
                            <a href="berita.php?category=<?php echo urlencode($c['slug']); ?>" class="sb-cat-item">
                                <?php echo $c['label']; ?>
                                <span><?php echo (int)$cnt; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                </div><!-- sticky -->
            </div><!-- col-lg-4 -->

        </div><!-- row -->
    </div><!-- container -->
</section>

<style>
/* ── Article ── */
.article-container {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
}
.article-hero-img {
    width: 100%;
    max-height: 420px;
    object-fit: cover;
    display: block;
}
.article-inner {
    padding: 2rem 2.5rem 2.5rem;
}
.art-cat-badge {
    display: inline-block;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    padding: .3rem .75rem;
    border-radius: 50px;
    margin-bottom: 1rem;
}
.art-title {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.3;
    margin-bottom: .75rem;
}
.art-meta-bar {
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
    margin-bottom: 1.75rem;
}
.art-meta-item {
    display: flex;
    align-items: center;
    gap: .3rem;
    font-size: .8rem;
    color: #64748b;
}
.art-content {
    font-size: .95rem;
    line-height: 1.85;
    color: #334155;
}
.art-hr {
    border: none;
    border-top: 1px solid #f1f5f9;
    margin: 1.75rem 0;
}
.art-back-link {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-size: .85rem;
    font-weight: 600;
    color: #c8890a;
    text-decoration: none;
    transition: gap .2s;
}
.art-back-link:hover { gap: .6rem; color: #a06c07; }

/* ── Sidebar Cards ── */
.sb-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    margin-bottom: 1.25rem;
}
.sb-card-head {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .9rem 1.1rem;
    font-size: .82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #c8890a;
    border-bottom: 1px solid #f8fafc;
}
.sb-card-head svg,
.sb-card-head i[data-lucide] { color: #c8890a; flex-shrink: 0; }

/* Sidebar news item */
.sb-news-item {
    display: flex;
    gap: .75rem;
    padding: .85rem 1.1rem;
    text-decoration: none;
    color: inherit;
    border-bottom: 1px solid #f8fafc;
    transition: background .15s ease;
}
.sb-news-item:last-child { border-bottom: none; }
.sb-news-item:hover { background: #fffbeb; }
.sb-news-item img {
    width: 58px;
    height: 44px;
    object-fit: cover;
    border-radius: 7px;
    flex-shrink: 0;
    background: #f1f5f9;
}
.sb-news-title {
    font-size: .8rem;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.35;
    margin: 0 0 .25rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.sb-news-date {
    font-size: .72rem;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: .25rem;
}

/* Sidebar category item */
.sb-cat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .7rem 1.1rem;
    text-decoration: none;
    font-size: .85rem;
    font-weight: 500;
    color: #374151;
    border-bottom: 1px solid #f8fafc;
    transition: all .18s ease;
}
.sb-cat-item:last-child { border-bottom: none; }
.sb-cat-item:hover { background: #fffbeb; color: #c8890a; padding-left: 1.4rem; }
.sb-cat-item span {
    font-size: .72rem;
    font-weight: 700;
    background: #f1f5f9;
    color: #64748b;
    border-radius: 50px;
    padding: .1rem .55rem;
}
</style>

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