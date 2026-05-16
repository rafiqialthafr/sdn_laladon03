<?php
include 'koneksi.php';
include 'header.php';

$query = "SELECT * FROM kepsek ORDER BY id DESC LIMIT 1";
$result = mysqli_query($koneksi, $query);
$kepsek = mysqli_fetch_assoc($result);

if (!$kepsek) {
    // Fallback data
    $kepsek = [
        'name' => 'Metkopwati, S.Pd.',
        'position' => 'Kepala Sekolah SDN Laladon 03',
        'photo' => 'img/kepsek.jpeg',
        'education' => 'S1 PGSD – Universitas Terbuka',
        'period' => '2022',
        'vision_mission' => 'Mewujudkan kepemimpinan yang inklusif, inovatif, dan berorientasi pada mutu pendidikan. Berkomitmen menciptakan lingkungan sekolah yang aman, nyaman, dan mampu mengembangkan potensi setiap peserta didik secara optimal.',
        'quote' => 'Pendidikan bukan hanya tentang mengisi wadah, tetapi menyalakan api. Mari kita bersama-sama menyalakan semangat belajar anak-anak kita demi masa depan yang gemilang.'
    ];
}

// Pastikan foto Kepala Sekolah selalu menggunakan file yang ada
if (empty($kepsek['photo']) || !file_exists(__DIR__ . '/' . $kepsek['photo'])) {
    $kepsek['photo'] = 'img/kepsek.jpeg';
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
                <h1 class="page-header-title"><i data-lucide="user-round-cog"
                        style="width:36px;height:36px;vertical-align:middle;margin-right:10px;margin-bottom:6px;"></i>Kepala
                    Sekolah</h1>
                <p class="page-header-subtitle">Pemimpin yang menginspirasi generasi penerus bangsa</p>
            </div>
        </div>
    </div>
</section>

<!-- Profil Kepala Sekolah -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="section-title-wrapper">
            <span class="section-label">Profil Pimpinan</span>
            <h2 class="section-title mt-2">Kepala Sekolah Kami</h2>
            <div class="section-divider"></div>
        </div>

        <div class="row g-5 align-items-center">
            <!-- Foto -->
            <div class="col-md-4 text-center">
                <div class="kepsek-photo-wrap mx-auto" style="max-width:300px;">
                    <img src="<?php echo !empty($kepsek['photo']) ? (strpos($kepsek['photo'], 'img/') === 0 ? htmlspecialchars($kepsek['photo']) : 'img/' . htmlspecialchars($kepsek['photo'])) : 'https://via.placeholder.com/300x400'; ?>"
                        alt="<?php echo htmlspecialchars($kepsek['name']); ?>">
                    <div class="kepsek-deco-1"></div>
                    <div class="kepsek-deco-2"></div>
                </div>
            </div>

            <!-- Info -->
            <div class="col-12 col-md-8">
                <div class="kepsek-info-card">
                    <h2 class="kepsek-name"><?php echo htmlspecialchars($kepsek['name']); ?></h2>
                    <p class="kepsek-position"><?php echo htmlspecialchars($kepsek['position']); ?></p>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="info-chip">
                            <i data-lucide="graduation-cap" style="width:16px;height:16px;color:#c8890a;"></i>
                            <strong>Pendidikan:</strong> <?php echo htmlspecialchars($kepsek['education']); ?>
                        </span>
                        <span class="info-chip">
                            <i data-lucide="briefcase" style="width:16px;height:16px;color:#c8890a;"></i>
                            <strong>Jabatan sejak:</strong> <?php echo htmlspecialchars($kepsek['period']); ?>
                        </span>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-2" style="color:#1a1a2e; display:flex; align-items:center; gap:0.5rem;">
                            <i data-lucide="target" style="width:18px;height:18px;color:#FFD700;"></i>
                            Visi & Misi Kepemimpinan
                        </h6>
                        <p class="text-muted" style="line-height:1.75; font-size:0.95rem;">
                            <?php echo nl2br(htmlspecialchars($kepsek['vision_mission'])); ?>
                        </p>
                    </div>

                    <blockquote class="kepsek-quote">
                        "<?php echo htmlspecialchars($kepsek['quote']); ?>"
                        <cite>— <?php echo htmlspecialchars($kepsek['name']); ?></cite>
                    </blockquote>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Riwayat Karir -->
<section class="py-5" style="background-color: #f8f9fc;">
    <div class="container">
        <div class="section-title-wrapper">
            <span class="section-label">Perjalanan Karir</span>
            <h2 class="section-title mt-2">Riwayat Pendidikan & Karir</h2>
            <div class="section-divider"></div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="timeline-container">
                    <?php
                    $res_riwayat = mysqli_query($koneksi, "SELECT * FROM kepsek_riwayat ORDER BY urutan ASC");
                    $i = 0;
                    while ($r = mysqli_fetch_assoc($res_riwayat)):
                        $side = ($i % 2 === 0) ? '' : 'right';
                        $i++;
                        ?>
                        <div class="timeline-item <?php echo $side; ?>">
                            <div class="timeline-dot"></div>
                            <div class="timeline-card">
                                <span class="timeline-year-badge"><?php echo htmlspecialchars($r['tahun']); ?></span>
                                <h5><?php echo htmlspecialchars($r['judul']); ?></h5>
                                <p><?php echo htmlspecialchars($r['deskripsi']); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>