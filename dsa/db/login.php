<?php
include("config.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT id, name, email, password, status FROM dsa WHERE email = ? LIMIT 1");

    if (!$stmt) {
        header("Location: ../index.php?err=Server error");
        exit;
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
        $dsa = mysqli_fetch_assoc($result);

        if (!password_verify($password, $dsa['password'])) {
            header("Location: ../index.php?err=Invalid credentials");
            exit;
        }

        if ($dsa['status'] !== 'active') {
            header("Location: ../index.php?err=Account inactive. Contact Admin.");
            exit;
        }

        $_SESSION['dsa_id'] = $dsa['id'];
        $_SESSION['dsa_name'] = $dsa['name'];
        $_SESSION['dsa_email'] = $dsa['email'];

        header("Location: ../dashboard.php");
        exit;
    }

    header("Location: ../index.php?err=Invalid credentials");
    exit;
}
?>
