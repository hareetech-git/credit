<?php
session_start();
include 'config.php';
include 'enquiry_helpers.php';
require_once __DIR__ . '/../../includes/mailer.php';
require_once __DIR__ . '/../../includes/enquiry_email_template.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../enquiries.php?err=Invalid request");
    exit;
}

$enquiry_id = (int)($_POST['enquiry_id'] ?? 0);
$to_email = trim($_POST['to_email'] ?? '');
$to_name = trim($_POST['to_name'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');
$staff_id = isset($_SESSION['staff_id']) ? (int)$_SESSION['staff_id'] : null;

if ($enquiry_id <= 0 || $to_email === '' || $subject === '' || $message === '' || !$staff_id) {
    header("Location: ../enquiry_email.php?id=$enquiry_id&err=Missing data");
    exit;
}

if (!staffCanAccessEnquiry($conn, $staff_id, $enquiry_id)) {
    header("Location: ../enquiries.php?err=Access denied");
    exit;
}

$enquiry_res = mysqli_query($conn, "SELECT id, full_name, loan_type_name FROM enquiries WHERE id = $enquiry_id LIMIT 1");
$enquiry = $enquiry_res ? mysqli_fetch_assoc($enquiry_res) : null;
if (!$enquiry) {
    header("Location: ../enquiries.php?err=Enquiry not found");
    exit;
}

$html = renderEnquiryEmailTemplate([
    'name' => $to_name,
    'enquiry_id' => $enquiry_id,
    'loan_type' => $enquiry['loan_type_name'] ?? '',
    'message' => $message,
    'brand' => 'Udhaar Capital',
]);

$sent = sendEnquiryEmail($to_email, $to_name, $subject, $html);

if ($sent) {
    header("Location: ../enquiry_email.php?id=$enquiry_id&msg=" . urlencode("Email sent successfully"));
} else {
    header("Location: ../enquiry_email.php?id=$enquiry_id&err=" . urlencode("Email failed to send"));
}
exit;
?>
