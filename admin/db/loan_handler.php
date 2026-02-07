<?php
session_start();
include 'config.php';
require_once '../../includes/loan_notifications.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid access.");
}

$action = $_POST['action'];
$interest_type_column_exists = false;
$interest_col_res = mysqli_query($conn, "SHOW COLUMNS FROM loan_applications LIKE 'interest_type'");
if ($interest_col_res && mysqli_num_rows($interest_col_res) > 0) {
    $interest_type_column_exists = true;
}

function normalizeInterestType($value) {
    $type = strtolower(trim((string)$value));
    return ($type === 'month') ? 'month' : 'year';
}

function calculateEmiAmount($principal, $tenure_months, $interest_rate, $interest_type) {
    $p = max(0.0, (float)$principal);
    $n = max(1, (int)$tenure_months);
    $r = max(0.0, (float)$interest_rate);
    $type = normalizeInterestType($interest_type);

    $monthly_rate = ($type === 'year') ? ($r / 1200) : ($r / 100);
    if ($monthly_rate <= 0) {
        return round($p / $n, 2);
    }

    $factor = pow(1 + $monthly_rate, $n);
    if ($factor <= 1) {
        return round($p / $n, 2);
    }

    $emi = $p * $monthly_rate * $factor / ($factor - 1);
    return round($emi, 2);
}

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
    $requested_amount = isset($_POST['requested_amount']) ? (float)$_POST['requested_amount'] : 0.0;
    $tenure_months = isset($_POST['tenure_months']) ? (int)$_POST['tenure_months'] : (int)($_POST['tenure_years'] ?? 0);
    $interest_rate = isset($_POST['interest_rate']) ? (float)$_POST['interest_rate'] : 0.0;
    $interest_type = normalizeInterestType($_POST['interest_type'] ?? 'year');
    $emi = calculateEmiAmount($requested_amount, $tenure_months, $interest_rate, $interest_type);
    $prev_status = '';

    $prev_res = mysqli_query($conn, "SELECT status FROM loan_applications WHERE id=$loan_id LIMIT 1");
    if ($prev_res && mysqli_num_rows($prev_res) > 0) {
        $prev = mysqli_fetch_assoc($prev_res);
        $prev_status = strtolower((string)$prev['status']);
    }

    $interest_type_set_sql = '';
    if ($interest_type_column_exists) {
        $interest_type_set_sql = ", interest_type='$interest_type'";
    }

    $sql = "UPDATE loan_applications SET 
            status='$status', 
            rejection_note='$note', 
            requested_amount=$requested_amount,
            tenure_years=$tenure_months, 
            emi_amount=$emi,
            interest_rate=$interest_rate
            $interest_type_set_sql
            WHERE id=$loan_id";
            
    if (mysqli_query($conn, $sql)) {
        $status_lc = strtolower($status);
        if (($status_lc === 'approved' || $status_lc === 'rejected') && $prev_status !== $status_lc) {
            $send_credentials = ($status_lc === 'approved');
            loanNotifyCustomerDecision($conn, $loan_id, $status_lc, $note, $send_credentials);
        }
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
    $prev_doc_status = '';

    $prev_doc_res = mysqli_query($conn, "SELECT status FROM loan_application_docs WHERE id=$doc_id LIMIT 1");
    if ($prev_doc_res && mysqli_num_rows($prev_doc_res) > 0) {
        $prev_doc = mysqli_fetch_assoc($prev_doc_res);
        $prev_doc_status = strtolower((string)$prev_doc['status']);
    }

    $sql = "UPDATE loan_application_docs SET 
            status='$status', 
            rejection_reason='$reason' 
            WHERE id=$doc_id";
            
    if (mysqli_query($conn, $sql)) {
        $status_lc = strtolower($status);
        if ($status_lc === 'verified' && $prev_doc_status !== 'verified') {
            $pending_res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM loan_application_docs WHERE loan_application_id=$loan_id AND status != 'verified'");
            $total_res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM loan_application_docs WHERE loan_application_id=$loan_id");
            $pending_count = $pending_res ? (int)(mysqli_fetch_assoc($pending_res)['c'] ?? 0) : 0;
            $total_count = $total_res ? (int)(mysqli_fetch_assoc($total_res)['c'] ?? 0) : 0;
            if ($total_count > 0 && $pending_count === 0) {
                loanNotifyCustomerDocumentsVerified($conn, $loan_id);
            }
        }
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
