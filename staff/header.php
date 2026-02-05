<?php
session_start();
include 'db/config.php';

// 1. Redirect to login if Staff is not logged in
// Note: We use 'staff_id' here to keep it separate from 'admin_id'
if (!isset($_SESSION['staff_id'])) {
    header('Location: index.php');
    exit();
}

/**
 * 2. SPECIFIC USER PERMISSION CHECKER
 * This function checks if the specific logged-in user 
 * has a specific permission key in the staff_permissions table.
 */
function hasAccess($conn, $perm_key) {
    if (!isset($_SESSION['staff_id'])) return false;
    $staff_id = (int)$_SESSION['staff_id'];

    $query = "
     
        SELECT p.id FROM permissions p 
        INNER JOIN role_permissions rp ON p.id = rp.permission_id 
        INNER JOIN staff s ON s.role_id = rp.role_id
        WHERE s.id = $staff_id AND p.perm_key = '$perm_key'
        
        UNION
       
        SELECT p.id FROM permissions p
        INNER JOIN staff_permissions sp ON p.id = sp.permission_id
        WHERE sp.staff_id = $staff_id AND p.perm_key = '$perm_key'
    ";

    $result = mysqli_query($conn, $query);
    return (mysqli_num_rows($result) > 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Staff Dashboard | Techmin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/vendor/daterangepicker/daterangepicker.css">

    <link rel="stylesheet" href="assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css">

    <script src="assets/js/config.js"></script>

    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <style>
        .icon-btn {
            background-color: transparent;
            border: none;
            cursor: pointer;
            padding: 5px;
            color: #007bff;
            font-size: 18px;
        }

        .icon-btn:hover {
            color: #0056b3;
            background-color: #f1f1f1;
            border-radius: 5px;
        }
        
        /* Style for locked/disabled actions */
        .access-locked {
            filter: grayscale(1);
            opacity: 0.6;
            cursor: not-allowed !important;
        }
    </style>
</head>

<body>
    <div class="wrapper">