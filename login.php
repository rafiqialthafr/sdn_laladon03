<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['admin'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = '';
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query  = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        // Support both plain text and hashed password
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['admin']    = true;
            $_SESSION['username'] = $username;
            header("Location: admin_dashboard.php");
            exit;
        }
    }
    $error = "Username atau password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — SDN Laladon 03</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f0f2f5;
        }

        /* ── LEFT PANEL ── */
        .login-left {
            flex: 1;
            background: linear-gradient(175deg, #0f0f1a 0%, #1a1a2e 45%, #16213e 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Decorative circles */
        .login-left::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,215,0,0.08) 0%, transparent 70%);
            top: -100px; left: -100px;
        }
        .login-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,165,0,0.06) 0%, transparent 70%);
            bottom: -80px; right: -80px;
        }

        /* Gold accent ring */
        .login-logo-wrap {
            width: 90px; height: 90px;
            border-radius: 24px;
            background: linear-gradient(135deg, #FFD700, #FFA500);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 32px rgba(255,215,0,0.3);
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }
        .login-logo-wrap img {
            width: 64px; height: 64px;
            border-radius: 16px;
            object-fit: cover;
        }

        .login-school-name {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            text-align: center;
            letter-spacing: 2px;
            font-family: Impact, 'Arial Narrow Bold', sans-serif;
            background: linear-gradient(135deg, #FFD700, #FFA500);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative; z-index: 1;
            margin-bottom: 0.5rem;
        }
        .login-school-sub {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
            text-align: center;
            position: relative; z-index: 1;
            margin-bottom: 3rem;
        }

        /* Features list */
        .login-features {
            list-style: none;
            width: 100%;
            max-width: 280px;
            position: relative; z-index: 1;
        }
        .login-features li {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            color: rgba(255,255,255,0.75);
            font-size: 0.875rem;
            padding: 0.65rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .login-features li:last-child { border-bottom: none; }
        .login-features li .feat-icon {
            width: 34px; height: 34px;
            border-radius: 9px;
            background: rgba(255,215,0,0.1);
            border: 1px solid rgba(255,215,0,0.2);
            display: flex; align-items: center; justify-content: center;
            color: #FFD700;
            flex-shrink: 0;
        }

        /* decorative gold line */
        .login-gold-bar {
            width: 60px; height: 4px;
            border-radius: 4px;
            background: linear-gradient(90deg, #FFD700, #FFA500);
            margin: 2rem auto 0;
            position: relative; z-index: 1;
        }

        /* ── RIGHT PANEL ── */
        .login-right {
            width: 480px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
            background: #f0f2f5;
        }

        .login-card {
            width: 100%;
            background: #fff;
            border-radius: 24px;
            padding: 2.75rem 2.5rem;
            box-shadow: 0 8px 40px rgba(0,0,0,0.08);
            animation: slideUp 0.5s cubic-bezier(0.23, 1, 0.32, 1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 0.35rem;
        }
        .login-subtitle {
            font-size: 0.875rem;
            color: #9ca3af;
            margin-bottom: 2rem;
        }

        /* Error alert */
        .login-error {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            color: #dc2626;
            font-weight: 500;
            animation: shake 0.4s ease;
        }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%,60%  { transform: translateX(-6px); }
            40%,80%  { transform: translateX(6px); }
        }

        /* Input group */
        .login-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.4rem;
            letter-spacing: 0.3px;
        }
        .input-wrap {
            position: relative;
            margin-bottom: 1.25rem;
        }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
            display: flex;
        }
        .login-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 0.8rem 1rem 0.8rem 2.75rem;
            font-size: 0.92rem;
            font-family: 'Poppins', sans-serif;
            color: #1a1a2e;
            background: #fafafa;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
            outline: none;
        }
        .login-input:focus {
            border-color: #FFD700;
            box-shadow: 0 0 0 3px rgba(255,215,0,0.15);
            background: #fff;
        }

        /* Toggle password */
        .toggle-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            display: flex;
            padding: 0;
            transition: color 0.2s;
        }
        .toggle-pass:hover { color: #FFA500; }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: 0.9rem;
            background: #FFD700;
            color: #3d1f00;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            margin-top: 0.5rem;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
            background: #FFA500;
        }
        .btn-login:active { transform: scale(0.98); }

        /* Back link */
        .login-back {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #9ca3af;
            text-decoration: none;
            transition: color 0.2s;
        }
        .login-back:hover { color: #FFA500; }

        /* Divider */
        .login-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0 1.25rem;
            color: #d1d5db;
            font-size: 0.75rem;
        }
        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        /* Responsive: hide left on small screens */
        @media (max-width: 768px) {
            .login-left { display: none; }
            .login-right { width: 100%; padding: 1.5rem; }
            .login-card  { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>

<!-- LEFT DECORATIVE PANEL -->
<div class="login-left">
    <div class="login-logo-wrap">
        <img src="img/logo.png" alt="Logo SDN Laladon 03">
    </div>
    <div class="login-school-name">SDN LALADON 03</div>
    <div class="login-school-sub">Sekolah Ramah Anak · Bogor, Jawa Barat</div>

    <ul class="login-features">
        <li>
            <div class="feat-icon"><i data-lucide="layout-dashboard" style="width:16px;height:16px;"></i></div>
            Kelola Berita &amp; Pengumuman
        </li>
        <li>
            <div class="feat-icon"><i data-lucide="users" style="width:16px;height:16px;"></i></div>
            Manajemen Data Guru &amp; Staf
        </li>
        <li>
            <div class="feat-icon"><i data-lucide="mail" style="width:16px;height:16px;"></i></div>
            Lihat Pesan Masuk
        </li>
        <li>
            <div class="feat-icon"><i data-lucide="shield-check" style="width:16px;height:16px;"></i></div>
            Akses Aman &amp; Terproteksi
        </li>
    </ul>
    <div class="login-gold-bar"></div>
</div>

<!-- RIGHT FORM PANEL -->
<div class="login-right">
    <div class="login-card">

        <h1 class="login-title">Selamat Datang 👋</h1>
        <p class="login-subtitle">Masuk ke panel admin SDN Laladon 03</p>

        <?php if ($error): ?>
        <div class="login-error">
            <i data-lucide="alert-circle" style="width:18px;height:18px;flex-shrink:0;"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form action="" method="POST" autocomplete="off" novalidate>

            <!-- Username -->
            <label class="login-label" for="username">Username</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <i data-lucide="user" style="width:17px;height:17px;"></i>
                </span>
                <input type="text" id="username" name="username" class="login-input"
                    placeholder="Masukkan username"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                    required autofocus>
            </div>

            <!-- Password -->
            <label class="login-label" for="password">Password</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <i data-lucide="lock" style="width:17px;height:17px;"></i>
                </span>
                <input type="password" id="password" name="password" class="login-input"
                    placeholder="Masukkan password" required>
                <button type="button" class="toggle-pass" onclick="togglePass()" id="togglePassBtn" title="Tampilkan password">
                    <i data-lucide="eye" style="width:17px;height:17px;" id="eyeIcon"></i>
                </button>
            </div>

            <button type="submit" name="login" class="btn-login">
                <i data-lucide="log-in" style="width:18px;height:18px;"></i>
                Masuk ke Dashboard
            </button>
        </form>

        <div class="login-divider">atau</div>

        <a href="index.php" class="login-back">
            <i data-lucide="arrow-left" style="width:15px;height:15px;"></i>
            Kembali ke Halaman Utama
        </a>
    </div>
</div>

<script>
    lucide.createIcons();

    function togglePass() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }
</script>
</body>
</html>
