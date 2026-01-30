<?php
include '../includes/connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cid = $_SESSION['customer_id'];
    $sid = $_POST['service_id'];
    $amount = $_POST['requested_amount'];
    $tenure = $_POST['tenure_years'];

    // 1. Insert Application
    $sql = "INSERT INTO loan_applications (customer_id, service_id, requested_amount, tenure_years) VALUES ($cid, $sid, $amount, $tenure)";
    mysqli_query($conn, $sql);
    $loan_id = mysqli_insert_id($conn);

    // 2. Handle Document Uploads
    if (isset($_FILES['docs'])) {
        $upload_dir = '../uploads/loans/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        foreach ($_FILES['docs']['name'] as $doc_name => $filename) {
            $tmp_name = $_FILES['docs']['tmp_name'][$doc_name];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $new_name = "loan_" . $loan_id . "_" . time() . "_" . str_replace(' ', '_', $doc_name) . "." . $ext;
            
            if (move_uploaded_file($tmp_name, $upload_dir . $new_name)) {
                $path = 'uploads/loans/' . $new_name;
                mysqli_query($conn, "INSERT INTO loan_application_docs (loan_application_id, doc_name, doc_path) VALUES ($loan_id, '$doc_name', '$path')");
            }
        }
    }

    header("Location: ../customer/dashboard.php?msg=Application submitted successfully&type=toast");
}