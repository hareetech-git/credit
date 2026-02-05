<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../enquiries.php?err=Invalid request");
    exit;
}

$enquiry_id = (int)($_POST['enquiry_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($enquiry_id <= 0) {
    header("Location: ../enquiries.php?err=Invalid enquiry");
    exit;
}

if ($action === 'convert') {
    $admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;
    $sql = "UPDATE enquiries 
            SET status='converted', 
                converted_at=NOW(),
                converted_by_role='admin',
                converted_by_id=" . ($admin_id ?: "NULL") . "
            WHERE id = $enquiry_id";
} elseif ($action === 'close') {
    $admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;
    $sql = "UPDATE enquiries 
            SET status='closed', 
                closed_at=NOW(),
                closed_by_role='admin',
                closed_by_id=" . ($admin_id ?: "NULL") . "
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
