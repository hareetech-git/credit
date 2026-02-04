<?php
include '../config.php';
session_start();

if (isset($_POST['update_perms'])) {
    $target = $_POST['staff_id']; // This could be an ID (integer) or the string "all"
    $selected_perms = isset($_POST['perms']) ? $_POST['perms'] : [];

    if ($target === 'all') {
        // --- BULK UPDATE ALL STAFF ---
        // 1. Fetch every staff ID
        $staff_query = mysqli_query($conn, "SELECT id FROM staff");
        
        while ($staff = mysqli_fetch_assoc($staff_query)) {
            $s_id = $staff['id'];
            
            // 2. Clear perms for this staff
            mysqli_query($conn, "DELETE FROM staff_permissions WHERE staff_id = $s_id");
            
            // 3. Insert new perms for this staff
            foreach ($selected_perms as $p_id) {
                $p_id = (int)$p_id;
                mysqli_query($conn, "INSERT INTO staff_permissions (staff_id, permission_id) VALUES ($s_id, $p_id)");
            }
        }
        $redirect_url = "../../manage_permissions.php?staff_id=all&msg=Bulk update successful";
        
    } else {
        // --- SINGLE USER UPDATE ---
        $staff_id = (int)$target;
        mysqli_query($conn, "DELETE FROM staff_permissions WHERE staff_id = $staff_id");
        
        foreach ($selected_perms as $p_id) {
            $p_id = (int)$p_id;
            mysqli_query($conn, "INSERT INTO staff_permissions (staff_id, permission_id) VALUES ($staff_id, $p_id)");
        }
        $redirect_url = "../../manage_permissions.php?staff_id=$staff_id&msg=Permissions updated";
    }

    header("Location: $redirect_url");
    exit();
}