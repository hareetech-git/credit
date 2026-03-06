<?php
include("config.php");
require_once __DIR__ . '/../../includes/customer_password_reset.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../forgot-password.php");
    exit;
}

$action = trim((string)($_POST['action'] ?? 'send_otp'));

if ($action === 'restart') {
    customerPasswordResetSessionClear();
    header("Location: ../../forgot-password.php");
    exit;
}

if ($action === 'send_otp') {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../../forgot-password.php?err=" . urlencode("Please enter a valid email address."));
        exit;
    }

    $safeEmail = mysqli_real_escape_string($conn, $email);
    $customerRes = mysqli_query(
        $conn,
        "SELECT id, full_name, email, status
         FROM customers
         WHERE email = '$safeEmail'
         LIMIT 1"
    );

    if (!$customerRes || mysqli_num_rows($customerRes) !== 1) {
        customerPasswordResetSessionClear();
        header("Location: ../../forgot-password.php?err=" . urlencode("Email is not registered."));
        exit;
    }

    $customer = mysqli_fetch_assoc($customerRes);
    if (strtolower((string)($customer['status'] ?? '')) !== 'active') {
        customerPasswordResetSessionClear();
        header("Location: ../../forgot-password.php?err=" . urlencode("Account is blocked. Please contact support."));
        exit;
    }

    $otp = customerPasswordResetGenerateOtp();
    if ($otp === null) {
        customerPasswordResetSessionClear();
        header("Location: ../../forgot-password.php?err=" . urlencode("Unable to generate OTP. Please try again."));
        exit;
    }

    customerPasswordResetSessionSet(
        (int)$customer['id'],
        (string)$customer['email'],
        (string)$customer['full_name'],
        $otp
    );

    $mailSent = customerPasswordResetSendOtpMail(
        (string)$customer['email'],
        (string)$customer['full_name'],
        $otp
    );

    if (!$mailSent) {
        customerPasswordResetSessionClear();
        header("Location: ../../forgot-password.php?err=" . urlencode("OTP email failed to send. Please try again."));
        exit;
    }

    header("Location: ../../forgot-password.php?step=verify&msg=" . urlencode("OTP sent to your registered email."));
    exit;
}

if ($action === 'verify_otp_reset') {
    if (!customerPasswordResetSessionIsActive()) {
        customerPasswordResetSessionClear();
        header("Location: ../../forgot-password.php?err=" . urlencode("OTP expired. Please request a new OTP."));
        exit;
    }

    $otp = trim((string)($_POST['otp'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if (!preg_match('/^[0-9]{6}$/', $otp)) {
        header("Location: ../../forgot-password.php?step=verify&err=" . urlencode("Enter a valid 6-digit OTP."));
        exit;
    }

    if (strlen($password) < 8) {
        header("Location: ../../forgot-password.php?step=verify&err=" . urlencode("Password must be at least 8 characters."));
        exit;
    }

    if ($password !== $confirmPassword) {
        header("Location: ../../forgot-password.php?step=verify&err=" . urlencode("Passwords do not match."));
        exit;
    }

    $otpHash = (string)($_SESSION['customer_reset_otp_hash'] ?? '');
    if ($otpHash === '' || !password_verify($otp, $otpHash)) {
        $attempts = (int)($_SESSION['customer_reset_attempts'] ?? 0) + 1;
        $_SESSION['customer_reset_attempts'] = $attempts;

        if ($attempts >= CUSTOMER_PASSWORD_OTP_MAX_ATTEMPTS) {
            customerPasswordResetSessionClear();
            header("Location: ../../forgot-password.php?err=" . urlencode("Too many invalid OTP attempts. Please request a new OTP."));
            exit;
        }

        header("Location: ../../forgot-password.php?step=verify&err=" . urlencode("Invalid OTP. Please try again."));
        exit;
    }

    $customerId = (int)($_SESSION['customer_reset_customer_id'] ?? 0);
    $email = mysqli_real_escape_string($conn, (string)($_SESSION['customer_reset_email'] ?? ''));
    if ($customerId <= 0 || $email === '') {
        customerPasswordResetSessionClear();
        header("Location: ../../forgot-password.php?err=" . urlencode("Session expired. Please request OTP again."));
        exit;
    }

    $passwordHash = mysqli_real_escape_string($conn, password_hash($password, PASSWORD_BCRYPT));
    $updated = mysqli_query(
        $conn,
        "UPDATE customers
         SET password = '$passwordHash', updated_at = NOW()
         WHERE id = $customerId AND email = '$email' AND status = 'active'
         LIMIT 1"
    );

    if (!$updated || mysqli_affected_rows($conn) < 1) {
        header("Location: ../../forgot-password.php?step=verify&err=" . urlencode("Failed to update password. Please try again."));
        exit;
    }

    customerPasswordResetSessionClear();
    header("Location: ../../login.php?msg=" . urlencode("Password updated successfully. Please sign in."));
    exit;
}

if ($action === 'resend_otp') {
    if (!customerPasswordResetSessionIsActive()) {
        customerPasswordResetSessionClear();
        header("Location: ../../forgot-password.php?err=" . urlencode("OTP expired. Please request a new OTP."));
        exit;
    }

    $waitSeconds = customerPasswordResetSessionResendWaitSeconds();
    if ($waitSeconds > 0) {
        header("Location: ../../forgot-password.php?step=verify&err=" . urlencode("Please wait {$waitSeconds} seconds before resending OTP."));
        exit;
    }

    $customerId = (int)($_SESSION['customer_reset_customer_id'] ?? 0);
    $sessionEmail = strtolower(trim((string)($_SESSION['customer_reset_email'] ?? '')));
    $sessionName = trim((string)($_SESSION['customer_reset_name'] ?? ''));

    if ($customerId <= 0 || $sessionEmail === '') {
        customerPasswordResetSessionClear();
        header("Location: ../../forgot-password.php?err=" . urlencode("Session expired. Please request OTP again."));
        exit;
    }

    $safeEmail = mysqli_real_escape_string($conn, $sessionEmail);
    $customerRes = mysqli_query(
        $conn,
        "SELECT id, full_name, email, status
         FROM customers
         WHERE id = $customerId AND email = '$safeEmail'
         LIMIT 1"
    );

    if (!$customerRes || mysqli_num_rows($customerRes) !== 1) {
        customerPasswordResetSessionClear();
        header("Location: ../../forgot-password.php?err=" . urlencode("Account not found. Please start again."));
        exit;
    }

    $customer = mysqli_fetch_assoc($customerRes);
    if (strtolower((string)($customer['status'] ?? '')) !== 'active') {
        customerPasswordResetSessionClear();
        header("Location: ../../forgot-password.php?err=" . urlencode("Account is blocked. Please contact support."));
        exit;
    }

    $otp = customerPasswordResetGenerateOtp();
    if ($otp === null) {
        header("Location: ../../forgot-password.php?step=verify&err=" . urlencode("Unable to generate OTP. Please try again."));
        exit;
    }

    $name = (string)($customer['full_name'] ?? $sessionName);
    $email = (string)($customer['email'] ?? $sessionEmail);

    $mailSent = customerPasswordResetSendOtpMail($email, $name, $otp);
    if (!$mailSent) {
        header("Location: ../../forgot-password.php?step=verify&err=" . urlencode("OTP email failed to send. Please try again."));
        exit;
    }

    customerPasswordResetSessionSet($customerId, $email, $name, $otp);

    header("Location: ../../forgot-password.php?step=verify&msg=" . urlencode("OTP resent successfully."));
    exit;
}

header("Location: ../../forgot-password.php?err=" . urlencode("Invalid request."));
exit;
