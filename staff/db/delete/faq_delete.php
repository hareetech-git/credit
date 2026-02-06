<?php
session_start();
include '../../db/config.php';
include '../enquiry_helpers.php';

$staff_id = isset($_SESSION['staff_id']) ? (int)$_SESSION['staff_id'] : 0;
$id = (int)($_GET['id'] ?? 0);

if ($staff_id <= 0) {
    header("Location: ../../index.php?err=Login required");
    exit;
}

if ($id <= 0) {
    header("Location: ../../faqs.php?err=Invalid FAQ ID");
    exit;
}

if (!staffHasAccess($conn, 'faq_delete', $staff_id)) {
    header("Location: ../../faqs.php?err=Delete permission required");
    exit;
}

if (mysqli_query($conn, "DELETE FROM faqs WHERE id = $id")) {
    header("Location: ../../faqs.php?msg=FAQ deleted");
} else {
    header("Location: ../../faqs.php?err=Delete failed");
}
exit;
?>
