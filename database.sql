-- Tabel pengguna admin
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel guru dan staf
CREATE TABLE IF NOT EXISTS teachers (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    position   VARCHAR(100) NOT NULL,
    photo      VARCHAR(255) DEFAULT 'https://placehold.co/400x400/34495e/ffffff?text=Guru',
    bio        TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel berita dan pengumuman
CREATE TABLE IF NOT EXISTS announcements (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(255) NOT NULL,
    content      TEXT NOT NULL,
    category     ENUM('pengumuman', 'berita', 'event') DEFAULT 'pengumuman',
    image        VARCHAR(255) DEFAULT 'https://placehold.co/600x400/34495e/ffffff?text=Berita',
    is_published TINYINT(1) DEFAULT 1,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel pesan dari formulir kontak
CREATE TABLE IF NOT EXISTS contact_messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nama       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    subjek     VARCHAR(255) NOT NULL,
    pesan      TEXT NOT NULL,
    is_read    TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
--  DATA AWAL (DEMO)
-- ============================================

-- Admin default (password: admin123)
INSERT INTO users (username, password) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Data guru contoh
INSERT INTO teachers (name, position, photo, bio) VALUES
('Metkopwati, S.Pd.', 'Kepala Sekolah', 'https://placehold.co/400x400/FFD700/3d1f00?text=MK', 'Kepala Sekolah SDN Laladon 03 sejak 2022. Lulusan S1 PGSD UPI Bandung.'),
('Siti Aminah, S.Pd.', 'Guru Kelas VI', 'https://placehold.co/400x400/3498db/ffffff?text=SA', 'Guru berpengalaman 15 tahun dengan keahlian di bidang Bahasa Indonesia.'),
('Agus Pratama, S.Pd.', 'Guru Kelas V', 'https://placehold.co/400x400/2ecc71/ffffff?text=AP', 'Pemenang Guru Berprestasi Tingkat Kecamatan Ciomas tahun 2022.'),
('Dewi Lestari, S.Pd.', 'Guru Kelas IV', 'https://placehold.co/400x400/9b59b6/ffffff?text=DL', 'Aktif dalam pengembangan metode pembelajaran inovatif berbasis teknologi.');

-- Data berita contoh
INSERT INTO announcements (title, content, category, image, is_published) VALUES
('Penerimaan Siswa Baru Tahun Ajaran 2025/2026', 'Segera daftarkan putra-putri Anda di SDN Laladon 03. Pendaftaran dibuka mulai bulan Januari 2025. Syarat dan ketentuan berlaku.', 'pengumuman', 'https://placehold.co/600x400/e67e22/ffffff?text=PPDB+2025', 1),
('Siswa SDN Laladon 03 Raih Juara Lomba Cerita Pendek', 'Selamat kepada siswa kami yang telah berhasil meraih Juara 1 dalam acara bercerita tingkat kecamatan. Prestasi yang membanggakan!', 'berita', 'https://placehold.co/600x400/2ecc71/ffffff?text=Juara+Lomba', 1),
('Jadwal Ujian Akhir Semester Genap 2025', 'Ujian Akhir Semester akan dilaksanakan pada tanggal 15–20 Juni 2025. Mohon siswa mempersiapkan diri dengan baik.', 'event', 'https://placehold.co/600x400/3498db/ffffff?text=Jadwal+Ujian', 1);

-- ============================================
--  TABEL GALERI
-- ============================================
CREATE TABLE IF NOT EXISTS galeri (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    judul      VARCHAR(255) NOT NULL,
    foto       VARCHAR(255) NOT NULL,
    kategori   VARCHAR(100) DEFAULT 'kegiatan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Data galeri contoh
INSERT INTO galeri (judul, foto, kategori) VALUES
('Upacara Bendera Hari Senin', 'https://placehold.co/800x600/1a237e/ffffff?text=Upacara+Bendera', 'kegiatan'),
('Lomba Mewarnai Tingkat SD', 'https://placehold.co/800x600/e65100/ffffff?text=Lomba+Mewarnai', 'prestasi'),
('Kegiatan Senam Pagi Bersama', 'https://placehold.co/800x600/1b5e20/ffffff?text=Senam+Pagi', 'kegiatan'),
('Wisuda Kelas VI Tahun 2024', 'https://placehold.co/800x600/4a148c/ffffff?text=Wisuda+2024', 'wisuda'),
('Juara Olimpiade Matematika', 'https://placehold.co/800x600/bf360c/ffffff?text=Olimpiade+MTK', 'prestasi'),
('Pentas Seni Akhir Tahun', 'https://placehold.co/800x600/006064/ffffff?text=Pentas+Seni', 'kegiatan'),
('Gotong Royong Kebersihan Sekolah', 'https://placehold.co/800x600/33691e/ffffff?text=Gotong+Royong', 'kegiatan'),
('Penerimaan Siswa Baru 2025', 'https://placehold.co/800x600/0d47a1/ffffff?text=PPDB+2025', 'kegiatan'),
('Juara Lomba Baca Puisi', 'https://placehold.co/800x600/880e4f/ffffff?text=Lomba+Puisi', 'prestasi');
