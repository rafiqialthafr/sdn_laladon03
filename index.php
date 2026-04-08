<?php
include 'koneksi.php';
include 'header.php';
?>

<!-- ══════════════════════════════════════════
     HERO SLIDER ══════════════════════════════════════════ -->
<section id="hero-section">
    <div id="heroCarousel" class="carousel slide hero-modern" data-bs-ride="carousel" data-bs-touch="true"
        data-bs-interval="5000">
        <!-- Carousel Indicators (Dots) -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true"
                aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">

            <!-- SLIDE 1 -->
            <div class="carousel-item active" data-bs-interval="5000">
                <div class="hero-overlay-gold"></div>
                <img src="img/foto1.jpeg" class="d-block w-100 hero-img"
                    alt="Siswa-siswi SDN Laladon 03 dalam kegiatan upacara sekolah" width="1920" height="700"
                    fetchpriority="high" loading="eager">
                <div class="carousel-caption hero-content-gold">
                    <div class="hero-badge-gold animate-fade-in-1">
                        <span class="badge-icon">⭐</span>
                        <span>Akreditasi A</span>
                    </div>
                    <h3 class="hero-pretitle animate-fade-in-2">Selamat Datang di</h3>
                    <h1 class="hero-title-gold animate-fade-in-3">
                        <span class="text-highlight">SDN LALADON 03</span>
                    </h1>
                    <p class="hero-subtitle animate-fade-in-4">
                        Mewujudkan Generasi Cerdas, Kreatif, Berkarakter, dan Berprestasi
                    </p>
                    <div class="hero-buttons animate-fade-in-5">
                        <a href="sejarah.php" class="btn-gold-primary">
                            <span>📝 Tentang Kami</span>
                            <span class="btn-arrow">→</span>
                        </a>
                        <a href="visimisi.php" class="btn-gold-outline">
                            <span>📚 Visi & Misi</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- SLIDE 2 -->
            <div class="carousel-item" data-bs-interval="5000">
                <div class="hero-overlay-gold"></div>
                <img src="img/foto2.jpeg" class="d-block w-100 hero-img"
                    alt="Prestasi siswa SDN Laladon 03 di kompetisi tingkat nasional" width="1920" height="700"
                    loading="lazy">
                <div class="carousel-caption hero-content-gold">
                    <div class="hero-badge-gold animate-fade-in-1">
                        <span class="badge-icon">🏆</span>
                        <span>Juara Nasional 2024</span>
                    </div>
                    <h3 class="hero-pretitle animate-fade-in-2">Prestasi Membanggakan</h3>
                    <h1 class="hero-title-gold animate-fade-in-3">
                        <span class="text-highlight">BERPRESTASI</span>
                    </h1>
                    <p class="hero-subtitle animate-fade-in-4">
                        15+ Juara Lomba Tingkat Kota dan Provinsi di Bidang Akademik & Non-Akademik
                    </p>
                    <div class="hero-buttons animate-fade-in-5">
                        <a href="berita.php" class="btn-gold-primary">
                            <span>🏅 Lihat Berita</span>
                            <span class="btn-arrow">→</span>
                        </a>
                        <a href="fasilitas.php" class="btn-gold-outline">
                            <span>📸 Fasilitas</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- SLIDE 3 -->
            <div class="carousel-item" data-bs-interval="5000">
                <div class="hero-overlay-gold"></div>
                <img src="img/foto3.jpeg" class="d-block w-100 hero-img"
                    alt="Fasilitas lengkap dan modern SDN Laladon 03" width="1920" height="700" loading="lazy">
                <div class="carousel-caption hero-content-gold">
                    <div class="hero-badge-gold animate-fade-in-1">
                        <span class="badge-icon">👨‍🏫</span>
                        <span>Guru Bersertifikat</span>
                    </div>
                    <h3 class="hero-pretitle animate-fade-in-2">Fasilitas Lengkap</h3>
                    <h1 class="hero-title-gold animate-fade-in-3">
                        <span class="text-highlight">MODERN & NYAMAN</span>
                    </h1>
                    <p class="hero-subtitle animate-fade-in-4">
                        Perpustakaan, Lab Komputer, Ruang Kelas Ber-AC, Lapangan Olahraga, dan Fasilitas Pendukung
                        Lainnya
                    </p>
                    <div class="hero-buttons animate-fade-in-5">
                        <a href="fasilitas.php" class="btn-gold-primary">
                            <span>🏫 Lihat Fasilitas</span>
                            <span class="btn-arrow">→</span>
                        </a>
                        <a href="guru_staf.php" class="btn-gold-outline">
                            <span>👩‍🏫 Tim Pengajar</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"
            aria-label="Slide sebelumnya">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Slide Sebelumnya</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"
            aria-label="Slide berikutnya">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Slide Berikutnya</span>
        </button>
    </div>
</section>


<!-- ══════════════════════════════════════════
     TENTANG SEKOLAH — ACHIEVEMENT CARDS
══════════════════════════════════════════ -->
<section id="aboutSection" class="py-5 bg-white">
    <div class="container">
        <div class="section-title-wrapper">
            <span class="section-label">Tentang Kami</span>
            <h2 class="section-title mt-2">Keunggulan Kami</h2>
            <div class="section-divider"></div>
            <p class="section-desc">Kami berkomitmen menghadirkan pendidikan terbaik dengan standar nasional yang telah
                terbukti melalui berbagai pencapaian dan akreditasi bergengsi.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="content-card p-4 text-center h-100">
                    <div class="card-icon-wrap gold mx-auto mb-3">
                        <i data-lucide="trophy" style="width:26px;height:26px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:#1a1a2e;">Terakreditasi A</h5>
                    <p class="text-muted small mb-0">Sejak 2016 mendapatkan akreditasi dari BANSM dengan No SK:
                        048/BAP-SM/HK/XI/2016</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="content-card p-4 text-center h-100">
                    <div class="card-icon-wrap blue mx-auto mb-3">
                        <i data-lucide="clipboard-list" style="width:26px;height:26px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:#1a1a2e;">Kurikulum K-13</h5>
                    <p class="text-muted small mb-0">Penilaian empat aspek: pengetahuan, keterampilan, sikap, dan
                        perilaku secara menyeluruh</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="content-card p-4 text-center h-100">
                    <div class="card-icon-wrap green mx-auto mb-3">
                        <i data-lucide="award" style="width:26px;height:26px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:#1a1a2e;">Guru Bersertifikat</h5>
                    <p class="text-muted small mb-0">Guru profesional bersertifikasi nasional yang berdedikasi tinggi
                        dan berpengalaman</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="content-card p-4 text-center h-100">
                    <div class="card-icon-wrap gold mx-auto mb-3">
                        <i data-lucide="layers" style="width:26px;height:26px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:#1a1a2e;">Fasilitas Lengkap</h5>
                    <p class="text-muted small mb-0">Ruang kelas, lab komputer, perpustakaan, lapangan olahraga, dan
                        musholla</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     VISI MISI — PROGRAM CARDS
══════════════════════════════════════════ -->
<section class="py-5" style="background:#f8f9fc;">
    <div class="container">
        <div class="section-title-wrapper">
            <span class="section-label">Identitas Sekolah</span>
            <h2 class="section-title mt-2">Visi, Misi & Program Kami</h2>
            <div class="section-divider"></div>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="content-card p-4 text-center h-100">
                    <div class="card-icon-wrap gold mx-auto mb-3">
                        <i data-lucide="eye" style="width:26px;height:26px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:#1a1a2e;">Visi</h5>
                    <p class="text-muted small mb-0">Terwujudnya Peserta Didik yang Beriman, Bertakwa, Berkarakter
                        Mulia, Cerdas, dan Berprestasi.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="content-card p-4 text-center h-100">
                    <div class="card-icon-wrap blue mx-auto mb-3">
                        <i data-lucide="rocket" style="width:26px;height:26px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:#1a1a2e;">Misi</h5>
                    <p class="text-muted small mb-0">Menyelenggarakan pendidikan berkualitas akademik & non-akademik
                        serta menanamkan nilai karakter.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="content-card p-4 text-center h-100">
                    <div class="card-icon-wrap green mx-auto mb-3">
                        <i data-lucide="star" style="width:26px;height:26px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:#1a1a2e;">Program Unggulan</h5>
                    <p class="text-muted small mb-0">Tahfidz Qur'an, Literasi Sekolah, Adiwiyata, dan Pembiasaan
                        Karakter Islami.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="content-card p-4 text-center h-100">
                    <div class="card-icon-wrap gold mx-auto mb-3">
                        <i data-lucide="zap" style="width:26px;height:26px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:#1a1a2e;">Ekstrakurikuler</h5>
                    <p class="text-muted small mb-0">Pramuka, Paskibra, Futsal, Tari, Marawis, dan berbagai kegiatan
                        pengembangan diri.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     SAMBUTAN KEPALA SEKOLAH
══════════════════════════════════════════ -->
<section id="welcomeSection" class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Foto -->
            <div class="col-md-4 text-center">
                <div class="principal-photo-wrap mx-auto" style="max-width:300px;">
                    <img src="kepsek.png" alt="Kepala Sekolah SDN Laladon 03, Metkopwati S.Pd."
                        onerror="this.src='https://ui-avatars.com/api/?name=Metkopwati&background=FFD700&color=3d1f00&size=300&bold=true'">
                    <div class="principal-deco-1"></div>
                    <div class="principal-deco-2"></div>
                </div>
                <div class="mt-4">
                    <h5 class="fw-bold mb-1" style="color:#1a1a2e;">Metkopwati, S.Pd.</h5>
                    <span class="section-label" style="display:inline-block;">Kepala Sekolah</span>
                </div>
            </div>

            <!-- Teks -->
            <div class="col-md-8">
                <span class="section-label">Sambutan Pimpinan</span>
                <h2 class="section-title mt-2 text-start">Sambutan Kepala Sekolah</h2>
                <div class="section-divider" style="margin:1rem 0;"></div>
                <p style="color:#4b5563;line-height:1.8;font-size:.95rem;">
                    Selamat datang di website resmi SDN Laladon 03. Kami berharap website ini menjadi sarana informasi
                    dan komunikasi yang efektif antara sekolah, orang tua, siswa, dan masyarakat. Kami terus berkomitmen
                    untuk meningkatkan kualitas pendidikan dan pelayanan prima kepada seluruh peserta didik.
                </p>
                <blockquote class="principal-quote mb-4">
                    "Pendidikan bukan hanya tentang mengisi wadah, tetapi menyalakan api. Mari kita bersama-sama
                    menyalakan semangat belajar anak-anak kita demi masa depan yang gemilang."
                    <cite>— Metkopwati, S.Pd.</cite>
                </blockquote>
                <a href="kepala_sekolah.php" class="news-read-more mt-2" style="display:inline-flex;font-weight:700;">
                    Profil Lengkap
                    <i data-lucide="arrow-right" style="width:16px;height:16px;"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     GURU & STAF
══════════════════════════════════════════ -->
<section id="teachersSection" class="py-5" style="background:#f8f9fc;">
    <div class="container">
        <div class="section-title-wrapper">
            <span class="section-label">Tim Kami</span>
            <h2 class="section-title mt-2">Guru & Staf Kami</h2>
            <div class="section-divider"></div>
            <p class="section-desc">Para pendidik berpengalaman yang berdedikasi membentuk karakter dan kecerdasan
                setiap peserta didik.</p>
        </div>

        <div class="row g-4">
            <?php
            $query_teachers = "SELECT * FROM teachers ORDER BY id ASC LIMIT 4";
            $result_teachers = mysqli_query($koneksi, $query_teachers);
            if (mysqli_num_rows($result_teachers) > 0):
                $delay = 1;
                while ($row = mysqli_fetch_assoc($result_teachers)):
                    ?>
                    <div class="col-md-6 col-lg-3 fade-delay-<?php echo min($delay, 4); ?>">
                        <div class="teacher-card">
                            <div class="teacher-card-img-wrap">
                                <img src="<?php echo htmlspecialchars($row['photo']); ?>"
                                    alt="<?php echo htmlspecialchars($row['name']); ?>">
                                <div class="teacher-card-overlay"></div>
                            </div>
                            <div class="teacher-badge-wrap">
                                <span class="teacher-badge">Pendidik</span>
                            </div>
                            <div class="teacher-card-body">
                                <h5 class="teacher-name"><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="teacher-position"><?php echo htmlspecialchars($row['position']); ?></p>
                                <p class="teacher-bio"><?php echo htmlspecialchars(substr($row['bio'], 0, 80)); ?>...</p>
                            </div>
                        </div>
                    </div>
                    <?php
                    $delay++;
                endwhile;
            else:
                ?>
                <div class="col-12 text-center text-muted py-4">
                    <i data-lucide="users"
                        style="width:40px;height:40px;color:#d1d5db;display:block;margin:0 auto .75rem;"></i>
                    Data guru belum tersedia.
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <a href="guru_staf.php" class="btn-admin-add"
                style="display:inline-flex;font-size:.9rem;padding:.75rem 1.75rem;">
                <i data-lucide="users" style="width:16px;height:16px;"></i>
                Lihat Semua Guru & Staf
            </a>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     BERITA TERBARU
══════════════════════════════════════════ -->
<section id="blogSection" class="py-5 bg-white">
    <div class="container">
        <div class="section-title-wrapper">
            <span class="section-label">Informasi Terkini</span>
            <h2 class="section-title mt-2">Berita & Pengumuman Terbaru</h2>
            <div class="section-divider"></div>
            <p class="section-desc">Tetap update dengan informasi, pengumuman, dan kegiatan terbaru dari SDN Laladon 03.
            </p>
        </div>

        <div class="row g-4">
            <?php
            $query_blog = "SELECT * FROM announcements WHERE is_published=1 ORDER BY created_at DESC LIMIT 3";
            $result_blog = mysqli_query($koneksi, $query_blog);

            function getCatClassIdx($cat)
            {
                return match ($cat) { 'pengumuman' => 'pengumuman', 'berita' => 'berita', 'event' => 'event', default => 'berita'};
            }
            function getCatLabelIdx($cat)
            {
                return match ($cat) { 'pengumuman' => 'Pengumuman', 'berita' => 'Berita', 'event' => 'Event', default => 'Berita'};
            }

            if (mysqli_num_rows($result_blog) > 0):
                $d = 1;
                while ($blog = mysqli_fetch_assoc($result_blog)):
                    ?>
                    <div class="col-md-4 fade-delay-<?php echo min($d, 4); ?>">
                        <div class="news-card-modern">
                            <div class="img-wrap">
                                <img src="<?php echo htmlspecialchars($blog['image']); ?>"
                                    alt="<?php echo htmlspecialchars($blog['title']); ?>">
                                <span class="news-cat-badge <?php echo getCatClassIdx($blog['category']); ?>">
                                    <?php echo getCatLabelIdx($blog['category']); ?>
                                </span>
                            </div>
                            <div class="news-card-content">
                                <div class="news-card-date">
                                    <i data-lucide="calendar" style="width:13px;height:13px;"></i>
                                    <?php echo date('d F Y', strtotime($blog['created_at'])); ?>
                                </div>
                                <h5 class="news-card-title"><?php echo htmlspecialchars($blog['title']); ?></h5>
                                <p class="news-card-excerpt">
                                    <?php echo htmlspecialchars(substr(strip_tags($blog['content']), 0, 110)); ?>...
                                </p>
                                <a href="berita_detail.php?id=<?php echo $blog['id']; ?>" class="news-read-more">
                                    Baca Selengkapnya
                                    <i data-lucide="arrow-right" style="width:15px;height:15px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                    $d++;
                endwhile;
            else:
                ?>
                <div class="col-12 text-center text-muted py-4">
                    <i data-lucide="inbox"
                        style="width:40px;height:40px;color:#d1d5db;display:block;margin:0 auto .75rem;"></i>
                    Belum ada berita terbaru.
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <a href="berita.php" class="btn-admin-add"
                style="display:inline-flex;font-size:.9rem;padding:.75rem 1.75rem;">
                <i data-lucide="newspaper" style="width:16px;height:16px;"></i>
                Lihat Semua Berita
            </a>
        </div>
    </div>
</section>



<?php include 'footer.php'; ?>

<!-- Swipe Touch Support untuk Hero Carousel di HP -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        (function () {
            var el = document.getElementById('heroCarousel');
            if (!el) return;

            // Sekarang bootstrap pasti sudah terdefinisi karena menunggu DOMContentLoaded
            var carousel = bootstrap.Carousel.getOrCreateInstance(el, { touch: true, ride: 'carousel', interval: 5000 });

            var startX = 0;
            var isDragging = false;

            el.addEventListener('touchstart', function (e) {
                startX = e.touches[0].clientX;
                isDragging = true;
            }, { passive: true });

            el.addEventListener('touchend', function (e) {
                if (!isDragging) return;
                var endX = e.changedTouches[0].clientX;
                var diff = startX - endX;
                if (Math.abs(diff) > 50) {
                    if (diff > 0) {
                        carousel.next();
                    } else {
                        carousel.prev();
                    }
                }
                isDragging = false;
            }, { passive: true });
        })();
    });
</script>