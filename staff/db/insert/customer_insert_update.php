<?php
// Adjust path to config.php (assuming it is in admin/db/)
include '../config.php';

$action = $_POST['action'] ?? '';

if ($action == 'create' || $action == 'update') {
    $id = $_POST['customer_id'] ?? 0;
    
    // Sanitize Basic Inputs
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $aadhaar = mysqli_real_escape_string($conn, $_POST['aadhaar_number']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if ($action == 'create') {
        // --- CREATE NEW CUSTOMER ---
        $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO customers (full_name, email, phone, password, aadhaar_number, status) 
                VALUES ('$name', '$email', '$phone', '$pass', '$aadhaar', '$status')";
        
        if(mysqli_query($conn, $sql)) {
            $id = mysqli_insert_id($conn);
            // Create empty profile entry for the new customer
            mysqli_query($conn, "INSERT INTO customer_profiles (customer_id) VALUES ($id)");
        } else {
            die("Error creating customer: " . mysqli_error($conn));
        }

    } else {
        // --- UPDATE EXISTING CUSTOMER ---
        $sql = "UPDATE customers SET 
                full_name='$name', 
                email='$email', 
                phone='$phone', 
                aadhaar_number='$aadhaar', 
                status='$status' 
                WHERE id=$id";
        mysqli_query($conn, $sql);
    }

    // --- UPDATE PROFILE DATA (Common for Create & Update) ---
    $pan = mysqli_real_escape_string($conn, $_POST['pan_number']);
    $dob = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $pin = mysqli_real_escape_string($conn, $_POST['pin_code']);
    $emp = mysqli_real_escape_string($conn, $_POST['employee_type']);
    $income = mysqli_real_escape_string($conn, $_POST['monthly_income']);

    $p_sql = "UPDATE customer_profiles SET 
              pan_number='$pan', 
              birth_date='$dob', 
              city='$city', 
              state='$state', 
              pin_code='$pin', 
              employee_type='$emp', 
              monthly_income='$income' 
              WHERE customer_id=$id";
              
    mysqli_query($conn, $p_sql);

    // Redirect back to customers list (Go up 2 levels: insert -> db -> admin)
    header("Location: ../../customers.php?msg=Customer Saved Successfully");
    exit;
}
?>