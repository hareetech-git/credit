<?php
session_start();
include '../config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: ../../brands.php");
    exit;
}

// First get the image path to delete the file
$result = mysqli_query($conn, "SELECT brand_img FROM brands WHERE id = $id");
if ($row = mysqli_fetch_assoc($result)) {
    // CORRECTED PATH: assets/brands/
    $image_path = "../../" . $row['brand_img'];
    if (file_exists($image_path) && !empty($row['brand_img'])) {
        unlink($image_path); // Delete the image file
    }
}

// Delete from database
$delete_query = "DELETE FROM brands WHERE id = $id";
if (mysqli_query($conn, $delete_query)) {
    $_SESSION['success_message'] = "Brand deleted successfully!";
} else {
    $_SESSION['errors'] = ["Error deleting brand: " . mysqli_error($conn)];
}

header("Location: ../../brands.php");
exit;
?>