<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid access.");
}

$action = $_POST['action'];

if ($action == 'update_loan_status') {
    $loan_id = (int)$_POST['loan_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $tenure = (int)$_POST['tenure_years'];
    $emi = (float)$_POST['emi_amount'];

    // Update with extra fields
    $sql = "UPDATE loan_applications SET 
            status='$status', 
            rejection_note='$note', 
            tenure_years=$tenure, 
            emi_amount=$emi 
            WHERE id=$loan_id";
            
    if (mysqli_query($conn, $sql)) {
        header("Location: ../loan_view.php?id=$loan_id&msg=Loan Status Updated Successfully");
    } else {
        header("Location: ../loan_view.php?id=$loan_id&err=Update Failed");
    }
    exit;
}

if ($action == 'verify_doc') {
    $doc_id = (int)$_POST['doc_id'];
    $loan_id = (int)$_POST['loan_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    $sql = "UPDATE loan_application_docs SET 
            status='$status', 
            rejection_reason='$reason' 
            WHERE id=$doc_id";
            
    if (mysqli_query($conn, $sql)) {
        header("Location: ../loan_view.php?id=$loan_id&msg=Document Status Updated");
    } else {
        header("Location: ../loan_view.php?id=$loan_id&err=Update Failed");
    }
    exit;
}
?>