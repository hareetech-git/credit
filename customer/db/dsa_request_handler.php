<?php
include 'config.php';
session_start();
require_once '../../includes/dsa_notifications.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['customer_id'])) {
    header('Location: ../become-dsa.php?err=Invalid request');
    exit;
}

$customer_id = (int)$_SESSION['customer_id'];
$tblRes = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_requests'");
if (!$tblRes || mysqli_num_rows($tblRes) === 0) {
    header('Location: ../become-dsa.php?err=DSA request system is not ready. Contact admin.');
    exit;
}

$pendingCheck = mysqli_query($conn, "SELECT id FROM dsa_requests WHERE customer_id = $customer_id AND status = 'pending' LIMIT 1");
if ($pendingCheck && mysqli_num_rows($pendingCheck) > 0) {
    header('Location: ../become-dsa.php?err=You already have a pending request');
    exit;
}

$full_name = mysqli_real_escape_string($conn, trim($_POST['full_name'] ?? ''));
$email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
$phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
$firm_name = mysqli_real_escape_string($conn, trim($_POST['firm_name'] ?? ''));
$pan_number = mysqli_real_escape_string($conn, strtoupper(trim($_POST['pan_number'] ?? '')));
$city = mysqli_real_escape_string($conn, trim($_POST['city'] ?? ''));
$state = mysqli_real_escape_string($conn, trim($_POST['state'] ?? ''));
$pin_code = mysqli_real_escape_string($conn, trim($_POST['pin_code'] ?? ''));
$bank_name = mysqli_real_escape_string($conn, trim($_POST['bank_name'] ?? ''));
$account_number = mysqli_real_escape_string($conn, trim($_POST['account_number'] ?? ''));
$ifsc_code = mysqli_real_escape_string($conn, strtoupper(trim($_POST['ifsc_code'] ?? '')));
$message = mysqli_real_escape_string($conn, trim($_POST['message'] ?? ''));

if ($full_name === '' || $email === '' || $phone === '' || $firm_name === '') {
    header('Location: ../become-dsa.php?err=Please fill all required fields');
    exit;
}

$sql = "INSERT INTO dsa_requests (customer_id, full_name, email, phone, firm_name, pan_number, city, state, pin_code, bank_name, account_number, ifsc_code, message, status)
        VALUES ($customer_id, '$full_name', '$email', '$phone', '$firm_name', '$pan_number', '$city', '$state', '$pin_code', '$bank_name', '$account_number', '$ifsc_code', '$message', 'pending')";

if (!mysqli_query($conn, $sql)) {
    header('Location: ../become-dsa.php?err=Unable to submit request');
    exit;
}

$request_id = (int)mysqli_insert_id($conn);
dsaNotifyAdminsOnNewRequest($conn, $request_id);

header('Location: ../become-dsa.php?msg=Request submitted successfully. Admin will verify and notify you by email.');
exit;
?>
