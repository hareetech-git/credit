<?php
include '../config.php';
session_start();

if (isset($_POST['save_perms']) || isset($_POST['update_perms'])) {
    
    $type = $_POST['update_type'] ?? ''; 
    $selected_perms = $_POST['perms'] ?? [];

    if ($type === 'bulk') {
        // 1. Clear everything first
        mysqli_query($conn, "TRUNCATE TABLE staff_permissions");

        if (!empty($selected_perms)) {
            // 2. Map permissions to integers safely
            $perm_ids = array_map('intval', $selected_perms);
            
            // 3. Get all Staff IDs from the 'staff' table
            $staff_query = mysqli_query($conn, "SELECT id FROM staff");
            
            $values = [];
            while ($staff = mysqli_fetch_assoc($staff_query)) {
                $s_id = $staff['id'];
                foreach ($perm_ids as $p_id) {
                    $values[] = "($s_id, $p_id)";
                }
            }

            // 4. Perform a single Bulk Insert
            if (!empty($values)) {
                $query = "INSERT INTO staff_permissions (staff_id, permission_id) VALUES " . implode(',', $values);
                mysqli_query($conn, $query);
            }
        }
        $url = "../../manage_permissions.php?mode=bulk&msg=Global Update Success";

    } else {
        // --- INDIVIDUAL UPDATE ---
        $staff_id = (int)($_POST['staff_id'] ?? 0);
        
        if ($staff_id > 0) {
            mysqli_query($conn, "DELETE FROM staff_permissions WHERE staff_id = $staff_id");

            if (!empty($selected_perms)) {
                $values = [];
                foreach ($selected_perms as $p_id) {
                    $p_id = (int)$p_id;
                    $values[] = "($staff_id, $p_id)";
                }
                $query = "INSERT INTO staff_permissions (staff_id, permission_id) VALUES " . implode(',', $values);
                mysqli_query($conn, $query);
            }
            $url = "../../manage_permissions.php?mode=individual&staff_id=$staff_id&msg=User Update Success";
        } else {
            $url = "../../manage_permissions.php?err=Invalid ID";
        }
    }

    header("Location: $url");
    exit();
}