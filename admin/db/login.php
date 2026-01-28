<?php
include("config.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare statement (NO SQL injection)
    $sql = "SELECT id, name, email, password, role 
            FROM admin 
            WHERE email = ? 
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        header("Location: ../index.php?err=Server error");
        exit;
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {

        $admin = mysqli_fetch_assoc($result);

        // 🔐 Verify hashed password
        if (password_verify($password, $admin['password'])) {

            // ✅ Store admin session data
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_role'] = $admin['role'];

            header("Location: ../dashboard.php");
            exit;

        } else {
            header("Location: ../index.php?err=Wrong password");
            exit;
        }

    } else {
        header("Location: ../index.php?err=Invalid email");
        exit;
    }
}
?>
