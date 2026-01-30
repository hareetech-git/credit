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


    /* Hide footer on mobile */
@media (max-width: 767px) {
    footer {
        display: none !important;
    }
}

</style>
<div style="height:30px;"></div>
<section class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 85px);">
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    <div id="statusToast" class="toast align-items-center border-0 text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body p-3">
                <i id="toastIcon" class="fas me-2"></i>
                <span id="toastMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
    <div class="login-container">
        <h2>Welcome Back</h2>
        <p>Login to manage your account</p>

        <form action="customer/db/login.php" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="login" placeholder="Enter your email" required>
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

// Updated Toast logic to handle encoded URLs and trigger correctly
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    const err = urlParams.get('err');

    if (msg || err) {
        const toastEl = document.getElementById('statusToast');
        const toastMsg = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');
        
        // Remove old classes
        toastEl.classList.remove('bg-success', 'bg-danger');
        toastIcon.classList.remove('fa-check-circle', 'fa-exclamation-circle');

        if (msg) {
            toastEl.classList.add('bg-success');
            toastIcon.classList.add('fa-check-circle');
            toastMsg.innerText = decodeURIComponent(msg); // Fixes %20 spaces
        } else {
            toastEl.classList.add('bg-danger');
            toastIcon.classList.add('fa-exclamation-circle');
            toastMsg.innerText = decodeURIComponent(err); // Fixes %20 spaces
        }
        
        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
        
        // Clean URL to prevent re-showing on refresh
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>

</main>

<footer class="border-top" style="background-color:#EEEEEE; border-color:#e2e8f0 !important;">
    <div class="container py-5">
        <div class="row text-center">
            <div class="col-12 mb-4">
                <h2 class="fw-bold mb-0" style="color: var(--primary-color); font-size: 2rem; letter-spacing: -0.5px;">
                    UDHAR CAPITAL
                </h2>
            </div>
        </div>
        <hr class="my-4" style="border-color: #e2e8f0;">
        <div class="row align-items-center">
            <div class="col-lg-4 text-lg-start text-center mb-3 mb-lg-0">
                <span class="me-3 fw-semibold" style="color: #334155;">Download App</span>
                <a href="#" class="d-inline-block">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" 
                         alt="Get it on Google Play" 
                         style="height: 40px;">
                </a>
            </div>
            <div class="col-lg-4 text-center mb-3 mb-lg-0">
                <div class="d-flex justify-content-center gap-3">
                    <a href="#" class="text-dark" style="font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                    <span style="color: #cbd5e1;">|</span>
                    <a href="#" class="text-dark" style="font-size: 1.5rem;"><i class="fab fa-youtube"></i></a>
                    <span style="color: #cbd5e1;">|</span>
                    <a href="#" class="text-dark" style="font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                    <span style="color: #cbd5e1;">|</span>
                    <a href="#" class="text-dark" style="font-size: 1.5rem;"><i class="fab fa-linkedin"></i></a>
                    <span style="color: #cbd5e1;">|</span>
                    <a href="#" class="text-dark" style="font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end text-center">
                <a href="#" class="text-dark text-decoration-none fw-semibold me-3">Media</a>
                <span style="color: #cbd5e1;">|</span>
                <a href="#" class="text-dark text-decoration-none fw-semibold ms-3">FAQs</a>
            </div>
        </div>
        <hr class="my-4" style="border-color: #e2e8f0;">
        <div class="row">
            <div class="col-12 text-center">
                <p class="text-muted small mb-0">
                    Copyright © <?php echo date('Y'); ?> Udhar Capital India. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>