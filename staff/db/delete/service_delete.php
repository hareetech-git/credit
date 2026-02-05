<?php
include '../../db/config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    header("Location: ../../services.php?error=Invalid ID");
    exit;
}

// 1. Delete from Child Tables first
$tables = [
    'service_overview',
    'service_features',
    'service_eligibility_criteria',
    'service_documents',
    'service_fees_charges',
    'service_loan_repayment',
    'service_why_choose_us',
    'service_banks'
];

foreach ($tables as $table) {
    mysqli_query($conn, "DELETE FROM $table WHERE service_id = $id");
}

// 2. Delete Main Service
$sql = "DELETE FROM services WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    header("Location: ../../services.php?msg=Service and all related data deleted successfully.");
} else {
    header("Location: ../../services.php?error=Failed to delete service.");
}
?>