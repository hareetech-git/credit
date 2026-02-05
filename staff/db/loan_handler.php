<?php
session_start();
include 'config.php';

if (!isset($_SESSION['staff_id'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid access.");
}

$staff_id = (int)$_SESSION['staff_id'];
$action = $_POST['action'] ?? '';

function staffHasAccess($conn, $perm_key, $staff_id) {
    $query = "
        SELECT p.id FROM permissions p 
        INNER JOIN role_permissions rp ON p.id = rp.permission_id 
        INNER JOIN staff s ON s.role_id = rp.role_id
        WHERE s.id = $staff_id AND p.perm_key = '$perm_key'
        UNION
        SELECT p.id FROM permissions p
        INNER JOIN staff_permissions sp ON p.id = sp.permission_id
        WHERE sp.staff_id = $staff_id AND p.perm_key = '$perm_key'
    ";
    $result = mysqli_query($conn, $query);
    return (mysqli_num_rows($result) > 0);
}

if ($action == 'update_loan_status') {
    if (!staffHasAccess($conn, 'loan_process', $staff_id)) {
        header("Location: ../loan_applications.php?err=No permission");
        exit();
    }
    $loan_id = (int)$_POST['loan_id'];

    $check = mysqli_query($conn, "SELECT id FROM loan_applications WHERE id=$loan_id AND assigned_staff_id=$staff_id");
    if (mysqli_num_rows($check) == 0) {
        header("Location: ../loan_applications.php?err=Not assigned to you");
        exit();
    }

    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $tenure = (int)$_POST['tenure_years'];
    $emi = (float)$_POST['emi_amount'];
    $interest_rate = isset($_POST['interest_rate']) ? (float)$_POST['interest_rate'] : 0.0;

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
    if (!staffHasAccess($conn, 'loan_process', $staff_id)) {
        header("Location: ../loan_applications.php?err=No permission");
        exit();
    }
    $doc_id = (int)$_POST['doc_id'];
    $loan_id = (int)$_POST['loan_id'];

    $check = mysqli_query($conn, "
        SELECT d.id FROM loan_application_docs d
        JOIN loan_applications l ON l.id = d.loan_application_id
        WHERE d.id = $doc_id AND l.assigned_staff_id = $staff_id
    ");
    if (mysqli_num_rows($check) == 0) {
        header("Location: ../loan_applications.php?err=Not assigned to you");
        exit();
    }

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
    if (!staffHasAccess($conn, 'loan_process', $staff_id)) {
        header("Location: ../loan_applications.php?err=No permission");
        exit();
    }
    $loan_id = (int)$_POST['loan_id'];
    $doc_name = trim($_POST['doc_name'] ?? '');

    if ($loan_id <= 0 || $doc_name === '' || empty($_FILES['doc_file']['name'])) {
        header("Location: ../loan_view.php?id=$loan_id&err=Missing document data");
        exit;
    }

    $check = mysqli_query($conn, "SELECT id FROM loan_applications WHERE id=$loan_id AND assigned_staff_id=$staff_id");
    if (mysqli_num_rows($check) == 0) {
        header("Location: ../loan_applications.php?err=Not assigned to you");
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
    if (!staffHasAccess($conn, 'loan_delete', $staff_id)) {
        header("Location: ../loan_applications.php?err=No permission");
        exit();
    }

    $loan_id = (int)$_POST['loan_id'];
    if ($loan_id <= 0) {
        header("Location: ../loan_applications.php?err=Invalid Loan ID");
        exit();
    }

    $check = mysqli_query($conn, "SELECT id FROM loan_applications WHERE id=$loan_id AND assigned_staff_id=$staff_id");
    if (mysqli_num_rows($check) == 0) {
        header("Location: ../loan_applications.php?err=Not assigned to you");
        exit();
    }

    $docs_res = mysqli_query($conn, "SELECT doc_path FROM loan_application_docs WHERE loan_application_id = $loan_id");
    if ($docs_res) {
        while ($doc = mysqli_fetch_assoc($docs_res)) {
            $file = realpath(__DIR__ . '/../../' . $doc['doc_path']);
            if ($file && file_exists($file)) {
                unlink($file);
            }
        }
    }

    mysqli_query($conn, "DELETE FROM loan_application_docs WHERE loan_application_id = $loan_id");

    if (mysqli_query($conn, "DELETE FROM loan_applications WHERE id = $loan_id")) {
        header("Location: ../loan_applications.php?msg=Loan deleted successfully");
    } else {
        header("Location: ../loan_applications.php?err=Delete failed");
    }
    exit;
}
?>
