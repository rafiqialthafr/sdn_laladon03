<?php 
include 'koneksi.php';
include 'header.php'; 

// Check if table 'ekskul' exists. If not, auto-create and seed (safety fallback for public page)
$table_check = mysqli_query($koneksi, "SHOW TABLES LIKE 'ekskul'");
if (mysqli_num_rows($table_check) == 0) {
    $create_table = "CREATE TABLE ekskul (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(50) NOT NULL DEFAULT 'pilihan',
        description TEXT,
        image VARCHAR(255) NOT NULL,
        schedule VARCHAR(255),
        instructor VARCHAR(255),
        location VARCHAR(255),
        icon VARCHAR(50) DEFAULT 'compass'
    )";
    mysqli_query($koneksi, $create_table);
    
    // Seed with initial Pramuka data
    $seed = "INSERT INTO ekskul (name, category, description, image, schedule, instructor, location, icon) VALUES 
    ('Gerakan Pramuka', 'wajib', 'Pramuka merupakan ekstrakurikuler wajib di SDN Laladon 03 untuk siswa kelas III, IV, dan V. Melalui gerakan kepramukaan, siswa dibimbing untuk membangun kedisiplinan, ketahanan mental, kerja sama kelompok, kepemimpinan, serta jiwa patriotisme yang berlandaskan Dasa Darma Pramuka.', 'img/slider1.jpeg', 'Sabtu, 08.00 - 10.00 WIB', 'Kak Dadan Ramdani, S.Pd.', 'Lapangan & Lingkungan Sekolah', 'compass')";
    mysqli_query($koneksi, $seed);
}

$query = "SELECT * FROM ekskul ORDER BY id ASC";
$result = mysqli_query($koneksi, $query);
$count = mysqli_num_rows($result);
?>

<!-- Page Header -->
<section class="page-header">
    <div class="geom-shape shape-1"></div>
    <div class="geom-shape shape-2"></div>
    <div class="geom-shape shape-3"></div>
    <div class="container">
        <div class="row page-header-inner">
            <div class="col-12 text-center">
                <h1 class="page-header-title">
                    <i data-lucide="compass"></i>Kegiatan Ekskul
                </h1>
                <p class="page-header-subtitle">Wadah pengembangan karakter, dan potensi luar biasa siswa</p>
            </div>
        </div>
    </div>
</section>

<!-- Extracurricular Section -->
<section class="py-5 bg-white position-relative ekskul-page-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center mb-5">
                <span class="section-label">Aktivitas Siswa</span>
                <h2 class="section-title mt-2">Ekstrakurikuler Sekolah</h2>
                <div class="section-divider"></div>
                <p class="section-desc">
                    Melalui program ekstrakurikuler, kami memfasilitasi pengembangan kedisiplinan, ketahanan mental, kerja sama kelompok, serta jiwa patriotisme siswa.
                </p>
            </div>
        </div>

        <!-- Dynamic Filter Controls (Only shown if more than 1 ekskul exists) -->
        <?php if ($count > 1): ?>
        <div class="row justify-content-center mb-5">
            <div class="col-12 text-center">
                <div class="filter-btn-group d-inline-flex flex-wrap justify-content-center gap-2 p-2 rounded-pill bg-light shadow-sm">
                    <button class="btn filter-btn active" data-filter="all">Semua Ekskul</button>
                    <button class="btn filter-btn" data-filter="wajib">Wajib</button>
                    <button class="btn filter-btn" data-filter="pilihan">Pilihan</button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Extracurricular Grid -->
        <div class="row g-4 <?php echo $count === 1 ? 'justify-content-center' : ''; ?>" id="ekskul-grid">
            <?php 
            if ($count > 0): 
                while ($e = mysqli_fetch_assoc($result)):
                    // Check badge class
                    $badge_class = $e['category'] === 'wajib' ? 'badge-wajib' : 'badge-pilihan';
                    $badge_label = $e['category'] === 'wajib' ? 'Ekskul Wajib' : 'Ekskul Pilihan';
                    $badge_icon = $e['category'] === 'wajib' ? 'shield' : 'award';
                    
                    // Gradient icon boxes based on name or fallback
                    $icon_bg = 'bg-orange-gradient';
                    $lower_name = strtolower($e['name']);
                    if (strpos($lower_name, 'pramuka') !== false) {
                        $icon_bg = 'bg-orange-gradient';
                    } elseif (strpos($lower_name, 'karate') !== false || strpos($lower_name, 'silat') !== false) {
                        $icon_bg = 'bg-red-gradient';
                    } elseif (strpos($lower_name, 'paskibra') !== false) {
                        $icon_bg = 'bg-blue-gradient';
                    } elseif (strpos($lower_name, 'tari') !== false || strpos($lower_name, 'musik') !== false || strpos($lower_name, 'seni') !== false) {
                        $icon_bg = 'bg-gold-gradient';
                    } elseif (strpos($lower_name, 'dokter') !== false || strpos($lower_name, 'uks') !== false || strpos($lower_name, 'sehat') !== false) {
                        $icon_bg = 'bg-green-gradient';
                    } elseif (strpos($lower_name, 'rohis') !== false || strpos($lower_name, 'quran') !== false || strpos($lower_name, 'agama') !== false) {
                        $icon_bg = 'bg-violet-gradient';
                    } else {
                        $gradients = ['bg-orange-gradient', 'bg-red-gradient', 'bg-blue-gradient', 'bg-gold-gradient', 'bg-green-gradient', 'bg-violet-gradient'];
                        $icon_bg = $gradients[$e['id'] % count($gradients)];
                    }
                    
                    // Card width based on count
                    $col_class = $count === 1 ? 'col-md-8 col-lg-6' : 'col-md-6 col-lg-4';
            ?>
            <div class="<?php echo $col_class; ?> ekskul-card-item" data-category="<?php echo htmlspecialchars($e['category']); ?>">
                <div class="ekskul-card h-100 <?php echo $e['category'] === 'wajib' ? 'featured' : ''; ?>">
                    <div class="ekskul-card-image">
                        <img src="<?php echo htmlspecialchars($e['image']); ?>" alt="<?php echo htmlspecialchars($e['name']); ?>">
                        <span class="ekskul-badge <?php echo $badge_class; ?>">
                            <i data-lucide="<?php echo $badge_icon; ?>"></i>
                            <?php echo $badge_label; ?>
                        </span>
                    </div>
                    <div class="ekskul-card-body">
                        <div class="ekskul-icon-box <?php echo $icon_bg; ?>">
                            <i data-lucide="<?php echo htmlspecialchars($e['icon']); ?>"></i>
                        </div>
                        <h4 class="ekskul-title"><?php echo htmlspecialchars($e['name']); ?></h4>
                        <p class="ekskul-text"><?php echo htmlspecialchars($e['description']); ?></p>
                        
                        <div class="ekskul-info-list">
                            <?php if ($e['schedule']): ?>
                            <div class="ekskul-info-item">
                                <i data-lucide="calendar"></i>
                                <strong>Jadwal Latihan:</strong>
                                <span><?php echo htmlspecialchars($e['schedule']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($e['instructor']): ?>
                            <div class="ekskul-info-item">
                                <i data-lucide="user"></i>
                                <strong>Pembina:</strong>
                                <span><?php echo htmlspecialchars($e['instructor']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($e['location']): ?>
                            <div class="ekskul-info-item">
                                <i data-lucide="map-pin"></i>
                                <strong>Lokasi:</strong>
                                <span><?php echo htmlspecialchars($e['location']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted">
                    <i data-lucide="compass" class="empty-state-icon mb-3"></i>
                    <h5>Belum Ada Kegiatan Ekskul</h5>
                    <p class="small">Silakan kembali lagi nanti atau hubungi pihak sekolah.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- No Results Fallback (Only shown if filtering is active) -->
        <?php if ($count > 1): ?>
        <div class="row d-none" id="ekskul-no-results">
            <div class="col-12 text-center py-5">
                <div class="text-muted">
                    <i data-lucide="frown" class="empty-state-icon mb-3"></i>
                    <h5>Ekskul Tidak Ditemukan</h5>
                    <p class="small">Silakan pilih kategori ekskul lainnya di atas.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>



<!-- Filtering Script (Only loaded/executed if more than 1 ekskul exists) -->
<?php if ($count > 1): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const cardItems = document.querySelectorAll('.ekskul-card-item');
        const noResults = document.getElementById('ekskul-no-results');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');

                const filterValue = this.getAttribute('data-filter');
                let matchesCount = 0;

                // Animate and filter items
                cardItems.forEach(item => {
                    const category = item.getAttribute('data-category');

                    if (filterValue === 'all' || category === filterValue) {
                        item.classList.remove('fade-out');
                        item.classList.add('fade-in');
                        matchesCount++;
                    } else {
                        item.classList.remove('fade-in');
                        item.classList.add('fade-out');
                    }
                });

                // Handle no results fallback
                if (matchesCount === 0) {
                    noResults.classList.remove('d-none');
                } else {
                    noResults.classList.add('d-none');
                }

                // Smoothly trigger Lucide icons update if needed
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        });
    });
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
