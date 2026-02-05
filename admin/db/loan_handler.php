<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid access.");
}

$action = $_POST['action'];

if ($action == 'assign_staff') {
    $loan_id = (int)$_POST['loan_id'];
    $staff_id = (int)$_POST['staff_id'];
    $admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;

    if ($staff_id > 0) {
        $sql = "UPDATE loan_applications
                SET assigned_staff_id = $staff_id,
                    assigned_by = " . ($admin_id ?: "NULL") . ",
                    assigned_at = NOW()
                WHERE id = $loan_id";
    } else {
        $sql = "UPDATE loan_applications
                SET assigned_staff_id = NULL,
                    assigned_by = NULL,
                    assigned_at = NULL
                WHERE id = $loan_id";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: ../loan_applications.php?msg=Loan assigned successfully");
    } else {
        header("Location: ../loan_applications.php?err=Assignment failed");
    }
    exit;
}

if ($action == 'update_loan_status') {
    $loan_id = (int)$_POST['loan_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $tenure = (int)$_POST['tenure_years'];
    $emi = (float)$_POST['emi_amount'];
    $interest_rate = isset($_POST['interest_rate']) ? (float)$_POST['interest_rate'] : 0.0;

    // Update with extra fields
    $sql = "UPDATE loan_applications SET 
            status='$status', 
            rejection_note='$note', 
            tenure_years=$tenure, 
            emi_amount=$emi,
            interest_rate=$interest_rate
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

if ($action == 'upload_doc') {
    $loan_id = (int)$_POST['loan_id'];
    $doc_name = trim($_POST['doc_name'] ?? '');

    if ($loan_id <= 0 || $doc_name === '' || empty($_FILES['doc_file']['name'])) {
        header("Location: ../loan_view.php?id=$loan_id&err=Missing document data");
        exit;
    }

    $allowed_exts = ['pdf', 'jpg', 'jpeg', 'png', 'jfif'];
    $upload_dir = realpath(__DIR__ . '/../../uploads/loans');
    if ($upload_dir === false) {
        header("Location: ../loan_view.php?id=$loan_id&err=Upload directory not found");
        exit;
    }

    $tmp_name = $_FILES['doc_file']['tmp_name'];
    $original = $_FILES['doc_file']['name'];
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts, true)) {
        header("Location: ../loan_view.php?id=$loan_id&err=Invalid file type");
        exit;
    }

    $safe_doc_name = mysqli_real_escape_string($conn, $doc_name);
    $new_name = "loan_{$loan_id}_" . time() . "_" . preg_replace('/[^A-Za-z0-9_-]/', '_', $safe_doc_name) . "." . $ext;
    $dest = $upload_dir . DIRECTORY_SEPARATOR . $new_name;

    if (!move_uploaded_file($tmp_name, $dest)) {
        header("Location: ../loan_view.php?id=$loan_id&err=Upload failed");
        exit;
    }

    $db_path = "uploads/loans/" . $new_name;
    $sql = "INSERT INTO loan_application_docs (loan_application_id, doc_name, doc_path, status)
            VALUES ($loan_id, '$safe_doc_name', '$db_path', 'pending')";
    if (mysqli_query($conn, $sql)) {
        header("Location: ../loan_view.php?id=$loan_id&msg=Document uploaded");
    } else {
        header("Location: ../loan_view.php?id=$loan_id&err=Database insert failed");
    }
    exit;
}

if ($action == 'delete_loan') {
    $loan_id = (int)$_POST['loan_id'];

    if ($loan_id <= 0) {
        header("Location: ../loan_applications.php?err=Invalid Loan ID");
        exit;
    }

    // Delete files from disk
    $docs_res = mysqli_query($conn, "SELECT doc_path FROM loan_application_docs WHERE loan_application_id = $loan_id");
    if ($docs_res) {
        while ($doc = mysqli_fetch_assoc($docs_res)) {
            $file = realpath(__DIR__ . '/../../' . $doc['doc_path']);
            if ($file && file_exists($file)) {
                unlink($file);
            }
        }
    }

    // Delete doc records
    mysqli_query($conn, "DELETE FROM loan_application_docs WHERE loan_application_id = $loan_id");

    // Delete loan
    if (mysqli_query($conn, "DELETE FROM loan_applications WHERE id = $loan_id")) {
        header("Location: ../loan_applications.php?msg=Loan deleted successfully");
    } else {
        header("Location: ../loan_applications.php?err=Delete failed");
    }
    exit;
}
?>
