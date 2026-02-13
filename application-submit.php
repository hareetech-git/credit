<?php
session_start();

$submission = $_SESSION['loan_submit_success'] ?? null;
if (!$submission || !is_array($submission)) {
    header('Location: apply-loan.php');
    exit;
}
unset($_SESSION['loan_submit_success']);

include 'includes/header.php';
$applicantName = trim((string) ($submission['full_name'] ?? ''));
?>

<style>
    :root {
        --submit-bg-start: #f0f9ff;
        --submit-bg-end: #dbeafe;
        --submit-card: #ffffff;
        --submit-ink: #0f172a;
    }

    body {
        background-color: #eaf4ff;
        background-image:
            linear-gradient(135deg, var(--submit-bg-start), var(--submit-bg-end)),
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160' viewBox='0 0 160 160'%3E%3Cg fill='none' stroke='%2394a3b8' stroke-opacity='0.20' stroke-width='1.4'%3E%3Cpath d='M0 80h160M80 0v160'/%3E%3Ccircle cx='80' cy='80' r='56'/%3E%3Ccircle cx='80' cy='80' r='28'/%3E%3C/g%3E%3C/svg%3E");
        background-size: cover, 170px 170px;
        background-attachment: fixed;
    }

    .submit-wrap {
        max-width: 820px;
        margin: 44px auto 80px;
        padding: 0 14px;
        position: relative;
    }

    .submit-card {
        background: var(--submit-card);
        border-radius: 28px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.12);
        padding: 44px 28px;
        text-align: center;
        position: relative;
        overflow: hidden;
        isolation: isolate;
    }

    .submit-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 10% 10%, rgba(16, 185, 129, 0.10), transparent 35%),
            radial-gradient(circle at 90% 0%, rgba(59, 130, 246, 0.12), transparent 42%);
        z-index: -1;
    }

    .floating {
        position: absolute;
        pointer-events: none;
        z-index: 0;
        opacity: 0.9;
        animation: floatY 7s ease-in-out infinite;
    }

    .float-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: linear-gradient(140deg, #34d399, #10b981);
        box-shadow: 0 8px 18px rgba(16, 185, 129, 0.28);
    }

    .float-ring {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        border: 3px solid rgba(59, 130, 246, 0.35);
        background: rgba(255, 255, 255, 0.4);
    }

    .float-spark {
        width: 30px;
        height: 30px;
        background: rgba(245, 158, 11, 0.18);
        transform: rotate(45deg);
        border-radius: 8px;
    }

    .f1 { top: -10px; left: 8%; animation-delay: 0s; }
    .f2 { top: 14%; right: -8px; animation-delay: 1.1s; }
    .f3 { bottom: 12%; left: -10px; animation-delay: 2.3s; }
    .f4 { bottom: -8px; right: 12%; animation-delay: 0.8s; }
    .f5 { top: 42%; right: 10%; animation-delay: 1.8s; }

    .success-icon {
        width: 84px;
        height: 84px;
        margin: 0 auto 18px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        color: #fff;
        background: linear-gradient(145deg, #10b981, #047857);
        box-shadow: 0 12px 28px rgba(5, 150, 105, 0.32);
        position: relative;
        z-index: 1;
    }

    .success-icon i {
        font-size: 34px;
    }

    .success-icon::after {
        content: "";
        position: absolute;
        width: 116px;
        height: 116px;
        border-radius: 50%;
        border: 2px dashed rgba(16, 185, 129, 0.35);
        animation: spinSlow 14s linear infinite;
    }

    .submit-title {
        margin: 0;
        color: var(--submit-ink);
        font-weight: 800;
        font-size: 35px;
        line-height: 1.15;
        position: relative;
        z-index: 1;
    }

    .submit-note {
        margin: 14px auto 0;
        max-width: 640px;
        color: #334155;
        font-size: 17px;
        line-height: 1.65;
        position: relative;
        z-index: 1;
    }

    .action-row {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }

    .btn-cta {
        text-decoration: none;
        border-radius: 12px;
        padding: 11px 18px;
        font-weight: 700;
        transition: transform 0.22s ease, box-shadow 0.22s ease;
    }

    .btn-home {
        background: #0f172a;
        color: #fff;
    }

    .btn-apply {
        background: #fff;
        border: 1px solid #cbd5e1;
        color: #0f172a;
    }

    .btn-cta:hover {
        transform: translateY(-2px);
    }

    .btn-home:hover {
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.25);
        color: #fff;
    }

    .btn-apply:hover {
        box-shadow: 0 10px 20px rgba(148, 163, 184, 0.26);
        color: #0f172a;
    }

    @keyframes floatY {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(6deg); }
        100% { transform: translateY(0px) rotate(0deg); }
    }

    @keyframes spinSlow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @media (max-width: 576px) {
        .submit-card {
            padding: 34px 20px;
        }

        .submit-title {
            font-size: 28px;
        }

        .submit-note {
            font-size: 15px;
        }

        .floating {
            opacity: 0.65;
            transform: scale(0.85);
        }
    }
</style>

<div class="submit-wrap">
    <div class="submit-card">
        <span class="floating float-dot f1"></span>
        <span class="floating float-ring f2"></span>
        <span class="floating float-spark f3"></span>
        <span class="floating float-dot f4"></span>
        <span class="floating float-ring f5"></span>

        <div class="success-icon"><i class="fas fa-check"></i></div>
        <h1 class="submit-title">
            Application Submitted Successfully
        </h1>
        <p class="submit-note">
            <?= $applicantName !== '' ? htmlspecialchars($applicantName) . ', ' : '' ?>thank you for applying with us.
            Our team has received your request and will contact you shortly with the next update.
            We appreciate your trust.
        </p>
        <div class="action-row">
            <a href="index.php" class="btn-cta btn-home">Back to Home</a>
            <a href="apply-loan.php" class="btn-cta btn-apply">Apply Again</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
