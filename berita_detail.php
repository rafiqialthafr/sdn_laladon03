<?php
include 'koneksi.php';
include 'header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$query = "SELECT * FROM announcements WHERE id='$id' AND is_published=1";
$result = mysqli_query($koneksi, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "<script>window.location.href='berita.php';</script>";
    exit;
}

function getCatClass($cat)
{
    return match ($cat) {
        'pengumuman' => 'pengumuman',
        'berita' => 'berita',
        'event' => 'event',
        default => 'berita'
    };
}
function getCatLabel($cat)
{
    return match ($cat) {
        'pengumuman' => 'Pengumuman',
        'berita' => 'Berita',
        'event' => 'Event',
        default => 'Berita'
    };
}
?>

<!-- Breadcrumb -->
<section class="py-3 bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb" class="fade-in-up">
            <ol class="breadcrumb mb-0" style="font-size:0.875rem;">
                <li class="breadcrumb-item"><a href="index.php" style="color:#c8890a;text-decoration:none;">Beranda</a>
                </li>
                <li class="breadcrumb-item"><a href="berita.php" style="color:#c8890a;text-decoration:none;">Berita &
                        Pengumuman</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page"
                    style="max-width:300px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
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
                <article
                    style="background:#fff;border-radius:20px;padding:2.5rem;box-shadow:0 4px 20px rgba(0,0,0,0.07);">
                    <!-- Meta -->
                    <div class="article-meta">
                        <span class="news-cat-badge <?php echo getCatClass($row['category']); ?>"
                            style="position:static;font-size:0.75rem;">
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
                        alt="<?php echo htmlspecialchars($row['title']); ?>" class="article-hero">

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
                            <img src="<?php echo htmlspecialchars($latest['image']); ?>"
                                alt="<?php echo htmlspecialchars($latest['title']); ?>">
                            <div>
                                <h6><?php echo htmlspecialchars($latest['title']); ?></h6>
                                <small><i data-lucide="calendar" style="width:11px;height:11px;display:inline;"></i>
                                    <?php echo date('d M Y', strtotime($latest['created_at'])); ?></small>
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
                        ['slug' => 'berita', 'label' => '📰 Berita'],
                        ['slug' => 'event', 'label' => '🎉 Event'],
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
<style>
gap: .5rem;

.sb-card-head svg { color: #c8890a; flex-shrink: 0; }

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
width: 58px; height: 44px;
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

<!-- Reading bar -->
<div id="rd-bar"></div>

<div class="article-page-bg">
    <div class="container">
        <div class="row g-4">

            <!-- ── Artikel ── -->
            <div class="col-lg-8">

                <!-- Breadcrumb -->
                <nav class="mb-3" style="font-size:.82rem;">
                    <a href="index.php" style="color:#c8890a;text-decoration:none;">Beranda</a>
                    <span style="color:#cbd5e1;margin:0 .4rem;">/</span>
                    <a href="berita.php" style="color:#c8890a;text-decoration:none;">Berita</a>
                    <span style="color:#cbd5e1;margin:0 .4rem;">/</span>
                    <span
                        style="color:#94a3b8;"><?php echo htmlspecialchars(mb_substr($row['title'], 0, 45)) . (mb_strlen($row['title']) > 45 ? '…' : ''); ?></span>
                </nav>

                <div class="article-container">

                    <!-- Hero Image -->
                    <img class="article-hero-img" src="<?php echo htmlspecialchars($row['image']); ?>"
                        alt="<?php echo htmlspecialchars($row['title']); ?>">

                    <div class="article-inner">

                        <!-- Badge -->
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

                        <!-- Konten -->
                        <div class="art-content">
                            <?php echo nl2br(htmlspecialchars($row['content'])); ?>
                        </div>

                        <hr class="art-hr">

                        <!-- Share Bar -->
                        <?php include 'includes/share_bar.php'; ?>

                        <!-- Kembali -->
                        <a href="berita.php" class="art-back-link">
                            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i>
                            Kembali ke Berita
                        </a>

                    </div><!-- .article-inner -->
                </div><!-- .article-container -->

            </div><!-- col-lg-8 -->

            <!-- ── Sidebar ── -->
            <div class="col-lg-4">
                <div style="position:sticky;top:90px;">

                    <!-- Berita Lainnya -->
                    <div class="sb-card">
                        <div class="sb-card-head">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                            </svg>
                            Berita Terbaru
                        </div>
                        <?php
                        $lr = mysqli_query($koneksi, "SELECT * FROM announcements WHERE is_published=1 AND id!='$id' ORDER BY created_at DESC LIMIT 5");
                        while ($l = mysqli_fetch_assoc($lr)): ?>
                            <a href="berita_detail.php?id=<?php echo $l['id']; ?>" class="sb-news-item">
                                <img src="<?php echo htmlspecialchars($l['image']); ?>"
                                    alt="<?php echo htmlspecialchars($l['title']); ?>">
                                <div>
                                    <p class="sb-news-title"><?php echo htmlspecialchars($l['title']); ?></p>
                                    <span class="sb-news-date">
                                        <i data-lucide="calendar" style="width:10px;height:10px;display:inline;"></i>
                                        <?php echo date('d M Y', strtotime($l['created_at'])); ?>
                                    </span>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    </div>

                    <!-- Kategori -->
                    <div class="sb-card">
                        <div class="sb-card-head">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z" />
                                <line x1="7" y1="7" x2="7.01" y2="7" />
                            </svg>
                            Kategori
                        </div>
                        <?php
                        $cats = [
                            ['slug' => 'pengumuman', 'label' => '📢 Pengumuman'],
                            ['slug' => 'berita', 'label' => '📰 Berita'],
                            ['slug' => 'event', 'label' => '🎉 Event'],
                        ];
                        foreach ($cats as $c):
                            $n = mysqli_fetch_assoc(mysqli_query(
                                $koneksi,
                                "SELECT COUNT(*) total FROM announcements WHERE is_published=1 AND category='{$c['slug']}'"
                            ))['total'];
                            ?>
                            <a href="berita.php?category=<?php echo $c['slug']; ?>" class="sb-cat-item">
                                <?php echo $c['label']; ?>
                                <span><?php echo $n; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                </div><!-- sticky -->
            </div><!-- col-lg-4 -->

        </div><!-- row -->
    </div><!-- container -->
</div><!-- article-page-bg -->

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