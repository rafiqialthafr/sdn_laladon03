<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="no-referrer-when-downgrade">

    <!-- ═══ Primary SEO ═══ -->
    <title>SDN Laladon 03 - Sekolah Ramah Anak</title>
    <meta name="description"
        content="Website resmi SDN Laladon 03 - Sekolah Dasar Negeri terakreditasi A di Kecamatan Ciomas, Kabupaten Bogor, Jawa Barat. Mewujudkan generasi cerdas, kreatif, berkarakter, dan berprestasi.">
    <meta name="keywords"
        content="SDN Laladon 03, SDN Laladon, SD Negeri Ciomas, Sekolah Bogor, Akreditasi A, Sekolah Ramah Anak">
    <meta name="author" content="SDN Laladon 03">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="http://localhost/laladon/">

    <!-- ═══ Open Graph / Facebook ═══ -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="http://localhost/laladon/">
    <meta property="og:title" content="SDN Laladon 03 - Sekolah Ramah Anak | Terakreditasi A">
    <meta property="og:description"
        content="Website resmi SDN Laladon 03 - Sekolah Dasar Negeri terakreditasi A di Kecamatan Ciomas, Kabupaten Bogor, Jawa Barat.">
    <meta property="og:image" content="http://localhost/laladon/logo.png">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="SDN Laladon 03">

    <!-- ═══ Twitter Card ═══ -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="SDN Laladon 03 - Sekolah Ramah Anak">
    <meta name="twitter:description"
        content="Website resmi SDN Laladon 03, Sekolah Dasar Negeri terakreditasi A di Kabupaten Bogor, Jawa Barat.">
    <meta name="twitter:image" content="http://localhost/laladon/logo.png">

    <!-- ═══ Favicon ═══ -->
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="apple-touch-icon" href="img/logo.png">

    <!-- ═══ DNS Prefetch & Preconnect (Performance) ═══ -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://unpkg.com">

    <!-- ═══ Preload resource paling kritis (LCP) ═══ -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="css/main.css" as="style">

    <!-- ═══ Bootstrap CSS ═══ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ═══ Google Fonts ═══ -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Barlow+Condensed:wght@700;800;900&display=swap"
        rel="stylesheet">

    <!-- ═══ FOUT Prevention: Hide page until fonts ready ═══ -->
    <script>
        // Reveal page when fonts are ready, with 2s safety timeout
        (function () {
            function reveal() {
                if (document.body) document.body.classList.add('fonts-loaded');
                else document.addEventListener('DOMContentLoaded', function () { document.body.classList.add('fonts-loaded'); });
            }
            var timeout = setTimeout(reveal, 2000);
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(function () {
                    clearTimeout(timeout);
                    reveal();
                });
            } else {
                clearTimeout(timeout);
                window.addEventListener('load', reveal);
            }
        })();
    </script>

    <!-- ═══ Custom CSS ═══ -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="css/responsive.css">

    <!-- ═══ AOS Animation CSS ═══ -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- ═══ JSON-LD Structured Data (SEO schema.org) ═══ -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "School",
      "name": "SDN Laladon 03",
      "alternateName": "Sekolah Dasar Negeri Laladon 03",
      "url": "http://localhost/laladon/",
      "logo": "http://localhost/laladon/logo.png",
      "image": "http://localhost/laladon/logo.png",
      "description": "Sekolah Dasar Negeri terakreditasi A di Kecamatan Ciomas, Kabupaten Bogor, Jawa Barat.",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Gg. Amil No.1, Laladon",
        "addressLocality": "Ciomas",
        "addressRegion": "Jawa Barat",
        "postalCode": "16610",
        "addressCountry": "ID"
      },
      "telephone": "(0251) 1234567",
      "email": "info@sdnlaladon03.sch.id",
      "sameAs": [
        "https://www.instagram.com/sdnlaladonnnn03",
        "https://www.youtube.com/@SDNLaladon03"
      ]
    }
    </script>
    <!-- ═══ Lucide Icons ═══ -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>

<body id="laladon-app" class="laladon-app d-flex flex-column min-vh-100">

    <!-- Main Navbar -->
    <nav id="mainNavbar" class="main-navbar navbar navbar-expand-lg navbar-light bg-white sticky-top py-2"
        role="navigation" aria-label="Navigasi Utama">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php"
                aria-label="SDN Laladon 03 - Halaman Beranda">
                <img src="img/logo.png" alt="Logo SDN Laladon 03" width="40" height="40" class="me-2">
                <div>
                    <span class="brand-text d-none d-md-inline-block">SDN LALADON 03</span>
                </div>
            </a>
            <!-- Tombol Daftar mini — hanya tampil di mobile, di luar hamburger -->
            <a href="daftar_ppdb.php" class="navbar-daftar-mobile d-lg-none" aria-label="Daftar PPDB">
                <i data-lucide="clipboard-list" style="width:15px;height:15px;" aria-hidden="true"></i>
                Daftar
            </a>
            <button class="hamburger-btn d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-expanded="false"
                aria-label="Buka atau tutup menu navigasi">
                <span class="hb-bar hb-top" aria-hidden="true"></span>
                <span class="hb-bar hb-mid" aria-hidden="true"></span>
                <span class="hb-bar hb-bot" aria-hidden="true"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i data-lucide="home" class="me-2" aria-hidden="true"></i>Beranda
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false" aria-haspopup="true">
                            <i data-lucide="graduation-cap" class="me-2" aria-hidden="true"></i>Profil
                        </a>
                        <ul class="dropdown-menu" aria-label="Menu Profil">
                            <li><a class="dropdown-item" href="sejarah.php"><i data-lucide="compass" class="me-2"
                                        aria-hidden="true"></i>Sejarah Singkat</a></li>
                            <li><a class="dropdown-item" href="visimisi.php"><i data-lucide="rocket" class="me-2"
                                        aria-hidden="true"></i>Visi Misi</a></li>
                            <li><a class="dropdown-item" href="kepala_sekolah.php"><i data-lucide="user-round-cog"
                                        class="me-2" aria-hidden="true"></i>Kepala Sekolah</a></li>
                            <li><a class="dropdown-item" href="guru_staf.php"><i data-lucide="users" class="me-2"
                                        aria-hidden="true"></i>Guru dan Staf</a></li>
                            <li><a class="dropdown-item" href="fasilitas.php"><i data-lucide="building-2" class="me-2"
                                        aria-hidden="true"></i>Fasilitas</a></li>
                            <li><a class="dropdown-item" href="galeri.php"><i data-lucide="layout-dashboard"
                                        class="me-2" aria-hidden="true"></i>Galeri</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="berita.php">
                            <i data-lucide="newspaper" class="me-2" aria-hidden="true"></i>Berita
                        </a>
                    </li>
        

                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
                            <i data-lucide="mail" class="me-2" aria-hidden="true"></i>Kontak
                        </a>
                    </li>

                    <li class="nav-item ms-2 d-none d-lg-block">
                        <a class="nav-link nav-cta-daftar" href="daftar_ppdb.php">
                            <i data-lucide="clipboard-list" class="me-2" aria-hidden="true"></i>Daftar        
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>



</body>
