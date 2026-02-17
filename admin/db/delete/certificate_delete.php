<?php
session_start();
include '../config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: ../../certificates.php");
    exit;
}

// First get the image path to delete the file
$result = mysqli_query($conn, "SELECT certificate_img FROM certificates WHERE id = $id");
if ($row = mysqli_fetch_assoc($result)) {
    $image_path = "../../" . $row['certificate_img'];
    if (file_exists($image_path) && !empty($row['certificate_img'])) {
        unlink($image_path); // Delete the image file
    }
}

// Delete from database
$delete_query = "DELETE FROM certificates WHERE id = $id";
if (mysqli_query($conn, $delete_query)) {
    $_SESSION['success_message'] = "Certificate deleted successfully!";
} else {
    $_SESSION['errors'] = ["Error deleting certificate: " . mysqli_error($conn)];
}

header("Location: ../../certificates.php");
exit;
?>