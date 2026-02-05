<?php
include '../config.php';
session_start();

if (isset($_POST['save_perms'])) {
    $type = $_POST['update_type']; 
    $selected_perms = $_POST['perms'] ?? [];

    if ($type === 'bulk') {
        // 1. Clear current Role permissions for "All Staff" (ID 1)
        mysqli_query($conn, "DELETE FROM role_permissions WHERE role_id = 1");

        // 2. Insert new Role permissions
        foreach ($selected_perms as $p_id) {
            $p_id = (int)$p_id;
            mysqli_query($conn, "INSERT INTO role_permissions (role_id, permission_id) VALUES (1, $p_id)");
        }
        $url = "../../manage_permissions.php?mode=bulk&msg=Global Role Updated";

    } else {
        // INDIVIDUAL UPDATE (remains the same)
        $staff_id = (int)$_POST['staff_id'];
        mysqli_query($conn, "DELETE FROM staff_permissions WHERE staff_id = $staff_id");
        foreach ($selected_perms as $p_id) {
            $p_id = (int)$p_id;
            mysqli_query($conn, "INSERT INTO staff_permissions (staff_id, permission_id) VALUES ($staff_id, $p_id)");
        }
        $url = "../../manage_permissions.php?mode=individual&staff_id=$staff_id&msg=User Permissions Updated";
    }

    header("Location: $url");
    exit();
}