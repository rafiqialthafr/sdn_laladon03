<?php
include 'koneksi.php';

// Handle Save/Insert
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $bio = $_POST['bio'];
    $photo_url = "https://placehold.co/400x400/34495e/ffffff?text=" . urlencode($name); // Default placeholder
    
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
        $photo_url = $target_file;
    }

    $query = "INSERT INTO teachers (name, position, photo, bio) VALUES ('$name', '$position', '$photo_url', '$bio')";
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
}

// Handle Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $bio = $_POST['bio'];
    $photo_url = $_POST['existing_photo'];

    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
        $photo_url = $target_file;
    }

    $query = "UPDATE teachers SET name='$name', position='$position', photo='$photo_url', bio='$bio' WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM teachers WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
