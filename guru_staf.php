<?php
include 'koneksi.php';
include 'header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row py-5">
            <div class="col-12 text-center">
                <h1 class="page-header-title">Guru & Staf</h1>
                <p class="page-header-subtitle">Para pendidik profesional SDN Laladon 03</p>
            </div>
        </div>
    </div>
</section>

<!-- Teachers Section -->
<section class="py-5" style="background-color: #f8f9fc;">
    <div class="container">
        <div class="section-title-wrapper">
            <h2 class="section-title mt-2">Tim Pengajar & Staf Profesional</h2>
            <div class="section-divider"></div>
            <p class="section-desc">Para pendidik berpengalaman yang berdedikasi tinggi untuk membentuk karakter dan kecerdasan peserta didik.</p>
        </div>

        <div class="row g-4">
            <?php
            $query_teachers = "SELECT * FROM teachers ORDER BY id ASC";
            $result_teachers = mysqli_query($koneksi, $query_teachers);

            if (mysqli_num_rows($result_teachers) > 0):
                $delay = 1;
                while ($row = mysqli_fetch_assoc($result_teachers)):
            ?>
            <div class="col-md-6 col-lg-3 fade-delay-<?php echo min($delay, 4); ?>">
                <div class="teacher-card">
                    <div class="teacher-card-img-wrap">
                        <img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <div class="teacher-card-overlay"></div>
                    </div>
                    <div class="teacher-badge-wrap">
                        <span class="teacher-badge">Pendidik</span>
                    </div>
                    <div class="teacher-card-body">
                        <h5 class="teacher-name"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p class="teacher-position"><?php echo htmlspecialchars($row['position']); ?></p>
                        <p class="teacher-bio"><?php echo htmlspecialchars(substr($row['bio'], 0, 100)); ?>...</p>
                    </div>
                </div>
            </div>
            <?php
                $delay++;
                endwhile;
            else:
            ?>
            <div class="col-12">
                <div class="news-empty-state">
                    <div class="empty-icon">
                        <i data-lucide="users" style="width:36px;height:36px;color:#d1d5db;"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:#6b7280;">Data Belum Tersedia</h5>
                    <p class="text-muted">Data guru dan staf belum ditambahkan.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
