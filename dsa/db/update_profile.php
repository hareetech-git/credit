<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['dsa_id'])) {
    header('Location: ../profile.php?err=Invalid request');
    exit;
}

$dsa_id = (int)$_SESSION['dsa_id'];
$permTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_permissions'");
$userPermTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_user_permissions'");
if ($permTbl && mysqli_num_rows($permTbl) > 0 && $userPermTbl && mysqli_num_rows($userPermTbl) > 0) {
    $permCheck = mysqli_query($conn, "SELECT 1
                                      FROM dsa_user_permissions up
                                      INNER JOIN dsa_permissions p ON p.id = up.permission_id
                                      WHERE up.dsa_id = $dsa_id AND p.perm_key = 'dsa_profile_manage'
                                      LIMIT 1");
    if (!$permCheck || mysqli_num_rows($permCheck) === 0) {
        header('Location: ../dashboard.php?err=Access denied');
        exit;
    }
}

$firm_name_raw = trim((string)($_POST['firm_name'] ?? ''));
$pan_plain = strtoupper(trim((string)($_POST['pan_number'] ?? '')));
$city_raw = trim((string)($_POST['city'] ?? ''));
$state_raw = trim((string)($_POST['state'] ?? ''));
$pin_code_raw = trim((string)($_POST['pin_code'] ?? ''));
$bank_name_raw = trim((string)($_POST['bank_name'] ?? ''));
$account_plain = trim((string)($_POST['account_number'] ?? ''));
$ifsc_raw = strtoupper(trim((string)($_POST['ifsc_code'] ?? '')));

if ($firm_name_raw === '' || $pan_plain === '' || $city_raw === '' || $state_raw === '' || $pin_code_raw === '' || $bank_name_raw === '' || $account_plain === '' || $ifsc_raw === '') {
    header('Location: ../profile.php?err=Please fill all required fields');
    exit;
}

if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan_plain)) {
    header('Location: ../profile.php?err=Enter valid PAN number');
    exit;
}

if (!preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $ifsc_raw)) {
    header('Location: ../profile.php?err=Enter valid IFSC code');
    exit;
}

$pin_digits = preg_replace('/\D+/', '', $pin_code_raw);
if (strlen($pin_digits) < 6 || strlen($pin_digits) > 10) {
    header('Location: ../profile.php?err=Enter valid pin code');
    exit;
}

$account_digits = preg_replace('/\D+/', '', $account_plain);
if (strlen($account_digits) < 6 || strlen($account_digits) > 20) {
    header('Location: ../profile.php?err=Enter valid account number');
    exit;
}

$firm_name = mysqli_real_escape_string($conn, $firm_name_raw);
$pan_number = mysqli_real_escape_string($conn, uc_encrypt_sensitive($pan_plain));
$city = mysqli_real_escape_string($conn, $city_raw);
$state = mysqli_real_escape_string($conn, $state_raw);
$pin_code = mysqli_real_escape_string($conn, $pin_code_raw);
$bank_name = mysqli_real_escape_string($conn, $bank_name_raw);
$account_number = mysqli_real_escape_string($conn, uc_encrypt_sensitive($account_plain));
$ifsc_code = mysqli_real_escape_string($conn, $ifsc_raw);

$existsRes = mysqli_query($conn, "SELECT id FROM dsa_profiles WHERE dsa_id = $dsa_id LIMIT 1");
if ($existsRes && mysqli_num_rows($existsRes) > 0) {
    $sql = "UPDATE dsa_profiles SET firm_name='$firm_name', pan_number='$pan_number', city='$city', state='$state', pin_code='$pin_code', bank_name='$bank_name', account_number='$account_number', ifsc_code='$ifsc_code' WHERE dsa_id=$dsa_id";
} else {
    $sql = "INSERT INTO dsa_profiles (dsa_id, firm_name, pan_number, city, state, pin_code, bank_name, account_number, ifsc_code) VALUES ($dsa_id, '$firm_name', '$pan_number', '$city', '$state', '$pin_code', '$bank_name', '$account_number', '$ifsc_code')";
}

if (mysqli_query($conn, $sql)) {
    header('Location: ../profile.php?msg=Profile updated successfully');
} else {
    header('Location: ../profile.php?err=Unable to update profile');
}
exit;
?>
