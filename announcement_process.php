<?php
include 'koneksi.php';

if (isset($_POST['save'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $is_published = $_POST['is_published'];
    $image_url = "https://placehold.co/600x400/34495e/ffffff?text=" . urlencode($title);

    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Format file tidak didukung! Hanya JPG, JPEG, PNG, dan WebP.'); window.history.back();</script>";
            exit;
        }
        if ($_FILES['image']['size'] > 1 * 1024 * 1024) {
            echo "<script>alert('Ukuran file terlalu besar! Maksimal 1MB.'); window.history.back();</script>";
            exit;
        }
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $target_file = $target_dir . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_url = $target_file;
    }

    $query = "INSERT INTO berita (title, content, category, image, is_published) VALUES ('$title', '$content', '$category', '$image_url', '$is_published')";
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin_berita.php?success=saved");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $is_published = $_POST['is_published'];
    $image_url = $_POST['existing_image'];

    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Format file tidak didukung! Hanya JPG, JPEG, PNG, dan WebP.'); window.history.back();</script>";
            exit;
        }
        if ($_FILES['image']['size'] > 1 * 1024 * 1024) {
            echo "<script>alert('Ukuran file terlalu besar! Maksimal 1MB.'); window.history.back();</script>";
            exit;
        }
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $target_file = $target_dir . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_url = $target_file;
    }

    $query = "UPDATE announcements SET title='$title', content='$content', category='$category', image='$image_url', is_published='$is_published' WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin_berita.php?success=saved");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM berita WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin_berita.php?success=deleted");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
