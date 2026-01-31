<?php 
session_start();
require_once 'includes/header.php';
include 'includes/connection.php';
?>

<style>
    :root {
        /* Udhhar Capital Premium Palette */
        --udhhar-navy: #0b081b;     /* Your primary-color */
        --udhhar-dark: #100b2c;     /* Your primary-dark */
        --udhhar-teal: #00d4aa;     /* Your accent-teal */
        --udhhar-slate: #64748b;    /* Your text-muted */
        --udhhar-glass: rgba(255, 255, 255, 0.98);
        --udhhar-border: #eef2f6;
    }

    body {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        font-family: 'Plus Jakarta Sans', sans-serif;
        min-height: 100vh;
    }

    .registration-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 60px 20px;
    }

    .glass-card {
        background: var(--udhhar-glass);
        backdrop-filter: blur(10px);
        border: 1px solid white;
        border-radius: 32px;
        display: flex;
        overflow: hidden;
        width: 100%;
        max-width: 1100px;
        box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.1);
    }

    /* Left Side: UX Focus (Why Trust Us) */
    .brand-sidebar {
        background: var(--udhhar-navy);
        padding: 60px;
        width: 40%;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .brand-name {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -1px;
    }

    .brand-name span { color: var(--udhhar-teal); }

    .ux-benefit {
        margin-bottom: 25px;
        padding: 15px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.03);
        transition: 0.3s;
    }

    .ux-benefit:hover { background: rgba(255, 255, 255, 0.07); }

    .ux-benefit h5 { font-size: 1rem; font-weight: 700; margin-bottom: 4px; color: var(--udhhar-teal); }
    .ux-benefit p { font-size: 0.85rem; color: #cbd5e1; margin: 0; }

    /* Right Side: High-Conversion Form */
    .form-content {
        padding: 60px;
        width: 60%;
        background: white;
    }

    .form-header h2 { font-weight: 800; color: var(--udhhar-navy); margin-bottom: 8px; }
    .form-header p { color: var(--udhhar-slate); margin-bottom: 40px; font-size: 1rem; }

    .input-group-custom { margin-bottom: 24px; position: relative; }
    
    .form-label {
        display: block;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--udhhar-navy);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid var(--udhhar-border);
        border-radius: 14px;
        font-size: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #fbfcfd;
    }

    .form-control:focus {
        border-color: var(--udhhar-teal);
        background: white;
        outline: none;
        box-shadow: 0 0 0 4px rgba(0, 212, 170, 0.1);
    }

    .submit-btn {
        background: var(--udhhar-navy);
        color: white;
        padding: 18px;
        border: none;
        border-radius: 16px;
        width: 100%;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
    }

    .submit-btn:hover {
        background: var(--udhhar-dark);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .login-footer {
        text-align: center;
        margin-top: 30px;
        color: var(--udhhar-slate);
        font-size: 0.95rem;
    }

    .login-footer a {
        color: var(--udhhar-navy);
        font-weight: 700;
        text-decoration: none;
        border-bottom: 2px solid var(--udhhar-teal);
    }

    @media (max-width: 992px) {
        .brand-sidebar { display: none; }
        .form-content { width: 100%; padding: 40px 30px; }
        .glass-card { border-radius: 0; }
    }
</style>

<div class="registration-container">
    <div class="glass-card">
        <div class="brand-sidebar">
            <div class="brand-name">Udhhar<span>Capital</span></div>
            
            <div class="benefits-stack">
                <div class="ux-benefit">
                    <h5>Paperless Application</h5>
                    <p>Apply in less than 3 minutes with zero physical documents.</p>
                </div>
                <div class="ux-benefit">
                    <h5>Instant Credit Check</h5>
                    <p>Get your loan eligibility status immediately after sign up.</p>
                </div>
                <div class="ux-benefit">
                    <h5>Encrypted & Secure</h5>
                    <p>Your Aadhaar and personal data are 256-bit encrypted.</p>
                </div>
            </div>

            <div class="sidebar-footer">
                <small style="color: #64748b;">© 2026 Udhhar Capital. RBI Registered NBFC Partner.</small>
            </div>
        </div>

        <div class="form-content">
            <div class="form-header">
                <h2>Get Started</h2>
                <p>Enter your details to check your loan limit.</p>
            </div>

            <form action="customer/db/register_process.php" method="POST">
                <div class="input-group-custom">
                    <label class="form-label">Full Name as per PAN</label>
                    <input type="text" name="full_name" class="form-control" placeholder="e.g. Rahul Sharma" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group-custom">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="10-digit Mobile" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group-custom">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="name@domain.com" required>
                        </div>
                    </div>
                </div>

                <div class="input-group-custom">
                    <label class="form-label">Aadhaar Card Number</label>
                    <input type="text" name="aadhaar_number" class="form-control" placeholder="1234 5678 9012" maxlength="12">
                </div>

                <div class="input-group-custom">
                    <label class="form-label">Set Account Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Choose a strong password" required>
                </div>
<small>
By continuing, you agree to our 
<a href="privacy-policy.php">Privacy Policy</a> &
<a href="terms.php">Terms</a>
</small>
                <button type="submit" class="submit-btn">
                    Check Eligibility Now <i class="ri-arrow-right-line"></i>
                </button>
            </form>

            <div class="login-footer">
                Already have an account? <a href="login.php">Log In here</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
require_once 'includes/footer.php';
?>