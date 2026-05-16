<footer class="site-footer text-white mt-auto">
    <div class="container">
        <!-- Brand / Logo Header -->
        <div class="footer-brand-wrap d-flex align-items-center gap-3 mb-4 pb-4">
            <img src="img/logo.png" alt="Logo SDN Laladon 03" width="56" height="56" class="footer-logo-img">
            <div>
                <div class="footer-brand-title">SDN LALADON 03</div>
                <div class="footer-brand-sub">Sekolah Ramah Anak · Terakreditasi A</div>
            </div>
        </div>

        <div class="row">

            <!-- Kolom 1: Hubungi Kami -->
            <div class="col-lg-4 col-md-12 mb-4">
                <h4 class="footer-col-title">Hubungi Kami</h4>
                <div class="footer-contact">
                    <p>
                        <i data-lucide="map-pin"></i>
                        <span>Gg. Amil No.1, Laladon, Kec. Ciomas, Kabupaten Bogor, Jawa Barat 16610</span>
                    </p>
                    <p>
                        <i data-lucide="phone"></i>
                        <span>(0251) 1234567</span>
                    </p>
                    <p>
                        <i data-lucide="mail"></i>
                        <span>sdnlaladon@gmail.com</span>
                    </p>
                </div>
                <div class="footer-social-wrap">
                    <a href="https://www.instagram.com/sdnlaladonnnn03?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                        target="_blank" rel="noopener noreferrer" class="social-icon-btn" title="Instagram">
                        <i data-lucide="instagram" style="width:18px;height:18px;"></i>
                    </a>
                    <a href="https://www.youtube.com/@SDNLaladon03" target="_blank" rel="noopener noreferrer"
                        class="social-icon-btn yt" title="YouTube">
                        <i data-lucide="youtube" style="width:18px;height:18px;"></i>
                    </a>
                    <a href="https://www.tiktok.com/@sdn.laladon.03" target="_blank" rel="noopener noreferrer"
                        class="social-icon-btn tiktok" title="TikTok">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Kolom 2: Link -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h4 class="footer-col-title">Menu</h4>
                <ul class="footer-link-list">
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="sejarah.php">Profil Guru</a></li>
                    <li><a href="berita.php">Berita Sekolah</a></li>
                    <li><a href="fasilitas.php">Fasilitas</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Peta -->
            <div class="col-lg-6 col-md-6 mb-4">
                <h4 class="footer-col-title">Lokasi</h4>
                <div class="map-container">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.5034889805406!2d106.75638967355988!3d-6.5841596643603735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c4fcb2dbba71%3A0xddc59b73fb7e05da!2sSDN%20Laladon%2003!5e0!3m2!1sid!2sid!4v1771216995269!5m2!1sid!2sid"
                        width="100%" height="220" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" title="Lokasi SDN Laladon 03 di Google Maps">
                    </iframe>
                </div>
            </div>

        </div>

        <hr class="footer-hr">

        <div class="row align-items-center footer-bottom text-center text-md-start">
            <div class="col-md-8 order-2 order-md-1">
                <p class="footer-copy mb-0">
                    © 2026 <strong>SDN Laladon 03</strong> · All Rights Reserved
                </p>
            </div>
            <div class="col-md-4 text-md-end order-1 order-md-2 mb-4 mb-md-0">
                <a href="#" class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;"
                    title="Kembali ke atas" aria-label="Kembali ke atas halaman">
                    <i data-lucide="chevron-up" style="width:18px;height:18px;" aria-hidden="true"></i>
                </a>
            </div>
        </div>

    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<!-- Lucide Icons -->
<script src="https://cdn.jsdelivr.net/npm/lucide@0.473.0/dist/umd/lucide.min.js" defer></script>
<!-- AOS Animation JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi Lucide Icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        // Inisialisasi AOS
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                once: true,
                offset: 50,
                easing: 'ease-out-cubic'
            });
        }
    });
</script>