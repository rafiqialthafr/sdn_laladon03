<?php
include 'koneksi.php';

if (isset($_POST['save'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $is_published = $_POST['is_published'];
    $image_url = "https://placehold.co/600x400/34495e/ffffff?text=" . urlencode($title);

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_url = $target_file;
    }

    $query = "INSERT INTO announcements (title, content, category, image, is_published) VALUES ('$title', '$content', '$category', '$image_url', '$is_published')";
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin_dashboard.php");
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
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_url = $target_file;
    }

    $query = "UPDATE announcements SET title='$title', content='$content', category='$category', image='$image_url', is_published='$is_published' WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM announcements WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
