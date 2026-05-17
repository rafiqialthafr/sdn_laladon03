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

    $query  = "SELECT * FROM admin WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
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
    <link rel="icon" type="image/png" href="img/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — SDN Laladon 03</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="login-page">

<!-- LEFT DECORATIVE PANEL -->
<div class="login-left">
    <div class="login-logo-wrap">
        <img src="img/logo.png" alt="Logo SDN Laladon 03">
    </div>
    <div class="login-school-name">SIM <span>Laladon 03</span></div>
    <div class="login-school-sub">Sistem Informasi Manajemen Terpadu</div>

    <ul class="login-features">
        <li>
            <div class="feat-icon"><i data-lucide="layout-dashboard" style="width:18px;height:18px;"></i></div>
            Kelola Berita &amp; Pengumuman Secara Real-time
        </li>
        <li>
            <div class="feat-icon"><i data-lucide="users" style="width:18px;height:18px;"></i></div>
            Manajemen Data Guru, Staf &amp; Siswa
        </li>
        <li>
            <div class="feat-icon"><i data-lucide="shield-check" style="width:18px;height:18px;"></i></div>
            Akses Admin Terenkripsi &amp; Aman
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

        <form action="" method="POST" autocomplete="off" id="loginForm">

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

            <button type="submit" name="login" class="btn-login" id="submitBtn">
                <i data-lucide="log-in" style="width:18px;height:18px;"></i>
                <span>Masuk ke Dashboard</span>
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

    document.getElementById('username').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('password').focus();
        }
    });
</script>
</body>
</html>