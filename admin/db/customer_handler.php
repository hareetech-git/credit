<?php
include 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action == 'create' || $action == 'update') {
    $id = $_POST['customer_id'];
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $status = $_POST['status'];

    if ($action == 'create') {
        $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $sql = "INSERT INTO customers (full_name, email, phone, password, status) 
                VALUES ('$name', '$email', '$phone', '$pass', '$status')";
        mysqli_query($conn, $sql);
        $id = mysqli_insert_id($conn);
        
        // Create empty profile entry
        mysqli_query($conn, "INSERT INTO customer_profiles (customer_id) VALUES ($id)");
    } else {
        $sql = "UPDATE customers SET full_name='$name', email='$email', phone='$phone', status='$status' WHERE id=$id";
        mysqli_query($conn, $sql);
    }

    // Update Profile Data
    $pan = mysqli_real_escape_string($conn, uc_encrypt_sensitive(strtoupper(trim((string)($_POST['pan_number'] ?? '')))));
    $dob = $_POST['birth_date'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pin = $_POST['pin_code'];
    $emp = $_POST['employee_type'];
    $income = $_POST['monthly_income'];

    $p_sql = "UPDATE customer_profiles SET 
              pan_number='$pan', birth_date='$dob', city='$city', state='$state', pin_code='$pin', 
              employee_type='$emp', monthly_income='$income' 
              WHERE customer_id=$id";
    mysqli_query($conn, $p_sql);

    header("Location: ../customers.php?msg=Customer Saved Successfully");
}

if ($action == 'delete') {
    $id = (int)$_GET['id'];
    // Delete profile first due to foreign key (if strict) or logic
    mysqli_query($conn, "DELETE FROM customer_profiles WHERE customer_id=$id");
    mysqli_query($conn, "DELETE FROM customers WHERE id=$id");
    header("Location: ../customers.php?msg=Customer Deleted");
}
?>
