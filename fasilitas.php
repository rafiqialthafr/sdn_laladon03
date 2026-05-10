<?php include 'header.php'; ?>

<!-- Page Header -->
<section class="page-header">
    <!-- Floating Geometric Shapes -->
    <div class="geom-shape shape-1"></div>
    <div class="geom-shape shape-2"></div>
    <div class="geom-shape shape-3"></div>

    <div class="container">
        <div class="row page-header-inner">
            <div class="col-12 text-center">
                <h1 class="page-header-title"><i data-lucide="building-2"
                        style="width:36px;height:36px;vertical-align:middle;margin-right:10px;margin-bottom:6px;"></i>Fasilitas
                    Sekolah</h1>
                <p class="page-header-subtitle">Sarana dan prasarana penunjang pembelajaran</p>
            </div>
        </div>
    </div>
</section>

<!-- Intro -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="section-title-wrapper">
            <h2 class="section-title mt-2">Fasilitas Penunjang Belajar</h2>
            <div class="section-divider"></div>
            <p class="section-desc">Kami menyediakan lingkungan belajar yang nyaman, aman, dan memadai untuk mendukung
                perkembangan optimal setiap peserta didik.</p>
        </div>

        <div class="row g-4">
            <?php
            require 'koneksi.php';
            $query_facilities = "SELECT * FROM fasilitas ORDER BY id ASC";
            $result_facilities = mysqli_query($koneksi, $query_facilities);

            if (mysqli_num_rows($result_facilities) > 0):
                while ($row = mysqli_fetch_assoc($result_facilities)):
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="facility-card">
                            <div class="facility-img-wrap">
                                <img src="<?php echo htmlspecialchars($row['image']); ?>"
                                    alt="<?php echo htmlspecialchars($row['name']); ?>">
                                <div class="facility-img-overlay"></div>

                            </div>
                            <div class="facility-body">
                                <h5 class="facility-name"><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="facility-desc"><?php echo htmlspecialchars($row['description']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
            else:
                ?>
                <div class="col-12 text-center py-5 text-muted">
                    <i data-lucide="building-2" style="width:48px;height:48px;color:#d1d5db;margin-bottom:1rem;"></i>
                    <p>Data fasilitas belum tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>