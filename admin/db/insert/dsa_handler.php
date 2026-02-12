<?php
include '../config.php';
session_start();
require_once '../../../includes/dsa_notifications.php';

if (isset($_POST['save_dsa'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $department_id = (int)($_POST['department_id'] ?? 0);
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'active');
    $admin_id = (int)($_SESSION['admin_id'] ?? 0);

    if (isset($_POST['dsa_id']) && (int)$_POST['dsa_id'] > 0) {
        $dsa_id = (int)$_POST['dsa_id'];
        $sql = "UPDATE dsa SET name='$name', email='$email', phone='$phone', department_id=$department_id, status='$status' WHERE id=$dsa_id";
        mysqli_query($conn, $sql);

        if (!empty($_POST['password'])) {
            $password = mysqli_real_escape_string($conn, password_hash($_POST['password'], PASSWORD_DEFAULT));
            mysqli_query($conn, "UPDATE dsa SET password='$password' WHERE id=$dsa_id");
        }

        $msg = 'DSA updated successfully';
    } else {
        $plainPassword = trim((string)($_POST['password'] ?? ''));
        $password = mysqli_real_escape_string($conn, password_hash($plainPassword, PASSWORD_DEFAULT));
        $sql = "INSERT INTO dsa (name, email, phone, password, department_id, created_by, status) VALUES ('$name', '$email', '$phone', '$password', $department_id, $admin_id, '$status')";
        if (mysqli_query($conn, $sql)) {
            $dsa_id = (int)mysqli_insert_id($conn);
            mysqli_query($conn, "INSERT INTO dsa_profiles (dsa_id) VALUES ($dsa_id)");
            $permTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_permissions'");
            $userPermTbl = mysqli_query($conn, "SHOW TABLES LIKE 'dsa_user_permissions'");
            if ($permTbl && mysqli_num_rows($permTbl) > 0 && $userPermTbl && mysqli_num_rows($userPermTbl) > 0) {
                mysqli_query($conn, "INSERT IGNORE INTO dsa_user_permissions (dsa_id, permission_id) SELECT $dsa_id, id FROM dsa_permissions");
            }

            $mailSent = dsaNotifyCreatedByAdmin($email, $name, $plainPassword);
            if (!$mailSent) {
                $msg = 'DSA created, but credentials email could not be sent';
                header('Location: ../../dsa_list.php?msg=' . urlencode($msg));
                exit;
            }
        }
        $msg = 'DSA created successfully';
    }

    header('Location: ../../dsa_list.php?msg=' . urlencode($msg));
    exit;
}
?>
