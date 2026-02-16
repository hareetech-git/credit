<?php
session_start();
include '../../db/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../index.php?err=Login required");
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: ../../blogs.php?err=Invalid blog ID");
    exit;
}

$res = mysqli_query($conn, "SELECT featured_image FROM blogs WHERE id = $id LIMIT 1");
$blog = $res ? mysqli_fetch_assoc($res) : null;

if (mysqli_query($conn, "DELETE FROM blogs WHERE id = $id")) {
    $img = trim((string) ($blog['featured_image'] ?? ''));
    if ($img !== '' && file_exists("../../../" . $img)) {
        @unlink("../../../" . $img);
    }
    header("Location: ../../blogs.php?msg=Blog deleted successfully");
    exit;
}

header("Location: ../../blogs.php?err=Failed to delete blog");
exit;
?>
