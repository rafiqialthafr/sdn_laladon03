<?php
include 'koneksi.php';
include 'header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row py-5">
            <div class="col-12 text-center">
                <h1 class="page-header-title">Hubungi Kami</h1>
                <p class="page-header-subtitle">Kami siap menjawab pertanyaan dan menerima masukan Anda</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5" style="background:#f8f9fc;">
    <div class="container">

        <!-- Info Cards Row -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="contact-info-card">
                    <div class="contact-icon-wrap gold">
                        <i data-lucide="map-pin" style="width:24px;height:24px;"></i>
                    </div>
                    <h5 class="contact-card-title">Alamat</h5>
                    <p class="contact-card-text">Gg. Amil No.1, Laladon, Kec. Ciomas,<br>Kabupaten Bogor, Jawa Barat 16610</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-info-card">
                    <div class="contact-icon-wrap blue">
                        <i data-lucide="phone" style="width:24px;height:24px;"></i>
                    </div>
                    <h5 class="contact-card-title">Telepon</h5>
                    <p class="contact-card-text">(0251) 1234567</p>
                    <p class="contact-card-text" style="margin-top:-0.5rem;">Senin – Jumat, 07.00 – 14.00 WIB</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-info-card">
                    <div class="contact-icon-wrap green">
                        <i data-lucide="mail" style="width:24px;height:24px;"></i>
                    </div>
                    <h5 class="contact-card-title">Email</h5>
                    <p class="contact-card-text">info@sdnlaladon03.sch.id</p>
                </div>
            </div>
        </div>

        <!-- Form & Map Row -->
        <div class="row g-4">

            <!-- Contact Form -->
            <div class="col-lg-6" style="align-self:flex-start;">
                <div class="contact-form-card">
                    <div class="section-title-wrapper text-start mb-4">
                        <span class="section-label">Pesan</span>
                        <h2 class="section-title" style="font-size:1.6rem;">Kirim Pesan Kepada Kami</h2>
                        <div class="section-divider" style="margin:0;"></div>
                    </div>

                    <?php
$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim(mysqli_real_escape_string($koneksi, $_POST['nama'] ?? ''));
    $email = trim(mysqli_real_escape_string($koneksi, $_POST['email'] ?? ''));
    $subjek = trim(mysqli_real_escape_string($koneksi, $_POST['subjek'] ?? ''));
    $pesan = trim(mysqli_real_escape_string($koneksi, $_POST['pesan'] ?? ''));

    if ($nama && $email && $subjek && $pesan) {
        $sql = "INSERT INTO contact_messages (nama, email, subjek, pesan) VALUES ('$nama', '$email', '$subjek', '$pesan')";
        if (mysqli_query($koneksi, $sql)) {
            $success = true;
        }
        else {
            $error = 'Gagal menyimpan pesan. Silakan coba lagi.';
        }
    }
    else {
        $error = 'Semua kolom wajib diisi.';
    }
}
?>

                    <?php if ($success): ?>
                        <div class="contact-success-msg">
                            <i data-lucide="check-circle" style="width:28px;height:28px;color:#15803d;"></i>
                            <div>
                                <strong>Pesan Terkirim!</strong>
                                <p style="margin:0;font-size:0.9rem;color:#6b7280;">Terima kasih, kami akan segera menghubungi Anda.</p>
                            </div>
                        </div>
                    <?php
endif; ?>

                    <?php if ($error): ?>
                        <div class="contact-error-msg">
                            <i data-lucide="alert-circle" style="width:20px;height:20px;"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php
endif; ?>

                    <form method="POST" action="contact.php" class="contact-form" novalidate>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="contact-label" for="nama">Nama Lengkap</label>
                                <input type="text" id="nama" name="nama" class="contact-input"
                                    placeholder="Nama Anda"
                                    value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="contact-label" for="email">Alamat Email</label>
                                <input type="email" id="email" name="email" class="contact-input"
                                    placeholder="email@contoh.com"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="contact-label" for="subjek">Subjek</label>
                                <input type="text" id="subjek" name="subjek" class="contact-input"
                                    placeholder="Perihal pesan Anda"
                                    value="<?php echo htmlspecialchars($_POST['subjek'] ?? ''); ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="contact-label" for="pesan">Pesan</label>
                                <textarea id="pesan" name="pesan" class="contact-input contact-textarea"
                                    placeholder="Tulis pesan Anda di sini..." required><?php echo htmlspecialchars($_POST['pesan'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-gold-primary w-100 justify-content-center" style="padding:0.9rem;">
                                    <i data-lucide="send" style="width:18px;height:18px;"></i>
                                    Kirim Pesan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Mini FAQ Card -->
                <div class="contact-faq-card mt-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i data-lucide="help-circle" style="width:20px;height:20px;color:#c8890a;"></i>
                        <h6 class="faq-title mb-0">Pertanyaan Umum</h6>
                    </div>
                    <div class="faq-list">
                        <details class="faq-item">
                            <summary class="faq-question">Bagaimana cara mendaftar sebagai siswa baru?</summary>
                            <p class="faq-answer">Pendaftaran siswa baru dilakukan setiap tahun ajaran baru. Hubungi kami atau kunjungi sekolah langsung untuk informasi lebih lanjut.</p>
                        </details>
                        <details class="faq-item">
                            <summary class="faq-question">Berapa lama biasanya pesan dibalas?</summary>
                            <p class="faq-answer">Pesan melalui formulir ini akan kami balas dalam 1×24 jam pada hari kerja. Untuk respons lebih cepat, gunakan WhatsApp.</p>
                        </details>
                        <details class="faq-item">
                            <summary class="faq-question">Apakah bisa berkunjung langsung ke sekolah?</summary>
                            <p class="faq-answer">Tentu! Anda bisa berkunjung pada hari dan jam operasional yang tertera. Disarankan membuat janji terlebih dahulu.</p>
                        </details>
                    </div>
                </div>
            </div>

            <!-- Map & Hours -->
            <div class="col-lg-6">

                <!-- Map -->
                <div class="contact-map-card mb-4">
                    <div class="section-title-wrapper text-start mb-3">
                        <span class="section-label">Lokasi</span>
                        <h2 class="section-title" style="font-size:1.6rem;">Temukan Kami di Sini</h2>
                        <div class="section-divider" style="margin:0;"></div>
                    </div>
                    <div class="map-embed-wrap">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.5034889805406!2d106.75638967355988!3d-6.5841596643603735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c4fcb2dbba71%3A0xddc59b73fb7e05da!2sSDN%20Laladon%2003!5e0!3m2!1sid!2sid!4v1771216995269!5m2!1sid!2sid"
                            width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Lokasi SDN Laladon 03 di Google Maps">
                        </iframe>
                    </div>
                </div>

                <!-- Jam Operasional -->
                <div class="contact-hours-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="contact-icon-wrap gold" style="width:44px;height:44px;flex-shrink:0;">
                            <i data-lucide="clock" style="width:20px;height:20px;"></i>
                        </div>
                        <h5 class="contact-card-title mb-0">Jam Operasional</h5>
                    </div>
                    <div class="hours-list">
                        <div class="hours-row">
                            <span class="hours-day">Senin – Kamis</span>
                            <span class="hours-time">07.00 – 14.00 WIB</span>
                        </div>
                        <div class="hours-row">
                            <span class="hours-day">Jumat</span>
                            <span class="hours-time">07.00 – 11.00 WIB</span>
                        </div>
                        <div class="hours-row">
                            <span class="hours-day">Sabtu</span>
                            <span class="hours-time">07.00 – 12.00 WIB</span>
                        </div>
                        <div class="hours-row closed">
                            <span class="hours-day">Minggu & Hari Libur</span>
                            <span class="hours-time">Tutup</span>
                        </div>
                    </div>
                    <!-- Social Media -->
                    <div class="mt-4 pt-3" style="border-top:1px solid #f3f4f6;">
                        <p class="contact-label mb-2">Ikuti Kami</p>
                        <div class="d-flex gap-2">
                            <a href="https://www.instagram.com/sdnlaladonnnn03" target="_blank" rel="noopener noreferrer"
                               class="contact-social-btn instagram" title="Instagram">
                                <i data-lucide="instagram" style="width:18px;height:18px;"></i>
                            </a>
                            <a href="https://www.youtube.com/@SDNLaladon03" target="_blank" rel="noopener noreferrer"
                               class="contact-social-btn youtube" title="YouTube">
                                <i data-lucide="youtube" style="width:18px;height:18px;"></i>
                            </a>
                            <a href="#" class="contact-social-btn tiktok" title="TikTok">
                                <i data-lucide="music" style="width:18px;height:18px;"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Inline Styles khusus halaman contact -->
<style>
/* ── Info Cards ── */
.contact-info-card {
    background: #fff;
    border-radius: 16px;
    padding: 2rem 1.75rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    height: 100%;
    transition: transform 0.3s, box-shadow 0.3s;
}
.contact-info-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.11);
}
.contact-icon-wrap {
    width: 56px; height: 56px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1.25rem;
}
.contact-icon-wrap.gold  { background: linear-gradient(135deg,#FFF3CD,#FFE082); color:#c8890a; }
.contact-icon-wrap.blue  { background: linear-gradient(135deg,#dbeafe,#bfdbfe); color:#1d4ed8; }
.contact-icon-wrap.green { background: linear-gradient(135deg,#dcfce7,#bbf7d0); color:#15803d; }
.contact-card-title { font-size:1.05rem; font-weight:700; color:#1a1a2e; margin-bottom:0.5rem; }
.contact-card-text  { font-size:0.9rem; color:#6b7280; line-height:1.6; margin:0; }

/* ── Form Card ── */
.contact-form-card {
    background: #fff;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07);
}
.contact-label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.4rem;
    letter-spacing: 0.3px;
}
.contact-input {
    width: 100%;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.7rem 1rem;
    font-size: 0.92rem;
    font-family: 'Poppins', sans-serif;
    color: #1a1a2e;
    background: #fafafa;
    transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
    outline: none;
}
.contact-input:focus {
    border-color: #FFD700;
    box-shadow: 0 0 0 3px rgba(255,215,0,0.18);
    background: #fff;
}
.contact-textarea {
    resize: vertical;
    min-height: 130px;
}
.contact-success-msg {
    display: flex; align-items: center; gap: 1rem;
    background: linear-gradient(135deg,#dcfce7,#bbf7d0);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.25rem;
    border: 1px solid #bbf7d0;
}
.contact-error-msg {
    display: flex; align-items: center; gap: 0.6rem;
    background: #fef2f2;
    color: #dc2626;
    border-radius: 10px;
    padding: 0.8rem 1rem;
    margin-bottom: 1rem;
    font-size: 0.88rem;
    font-weight: 500;
}

/* ── Map Card ── */
.contact-map-card {
    background: #fff;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07);
}
.map-embed-wrap {
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #f3f4f6;
}

/* ── Hours Card ── */
.contact-hours-card {
    background: #fff;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07);
}
.hours-list { display: flex; flex-direction: column; gap: 0.5rem; }
.hours-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.55rem 0.75rem;
    border-radius: 8px;
    background: #f9fafb;
    font-size: 0.875rem;
}
.hours-row.closed { background: #fef2f2; }
.hours-day  { font-weight: 600; color: #374151; }
.hours-time { color: #6b7280; font-weight: 500; }
.hours-row.closed .hours-time { color: #dc2626; font-weight: 600; }

/* ── Social Buttons ── */
.contact-social-btn {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    text-decoration: none;
    transition: transform 0.3s, box-shadow 0.3s;
    color: #fff;
}
.contact-social-btn:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.2); color: #fff; }
.contact-social-btn.instagram { background: linear-gradient(135deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); }
.contact-social-btn.youtube   { background: #ff0000; }
.contact-social-btn.tiktok    { background: #010101; }

/* ── WhatsApp Card ── */
.contact-whatsapp-card {
    background: linear-gradient(135deg, #e7fbe6 0%, #dcfce7 100%);
    border: 1.5px solid #bbf7d0;
    border-radius: 20px;
    padding: 1.5rem 1.75rem;
    position: relative;
    overflow: hidden;
}
.contact-whatsapp-card::before {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 110px; height: 110px;
    background: rgba(37,211,102,0.1);
    border-radius: 50%;
}
.wa-badge {
    display: inline-block;
    background: #25d366;
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 0.2rem 0.65rem;
    border-radius: 20px;
    margin-bottom: 0.85rem;
    text-transform: uppercase;
}
.wa-icon-wrap {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: #25d366;
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(37,211,102,0.35);
}
.wa-title {
    font-size: 1rem;
    font-weight: 700;
    color: #14532d;
    margin: 0 0 0.15rem;
}
.wa-sub {
    font-size: 0.78rem;
    color: #16a34a;
    margin: 0;
}
.btn-whatsapp {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: #25d366;
    color: #fff;
    font-weight: 700;
    font-size: 0.9rem;
    border-radius: 12px;
    padding: 0.75rem 1.25rem;
    text-decoration: none;
    transition: background 0.25s, transform 0.25s, box-shadow 0.25s;
    box-shadow: 0 4px 14px rgba(37,211,102,0.3);
}
.btn-whatsapp:hover {
    background: #128c7e;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(37,211,102,0.4);
}

/* ── FAQ Card ── */
.contact-faq-card {
    background: #fff;
    border-radius: 20px;
    padding: 1.5rem 1.75rem;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    border: 1.5px solid #fef9c3;
}
.faq-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #1a1a2e;
}
.faq-list { display: flex; flex-direction: column; gap: 0.5rem; }
.faq-item {
    border-radius: 10px;
    background: #fafafa;
    border: 1px solid #f3f4f6;
    overflow: hidden;
    transition: border-color 0.2s;
}
.faq-item[open] {
    border-color: #FFD700;
    background: #fffdf0;
}
.faq-question {
    cursor: pointer;
    padding: 0.7rem 1rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
    list-style: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    user-select: none;
}
.faq-question::-webkit-details-marker { display: none; }
.faq-question::after {
    content: '+';
    font-size: 1.1rem;
    font-weight: 700;
    color: #c8890a;
    transition: transform 0.2s;
    flex-shrink: 0;
}
.faq-item[open] .faq-question::after {
    transform: rotate(45deg);
}
.faq-answer {
    padding: 0 1rem 0.75rem;
    font-size: 0.82rem;
    color: #6b7280;
    line-height: 1.65;
    margin: 0;
}
</style>

<?php include 'footer.php'; ?>
