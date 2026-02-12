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

$name_raw = trim((string)($_POST['name'] ?? ''));
$phone_raw = trim((string)($_POST['phone'] ?? ''));
$firm_name_raw = trim((string)($_POST['firm_name'] ?? ''));

if ($name_raw === '' || $phone_raw === '' || $firm_name_raw === '') {
    header('Location: ../profile.php?err=Name, phone and firm name are required');
    exit;
}

$phone_digits = preg_replace('/\D+/', '', $phone_raw);
if (!preg_match('/^[6-9][0-9]{9}$/', $phone_digits)) {
    header('Location: ../profile.php?err=Enter valid 10-digit mobile number');
    exit;
}

$name = mysqli_real_escape_string($conn, $name_raw);
$phone = mysqli_real_escape_string($conn, $phone_digits);
$firm_name = mysqli_real_escape_string($conn, $firm_name_raw);

mysqli_begin_transaction($conn);

try {
    $dupeRes = mysqli_query($conn, "SELECT id FROM dsa WHERE phone = '$phone' AND id != $dsa_id LIMIT 1");
    if ($dupeRes && mysqli_num_rows($dupeRes) > 0) {
        throw new Exception('Phone number already used by another DSA account');
    }

    if (!mysqli_query($conn, "UPDATE dsa SET name='$name', phone='$phone' WHERE id=$dsa_id")) {
        throw new Exception('Unable to update DSA account');
    }

    $existsRes = mysqli_query($conn, "SELECT id FROM dsa_profiles WHERE dsa_id = $dsa_id LIMIT 1");
    if ($existsRes && mysqli_num_rows($existsRes) > 0) {
        $sql = "UPDATE dsa_profiles SET firm_name='$firm_name' WHERE dsa_id=$dsa_id";
    } else {
        $sql = "INSERT INTO dsa_profiles (dsa_id, firm_name) VALUES ($dsa_id, '$firm_name')";
    }

    if (!mysqli_query($conn, $sql)) {
        throw new Exception('Unable to update profile');
    }

    $_SESSION['dsa_name'] = $name_raw;

    mysqli_commit($conn);
    header('Location: ../profile.php?msg=Profile updated successfully');
    exit;
} catch (Throwable $e) {
    mysqli_rollback($conn);
    header('Location: ../profile.php?err=' . urlencode($e->getMessage()));
    exit;
}
?>
