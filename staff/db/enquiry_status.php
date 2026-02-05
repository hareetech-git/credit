<?php
session_start();
include 'config.php';
include 'enquiry_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../enquiries.php?err=Invalid request");
    exit;
}

$enquiry_id = (int)($_POST['enquiry_id'] ?? 0);
$action = $_POST['action'] ?? '';
$staff_id = isset($_SESSION['staff_id']) ? (int)$_SESSION['staff_id'] : null;

if ($enquiry_id <= 0 || !$staff_id) {
    header("Location: ../enquiries.php?err=Invalid enquiry");
    exit;
}

if (!staffCanAccessEnquiry($conn, $staff_id, $enquiry_id)) {
    header("Location: ../enquiries.php?err=Access denied");
    exit;
}

if (!staffHasAccess($conn, 'enquiry_status_change', $staff_id)) {
    header("Location: ../enquiry_view.php?id=$enquiry_id&err=Status permission required");
    exit;
}

if ($action === 'convert') {
    $sql = "UPDATE enquiries 
            SET status='converted', 
                converted_at=NOW(),
                converted_by_role='staff',
                converted_by_id=$staff_id
            WHERE id = $enquiry_id";
} elseif ($action === 'close') {
    $sql = "UPDATE enquiries 
            SET status='closed', 
                closed_at=NOW(),
                closed_by_role='staff',
                closed_by_id=$staff_id
            WHERE id = $enquiry_id";
} else {
    header("Location: ../enquiry_view.php?id=$enquiry_id&err=Invalid action");
    exit;
}

if (mysqli_query($conn, $sql)) {
    header("Location: ../enquiry_view.php?id=$enquiry_id&msg=Status updated");
} else {
    header("Location: ../enquiry_view.php?id=$enquiry_id&err=Update failed");
}
exit;
?>
