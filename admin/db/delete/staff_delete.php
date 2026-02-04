<?php
include '../config.php';
session_start();

if (isset($_GET['id'])) {
    $staff_id = (int)$_GET['id'];

    // 1. Manually delete staff permissions first
    mysqli_query($conn, "DELETE FROM staff_permissions WHERE staff_id = $staff_id");

    // 2. Delete the staff record
    $delete_staff = "DELETE FROM staff WHERE id = $staff_id";
    
    if (mysqli_query($conn, $delete_staff)) {
        header("Location: ../../staff_list.php?msg=Staff Deleted Successfully");
    } else {
        header("Location: ../../staff_list.php?error=Could not delete staff");
    }
}