<?php
require_once 'includes/header.php';
require_once 'includes/customer_password_reset.php';

$msg = isset($_GET['msg']) ? trim((string)$_GET['msg']) : '';
$err = isset($_GET['err']) ? trim((string)$_GET['err']) : '';

if (!customerPasswordResetSessionIsActive() && isset($_SESSION['customer_reset_otp_expires'])) {
    customerPasswordResetSessionClear();
}

$otpFlowActive = customerPasswordResetSessionIsActive();
$registeredEmail = $otpFlowActive ? (string)($_SESSION['customer_reset_email'] ?? '') : '';
$otpMinutesLeft = $otpFlowActive ? (int)ceil(customerPasswordResetSessionSecondsLeft() / 60) : 0;
$resendSecondsLeft = $otpFlowActive ? customerPasswordResetSessionResendWaitSeconds() : 0;
$stepLabel = $otpFlowActive ? 'Step 2 of 2' : 'Step 1 of 2';
?>
<style>
    :root {
        --header-offset: 85px;
        --brand-dark: #0b081b;
        --brand-teal: #00d4aa;
    }

    body {
        background:
            radial-gradient(circle at 18% 10%, rgba(0, 212, 170, 0.08), transparent 40%),
            radial-gradient(circle at 85% 90%, rgba(11, 8, 27, 0.08), transparent 45%),
            #f8fafc;
    }

    .forgot-shell {
        min-height: calc(100vh - var(--header-offset));
        padding: clamp(24px, 4vw, 44px) 16px 36px;
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }

    .auth-container {
        width: min(560px, 100%);
        background: #fff;
        border-radius: 22px;
        padding: clamp(24px, 4vw, 40px);
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
        border: 1px solid #e8edf3;
    }

    .step-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(0, 212, 170, 0.12);
        color: #0f766e;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    .auth-container h2 {
        font-weight: 800;
        margin-bottom: 8px;
        color: #0b081b;
        font-size: 30px;
        line-height: 1.2;
    }

    .lead-copy {
        font-size: 15px;
        color: #64748b;
        margin-bottom: 24px;
    }

    .otp-meta {
        margin-bottom: 16px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #d1fae5;
        background: #ecfdf5;
        color: #065f46;
        font-size: 13px;
    }

    .otp-meta strong {
        font-weight: 800;
    }

    .timer-chip {
        display: inline-block;
        margin-top: 6px;
        padding: 3px 8px;
        border-radius: 999px;
        background: #d1fae5;
        font-size: 12px;
        font-weight: 700;
        color: #065f46;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #0b081b;
        margin-bottom: 8px;
        letter-spacing: 0.7px;
    }

    .form-group input {
        width: 100%;
        height: 48px;
        padding: 0 14px;
        font-size: 14px;
        border-radius: 12px;
        border: 2px solid #eef2f6;
        transition: all 0.2s ease;
        box-sizing: border-box;
        background: #fbfcfd;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--brand-teal);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(0, 212, 170, 0.1);
    }

    .otp-input {
        letter-spacing: 2px;
        font-weight: 700;
        text-align: center;
    }

    .auth-btn {
        width: 100%;
        height: 48px;
        border: none;
        border-radius: 12px;
        background: var(--brand-dark);
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s ease;
        margin-top: 4px;
    }

    .auth-btn:hover {
        background: #1a1635;
        transform: translateY(-1px);
        box-shadow: 0 10px 18px rgba(11, 8, 27, 0.15);
    }

    .auth-btn:disabled {
        background: #94a3b8;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .auth-btn-secondary {
        background: #fff;
        color: #0f172a;
        border: 1px solid #cbd5e1;
        box-shadow: none;
    }

    .auth-btn-secondary:hover {
        background: #f8fafc;
        color: #0f172a;
        box-shadow: none;
    }

    .auth-btn-ghost {
        background: transparent;
        color: #475569;
        border: 1px dashed #cbd5e1;
        box-shadow: none;
    }

    .auth-btn-ghost:hover {
        background: #f8fafc;
        color: #0f172a;
        box-shadow: none;
    }

    .resend-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-top: 10px;
    }

    .password-wrap {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        font-size: 11px;
        color: #64748b;
        cursor: pointer;
        user-select: none;
        font-weight: 700;
        text-transform: uppercase;
    }

    .extra-links {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: #64748b;
    }

    .extra-links a {
        color: #0b081b;
        text-decoration: none;
        font-weight: 700;
    }

    .extra-links a:hover {
        color: #00d4aa;
        text-decoration: underline;
    }

    .status-box {
        margin-bottom: 14px;
        border-radius: 12px;
        padding: 11px 12px;
        font-size: 13px;
        border: 1px solid transparent;
    }

    .status-success {
        background: #ecfdf5;
        color: #065f46;
        border-color: #a7f3d0;
    }

    .status-error {
        background: #fef2f2;
        color: #991b1b;
        border-color: #fecaca;
    }

    @media (max-width: 991.98px) {
        :root {
            --header-offset: 75px;
        }
    }

    @media (min-width: 576px) {
        .resend-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 767px) {
        footer { display: none !important; }
    }
</style>

<section class="forgot-shell">
    <div class="auth-container">
        <div class="step-pill"><?php echo htmlspecialchars($stepLabel); ?></div>
        <h2>Forgot Password</h2>
        <p class="lead-copy">
            <?php if ($otpFlowActive): ?>
                Verify your OTP and set a new password.
            <?php else: ?>
                Enter your registered email to get a one-time verification code.
            <?php endif; ?>
        </p>

        <?php if ($msg !== ''): ?>
            <div class="status-box status-success"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <?php if ($err !== ''): ?>
            <div class="status-box status-error"><?php echo htmlspecialchars($err); ?></div>
        <?php endif; ?>

        <?php if ($otpFlowActive): ?>
            <div class="otp-meta">
                OTP sent to <strong><?php echo htmlspecialchars($registeredEmail); ?></strong>.
                <div class="timer-chip">Code expires in <?php echo max(1, $otpMinutesLeft); ?> minute(s)</div>
            </div>

            <form action="customer/db/forgot_password.php" method="POST">
                <input type="hidden" name="action" value="verify_otp_reset">
                <div class="form-group">
                    <label>Registered Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($registeredEmail); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>OTP</label>
                    <input type="text" name="otp" class="otp-input" placeholder="Enter 6-digit OTP" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric">
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <div class="password-wrap">
                        <input type="password" name="password" id="password" placeholder="Enter new password" required minlength="8">
                        <span class="toggle-password" data-target="password">Show</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="password-wrap">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required minlength="8">
                        <span class="toggle-password" data-target="confirm_password">Show</span>
                    </div>
                </div>

                <button type="submit" class="auth-btn">Verify OTP & Update Password</button>
            </form>

            <div class="resend-grid">
                <form action="customer/db/forgot_password.php" method="POST">
                    <input type="hidden" name="action" value="resend_otp">
                    <button
                        type="submit"
                        class="auth-btn auth-btn-secondary"
                        id="resendOtpBtn"
                        data-seconds-left="<?php echo (int)$resendSecondsLeft; ?>"
                        <?php echo $resendSecondsLeft > 0 ? 'disabled' : ''; ?>
                    >
                        Resend OTP <span id="resendOtpCountdown"><?php echo $resendSecondsLeft > 0 ? '(' . (int)$resendSecondsLeft . 's)' : ''; ?></span>
                    </button>
                </form>
                <form action="customer/db/forgot_password.php" method="POST">
                    <input type="hidden" name="action" value="restart">
                    <button type="submit" class="auth-btn auth-btn-ghost">Use Different Email</button>
                </form>
            </div>
        <?php else: ?>
            <form action="customer/db/forgot_password.php" method="POST">
                <input type="hidden" name="action" value="send_otp">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your registered email" required>
                </div>

                <button type="submit" class="auth-btn">Send OTP</button>
            </form>
        <?php endif; ?>

        <div class="extra-links">
            <div class="mb-2"><a href="login.php">Back to Login</a></div>
            <div>Don't have an account? <a href="apply-loan.php?mode=register">Create Account</a></div>
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.toggle-password').forEach(function(toggle) {
    toggle.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        if (!input) {
            return;
        }
        if (input.type === 'password') {
            input.type = 'text';
            this.innerText = 'Hide';
        } else {
            input.type = 'password';
            this.innerText = 'Show';
        }
    });
});

const resendOtpBtn = document.getElementById('resendOtpBtn');
const resendOtpCountdown = document.getElementById('resendOtpCountdown');
if (resendOtpBtn) {
    let secondsLeft = parseInt(resendOtpBtn.getAttribute('data-seconds-left') || '0', 10);
    if (isNaN(secondsLeft) || secondsLeft < 0) {
        secondsLeft = 0;
    }

    const updateResendState = function() {
        if (secondsLeft > 0) {
            resendOtpBtn.disabled = true;
            if (resendOtpCountdown) {
                resendOtpCountdown.textContent = '(' + secondsLeft + 's)';
            }
        } else {
            resendOtpBtn.disabled = false;
            if (resendOtpCountdown) {
                resendOtpCountdown.textContent = '';
            }
        }
    };

    updateResendState();
    if (secondsLeft > 0) {
        const intervalId = setInterval(function() {
            secondsLeft -= 1;
            updateResendState();
            if (secondsLeft <= 0) {
                clearInterval(intervalId);
            }
        }, 1000);
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
