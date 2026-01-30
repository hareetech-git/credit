<?php
include("config.php");
session_start();

if (!isset($_SESSION['customer_id'])) { exit; }
$cid = $_SESSION['customer_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $aadhaar = mysqli_real_escape_string($conn, $_POST['aadhaar_number'] ?? '');
    $pan     = strtoupper(mysqli_real_escape_string($conn, $_POST['pan_number'] ?? ''));
    $dob     = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $emp_type = mysqli_real_escape_string($conn, $_POST['employee_type']);
    $company  = mysqli_real_escape_string($conn, $_POST['company_name']);
    $income   = mysqli_real_escape_string($conn, $_POST['monthly_income']);
    $city    = mysqli_real_escape_string($conn, $_POST['city']);
    $state   = mysqli_real_escape_string($conn, $_POST['state']);
    $pin     = mysqli_real_escape_string($conn, $_POST['pin_code']);
    $ref1_n  = mysqli_real_escape_string($conn, $_POST['reference1_name']);
    $ref1_p  = mysqli_real_escape_string($conn, $_POST['reference1_phone']);
    $ref2_n  = mysqli_real_escape_string($conn, $_POST['reference2_name']);
    $ref2_p  = mysqli_real_escape_string($conn, $_POST['reference2_phone']);

    $current_pass = $_POST['current_password'] ?? '';
    $new_pass     = $_POST['new_password'] ?? '';
    $pass_msg     = "";

    mysqli_begin_transaction($conn);
    try {
        // Password Logic
        if (!empty($new_pass)) {
            $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM customers WHERE id = $cid"));
            if (password_verify($current_pass, $user['password'])) {
                $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
                mysqli_query($conn, "UPDATE customers SET password = '$hashed' WHERE id = $cid");
                $pass_msg = " & Password Updated";
            } else { throw new Exception("Current password incorrect"); }
        }

        // Identity Update
        if (!empty($aadhaar)) {
            mysqli_query($conn, "UPDATE customers SET aadhaar_number = '$aadhaar' WHERE id = $cid AND (aadhaar_number IS NULL OR aadhaar_number = '')");
        }

        // Profile Update
        $sql = "INSERT INTO customer_profiles (customer_id, pan_number, birth_date, state, city, pin_code, employee_type, company_name, monthly_income, reference1_name, reference1_phone, reference2_name, reference2_phone) 
                VALUES ($cid, '$pan', '$dob', '$state', '$city', '$pin', '$emp_type', '$company', '$income', '$ref1_n', '$ref1_p', '$ref2_n', '$ref2_p') 
                ON DUPLICATE KEY UPDATE 
                pan_number = IF(pan_number IS NULL OR pan_number = '', '$pan', pan_number), birth_date='$dob', state='$state', city='$city', pin_code='$pin', employee_type='$emp_type', company_name='$company', monthly_income='$income', reference1_name='$ref1_n', reference1_phone='$ref1_p', reference2_name='$ref2_n', reference2_phone='$ref2_p'";
        
        mysqli_query($conn, $sql);
        mysqli_commit($conn);
        header("Location: ../profile.php?msg=Profile Updated" . $pass_msg);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: ../profile.php?err=" . $e->getMessage());
    }
}