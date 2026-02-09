<?php
session_start();
include 'db/config.php';
if (!isset($_SESSION['dsa_id'])) {
    header('Location: index.php');
    exit();
}

function dsaPermissionTablesReady($conn) {
    static $checked = null;
    if ($checked !== null) {
        return $checked;
    }

    $permTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_permissions'");
    $mapTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_user_permissions'");
    $checked = ($permTbl && mysqli_num_rows($permTbl) > 0 && $mapTbl && mysqli_num_rows($mapTbl) > 0);

    return $checked;
}

function dsaHasAccess($conn, $perm_key) {
    if (!isset($_SESSION['dsa_id'])) {
        return false;
    }

    // Backward-compatible: if permission tables are not migrated yet, do not block.
    if (!dsaPermissionTablesReady($conn)) {
        return true;
    }

    $dsa_id = (int)$_SESSION['dsa_id'];
    $perm_key = mysqli_real_escape_string($conn, $perm_key);

    $sql = "SELECT p.id
            FROM dsa_permissions p
            INNER JOIN dsa_user_permissions up ON up.permission_id = p.id
            WHERE up.dsa_id = $dsa_id AND p.perm_key = '$perm_key'
            LIMIT 1";
    $res = mysqli_query($conn, $sql);

    return ($res && mysqli_num_rows($res) > 0);
}

function dsaRequireAccess($conn, $perm_key, $redirect = 'dashboard.php') {
    if (!dsaHasAccess($conn, $perm_key)) {
        header('Location: ' . $redirect . '?err=' . urlencode('Access denied'));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>DSA Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../admin/assets/udhaar_logo.png">
    <script src="../staff/assets/js/config.js"></script>
    <link href="../staff/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../staff/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<div class="wrapper">
