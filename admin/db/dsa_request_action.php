<?php
include 'config.php';
session_start();
require_once '../../includes/dsa_notifications.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['admin_id'])) {
    header('Location: ../dsa_requests.php?err=Invalid request');
    exit;
}

$admin_id = (int)$_SESSION['admin_id'];
$tblRes = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_requests'");
if (!$tblRes || mysqli_num_rows($tblRes) === 0) {
    header('Location: ../dsa_requests.php?err=DSA request system is not ready');
    exit;
}
$request_id = (int)($_POST['request_id'] ?? 0);
$action_type = strtolower(trim($_POST['action_type'] ?? ''));
$admin_note = mysqli_real_escape_string($conn, trim($_POST['admin_note'] ?? ''));

if ($request_id <= 0 || !in_array($action_type, ['approve', 'reject'], true)) {
    header('Location: ../dsa_requests.php?err=Invalid action');
    exit;
}

$requestRes = mysqli_query($conn, "SELECT * FROM dsa_requests WHERE id = $request_id AND status = 'pending' LIMIT 1");
if (!$requestRes || mysqli_num_rows($requestRes) === 0) {
    header('Location: ../dsa_requests.php?err=Request already processed or not found');
    exit;
}
$request = mysqli_fetch_assoc($requestRes);

mysqli_begin_transaction($conn);

try {
    if ($action_type === 'approve') {
        $full_name = mysqli_real_escape_string($conn, (string)$request['full_name']);
        $email = mysqli_real_escape_string($conn, (string)$request['email']);
        $phone = mysqli_real_escape_string($conn, (string)$request['phone']);

        $plainPassword = 'DSA' . random_int(100000, 999999) . '!';
        $hashedPassword = mysqli_real_escape_string($conn, password_hash($plainPassword, PASSWORD_DEFAULT));

        $dsa_id = 0;
        $existingRes = mysqli_query($conn, "SELECT id FROM dsa WHERE email = '$email' OR phone = '$phone' LIMIT 1");
        if ($existingRes && mysqli_num_rows($existingRes) > 0) {
            $existing = mysqli_fetch_assoc($existingRes);
            $dsa_id = (int)$existing['id'];
            mysqli_query($conn, "UPDATE dsa SET name='$full_name', email='$email', phone='$phone', password='$hashedPassword', status='active', updated_at=NOW() WHERE id=$dsa_id");
        } else {
            mysqli_query($conn, "INSERT INTO dsa (name, email, phone, password, created_by, status) VALUES ('$full_name', '$email', '$phone', '$hashedPassword', $admin_id, 'active')");
            $dsa_id = (int)mysqli_insert_id($conn);
        }

        $firm_name = mysqli_real_escape_string($conn, (string)$request['firm_name']);
        $pan_plain = uc_decrypt_sensitive((string)$request['pan_number']);
        $pan_number = mysqli_real_escape_string($conn, uc_encrypt_sensitive($pan_plain));
        $city = mysqli_real_escape_string($conn, (string)$request['city']);
        $state = mysqli_real_escape_string($conn, (string)$request['state']);
        $pin_code = mysqli_real_escape_string($conn, (string)$request['pin_code']);
        $bank_name = mysqli_real_escape_string($conn, (string)$request['bank_name']);
        $account_plain = uc_decrypt_sensitive((string)$request['account_number']);
        $account_number = mysqli_real_escape_string($conn, uc_encrypt_sensitive($account_plain));
        $ifsc_code = mysqli_real_escape_string($conn, (string)$request['ifsc_code']);

        $profileRes = mysqli_query($conn, "SELECT id FROM dsa_profiles WHERE dsa_id = $dsa_id LIMIT 1");
        if ($profileRes && mysqli_num_rows($profileRes) > 0) {
            mysqli_query($conn, "UPDATE dsa_profiles SET firm_name='$firm_name', pan_number='$pan_number', city='$city', state='$state', pin_code='$pin_code', bank_name='$bank_name', account_number='$account_number', ifsc_code='$ifsc_code', updated_at=NOW() WHERE dsa_id = $dsa_id");
        } else {
            mysqli_query($conn, "INSERT INTO dsa_profiles (dsa_id, firm_name, pan_number, city, state, pin_code, bank_name, account_number, ifsc_code) VALUES ($dsa_id, '$firm_name', '$pan_number', '$city', '$state', '$pin_code', '$bank_name', '$account_number', '$ifsc_code')");
        }

        $permTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_permissions'");
        $userPermTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_user_permissions'");
        if ($permTbl && mysqli_num_rows($permTbl) > 0 && $userPermTbl && mysqli_num_rows($userPermTbl) > 0) {
            mysqli_query($conn, "INSERT IGNORE INTO dsa_user_permissions (dsa_id, permission_id) SELECT $dsa_id, id FROM dsa_permissions");
        }

        mysqli_query($conn, "UPDATE dsa_requests SET status='approved', dsa_id=$dsa_id, admin_note='$admin_note', reviewed_by=$admin_id, reviewed_at=NOW(), updated_at=NOW() WHERE id=$request_id");

        mysqli_commit($conn);

        dsaNotifyApplicantDecision($conn, $request_id, 'approved', $admin_note, $request['email'], $plainPassword);

        header('Location: ../dsa_requests.php?msg=Request approved and credentials sent successfully');
        exit;
    }

    mysqli_query($conn, "UPDATE dsa_requests SET status='rejected', admin_note='$admin_note', reviewed_by=$admin_id, reviewed_at=NOW(), updated_at=NOW() WHERE id=$request_id");
    mysqli_commit($conn);

    dsaNotifyApplicantDecision($conn, $request_id, 'rejected', $admin_note);

    header('Location: ../dsa_requests.php?msg=Request rejected and applicant notified');
    exit;

} catch (Throwable $e) {
    mysqli_rollback($conn);
    header('Location: ../dsa_requests.php?err=Unable to process request');
    exit;
}
?>
