<?php
// Adjust path to config.php (assuming it is in admin/db/)
include '../config.php';

$action = $_GET['action'] ?? '';

if ($action == 'delete') {
    $id = (int)$_GET['id'];
    
    if ($id > 0) {
        // 1. Delete profile first (to avoid foreign key constraint issues)
        $del_profile = "DELETE FROM customer_profiles WHERE customer_id=$id";
        mysqli_query($conn, $del_profile);

        // 2. Delete main customer record
        $del_customer = "DELETE FROM customers WHERE id=$id";
        mysqli_query($conn, $del_customer);
        
        $msg = "Customer Deleted Successfully";
    } else {
        $msg = "Invalid Customer ID";
    }

    // Redirect back to customers list (Go up 2 levels: delete -> db -> admin)
    header("Location: ../../customers.php?msg=" . urlencode($msg));
    exit;
}
?>