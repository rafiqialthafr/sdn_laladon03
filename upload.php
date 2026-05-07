<?php
/**
 * Upload Gambar - SDN Laladon 03
 * 
 * Memproses upload gambar ke folder 'uploads' dengan validasi:
 * - Ekstensi: jpg, jpeg, png
 * - Ukuran maksimal: 1MB
 * - Nama file unik menggunakan time()
 */

// Pastikan folder uploads ada
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Cek apakah ada file yang diupload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gambar'])) {

    $file     = $_FILES['gambar'];
    $fileName = $file['name'];
    $fileTmp  = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    // Ambil ekstensi file (lowercase)
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // 1. Validasi ekstensi
    $allowedExt = ['jpg', 'jpeg', 'png'];
    if (!in_array($ext, $allowedExt)) {
        echo "<script>
            alert('Error: Format file tidak didukung! Hanya JPG, JPEG, dan PNG yang diperbolehkan.');
            window.history.back();
        </script>";
        exit;
    }

    // 2. Validasi ukuran file (maksimal 1MB = 1048576 bytes)
    $maxSize = 1 * 1024 * 1024; // 1MB
    if ($fileSize > $maxSize) {
        echo "<script>
            alert('Error: Ukuran file terlalu besar! Maksimal 1MB.');
            window.history.back();
        </script>";
        exit;
    }

    // 3. Cek error upload
    if ($fileError !== UPLOAD_ERR_OK) {
        echo "<script>
            alert('Error: Terjadi kesalahan saat upload file. Silakan coba lagi.');
            window.history.back();
        </script>";
        exit;
    }

    // 4. Buat nama file unik dengan time()
    $newFileName = time() . '.' . $ext;
    $destination = $uploadDir . $newFileName;

    // 5. Pindahkan file ke folder uploads
    if (move_uploaded_file($fileTmp, $destination)) {
        echo "<script>
            alert('Berhasil Upload');
            window.location.href = 'index.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Error: Gagal memindahkan file. Silakan coba lagi.');
            window.history.back();
        </script>";
        exit;
    }

} else {
    // Jika diakses langsung tanpa POST, arahkan ke halaman utama
    header('Location: index.php');
    exit;
}
?>
