<?php
include 'koneksi.php';
include 'header.php';

// Ambil filter kategori
$filter = isset($_GET['kategori']) ? mysqli_real_escape_string($koneksi, $_GET['kategori']) : '';

// Query data galeri
if ($filter) {
    $query = "SELECT * FROM galeri WHERE kategori='$filter' ORDER BY created_at DESC";
} else {
    $query = "SELECT * FROM galeri ORDER BY created_at DESC";
}
$result = mysqli_query($koneksi, $query);

// Ambil semua kategori yang tersedia untuk filter
$cat_query = "SELECT DISTINCT kategori FROM galeri ORDER BY kategori ASC";
$cat_result = mysqli_query($koneksi, $cat_query);
$kategori_list = [];
while ($c = mysqli_fetch_assoc($cat_result)) {
    $kategori_list[] = $c['kategori'];
}
?>

<!-- Page Header -->
<section class="page-header">
    <!-- Floating Geometric Shapes -->
    <div class="geom-shape shape-1"></div>
    <div class="geom-shape shape-2"></div>
    <div class="geom-shape shape-3"></div>

    <div class="container">
        <div class="row page-header-inner">
            <div class="col-12 text-center">
                <h1 class="page-header-title"><i data-lucide="layout-dashboard"
                        style="width:36px;height:36px;vertical-align:middle;margin-right:10px;margin-bottom:6px;"></i>Galeri
                    Foto</h1>
                <p class="page-header-subtitle">Momen dan kegiatan bersama SDN Laladon 03</p>
            </div>
        </div>
    </div>
</section>

<!-- Filter Bar -->
<section class="py-4 bg-white">
    <div class="container">
        <div class="text-center">
            <div class="news-filter-bar">
                <a href="galeri.php" class="news-filter-btn <?php echo !$filter ? 'active' : ''; ?>">
                    <i data-lucide="layout-grid"
                        style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:4px;"></i>
                    Semua
                </a>
                <?php foreach ($kategori_list as $kat): ?>
                    <a href="galeri.php?kategori=<?php echo urlencode($kat); ?>"
                        class="news-filter-btn <?php echo $filter === $kat ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars(ucfirst($kat)); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Galeri Grid -->
<section class="py-5" style="background-color:#f8f9fc;">
    <div class="container">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <div class="galeri-grid" id="galeriGrid">
                <?php $i = 0;
                while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="galeri-item" data-index="<?php echo $i; ?>"
                        data-src="<?php echo htmlspecialchars($row['foto']); ?>"
                        data-caption="<?php echo htmlspecialchars($row['judul']); ?>">
                        <div class="galeri-img-wrap">
                            <img src="<?php echo htmlspecialchars($row['foto']); ?>"
                                alt="<?php echo htmlspecialchars($row['judul']); ?>" loading="lazy">
                            <div class="galeri-overlay">
                                <div class="galeri-overlay-content">
                                    <i data-lucide="zoom-in" style="width:28px;height:28px;color:#fff;"></i>
                                    <p class="galeri-overlay-caption"><?php echo htmlspecialchars($row['judul']); ?></p>
                                </div>
                            </div>
                            <?php if (!empty($row['kategori'])): ?>
                                <span class="galeri-badge"><?php echo htmlspecialchars(ucfirst($row['kategori'])); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php $i++; endwhile; ?>
            </div>
        <?php else: ?>
            <div class="news-empty-state">
                <div class="empty-icon">
                    <i data-lucide="image" style="width:36px;height:36px;color:#d1d5db;"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color:#6b7280;">Belum Ada Foto</h5>
                <p class="text-muted">Foto galeri akan segera ditambahkan.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Lightbox -->
<div id="galeriLightbox" class="galeri-lightbox" role="dialog" aria-modal="true" aria-label="Tampilan foto penuh">
    <button class="lb-close" id="lbClose" aria-label="Tutup">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
    </button>
    <button class="lb-nav lb-prev" id="lbPrev" aria-label="Foto sebelumnya">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6" />
        </svg>
    </button>
    <div class="lb-content">
        <img id="lbImg" src="" alt="">
        <p id="lbCaption" class="lb-caption"></p>
    </div>
    <button class="lb-nav lb-next" id="lbNext" aria-label="Foto berikutnya">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6" />
        </svg>
    </button>
    <div class="lb-counter" id="lbCounter"></div>
</div>
<script>
    (function () {
        const items = Array.from(document.querySelectorAll('.galeri-item'));
        const lb = document.getElementById('galeriLightbox');
        const lbImg = document.getElementById('lbImg');
        const lbCap = document.getElementById('lbCaption');
        const lbCnt = document.getElementById('lbCounter');
        let current = 0;

        function openLb(idx) {
            current = idx;
            updateLb();
            lb.classList.add('lb-open');
            document.body.style.overflow = 'hidden';
        }
        function closeLb() {
            lb.classList.remove('lb-open');
            document.body.style.overflow = '';
        }
        function updateLb() {
            const item = items[current];
            lbImg.style.opacity = '0';
            lbImg.src = item.dataset.src;
            lbImg.alt = item.dataset.caption;
            lbCap.textContent = item.dataset.caption;
            lbCnt.textContent = (current + 1) + ' / ' + items.length;
            lbImg.onload = () => { lbImg.style.opacity = '1'; };
        }
        function prev() { current = (current - 1 + items.length) % items.length; updateLb(); }
        function next() { current = (current + 1) % items.length; updateLb(); }

        items.forEach((item, idx) => item.addEventListener('click', () => openLb(idx)));
        document.getElementById('lbClose').addEventListener('click', closeLb);
        document.getElementById('lbPrev').addEventListener('click', prev);
        document.getElementById('lbNext').addEventListener('click', next);
        lb.addEventListener('click', function (e) { if (e.target === lb) closeLb(); });
        document.addEventListener('keydown', function (e) {
            if (!lb.classList.contains('lb-open')) return;
            if (e.key === 'Escape') closeLb();
            if (e.key === 'ArrowLeft') prev();
            if (e.key === 'ArrowRight') next();
        });
    })();
</script>

<?php include 'footer.php'; ?>