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

$firm_name = mysqli_real_escape_string($conn, trim($_POST['firm_name'] ?? ''));
$pan_number = mysqli_real_escape_string($conn, strtoupper(trim($_POST['pan_number'] ?? '')));
$city = mysqli_real_escape_string($conn, trim($_POST['city'] ?? ''));
$state = mysqli_real_escape_string($conn, trim($_POST['state'] ?? ''));
$pin_code = mysqli_real_escape_string($conn, trim($_POST['pin_code'] ?? ''));
$bank_name = mysqli_real_escape_string($conn, trim($_POST['bank_name'] ?? ''));
$account_number = mysqli_real_escape_string($conn, trim($_POST['account_number'] ?? ''));
$ifsc_code = mysqli_real_escape_string($conn, strtoupper(trim($_POST['ifsc_code'] ?? '')));

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
