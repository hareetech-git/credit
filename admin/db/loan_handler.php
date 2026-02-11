<?php
session_start();
include 'config.php';
require_once '../../includes/loan_notifications.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid access.");
}

$action = $_POST['action'] ?? '';
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

function hasStaffPermission(mysqli $conn, int $staff_id, string $perm_key): bool {
    if ($staff_id <= 0) {
        return false;
    }
    $q = "SELECT 1
          FROM permissions p
          LEFT JOIN role_permissions rp ON rp.permission_id = p.id
          LEFT JOIN staff s ON s.role_id = rp.role_id
          LEFT JOIN staff_permissions sp ON sp.permission_id = p.id
          WHERE p.perm_key = '" . mysqli_real_escape_string($conn, $perm_key) . "'
            AND (s.id = $staff_id OR sp.staff_id = $staff_id)
          LIMIT 1";
    $res = mysqli_query($conn, $q);
    return ($res && mysqli_num_rows($res) > 0);
}

if ($action == 'assign_staff') {
    $loan_id = (int)$_POST['loan_id'];
    $staff_id = (int)$_POST['staff_id'];
    $admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;
    $redirect_to = ($_POST['redirect_to'] ?? '') === 'manual' ? '../manual_loan_assign.php' : '../loan_applications.php';

    if ($loan_id <= 0) {
        header("Location: {$redirect_to}?err=Invalid loan");
        exit;
    }

    if ($staff_id > 0 && !hasStaffPermission($conn, $staff_id, 'loan_process')) {
        header("Location: {$redirect_to}?err=Selected staff is not eligible for loan assignment");
        exit;
    }

    $prev_staff_id = 0;
    $prevRes = mysqli_query($conn, "SELECT assigned_staff_id FROM loan_applications WHERE id = $loan_id LIMIT 1");
    if ($prevRes && mysqli_num_rows($prevRes) > 0) {
        $prevRow = mysqli_fetch_assoc($prevRes);
        $prev_staff_id = (int)($prevRow['assigned_staff_id'] ?? 0);
    }

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
        if ($staff_id > 0 && $staff_id !== $prev_staff_id) {
            loanNotifyStaffOnAssignment($conn, $loan_id, $staff_id, 'Admin');
        }
        header("Location: {$redirect_to}?msg=Loan assigned successfully");
    } else {
        $errMsg = 'Assignment failed';
        $dbErr = trim((string)mysqli_error($conn));
        if ($dbErr !== '') {
            $errMsg .= ': ' . $dbErr;
        }
        header("Location: {$redirect_to}?err=" . urlencode($errMsg));
    }
    exit;
}

if ($action == 'manual_create_assign') {
    $customer_id = (int)($_POST['customer_id'] ?? 0);
    $service_id = (int)($_POST['service_id'] ?? 0);
    $staff_id = (int)($_POST['staff_id'] ?? 0);
    $requested_amount = (float)($_POST['requested_amount'] ?? 0);
    $tenure_months = (int)($_POST['tenure_months'] ?? 0);
    $interest_rate = (float)($_POST['interest_rate'] ?? 0);
    $interest_type = normalizeInterestType($_POST['interest_type'] ?? 'year');
    $status = mysqli_real_escape_string($conn, trim((string)($_POST['status'] ?? 'pending')));
    $admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;

    if ($customer_id <= 0 || $service_id <= 0 || $staff_id <= 0) {
        header("Location: ../manual_loan_assign.php?err=Please select customer, service and staff");
        exit;
    }
    if ($requested_amount <= 0) {
        header("Location: ../manual_loan_assign.php?err=Requested amount must be greater than zero");
        exit;
    }
    if ($tenure_months < 0) {
        header("Location: ../manual_loan_assign.php?err=Tenure cannot be negative");
        exit;
    }
    if ($interest_rate < 0) {
        header("Location: ../manual_loan_assign.php?err=Interest rate cannot be negative");
        exit;
    }
    if (!in_array($status, ['pending', 'approved', 'rejected', 'disbursed'], true)) {
        $status = 'pending';
    }
    if (!hasStaffPermission($conn, $staff_id, 'loan_process')) {
        header("Location: ../manual_loan_assign.php?err=Selected staff is not eligible for manual assignment");
        exit;
    }

    $custRes = mysqli_query($conn, "SELECT id FROM customers WHERE id = $customer_id LIMIT 1");
    $svcRes = mysqli_query($conn, "SELECT id FROM services WHERE id = $service_id LIMIT 1");
    if (!$custRes || mysqli_num_rows($custRes) === 0 || !$svcRes || mysqli_num_rows($svcRes) === 0) {
        header("Location: ../manual_loan_assign.php?err=Invalid customer or service selection");
        exit;
    }

    $emi = ($tenure_months > 0)
        ? calculateEmiAmount($requested_amount, $tenure_months, $interest_rate, $interest_type)
        : 0.00;

    $interest_type_insert_sql = '';
    $interest_type_value_sql = '';
    if ($interest_type_column_exists) {
        $interest_type_insert_sql = ', interest_type';
        $interest_type_value_sql = ", '$interest_type'";
    }

    $insert = "INSERT INTO loan_applications
               (customer_id, service_id, requested_amount, tenure_years, emi_amount, status, assigned_staff_id, assigned_by, assigned_at, interest_rate$interest_type_insert_sql)
               VALUES
               ($customer_id, $service_id, $requested_amount, $tenure_months, $emi, '$status', $staff_id, " . ($admin_id ?: "NULL") . ", NOW(), $interest_rate$interest_type_value_sql)";

    if (mysqli_query($conn, $insert)) {
        $loan_id = (int)mysqli_insert_id($conn);

        $doc_error = '';
        $moved_files = [];
        if (!empty($_FILES['loan_docs']['name'])) {
            $allowed_exts = ['pdf', 'jpg', 'jpeg', 'png'];
            $max_size = 5 * 1024 * 1024;
            $upload_dir = realpath(__DIR__ . '/../../uploads/loans');
            if ($upload_dir === false) {
                $doc_error = 'Upload directory not found';
            } else {
                foreach ($_FILES['loan_docs']['name'] as $key => $val) {
                    if (empty($val)) {
                        continue;
                    }
                    $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed_exts, true)) {
                        $doc_error = 'Only PDF, JPG, JPEG, PNG files are allowed';
                        break;
                    }
                    $file_size = (int)($_FILES['loan_docs']['size'][$key] ?? 0);
                    if ($file_size > $max_size) {
                        $doc_error = 'Each document must be 5 MB or less';
                        break;
                    }
                }
            }

            if ($doc_error === '') {
                foreach ($_FILES['loan_docs']['name'] as $key => $val) {
                    if (empty($val)) {
                        continue;
                    }
                    $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
                    $safe_key = preg_replace('/[^A-Za-z0-9_-]/', '_', (string)$key);
                    $new_name = "loan_{$loan_id}_" . time() . "_$safe_key.$ext";
                    $dest = $upload_dir . DIRECTORY_SEPARATOR . $new_name;
                    if (!move_uploaded_file($_FILES['loan_docs']['tmp_name'][$key], $dest)) {
                        $doc_error = 'Document upload failed';
                        break;
                    }
                    $moved_files[] = $dest;
                    $db_path = "uploads/loans/$new_name";
                    $title = str_replace('_', ' ', (string)$key);
                    mysqli_query($conn, "INSERT INTO loan_application_docs (loan_application_id, doc_name, doc_path) VALUES ($loan_id, '$title', '$db_path')");
                }
            }
        }

        if ($doc_error !== '') {
            mysqli_query($conn, "DELETE FROM loan_application_docs WHERE loan_application_id = $loan_id");
            foreach ($moved_files as $file) {
                if (is_string($file) && file_exists($file)) {
                    unlink($file);
                }
            }
            mysqli_query($conn, "DELETE FROM loan_applications WHERE id = $loan_id");
            header("Location: ../manual_loan_assign.php?err=" . urlencode($doc_error));
            exit;
        }

        loanNotifyStaffOnAssignment($conn, $loan_id, $staff_id, 'Admin');
        header("Location: ../manual_loan_assign.php?msg=Loan created and assigned successfully");
    } else {
        header("Location: ../manual_loan_assign.php?err=Unable to create loan assignment");
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
