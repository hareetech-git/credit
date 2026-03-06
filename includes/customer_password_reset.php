<?php
require_once __DIR__ . '/mailer.php';

if (!defined('CUSTOMER_PASSWORD_OTP_TTL_SECONDS')) {
    define('CUSTOMER_PASSWORD_OTP_TTL_SECONDS', 600);
}

if (!defined('CUSTOMER_PASSWORD_OTP_MAX_ATTEMPTS')) {
    define('CUSTOMER_PASSWORD_OTP_MAX_ATTEMPTS', 5);
}

if (!defined('CUSTOMER_PASSWORD_RESEND_COOLDOWN_SECONDS')) {
    define('CUSTOMER_PASSWORD_RESEND_COOLDOWN_SECONDS', 120);
}

if (!function_exists('customerPasswordResetGenerateOtp')) {
    function customerPasswordResetGenerateOtp(): ?string
    {
        try {
            return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } catch (Throwable $e) {
            return null;
        }
    }
}

if (!function_exists('customerPasswordResetSendOtpMail')) {
    function customerPasswordResetSendOtpMail(string $toEmail, string $toName, string $otp): bool
    {
        $toEmail = trim($toEmail);
        $toName = trim($toName);
        $otp = trim($otp);

        if ($toEmail === '' || $otp === '' || !preg_match('/^[0-9]{6}$/', $otp)) {
            return false;
        }
        if ($toName === '') {
            $toName = 'Customer';
        }

        $safeName = htmlspecialchars($toName, ENT_QUOTES, 'UTF-8');
        $safeOtp = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');
        $validMinutes = (int)ceil(CUSTOMER_PASSWORD_OTP_TTL_SECONDS / 60);

        $subject = 'Your Udhar Capital Password Reset OTP';
        $htmlBody = '
            <div style="font-family: Arial, sans-serif; background:#f6f8fb; padding:24px;">
                <div style="max-width:620px; margin:0 auto; background:#ffffff; border:1px solid #e6eaf0; border-radius:12px; overflow:hidden;">
                    <div style="background:#0f172a; color:#ffffff; padding:18px 24px; font-size:18px; font-weight:700;">
                        Udhar Capital
                    </div>
                    <div style="padding:24px; color:#0f172a;">
                        <p style="font-size:16px; margin:0 0 12px;">Hello ' . $safeName . ',</p>
                        <p style="margin:0 0 16px; color:#475569;">
                            Use the OTP below to reset your account password:
                        </p>
                        <div style="font-size:28px; letter-spacing:6px; font-weight:800; color:#0f172a; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; text-align:center;">
                            ' . $safeOtp . '
                        </div>
                        <p style="margin:16px 0 0; color:#64748b; font-size:13px;">
                            This OTP is valid for ' . $validMinutes . ' minutes. If you did not request this, ignore this email.
                        </p>
                    </div>
                    <div style="padding:14px 24px; background:#f8fafc; color:#94a3b8; font-size:12px;">
                        &copy; ' . date('Y') . ' Udhar Capital
                    </div>
                </div>
            </div>';

        $textBody = "Hello {$toName},\n\n"
            . "Your password reset OTP is: {$otp}\n"
            . "This OTP is valid for {$validMinutes} minutes.\n\n"
            . "If you did not request this, please ignore this email.";

        return sendEnquiryEmail($toEmail, $toName, $subject, $htmlBody, $textBody);
    }
}

if (!function_exists('customerPasswordResetSessionSet')) {
    function customerPasswordResetSessionSet(int $customerId, string $email, string $name, string $otp): void
    {
        $_SESSION['customer_reset_customer_id'] = $customerId;
        $_SESSION['customer_reset_email'] = strtolower(trim($email));
        $_SESSION['customer_reset_name'] = trim($name);
        $_SESSION['customer_reset_otp_hash'] = password_hash($otp, PASSWORD_BCRYPT);
        $_SESSION['customer_reset_otp_expires'] = time() + CUSTOMER_PASSWORD_OTP_TTL_SECONDS;
        $_SESSION['customer_reset_last_sent_at'] = time();
        $_SESSION['customer_reset_attempts'] = 0;
    }
}

if (!function_exists('customerPasswordResetSessionIsActive')) {
    function customerPasswordResetSessionIsActive(): bool
    {
        if (
            !isset($_SESSION['customer_reset_customer_id'], $_SESSION['customer_reset_email'], $_SESSION['customer_reset_otp_hash'], $_SESSION['customer_reset_otp_expires'])
        ) {
            return false;
        }

        $expiresAt = (int)$_SESSION['customer_reset_otp_expires'];
        return $expiresAt > time();
    }
}

if (!function_exists('customerPasswordResetSessionSecondsLeft')) {
    function customerPasswordResetSessionSecondsLeft(): int
    {
        if (!isset($_SESSION['customer_reset_otp_expires'])) {
            return 0;
        }
        return max(0, (int)$_SESSION['customer_reset_otp_expires'] - time());
    }
}

if (!function_exists('customerPasswordResetSessionResendWaitSeconds')) {
    function customerPasswordResetSessionResendWaitSeconds(): int
    {
        if (!isset($_SESSION['customer_reset_last_sent_at'])) {
            return 0;
        }

        $lastSentAt = (int)$_SESSION['customer_reset_last_sent_at'];
        $remaining = CUSTOMER_PASSWORD_RESEND_COOLDOWN_SECONDS - (time() - $lastSentAt);
        return max(0, $remaining);
    }
}

if (!function_exists('customerPasswordResetSessionClear')) {
    function customerPasswordResetSessionClear(): void
    {
        unset(
            $_SESSION['customer_reset_customer_id'],
            $_SESSION['customer_reset_email'],
            $_SESSION['customer_reset_name'],
            $_SESSION['customer_reset_otp_hash'],
            $_SESSION['customer_reset_otp_expires'],
            $_SESSION['customer_reset_last_sent_at'],
            $_SESSION['customer_reset_attempts']
        );
    }
}
