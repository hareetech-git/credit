<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../enquiries.php?err=Invalid request");
    exit;
}

$enquiry_id = (int)($_POST['enquiry_id'] ?? 0);
$action = $_POST['action'] ?? '';
$customer_id = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;

if ($enquiry_id <= 0 || !$customer_id) {
    header("Location: ../enquiries.php?err=Invalid enquiry");
    exit;
}

$own_res = mysqli_query($conn, "SELECT id FROM enquiries WHERE id = $enquiry_id AND customer_id = $customer_id LIMIT 1");
if (!$own_res || mysqli_num_rows($own_res) === 0) {
    header("Location: ../enquiries.php?err=Access denied");
    exit;
}

if ($action === 'close') {
    $sql = "UPDATE enquiries 
            SET status='closed', 
                closed_at=NOW(),
                closed_by_role='customer',
                closed_by_id=$customer_id
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
