<?php
include("config.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = trim($_POST['email']);   // can be email OR username
    $password = $_POST['password'];   // raw password

    // Escape only the identifier
    $login = mysqli_real_escape_string($conn, $login);

    // Fetch admin using email OR username
    $sql = "SELECT id, name, email, username, password 
            FROM admin 
            WHERE email = '$login' OR username = '$login'
            LIMIT 1";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {

        $admin = mysqli_fetch_assoc($result);

        // 🔐 Verify hashed password
        if (password_verify($password, $admin['password'])) {

            // Store important session data
          $_SESSION['login_user'] = $admin['username'];

            header("Location: ../dashboard.php");
            exit;

        } else {
            header("Location: ../index.php?err=Wrong password");
            exit;
        }

    } else {
        header("Location: ../index.php?err=Invalid email or username");
        exit;
    }
}
?>
