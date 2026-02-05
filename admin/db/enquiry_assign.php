<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../enquiries.php?err=Invalid request");
    exit;
}

$enquiry_id = (int)($_POST['enquiry_id'] ?? 0);
$staff_id = (int)($_POST['staff_id'] ?? 0);
$admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;
$redirect = $_POST['redirect'] ?? '../enquiries.php';

function addQueryParam($url, $param) {
    return (strpos($url, '?') === false) ? ($url . '?' . $param) : ($url . '&' . $param);
}

if ($enquiry_id <= 0) {
    header("Location: " . addQueryParam($redirect, "err=Invalid enquiry"));
    exit;
}

if ($staff_id > 0) {
    $sql = "UPDATE enquiries
            SET assigned_staff_id = $staff_id,
                assigned_by = " . ($admin_id ?: "NULL") . ",
                assigned_at = NOW(),
                status = CASE 
                    WHEN status IN ('converted','closed') THEN status 
                    ELSE 'assigned' 
                END
            WHERE id = $enquiry_id";
} else {
    $sql = "UPDATE enquiries
            SET assigned_staff_id = NULL,
                assigned_by = NULL,
                assigned_at = NULL,
                status = CASE 
                    WHEN status IN ('converted','closed') THEN status 
                    ELSE 'new' 
                END
            WHERE id = $enquiry_id";
}

if (mysqli_query($conn, $sql)) {
    header("Location: " . addQueryParam($redirect, "msg=Enquiry assignment updated"));
} else {
    header("Location: " . addQueryParam($redirect, "err=Assignment failed"));
}
exit;
?>
