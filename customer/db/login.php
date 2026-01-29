<?php
include("config.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login    = trim($_POST['login']);   // email OR phone
    $password = $_POST['password'];

    $login = mysqli_real_escape_string($conn, $login);

    // Fetch customer using email OR phone
    $sql = "SELECT id, full_name, email, phone, password, status
            FROM customers
            WHERE email = '$login' OR phone = '$login'
            LIMIT 1";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {

        $customer = mysqli_fetch_assoc($result);

        // Check account status
        if ($customer['status'] !== 'active') {
            header("Location: ../login.php?err=Account blocked");
            exit;
        }

        // Verify password
        if (password_verify($password, $customer['password'])) {

            // ✅ Store customer session
            $_SESSION['customer_id']    = $customer['id'];
            $_SESSION['customer_name']  = $customer['full_name'];
            $_SESSION['customer_email'] = $customer['email'];
            $_SESSION['customer_phone'] = $customer['phone'];

            header("Location: ../dashboard.php");
            exit;

        } else {
            header("Location: ../login.php?err=Wrong password");
            exit;
        }

    } else {
        header("Location: ../login.php?err=Invalid email or phone");
        exit;
    }
}
?>
