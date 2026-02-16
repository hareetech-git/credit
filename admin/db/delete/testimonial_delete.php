<?php
session_start();
include '../config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: ../../testimonials.php");
    exit;
}

// First get the image path to delete the file
$result = mysqli_query($conn, "SELECT partner_img FROM testimonials WHERE id = $id");
if ($row = mysqli_fetch_assoc($result)) {
    $image_path = "../../" . $row['partner_img'];
    if (file_exists($image_path) && !empty($row['partner_img'])) {
        unlink($image_path); // Delete the image file
    }
}

// Delete from database
$delete_query = "DELETE FROM testimonials WHERE id = $id";
if (mysqli_query($conn, $delete_query)) {
    $_SESSION['success_message'] = "Testimonial deleted successfully!";
} else {
    $_SESSION['errors'] = ["Error deleting testimonial: " . mysqli_error($conn)];
}

header("Location: ../../testimonials.php");
exit;
?>