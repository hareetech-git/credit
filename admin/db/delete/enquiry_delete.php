<?php
session_start();
include '../config.php';

$enquiry_id = (int)($_GET['id'] ?? 0);
if ($enquiry_id <= 0) {
    header("Location: ../../enquiries.php?err=Invalid enquiry");
    exit;
}

// Find conversation
$conv_res = mysqli_query($conn, "SELECT id FROM enquiry_conversations WHERE enquiry_id = $enquiry_id LIMIT 1");
if ($conv_res && mysqli_num_rows($conv_res) > 0) {
    $conv = mysqli_fetch_assoc($conv_res);
    $conv_id = (int)$conv['id'];
    mysqli_query($conn, "DELETE FROM enquiry_messages WHERE conversation_id = $conv_id");
    mysqli_query($conn, "DELETE FROM enquiry_conversations WHERE id = $conv_id");
}

// Delete notes
mysqli_query($conn, "DELETE FROM enquiry_notes WHERE enquiry_id = $enquiry_id");

// Delete enquiry
if (mysqli_query($conn, "DELETE FROM enquiries WHERE id = $enquiry_id")) {
    header("Location: ../../enquiries.php?msg=Enquiry deleted");
} else {
    header("Location: ../../enquiries.php?err=Delete failed");
}
exit;
?>
