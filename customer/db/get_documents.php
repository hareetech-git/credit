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
