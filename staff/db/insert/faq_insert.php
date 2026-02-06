<?php
session_start();
include '../../db/config.php';
include '../enquiry_helpers.php';

$staff_id = isset($_SESSION['staff_id']) ? (int)$_SESSION['staff_id'] : 0;
$question = trim($_POST['question'] ?? '');
$answer = trim($_POST['answer'] ?? '');
$status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

if ($staff_id <= 0) {
    header("Location: ../../index.php?err=Login required");
    exit;
}

if (!staffHasAccess($conn, 'faq_create', $staff_id)) {
    header("Location: ../../faqs.php?err=Create permission required");
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
        VALUES ('$question_safe', '$answer_safe', $staff_id, 'staff', $status)";

if (mysqli_query($conn, $sql)) {
    header("Location: ../../faqs.php?msg=FAQ added");
} else {
    header("Location: ../../faqs.php?err=Failed to add FAQ");
}
exit;
?>
