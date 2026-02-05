<?php
session_start();
include 'config.php';
include 'enquiry_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../enquiries.php?err=Invalid request");
    exit;
}

$enquiry_id = (int)($_POST['enquiry_id'] ?? 0);
$note = trim($_POST['note'] ?? '');
$staff_id = isset($_SESSION['staff_id']) ? (int)$_SESSION['staff_id'] : null;

if ($enquiry_id <= 0 || $note === '' || !$staff_id) {
    header("Location: ../enquiry_view.php?id=$enquiry_id&err=Missing note");
    exit;
}

if (!staffCanAccessEnquiry($conn, $staff_id, $enquiry_id)) {
    header("Location: ../enquiries.php?err=Access denied");
    exit;
}

$safe_note = mysqli_real_escape_string($conn, $note);
$sql = "INSERT INTO enquiry_notes (enquiry_id, note, created_by_role, created_by_id)
        VALUES ($enquiry_id, '$safe_note', 'staff', $staff_id)";

if (mysqli_query($conn, $sql)) {
    header("Location: ../enquiry_view.php?id=$enquiry_id#notes");
} else {
    header("Location: ../enquiry_view.php?id=$enquiry_id&err=Note failed");
}
exit;
?>
