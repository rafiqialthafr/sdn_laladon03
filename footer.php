<style>
    /* ── FOOTER — Satu tema gold dengan beranda ── */
    :root {
        --footer-gold: #FFD700;
        --footer-gold-dark: #FFA500;
        --footer-bg: linear-gradient(165deg, #0a1228 0%, #06102e 35%, #050a1a 100%);
        --footer-bg-gold-tint: linear-gradient(165deg, rgba(255, 215, 0, 0.03) 0%, transparent 40%, rgba(255, 165, 0, 0.02) 100%);
    }
    footer.site-footer {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--footer-bg);
        color: #e2e8f0;
        padding-top: 4.5rem;
        padding-bottom: 0;
        position: relative;
        overflow: hidden;
    }
    footer.site-footer::after {
        content: '';
        position: absolute;
        inset: 0;
        background: var(--footer-bg-gold-tint);
        pointer-events: none;
    }
    footer.site-footer .container { position: relative; z-index: 1; }
    footer.site-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent 5%, var(--footer-gold) 30%, var(--footer-gold-dark) 50%, var(--footer-gold) 70%, transparent 95%);
        opacity: 0.9;
        z-index: 1;
    }

    /* Judul kolom — gold seperti beranda */
    .footer-col-title {
        color: var(--footer-gold);
        font-weight: 700;
        letter-spacing: 0.02em;
        margin-bottom: 1.25rem;
        padding-bottom: 0;
        border: none;
        text-shadow: 0 0 20px rgba(255, 215, 0, 0.15);
    }
    .footer-col-title::after { display: none; }

    /* Kontak — ikon gold */
    .footer-contact p {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        font-size: 0.9375rem;
        color: rgba(226, 232, 240, 0.9);
        margin-bottom: 0.875rem;
        line-height: 1.6;
    }
    .footer-contact p:last-of-type { margin-bottom: 0; }
    .footer-contact i {
        width: 18px;
        height: 18px;
        color: var(--footer-gold);
        flex-shrink: 0;
        margin-top: 3px;
    }

    /* Social — default gold ring, hover gold fill */
    .footer-social-wrap {
        display: flex;
        gap: 0.5rem;
        margin-top: 1.25rem;
    }
    .social-icon-btn {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(255, 215, 0, 0.06);
        border: 1px solid rgba(255, 215, 0, 0.2);
        color: var(--footer-gold);
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .social-icon-btn:hover {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(255, 165, 0, 0.15));
        border-color: var(--footer-gold);
        color: #1a1a2e;
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(255, 215, 0, 0.25);
    }
    .social-icon-btn.yt:hover { color: #fff; border-color: #ff4444; background: rgba(255, 68, 68, 0.85); }
    .social-icon-btn.tiktok:hover { color: #000; border-color: #00f2ea; background: rgba(0, 242, 234, 0.2); }

    /* Link list — hover gold */
    .footer-link-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .footer-link-list li { margin-bottom: 0.5rem; }
    .footer-link-list a {
        color: rgba(226, 232, 240, 0.85);
        text-decoration: none;
        font-size: 0.9375rem;
        font-weight: 500;
        transition: color 0.2s, padding-left 0.2s;
        display: inline-block;
    }
    .footer-link-list a:hover {
        color: var(--footer-gold);
        padding-left: 4px;
    }

    /* Map container — gold border hover */
    .map-container {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(255, 215, 0, 0.12);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 215, 0, 0.05);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .map-container:hover {
        border-color: rgba(255, 215, 0, 0.35);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.35), 0 0 24px rgba(255, 215, 0, 0.08);
    }
    .map-container iframe {
        display: block;
        filter: grayscale(0.1) contrast(1.02);
    }

    /* Pemisah konten & copyright */
    .footer-hr {
        border: none;
        height: 0;
        margin: 2.75rem 0 1.5rem;
    }

    /* Copyright — gold aksen */
    .footer-bottom {
        padding-bottom: 1.25rem;
    }
    .footer-copy {
        font-size: 0.8125rem;
        color: rgba(226, 232, 240, 0.55);
        margin: 0;
        font-weight: 500;
    }
    .footer-copy strong {
        color: var(--footer-gold);
        font-weight: 600;
    }

    /* Back to top — gold theme */
    .back-to-top {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(255, 215, 0, 0.08);
        border: 1px solid rgba(255, 215, 0, 0.25);
        color: var(--footer-gold);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .back-to-top:hover {
        background: linear-gradient(135deg, var(--footer-gold), var(--footer-gold-dark));
        border-color: var(--footer-gold);
        color: #1a1a2e;
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(255, 215, 0, 0.4);
    }

    /* Responsive — diseragamkan dengan sistem baru */
    @media (max-width: 1024px) {
        footer.site-footer { padding-top: 3rem; }
    }
    @media (max-width: 767.98px) {
        footer.site-footer { padding-top: 2.5rem; }
        footer.site-footer,
        footer.site-footer p, footer.site-footer h4,
        footer.site-footer ul, footer.site-footer li,
        footer.site-footer .text-center, footer.site-footer .text-md-end {
            text-align: left !important;
        }
        footer.site-footer .d-flex:not(.footer-social-wrap):not(.back-to-top) { justify-content: flex-start !important; }
        .map-container iframe { height: 200px !important; }
        footer.site-footer .row > div { margin-bottom: 1.5rem; }
        footer.site-footer .row > div:last-child { margin-bottom: 0; }
        footer.site-footer .footer-bottom .col-md-4 {
            text-align: right !important;
            display: flex !important;
            justify-content: flex-end !important;
            margin-top: 0.75rem;
        }
    }
    @media (max-width: 480px) {
        footer.site-footer { padding-top: 2rem; }
        .map-container iframe { height: 165px !important; }
        .footer-col-title {letter-spacing: 0.075em; }
        .footer-copy { font-size: 0.75rem; }
    }
</style>

<footer class="site-footer text-white">
    <div class="container">
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
                        <span>info@sdnlaladon03.sch.id</span>
                    </p>
                </div>
                <div class="footer-social-wrap">
                    <a href="https://www.instagram.com/sdnlaladonnnn03?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" rel="noopener noreferrer" class="social-icon-btn" title="Instagram">
                        <i data-lucide="instagram" style="width:18px;height:18px;"></i>
                    </a>
                    <a href="https://www.youtube.com/@SDNLaladon03" target="_blank" rel="noopener noreferrer" class="social-icon-btn yt" title="YouTube">
                        <i data-lucide="youtube" style="width:18px;height:18px;"></i>
                    </a>
                    <a href="#" target="_blank" rel="noopener noreferrer" class="social-icon-btn tiktok" title="TikTok">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="18" height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/>
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
                        width="100%"
                        height="220"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Lokasi SDN Laladon 03 di Google Maps">
                    </iframe>
                </div>
            </div>

        </div>

        <hr class="footer-hr">

        <div class="row align-items-center footer-bottom">
            <div class="col-md-8 text-md-start">
                <p class="footer-copy">
                    © 2026 <strong>Directed By SDN Laladon 03</strong> · All Rights Reserved
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                <a href="#" class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" title="Kembali ke atas" aria-label="Kembali ke atas halaman">
                    <i data-lucide="chevron-up" style="width:18px;height:18px;" aria-hidden="true"></i>
                </a>
            </div>
        </div>

    </div>
</footer>

<!-- Bootstrap 5 JS Bundle (defer = tidak blocking render) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<!-- Lucide Icons (defer) -->
<script src="https://cdn.jsdelivr.net/npm/lucide@0.473.0/dist/umd/lucide.min.js"></script>

<script>
    lucide.createIcons();
</script>