<?php
include '../includes/connection.php';
require_once '../includes/dsa_notifications.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dsa-register.php?err=Invalid request');
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$tblRes = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_requests'");
if (!$tblRes || mysqli_num_rows($tblRes) === 0) {
    header('Location: ../dsa-register.php?err=DSA request system is not ready. Contact admin.');
    exit;
}

$customer_id = (int)($_SESSION['customer_id'] ?? 0);

$full_name = mysqli_real_escape_string($conn, trim($_POST['full_name'] ?? ''));
$email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
$phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
$firm_name = mysqli_real_escape_string($conn, trim($_POST['firm_name'] ?? ''));
$pan_plain = strtoupper(trim((string)($_POST['pan_number'] ?? '')));
$pan_number = mysqli_real_escape_string($conn, uc_encrypt_sensitive($pan_plain));
$city = mysqli_real_escape_string($conn, trim($_POST['city'] ?? ''));
$state = mysqli_real_escape_string($conn, trim($_POST['state'] ?? ''));
$pin_code = mysqli_real_escape_string($conn, trim($_POST['pin_code'] ?? ''));
$bank_name = mysqli_real_escape_string($conn, trim($_POST['bank_name'] ?? ''));
$account_plain = trim((string)($_POST['account_number'] ?? ''));
$account_number = mysqli_real_escape_string($conn, uc_encrypt_sensitive($account_plain));
$ifsc_code = mysqli_real_escape_string($conn, strtoupper(trim($_POST['ifsc_code'] ?? '')));
$message = mysqli_real_escape_string($conn, trim($_POST['message'] ?? ''));

if ($full_name === '' || $email === '' || $phone === '' || $firm_name === '' || $pan_plain === '' || $city === '' || $state === '' || $pin_code === '' || $bank_name === '' || $account_plain === '' || $ifsc_code === '') {
    header('Location: ../dsa-register.php?err=Please fill all required fields');
    exit;
}

$pendingCheck = mysqli_query($conn, "SELECT id FROM dsa_requests WHERE status='pending' AND (email='$email' OR phone='$phone') LIMIT 1");
if ($pendingCheck && mysqli_num_rows($pendingCheck) > 0) {
    header('Location: ../dsa-register.php?err=You already have a pending request. Please wait for admin verification.');
    exit;
}

$sql = "INSERT INTO dsa_requests (customer_id, full_name, email, phone, firm_name, pan_number, city, state, pin_code, bank_name, account_number, ifsc_code, message, status)
        VALUES ($customer_id, '$full_name', '$email', '$phone', '$firm_name', '$pan_number', '$city', '$state', '$pin_code', '$bank_name', '$account_number', '$ifsc_code', '$message', 'pending')";

if (!mysqli_query($conn, $sql)) {
    header('Location: ../dsa-register.php?err=Unable to submit request');
    exit;
}

$request_id = (int)mysqli_insert_id($conn);
dsaNotifyAdminsOnNewRequest($conn, $request_id);

header('Location: ../dsa-register.php?msg=Request submitted successfully. Admin will verify and notify you by email.');
exit;
?>
