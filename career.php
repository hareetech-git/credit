<?php
require_once 'includes/header.php';

$msg = trim((string)($_GET['msg'] ?? ''));
$err = trim((string)($_GET['err'] ?? ''));
?>

<style>
    .career-hero {
        padding: 90px 0 40px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #334155 100%);
        color: #fff;
    }
    .career-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
    }
    .career-list li {
        margin-bottom: 10px;
        color: #334155;
    }
    .career-form .form-control {
        border-radius: 10px;
        padding: 12px 14px;
    }
    .career-btn {
        border-radius: 10px;
        padding: 11px 18px;
        font-weight: 600;
    }
</style>

<section class="career-hero">
    <div class="container">
        <span class="badge bg-light text-dark mb-3">Careers</span>
        <h1 class="fw-bold mb-2">Build Your Career With Udhar Capital</h1>
        <p class="mb-0 text-light">Join a fast-growing team focused on customer-first financial solutions.</p>
    </div>
</section>

<section class="py-5" style="background:#f8fafc;">
    <div class="container">
        <?php if ($msg !== ''): ?>
            <div class="alert alert-success mb-4"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <?php if ($err !== ''): ?>
            <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($err); ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="career-card p-4 p-lg-5 h-100">
                    <h3 class="fw-bold mb-3">Why Work With Us</h3>
                    <ul class="career-list ps-3">
                        <li>Fast-paced learning environment with real growth opportunities.</li>
                        <li>Work on high-impact projects in lending, operations and technology.</li>
                        <li>Collaborative team culture with ownership and accountability.</li>
                        <li>Performance-driven recognition and long-term career path.</li>
                    </ul>
                    <p class="text-muted mb-0">Share your profile with us. Our HR team will contact shortlisted candidates.</p>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="career-card p-4 p-lg-5">
                    <h4 class="fw-bold mb-3">Apply Now</h4>
                    <form method="POST" action="insert/career_apply.php" enctype="multipart/form-data" class="career-form">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address *</label>
                            <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Resume (PDF only, max 5 MB) *</label>
                            <input type="file" name="resume_pdf" class="form-control" accept="application/pdf,.pdf" required>
                        </div>
                        <button type="submit" class="btn btn-primary career-btn w-100">Submit Application</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

