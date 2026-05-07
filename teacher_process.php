<?php
include 'koneksi.php';

// Handle Save/Insert
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $bio = '';
    $photo_url = "https://placehold.co/400x400/34495e/ffffff?text=" . urlencode($name); // Default placeholder
    
    if (!empty($_FILES['photo']['name'])) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Format file tidak didukung! Hanya JPG, JPEG, dan PNG.'); window.history.back();</script>";
            exit;
        }
        if ($_FILES['photo']['size'] > 1 * 1024 * 1024) {
            echo "<script>alert('Ukuran file terlalu besar! Maksimal 1MB.'); window.history.back();</script>";
            exit;
        }
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $target_file = $target_dir . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
        $photo_url = $target_file;
    }

    $query = "INSERT INTO teachers (name, position, photo, bio) VALUES ('$name', '$position', '$photo_url', '$bio')";
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin_guru.php?success=saved");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
}

// Handle Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $bio = '';
    $photo_url = $_POST['existing_photo'];

    if (!empty($_FILES['photo']['name'])) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Format file tidak didukung! Hanya JPG, JPEG, dan PNG.'); window.history.back();</script>";
            exit;
        }
        if ($_FILES['photo']['size'] > 1 * 1024 * 1024) {
            echo "<script>alert('Ukuran file terlalu besar! Maksimal 1MB.'); window.history.back();</script>";
            exit;
        }
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $target_file = $target_dir . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
        $photo_url = $target_file;
    }

    $query = "UPDATE teachers SET name='$name', position='$position', photo='$photo_url', bio='$bio' WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin_guru.php?success=saved");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM teachers WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin_guru.php?success=deleted");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
