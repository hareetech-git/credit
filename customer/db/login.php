<?php
include("config.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login    = trim($_POST['login']);
    $password = $_POST['password'];
    $redirect = trim($_POST['redirect_to'] ?? ''); // Capture the hidden field

    $login = mysqli_real_escape_string($conn, $login);

    $sql = "SELECT id, full_name, email, phone, password, status
            FROM customers
            WHERE email = '$login' OR phone = '$login'
            LIMIT 1";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $customer = mysqli_fetch_assoc($result);

        if ($customer['status'] !== 'active') {
            header("Location: ../login.php?err=Account blocked");
            exit;
        }

        if (password_verify($password, $customer['password'])) {
            $_SESSION['customer_id']    = $customer['id'];
            $_SESSION['customer_name']  = $customer['full_name'];
            $_SESSION['customer_email'] = $customer['email'];
            $_SESSION['customer_phone'] = $customer['phone'];

            // ✅ REDIRECT LOGIC
            // If a redirect URL was passed, go there. Otherwise, go to profile.
            if (!empty($redirect)) {
                header("Location: ../../" . $redirect);
            } else {
                header("Location: ../profile.php");
            }
            exit;

        } else {
            header("Location: ../login.php?err=Wrong password" . ($redirect ? "&redirect=".urlencode($redirect) : ""));
            exit;
        }
    } else {
        header("Location: ../../lo.php?err=Invalid email or phone" . ($redirect ? "&redirect=".urlencode($redirect) : ""));
        exit;
    }
}
?>