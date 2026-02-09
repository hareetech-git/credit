<?php
include '../config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../index.php?err=Unauthorized');
    exit;
}

if (!isset($_POST['save_dsa_perms'])) {
    header('Location: ../../manage_dsa_permissions.php?err=Invalid request');
    exit;
}

$dsa_id = (int)($_POST['dsa_id'] ?? 0);
$selected_perms = $_POST['perms'] ?? [];

if ($dsa_id <= 0) {
    header('Location: ../../manage_dsa_permissions.php?err=Invalid DSA selected');
    exit;
}

$permTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_permissions'");
$userPermTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_user_permissions'");
if (!$permTbl || mysqli_num_rows($permTbl) === 0 || !$userPermTbl || mysqli_num_rows($userPermTbl) === 0) {
    header('Location: ../../manage_dsa_permissions.php?dsa_id=' . $dsa_id . '&err=Permission tables missing. Run migration first.');
    exit;
}

mysqli_query($conn, "DELETE FROM dsa_user_permissions WHERE dsa_id = $dsa_id");

foreach ($selected_perms as $p_id) {
    $p_id = (int)$p_id;
    if ($p_id > 0) {
        mysqli_query($conn, "INSERT INTO dsa_user_permissions (dsa_id, permission_id) VALUES ($dsa_id, $p_id)");
    }
}

header('Location: ../../manage_dsa_permissions.php?dsa_id=' . $dsa_id . '&msg=DSA permissions updated');
exit;
?>
