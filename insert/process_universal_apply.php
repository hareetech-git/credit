<?php
include '../includes/connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$cid = $_SESSION['customer_id'] ?? null;
$mode = $_POST['mode'] ?? 'apply'; 
mysqli_begin_transaction($conn);

try {
    if (!$cid) {
        $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
        $email     = mysqli_real_escape_string($conn, trim($_POST['email']));
        $phone     = mysqli_real_escape_string($conn, trim($_POST['phone']));
        $password  = $_POST['password'];

        $check = mysqli_query($conn, "SELECT id FROM customers WHERE email = '$email' OR phone = '$phone'");
        if(mysqli_num_rows($check) > 0) throw new Exception("Email or Phone already registered.");

        $hashedPass = password_hash($password, PASSWORD_BCRYPT);
        mysqli_query($conn, "INSERT INTO customers (full_name, email, phone, password, status) VALUES ('$full_name', '$email', '$phone', '$hashedPass', 'active')");
        $cid = mysqli_insert_id($conn);

        $pan = strtoupper(mysqli_real_escape_string($conn, $_POST['pan_number']));
        $dob = $_POST['birth_date'];
        $emp = $_POST['employee_type'];
        $inc = (float)$_POST['monthly_income'];
        $state = mysqli_real_escape_string($conn, $_POST['state']);
        $city = mysqli_real_escape_string($conn, $_POST['city']);
        $pin = mysqli_real_escape_string($conn, $_POST['pin_code']);
        $r1n = mysqli_real_escape_string($conn, $_POST['reference1_name']);
        $r1p = mysqli_real_escape_string($conn, $_POST['reference1_phone']);
        $r2n = mysqli_real_escape_string($conn, $_POST['reference2_name']);
        $r2p = mysqli_real_escape_string($conn, $_POST['reference2_phone']);

        mysqli_query($conn, "INSERT INTO customer_profiles (customer_id, pan_number, birth_date, state, city, pin_code, employee_type, monthly_income, reference1_name, reference1_phone, reference2_name, reference2_phone) 
                             VALUES ($cid, '$pan', '$dob', '$state', '$city', '$pin', '$emp', '$inc', '$r1n', '$r1p', '$r2n', '$r2p')");

        // Set Full Sessions
        $_SESSION['customer_id']     = $cid;
        $_SESSION['customer_name']   = $full_name;
        $_SESSION['customer_email']  = $email;
        $_SESSION['customer_phone']  = $phone;
        $_SESSION['reference1_name'] = $r1n;
        $_SESSION['reference2_name'] = $r2n;
    }

    if ($mode === 'register') {
        mysqli_commit($conn);
        header("Location: ../customer/dashboard.php?msg=" . urlencode("Registration successful"));
        exit;
    } else {
        $sid = (int)$_POST['service_id'];
        $amt = (float)$_POST['requested_amount'];
        mysqli_query($conn, "INSERT INTO loan_applications (customer_id, service_id, requested_amount, status) VALUES ($cid, $sid, $amt, 'pending')");
        $loan_id = mysqli_insert_id($conn);

        if (!empty($_FILES['loan_docs']['name'])) {
            foreach ($_FILES['loan_docs']['name'] as $key => $val) {
                if(empty($val)) continue;
                $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
                $new_name = "loan_{$loan_id}_" . time() . "_$key.$ext";
                if(move_uploaded_file($_FILES['loan_docs']['tmp_name'][$key], "../uploads/loans/$new_name")) {
                    $db_path = "uploads/loans/$new_name";
                    $title = str_replace('_', ' ', $key);
                    mysqli_query($conn, "INSERT INTO loan_application_docs (loan_application_id, doc_name, doc_path) VALUES ($loan_id, '$title', '$db_path')");
                }
            }
        }
        mysqli_commit($conn);
        header("Location: ../customer/dashboard.php?msg=" . urlencode("Application Submitted"));
        exit;
    }
} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: ../apply-loan.php?err=" . urlencode($e->getMessage()));
}