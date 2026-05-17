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
                <h1 class="page-header-title"><i data-lucide="mail"
                        style="width:36px;height:36px;vertical-align:middle;margin-right:10px;margin-bottom:6px;"></i>Hubungi
                    Kami</h1>
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
                    <p class="contact-card-text">Gg. Amil No.1, Laladon, Kec. Ciomas,<br>Kabupaten Bogor, Jawa Barat
                        16610</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-info-card">
                    <div class="contact-icon-wrap blue">
                        <i data-lucide="phone" style="width:24px;height:24px;"></i>
                    </div>
                    <h5 class="contact-card-title">Telepon</h5>
                    <p class="contact-card-text">0895-3149-7117</p>
                    <p class="contact-card-text" style="margin-top:-0.5rem;">Senin – Jumat, 07.00 – 14.00 WIB</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-info-card">
                    <div class="contact-icon-wrap green">
                        <i data-lucide="mail" style="width:24px;height:24px;"></i>
                    </div>
                    <h5 class="contact-card-title">Email</h5>
                    <p class="contact-card-text">sdnlaladon@gmail.com</p>
                </div>
            </div>
        </div>

        <!-- Form & Map Row -->
        <div class="row g-4">

            <!-- Form WhatsApp -->
            <div class="col-lg-6" style="align-self:flex-start;">
                <div class="contact-form-card">
                    <div class="section-title-wrapper text-start mb-4">
                        <span class="section-label">Pesan</span>
                        <h2 class="section-title" style="font-size:1.6rem;">Kirim Pesan via WhatsApp</h2>
                        <div class="section-divider" style="margin:0;"></div>
                        <p style="margin-top:0.75rem;font-size:0.88rem;color:#6b7280;">Pesan Anda akan langsung terkirim
                            ke WhatsApp kami. Lebih cepat & mudah!</p>
                    </div>

                    <!-- notifikasi sukses (muncul via JS) -->
                    <div id="wa-success" class="contact-success-msg" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                            stroke="#15803d" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <div>
                            <strong>WhatsApp terbuka!</strong>
                            <p style="margin:0;font-size:0.9rem;color:#6b7280;">Cek aplikasi WhatsApp Anda dan kirim
                                pesannya.</p>
                        </div>
                    </div>

                    <!-- notifikasi error (muncul via JS) -->
                    <div id="wa-error" class="contact-error-msg" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <span id="wa-error-text">Semua kolom wajib diisi.</span>
                    </div>

                    <form id="wa-contact-form" class="contact-form" novalidate>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="contact-label" for="nama">Nama Lengkap</label>
                                <input type="text" id="nama" name="nama" class="contact-input" placeholder="Nama Anda"
                                    required>
                            </div>
                            <div class="col-sm-6">
                                <label class="contact-label" for="wa_pengirim">Nomor WhatsApp Anda</label>
                                <input type="tel" id="wa_pengirim" name="wa_pengirim" class="contact-input"
                                    placeholder="08xxxxxxxxxx" required>
                            </div>
                            <div class="col-12">
                                <label class="contact-label" for="subjek">Subjek</label>
                                <input type="text" id="subjek" name="subjek" class="contact-input"
                                    placeholder="Perihal pesan Anda" required>
                            </div>
                            <div class="col-12">
                                <label class="contact-label" for="pesan">Pesan</label>
                                <textarea id="pesan" name="pesan" class="contact-input contact-textarea"
                                    placeholder="Tulis pesan Anda di sini..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" id="btn-kirim-wa" class="btn-wa-submit w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                    </svg>
                                    Kirim via WhatsApp
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
                            <p class="faq-answer">Pendaftaran siswa baru dilakukan setiap tahun ajaran baru. Hubungi
                                kami via WhatsApp atau kunjungi sekolah langsung untuk informasi lebih lanjut.</p>
                        </details>
                        <details class="faq-item">
                            <summary class="faq-question">Berapa lama pesan WhatsApp dibalas?</summary>
                            <p class="faq-answer">Pesan WhatsApp umumnya kami balas dalam 1–2 jam pada jam operasional
                                (Senin–Jumat, 07.00–14.00 WIB).</p>
                        </details>
                        <details class="faq-item">
                            <summary class="faq-question">Apakah bisa berkunjung langsung ke sekolah?</summary>
                            <p class="faq-answer">Tentu! Anda bisa berkunjung pada hari dan jam operasional yang
                                tertera. Disarankan membuat janji dulu via WhatsApp.</p>
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
                            referrerpolicy="no-referrer-when-downgrade" title="Lokasi SDN Laladon 03 di Google Maps">
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
                            <a href="https://www.instagram.com/sdnlaladonnnn03" target="_blank"
                                rel="noopener noreferrer" class="contact-social-btn instagram" title="Instagram">
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
<script>
    (function () {
        // nomor WA tujuan sekolah (format internasional tanpa +)
        var WA_NUMBER = '6289531497117';

        var form = document.getElementById('wa-contact-form');
        var success = document.getElementById('wa-success');
        var errBox = document.getElementById('wa-error');
        var errTxt = document.getElementById('wa-error-text');

        function showError(msg) {
            errTxt.textContent = msg;
            errBox.style.display = 'flex';
            success.style.display = 'none';
            errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function hideAlerts() {
            errBox.style.display = 'none';
            success.style.display = 'none';
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            hideAlerts();

            var nama = document.getElementById('nama').value.trim();
            var wa = document.getElementById('wa_pengirim').value.trim();
            var subj = document.getElementById('subjek').value.trim();
            var pesan = document.getElementById('pesan').value.trim();

            if (!nama) return showError('Nama lengkap wajib diisi.');
            if (!wa) return showError('Nomor WhatsApp Anda wajib diisi.');
            if (!subj) return showError('Subjek pesan wajib diisi.');
            if (!pesan) return showError('Isi pesan wajib diisi.');

            var teks =
                '*Pesan dari Website SDN Laladon 03*%0A' +
                '-----------------------------------%0A' +
                '*Nama*: ' + encodeURIComponent(nama) + '%0A' +
                '*No. WA*: ' + encodeURIComponent(wa) + '%0A' +
                '*Subjek*: ' + encodeURIComponent(subj) + '%0A' +
                '*Pesan*:%0A' + encodeURIComponent(pesan);

            var url = 'https://wa.me/' + WA_NUMBER + '?text=' + teks;

            // buka WA di tab baru
            window.open(url, '_blank', 'noopener,noreferrer');

            // tampilkan notifikasi sukses & reset form
            success.style.display = 'flex';
            form.reset();
            success.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    })();
</script>

<?php include 'footer.php'; ?>