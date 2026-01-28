<?php
session_start();
include 'config.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid department';
    header('Location: ../departments.php');
    exit;
}

/*
| CHECK: Is any category using this department?
*/
$check = mysqli_prepare(
    $conn,
    "SELECT id FROM service_categories WHERE department = ? LIMIT 1"
);
mysqli_stmt_bind_param($check, "i", $id);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    $_SESSION['error'] = 'First delete all related categories';
    header('Location: ../departments.php');
    exit;
}

/*
| DELETE DEPARTMENT
*/
$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM departments WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$_SESSION['success'] = 'Department deleted successfully';
header('Location: ../departments.php');
exit;
