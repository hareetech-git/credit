<?php
session_start();
include '../../includes/connection.php';

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    exit;
}

$customer_id = (int) $_SESSION['customer_id'];
$action = $_GET['action'] ?? '';

/* ================= FETCH DOCUMENTS ================= */
if ($action === 'fetch') {

    $sql = "
    SELECT 
        d.id,
        d.doc_name,
        d.doc_path,
        d.status,
        d.rejection_reason,
        d.created_at,
        la.id AS loan_id,
        s.service_name
    FROM loan_application_docs d
    JOIN loan_applications la ON la.id = d.loan_application_id
    JOIN services s ON s.id = la.service_id
    WHERE la.customer_id = $customer_id
    ORDER BY d.created_at DESC
    ";

    $res = mysqli_query($conn, $sql);
    $data = [];

    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}

/* ================= DELETE ONLY REJECTED DOC ================= */
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $doc_id = (int)($_POST['doc_id'] ?? 0);

    $check = "
    SELECT d.doc_path
    FROM loan_application_docs d
    JOIN loan_applications la ON la.id = d.loan_application_id
    WHERE d.id = $doc_id
    AND la.customer_id = $customer_id
    AND d.status = 'rejected'
    ";

    $res = mysqli_query($conn, $check);

    if (mysqli_num_rows($res) === 1) {

        $doc = mysqli_fetch_assoc($res);
        $file = '../../' . $doc['doc_path'];

        if (file_exists($file)) {
            unlink($file);
        }

        mysqli_query($conn, "DELETE FROM loan_application_docs WHERE id = $doc_id");

        echo json_encode(['success' => true]);
    } else {
        http_response_code(403);
        echo json_encode(['success' => false]);
    }
    exit;
}

/* ================= UPLOAD NEW DOC ================= */
if ($action === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $loan_id = (int)($_POST['loan_id'] ?? 0);
    $doc_name = trim($_POST['doc_name'] ?? '');

    if ($loan_id <= 0 || $doc_name === '' || empty($_FILES['doc_file']['name'])) {
        header("Location: ../view-application-detail.php?id=$loan_id&err=Missing document data");
        exit;
    }

    $check = "
    SELECT la.id
    FROM loan_applications la
    WHERE la.id = $loan_id
    AND la.customer_id = $customer_id
    ";
    $res = mysqli_query($conn, $check);
    if (mysqli_num_rows($res) !== 1) {
        header("Location: ../view-application-detail.php?id=$loan_id&err=Invalid loan");
        exit;
    }

    $allowed_exts = ['pdf', 'jpg', 'jpeg', 'png', 'jfif'];
    $upload_dir = realpath(__DIR__ . '/../../uploads/loans');
    if ($upload_dir === false) {
        header("Location: ../view-application-detail.php?id=$loan_id&err=Upload directory not found");
        exit;
    }

    $tmp_name = $_FILES['doc_file']['tmp_name'];
    $original = $_FILES['doc_file']['name'];
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts, true)) {
        header("Location: ../view-application-detail.php?id=$loan_id&err=Invalid file type");
        exit;
    }

    $safe_doc_name = mysqli_real_escape_string($conn, $doc_name);
    $new_name = "loan_{$loan_id}_" . time() . "_" . preg_replace('/[^A-Za-z0-9_-]/', '_', $safe_doc_name) . "." . $ext;
    $dest = $upload_dir . DIRECTORY_SEPARATOR . $new_name;

    if (!move_uploaded_file($tmp_name, $dest)) {
        header("Location: ../view-application-detail.php?id=$loan_id&err=Upload failed");
        exit;
    }

    $db_path = "uploads/loans/" . $new_name;
    $sql = "INSERT INTO loan_application_docs (loan_application_id, doc_name, doc_path, status)
            VALUES ($loan_id, '$safe_doc_name', '$db_path', 'pending')";
    if (mysqli_query($conn, $sql)) {
        header("Location: ../view-application-detail.php?id=$loan_id&msg=Document uploaded");
    } else {
        header("Location: ../view-application-detail.php?id=$loan_id&err=Database insert failed");
    }
    exit;
}
