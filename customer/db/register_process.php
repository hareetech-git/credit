<?php
include("config.php"); // Database connection
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitize inputs
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $email     = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone     = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $aadhaar   = mysqli_real_escape_string($conn, trim($_POST['aadhaar_number']));
    $password  = $_POST['password'];

    // 1. Check if email or phone already exists
    $checkQuery = "SELECT id FROM customers WHERE email = '$email' OR phone = '$phone' LIMIT 1";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        header("Location: ../customer-registration.php?err=Email or Phone already registered");
        exit;
    }

    // 2. Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 3. Insert into database
    $sql = "INSERT INTO customers (full_name, email, phone, password, aadhaar_number, status) 
            VALUES ('$full_name', '$email', '$phone', '$hashedPassword', '$aadhaar', 'active')";

    if (mysqli_query($conn, $sql)) {
        // Get the ID of the newly created user
        $new_id = mysqli_insert_id($conn);

        // 4. Set Session variables to log them in automatically
        $_SESSION['customer_id']    = $new_id;
        $_SESSION['customer_name']  = $full_name;
        $_SESSION['customer_email'] = $email;
        $_SESSION['customer_phone'] = $phone;

        // Redirect to dashboard
        header("Location: ../profile.php");
        exit;
    } else {
        header("Location: ../customer-registration.php?err=Registration failed. Please try again.");
        exit;
    }
}
?>