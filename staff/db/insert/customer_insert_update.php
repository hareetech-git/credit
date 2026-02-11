<?php
include '../config.php';

$action = $_POST['action'] ?? '';

if ($action === 'create' || $action === 'update') {
    $id = (int)($_POST['customer_id'] ?? 0);

    $name = mysqli_real_escape_string($conn, (string)($_POST['full_name'] ?? ''));
    $email = mysqli_real_escape_string($conn, (string)($_POST['email'] ?? ''));
    $phone = mysqli_real_escape_string($conn, (string)($_POST['phone'] ?? ''));
    $status = mysqli_real_escape_string($conn, (string)($_POST['status'] ?? 'active'));

    if ($action === 'create') {
        $pass = password_hash((string)($_POST['password'] ?? ''), PASSWORD_BCRYPT);
        $sql = "INSERT INTO customers (full_name, email, phone, password, status)
                VALUES ('$name', '$email', '$phone', '$pass', '$status')";

        if (mysqli_query($conn, $sql)) {
            $id = (int)mysqli_insert_id($conn);
            mysqli_query($conn, "INSERT INTO customer_profiles (customer_id) VALUES ($id)");
            mysqli_query($conn, "UPDATE enquiries SET customer_id = $id WHERE customer_id IS NULL AND email = '$email'");
        } else {
            die('Error creating customer: ' . mysqli_error($conn));
        }
    } else {
        $sql = "UPDATE customers SET
                full_name='$name',
                email='$email',
                phone='$phone',
                status='$status'
                WHERE id=$id";
        mysqli_query($conn, $sql);
        mysqli_query($conn, "UPDATE enquiries SET customer_id = $id WHERE customer_id IS NULL AND email = '$email'");
    }

    $panPlain = strtoupper(trim((string)($_POST['pan_number'] ?? '')));
    $pan = mysqli_real_escape_string($conn, uc_encrypt_sensitive($panPlain));
    $dob = mysqli_real_escape_string($conn, (string)($_POST['birth_date'] ?? ''));
    $city = mysqli_real_escape_string($conn, (string)($_POST['city'] ?? ''));
    $state = mysqli_real_escape_string($conn, (string)($_POST['state'] ?? ''));
    $pin = mysqli_real_escape_string($conn, (string)($_POST['pin_code'] ?? ''));
    $emp = mysqli_real_escape_string($conn, (string)($_POST['employee_type'] ?? ''));
    $income = mysqli_real_escape_string($conn, (string)($_POST['monthly_income'] ?? ''));

    $profileSql = "UPDATE customer_profiles SET
                   pan_number='$pan',
                   birth_date='$dob',
                   city='$city',
                   state='$state',
                   pin_code='$pin',
                   employee_type='$emp',
                   monthly_income='$income'
                   WHERE customer_id=$id";
    mysqli_query($conn, $profileSql);

    header('Location: ../../customers.php?msg=Customer Saved Successfully');
    exit;
}

header('Location: ../../customers.php?err=Invalid Request');
exit;

