<?php
session_start();
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$id   = isset($data['id']) ? (int)$data['id'] : 0;
$name = trim($data['name']);

if ($name === '') {
    echo json_encode(['status' => 'error']);
    exit;
}

if ($id > 0) {

    $stmt = mysqli_prepare($conn,
        "UPDATE departments SET name = ?, updated_at = NOW() WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, "si", $name, $id);
    mysqli_stmt_execute($stmt);

} else {

    $createdBy = $_SESSION['admin_id'] ?? null;

    $stmt = mysqli_prepare($conn,
        "INSERT INTO departments (name, created_by) VALUES (?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "si", $name, $createdBy);
    mysqli_stmt_execute($stmt);
}

echo json_encode(['status' => 'success']);
