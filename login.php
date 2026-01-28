<?php
require_once 'includes/header.php';
?>

<style>
    body {
        margin: 0;
        font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .login-container {
        width: 450px;
        height: 450px;
        background: #fff;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .login-container h2 {
        text-align: center;
        font-weight: 700;
        margin-bottom: 8px;
        color: #111827;
        font-size: 24px;
    }

    .login-container p {
        text-align: center;
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .form-group input {
        width: 100%;
        padding: 12px 14px;
        font-size: 14px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .form-group input:focus {
        outline: none;
        border-color: #0b081b;
        box-shadow: 0 0 0 3px rgba(11, 8, 27, 0.1);
    }

    .password-wrap {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        font-size: 13px;
        color: #0b081b;
        cursor: pointer;
        user-select: none;
        font-weight: 500;
    }

    .toggle-password:hover {
        color: #100b2c;
    }

    .login-btn {
        width: 100%;
        padding: 13px;
        border: none;
        border-radius: 8px;
        background: #0b081b;
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 10px;
    }

    .login-btn:hover {
        background: #100b2c;
        transform: translateY(-1px);
    }

    .extra-links {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
    }

    .extra-links a {
        color: #0b081b;
        text-decoration: none;
        font-weight: 500;
    }

    .extra-links a:hover {
        text-decoration: underline;
    }

    .security-text {
        text-align: center;
        font-size: 12px;
        color: #9ca3af;
        margin-top: 15px;
    }

    @media (max-width: 576px) {
        .login-container {
            width: 100%;
            max-width: 400px;
            height: auto;
            min-height: 450px;
        }
    }
</style>
<div style="height:30px;"></div>
<section class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 85px);">
    <div class="login-container">
        <h2>Welcome Back</h2>
        <p>Login to manage your account</p>

        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="password-wrap">
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                    <span class="toggle-password" onclick="togglePassword()">Show</span>
                </div>
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="extra-links">
            <a href="forgot-password.php">Forgot Password?</a>
        </div>

        <div class="security-text">
            🔒 Your data is securely encrypted
        </div>
    </div>
</section>

<script>
function togglePassword() {
    const pass = document.getElementById("password");
    const toggleBtn = event.target;
    
    if (pass.type === "password") {
        pass.type = "text";
        toggleBtn.innerText = "Hide";
    } else {
        pass.type = "password";
        toggleBtn.innerText = "Show";
    }
}
</script>

