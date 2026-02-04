<?php
include("config.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Fetch staff by email
    $sql = "SELECT id, name, email, password, status 
            FROM staff 
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
        $staff = mysqli_fetch_assoc($result);

        // 2. Check if account is active
        if ($staff['status'] !== 'active') {
            header("Location: ../index.php?err=Account inactive. Contact Admin.");
            exit;
        }

        // 3. 🔐 Verify hashed password
        if (password_verify($password, $staff['password'])) {

            // ✅ Store specific STAFF session data
            $_SESSION['staff_id']    = $staff['id'];
            $_SESSION['staff_name']  = $staff['name'];
            $_SESSION['staff_email'] = $staff['email'];

            header("Location: ../dashboard.php");
            exit;

        } else {
            header("Location: ../index.php?err=Invalid password");
            exit;
        }

    } else {
        header("Location: ../index.php?err=Email not found");
        exit;
    }
}
?>