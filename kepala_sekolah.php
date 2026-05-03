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
        'nip' => '19751204 200501 2 005',
        'education' => 'S1 PGSD – UPI Bandung',
        'period' => '2022',
        'vision_mission' => 'Mewujudkan kepemimpinan yang inklusif, inovatif, dan berorientasi pada mutu pendidikan. Berkomitmen menciptakan lingkungan sekolah yang aman, nyaman, dan mampu mengembangkan potensi setiap peserta didik secara optimal.',
        'quote' => 'Pendidikan bukan hanya tentang mengisi wadah, tetapi menyalakan api. Mari kita bersama-sama menyalakan semangat belajar anak-anak kita demi masa depan yang gemilang.'
    ];
}
?>
<!-- Page Header -->
<section class="page-header">
    <!-- Floating Geometric Shapes -->
    <div class="geom-shape shape-1"></div>
    <div class="geom-shape shape-2"></div>
    <div class="geom-shape shape-3"></div>
    
    <div class="container">
        <div class="row py-5">
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
                    <img src="img/<?php echo htmlspecialchars($kepsek['photo']); ?>"
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
                            <i data-lucide="id-card" style="width:16px;height:16px;color:#c8890a;"></i>
                            <strong>NIP:</strong> <?php echo htmlspecialchars($kepsek['nip']); ?>
                        </span>
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
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-card">
                            <span class="timeline-year-badge">1998</span>
                            <h5>S1 Pendidikan Guru Sekolah Dasar</h5>
                            <p>Universitas Pendidikan Indonesia (UPI) Bandung — Lulus dengan predikat Cum Laude.</p>
                        </div>
                    </div>
                    <div class="timeline-item right">
                        <div class="timeline-dot"></div>
                        <div class="timeline-card">
                            <span class="timeline-year-badge">2000</span>
                            <h5>Guru Kelas — SDN Ciomas 01</h5>
                            <p>Memulai karir sebagai guru kelas dan aktif dalam kegiatan Kelompok Kerja Guru (KKG)
                                Kecamatan Ciomas.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-card">
                            <span class="timeline-year-badge">2015</span>
                            <h5>Wakil Kepala Sekolah Bid. Kurikulum</h5>
                            <p>SDN Laladon 01 — Memimpin transformasi kurikulum dan penerapan standar pembelajaran
                                nasional.</p>
                        </div>
                    </div>
                    <div class="timeline-item right">
                        <div class="timeline-dot"></div>
                        <div class="timeline-card">
                            <span class="timeline-year-badge">2022</span>
                            <h5>Kepala Sekolah — SDN Laladon 03</h5>
                            <p>Menjabat sebagai Kepala Sekolah SDN Laladon 03 hingga sekarang, membawa visi sekolah
                                digital dan akreditasi A+.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Kepala Sekolah dari Masa ke Masa -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="section-title-wrapper">
            <span class="section-label">Sejarah Kepemimpinan</span>
            <h2 class="section-title mt-2">Kepala Sekolah dari Masa ke Masa</h2>
            <div class="section-divider"></div>
            <p class="section-desc">Dedikasi para pemimpin yang telah membangun SDN Laladon 03 menjadi sekolah yang kita
                banggakan hari ini.</p>
        </div>

        <div class="row g-4">
            <?php
            $history = [
                ['name' => 'Drs. H. Ahmad Sanusi', 'period' => '1985 – 1995'],
                ['name' => 'Hj. Siti Aminah, S.Pd.', 'period' => '1995 – 2005'],
                ['name' => 'Budi Santoso, M.Pd.', 'period' => '2005 – 2015'],
                ['name' => 'Ratna Dewi, S.Pd.SD.', 'period' => '2015 – 2022'],
            ];
            foreach ($history as $i => $person):
                ?>
                <div class="col-md-6 col-lg-3 fade-delay-<?php echo $i + 1; ?>">
                    <div class="kepsek-history-card">
                        <div class="principal-avatar">
                            <i data-lucide="user" style="width:36px;height:36px;color:#94a3b8;"></i>
                        </div>
                        <h6 class="fw-bold mb-1" style="color:#;"><?php echo $person['name']; ?></h6>
                        <span
                            style="font-size:0.8rem;font-weight:700;padding:0.25rem 0.85rem;border-radius:50px;background:#FFD700;color:#;">
                            <?php echo $person['period']; ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>