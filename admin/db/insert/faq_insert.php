<?php
session_start();
include '../../db/config.php';

$admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : 0;
$question = trim($_POST['question'] ?? '');
$answer = trim($_POST['answer'] ?? '');
$status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

if ($admin_id <= 0) {
    header("Location: ../../index.php?err=Login required");
    exit;
}

if ($question === '' || $answer === '') {
    header("Location: ../../faqs.php?err=Question and answer required");
    exit;
}

$question_safe = mysqli_real_escape_string($conn, $question);
$answer_safe = mysqli_real_escape_string($conn, $answer);
$status = ($status === 1) ? 1 : 0;

$sql = "INSERT INTO faqs (question, answer, created_by, created_role, status)
        VALUES ('$question_safe', '$answer_safe', $admin_id, 'admin', $status)";

if (mysqli_query($conn, $sql)) {
    header("Location: ../../faqs.php?msg=FAQ added");
} else {
    header("Location: ../../faqs.php?err=Failed to add FAQ");
}
exit;
?>
