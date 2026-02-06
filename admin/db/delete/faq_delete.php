<?php
session_start();
include '../../db/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../index.php?err=Login required");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: ../../faqs.php?err=Invalid FAQ ID");
    exit;
}

if (mysqli_query($conn, "DELETE FROM faqs WHERE id = $id")) {
    header("Location: ../../faqs.php?msg=FAQ deleted");
} else {
    header("Location: ../../faqs.php?err=Delete failed");
}
exit;
?>
