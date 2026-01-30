<?php
include 'config.php';

$action = $_POST['action'];

if ($action == 'update_loan_status') {
    $loan_id = (int)$_POST['loan_id'];
    $status = $_POST['status'];
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    $sql = "UPDATE loan_applications SET status='$status', rejection_note='$note' WHERE id=$loan_id";
    mysqli_query($conn, $sql);

    header("Location: ../loan_view.php?id=$loan_id&msg=Loan Status Updated");
}

if ($action == 'verify_doc') {
    $doc_id = (int)$_POST['doc_id'];
    $loan_id = (int)$_POST['loan_id'];
    $status = $_POST['status'];
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    $sql = "UPDATE loan_application_docs SET status='$status', rejection_reason='$reason' WHERE id=$doc_id";
    mysqli_query($conn, $sql);

    header("Location: ../loan_view.php?id=$loan_id&msg=Document Updated");
}
?>