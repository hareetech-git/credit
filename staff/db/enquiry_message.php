<?php
session_start();
include 'config.php';
include 'enquiry_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../enquiries.php?err=Invalid request");
    exit;
}

$enquiry_id = (int)($_POST['enquiry_id'] ?? 0);
$message = trim($_POST['message'] ?? '');
$staff_id = isset($_SESSION['staff_id']) ? (int)$_SESSION['staff_id'] : null;

if ($enquiry_id <= 0 || $message === '' || !$staff_id) {
    header("Location: ../enquiry_view.php?id=$enquiry_id&err=Missing message");
    exit;
}

if (!staffCanAccessEnquiry($conn, $staff_id, $enquiry_id)) {
    header("Location: ../enquiries.php?err=Access denied");
    exit;
}

$safe_message = mysqli_real_escape_string($conn, $message);

$status_res = mysqli_query($conn, "SELECT status FROM enquiries WHERE id = $enquiry_id LIMIT 1");
if ($status_res && ($st = mysqli_fetch_assoc($status_res))) {
    if (in_array($st['status'], ['closed','converted'], true)) {
        header("Location: ../enquiry_view.php?id=$enquiry_id&err=Enquiry closed/converted");
        exit;
    }
}

mysqli_query($conn, "INSERT IGNORE INTO enquiry_conversations (enquiry_id) VALUES ($enquiry_id)");
$conv_res = mysqli_query($conn, "SELECT id FROM enquiry_conversations WHERE enquiry_id = $enquiry_id LIMIT 1");
$conversation_id = 0;
if ($conv_res && mysqli_num_rows($conv_res) > 0) {
    $conv = mysqli_fetch_assoc($conv_res);
    $conversation_id = (int)$conv['id'];
}

if ($conversation_id <= 0) {
    header("Location: ../enquiry_view.php?id=$enquiry_id&err=Conversation error");
    exit;
}

$insert = "INSERT INTO enquiry_messages (conversation_id, sender_role, sender_id, message)
           VALUES ($conversation_id, 'staff', $staff_id, '$safe_message')";

if (mysqli_query($conn, $insert)) {
    mysqli_query($conn, "UPDATE enquiries 
                         SET status = CASE 
                             WHEN status IN ('converted','closed') THEN status 
                             ELSE 'conversation' 
                         END 
                         WHERE id = $enquiry_id");
    header("Location: ../enquiry_view.php?id=$enquiry_id#messages");
} else {
    header("Location: ../enquiry_view.php?id=$enquiry_id&err=Message failed");
}
exit;
?>
