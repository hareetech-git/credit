<?php
// staff/db/delete/customer_delete.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Correct relative path to config
include '../config.php';
session_start();

// 2. Security Check: Ensure the staff has permission
// Assuming hasPermission function is available via auth_helper included in header or required here
require_once '../../../core/auth_helper.php'; 
if (!isset($_SESSION['staff_id']) || !hasPermission($_SESSION['staff_id'], 'cust_delete', $conn)) {
    header("Location: ../../customers.php?err=Unauthorized+Access");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = $_GET['action'] ?? '';

if ($action === 'delete' && $id > 0) {
    
    // Use a transaction to ensure both or neither are deleted
    mysqli_begin_transaction($conn);

    try {
        // A. Delete secondary data (loan docs and applications if they exist)
        // This prevents foreign key constraint errors
        mysqli_query($conn, "DELETE FROM loan_application_docs WHERE loan_application_id IN (SELECT id FROM loan_applications WHERE customer_id=$id)");
        mysqli_query($conn, "DELETE FROM loan_applications WHERE customer_id=$id");

        // B. Delete profile
        mysqli_query($conn, "DELETE FROM customer_profiles WHERE customer_id=$id");

        // C. Delete main customer record
        mysqli_query($conn, "DELETE FROM customers WHERE id=$id");

        mysqli_commit($conn);
        $msg = "Customer and all associated data deleted successfully.";
        $type = "msg";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $msg = "Error deleting customer: " . $e->getMessage();
        $type = "err";
    }

    // Redirect back to customers list
    header("Location: ../../customers.php?$type=" . urlencode($msg));
    exit;

} else {
   
    header("Location: ../../customers.php?err=Invalid+Request");
    exit;
}