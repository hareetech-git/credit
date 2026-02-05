<?php
include '../config.php';
session_start();

if (isset($_POST['save_staff'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $dept_id = (int)$_POST['department_id'];
    $admin_id = $_SESSION['admin_id'];
    $selected_perms = isset($_POST['perms']) ? $_POST['perms'] : [];

    if (isset($_POST['staff_id'])) {
        // --- UPDATE MODE ---
        $staff_id = (int)$_POST['staff_id'];
        
        // Update basic info
        $update_sql = "UPDATE staff SET name='$name', email='$email', department_id=$dept_id WHERE id=$staff_id";
        mysqli_query($conn, $update_sql);

        // Update password ONLY if provided
        if (!empty($_POST['password'])) {
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE staff SET password='$pass' WHERE id=$staff_id");
        }

        // Sync Permissions: Delete and Re-insert
        mysqli_query($conn, "DELETE FROM staff_permissions WHERE staff_id=$staff_id");
        foreach ($selected_perms as $p_id) {
            $p_id = (int)$p_id;
            mysqli_query($conn, "INSERT INTO staff_permissions (staff_id, permission_id) VALUES ($staff_id, $p_id)");
        }
        $msg = "Staff updated successfully";

    } else {
        // --- ADD MODE ---
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $insert_sql = "INSERT INTO staff (name, email, password, department_id, created_by) 
                       VALUES ('$name', '$email', '$pass', $dept_id, $admin_id)";
        
        if (mysqli_query($conn, $insert_sql)) {
            $staff_id = mysqli_insert_id($conn);
            foreach ($selected_perms as $p_id) {
                $p_id = (int)$p_id;
                mysqli_query($conn, "INSERT INTO staff_permissions (staff_id, permission_id) VALUES ($staff_id, $p_id)");
            }
        }
        $msg = "Staff created successfully";
    }

    header("Location: ../../staff_list.php?msg=" . urlencode($msg));
    exit();
}