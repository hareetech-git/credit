<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();
include '../../../db/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../../index.php?err=Login required");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: ../../career_applications.php?err=Invalid application ID");
    exit;
}

/* Fetch resume path */
$res = mysqli_query($conn, "SELECT resume_path FROM career_applications WHERE id = $id LIMIT 1");
$app = $res ? mysqli_fetch_assoc($res) : null;

if (!$app) {
    header("Location: ../../career_applications.php?err=Application not found");
    exit;
}

/* Delete record */
if (mysqli_query($conn, "DELETE FROM career_applications WHERE id = $id")) {

    /* Delete resume file */
    $resume = trim((string)$app['resume_path']);
    if ($resume !== '' && file_exists("../../../" . $resume)) {
        @unlink("../../../" . $resume);
    }

    header("Location: ../../career_applications.php?msg=Application deleted successfully");
    exit;
}

header("Location: ../../career_applications.php?err=Failed to delete application");
exit;
