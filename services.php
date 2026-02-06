<?php

require_once 'insert/service_detail.php';

// Get slug from URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

// Get data from backend
$data = getServiceData(slug: $slug);

// Extract variables for easy use
$service = $data['service'];
$overview = $data['overview'];
$features = $data['features'];
$why_choose = $data['why_choose'];
$eligibility = $data['eligibility'];
$documents = $data['documents'];
$fees = $data['fees'];
$banks = $data['banks'];
$repayments = $data['repayments'];
$error = $data['error'];
$pageTitle = $data['pageTitle'];
session_start();
// Now include header
include 'includes/header.php';
?>

<style>
    :root {
        --service-primary: #130c3b;
        --service-accent: #00a08e;
        --service-bg: #f6f7fb;
        --service-text: #4b5563;
        --service-border: #e5e7eb;
        --service-ink: #0f172a;
    }

    body {
        background: var(--service-bg);
    }

    .service-page {
        background: var(--service-bg);
    }

    /* === ANIMATIONS === */
    .fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
        transform: translateY(30px);
    }

    .fade-in-left {
        animation: fadeInLeft 0.8s ease-out forwards;
        opacity: 0;
        transform: translateX(-30px);
    }

    .fade-in-right {
        animation: fadeInRight 0.8s ease-out forwards;
        opacity: 0;
        transform: translateX(30px);
    }

    .delay-100 {
        animation-delay: 0.1s;
    }

    .delay-200 {
        animation-delay: 0.2s;
    }

    .delay-300 {
        animation-delay: 0.3s;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInLeft {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    @keyframes fadeInRight {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* === HERO SECTION === */
    .service-hero-section {
        padding: 90px 0;
        background:
            radial-gradient(600px 300px at 10% 10%, rgba(0, 160, 142, 0.12), transparent),
            radial-gradient(500px 260px at 90% 70%, rgba(19, 12, 59, 0.12), transparent),
            linear-gradient(135deg, #f0fdfa 0%, #ffffff 100%);
        position: relative;
        overflow: hidden;
    }

    .hero-orb {
        position: absolute;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle at 30% 30%, rgba(0, 160, 142, 0.35), rgba(0, 160, 142, 0.05));
        filter: blur(2px);
        opacity: 0.7;
    }

    .hero-orb.orb-1 { top: 10%; left: -40px; }
    .hero-orb.orb-2 { bottom: 10%; right: -50px; }

    .parallax-img {
        transform: translateY(0);
        transition: transform 0.1s linear;
        will-change: transform;
    }

    .service-hero-card {
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(229, 231, 235, 0.9);
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        backdrop-filter: blur(6px);
    }

    .service-hero-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
    }

    .service-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(0, 160, 142, 0.12);
        color: var(--service-primary);
        font-weight: 600;
        font-size: 0.8rem;
    }

    .service-hero-title {
        font-size: 3.2rem;
        font-weight: 800;
        color: var(--service-primary);
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }

    .service-btn-custom {
        padding: 14px 40px;
        font-weight: 600;
        border-radius: 50px;
        background: var(--service-accent);
        color: white;
        border: none;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(0, 160, 142, 0.3);
        transition: all 0.3s ease;
        display: inline-block;
    }

    .service-btn-custom:hover {
        background: var(--service-primary);
        color: white;
        transform: translateY(-3px);
    }

    /* === SECTIONS COMMON === */
    .section-padding {
        padding: 90px 0;
    }

    .bg-light {
        background-color: #f9fafb;
    }

    .bg-white {
        background-color: #ffffff;
    }

    .section-title {
        color: var(--service-primary);
        font-weight: 800;
        font-size: 2.3rem;
        margin-bottom: 10px;
        position: relative;
        display: inline-block;
    }

    .section-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 3px;
        background: var(--service-accent);
        margin: 10px auto 0;
        border-radius: 2px;
    }

    .section-subtitle {
        color: var(--service-text);
        font-size: 1.1rem;
        max-width: 700px;
        margin: 0 auto 40px;
    }

    /* === OVERVIEW (TABLE) === */
    .overview-text {
        font-size: 1.2rem;
        line-height: 1.8;
        color: var(--service-text);
        margin-bottom: 40px;
        text-align: center;
    }

    .overview-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid var(--service-border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }

    .overview-table td {
        padding: 20px 30px;
        border-bottom: 1px solid var(--service-border);
        font-size: 1rem;
    }

    .overview-table tr:last-child td {
        border-bottom: none;
    }

    .overview-key {
        font-weight: 700;
        color: var(--service-primary);
        width: 30%;
        background-color: #f8fafc;
        border-right: 1px solid var(--service-border);
    }

    /* === FEATURES === */
    .feature-item {
        background: white;
        padding: 30px;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        transition: transform 0.3s;
        display: flex;
        align-items: flex-start;
        height: 100%;
        border: 1px solid #eef2f7;
    }

    .feature-item:hover .feature-icon {
        transform: scale(1.08) rotate(-2deg);
    }

    .feature-item:hover {
        transform: translateY(-5px);
        border-color: var(--service-accent);
    }

    .feature-icon {
        width: 50px;
        height: 50px;
        background: rgba(0, 160, 142, 0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--service-accent);
        font-size: 1.5rem;
        margin-right: 20px;
        flex-shrink: 0;
    }

    /* === WHY CHOOSE US === */
    .wc-card {
        text-align: center;
        padding: 35px 25px;
        background: white;
        border-radius: 18px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.06);
        transition: all 0.3s;
        border: 1px solid #eef2f7;
    }

    .wc-card:hover {
        transform: translateY(-10px);
    }

    .wc-icon-lg {
        font-size: 3rem;
        color: var(--service-accent);
        margin-bottom: 20px;
        display: inline-block;
    }

    /* === TABLES (ELIGIBILITY, FEES, BANKS) === */
    .custom-table {
        width: 100%;
        border: 1px solid var(--service-border);
        border-radius: 16px;
        overflow: hidden;
        font-size: 1.05rem;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    /* === BANKS GRID === */
    .bank-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
    }

    .bank-card {
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 16px;
        padding: 18px;
        display: grid;
        grid-template-columns: 64px 1fr;
        gap: 14px;
        align-items: center;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.04);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .bank-card:hover {
        transform: translateY(-6px);
        border-color: rgba(0, 160, 142, 0.25);
        box-shadow: 0 16px 30px rgba(0, 0, 0, 0.08);
    }

    .bank-logo {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .bank-logo img {
        max-width: 52px;
        max-height: 52px;
        object-fit: contain;
    }

    .bank-name {
        font-weight: 700;
        color: var(--service-primary);
        margin-bottom: 4px;
    }

    .bank-meta {
        color: var(--service-text);
        font-size: 0.95rem;
    }

    /* === REPAYMENT TIMELINE === */
    .repayment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
    }

    .repayment-card {
        background: white;
        padding: 26px;
        border-radius: 16px;
        border: 1px solid #eef2f7;
        transition: 0.3s;
        position: relative;
        overflow: hidden;
    }

    .repayment-card::after {
        content: '';
        position: absolute;
        inset: auto -40px -40px auto;
        width: 120px;
        height: 120px;
        background: rgba(0, 160, 142, 0.08);
        border-radius: 50%;
    }

    .custom-table td {
        padding: 18px 25px;
        border-bottom: 1px solid #f1f1f1;
    }

    .custom-table td:first-child {
        font-weight: 600;
        color: var(--service-primary);
        width: 40%;
        background: #f9fafb;
    }

    /* === REPAYMENT CARDS === */
    .repayment-card {
        background: white;
        padding: 30px;
        border-radius: 16px;
        border: 1px solid #eef2f7;
        transition: 0.3s;
    }

    .repayment-card:hover {
        transform: translateY(-6px);
    }

    .reveal {
        opacity: 0;
        transform: translateY(26px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }

    .reveal.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(19, 12, 59, 0.2), transparent);
        margin: 20px 0 0;
    }

    .repayment-card:hover {
        border-color: var(--service-accent);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .service-hero-title {
            font-size: 2.2rem;
        }

        .overview-key {
            width: 40%;
        }
    }
    /* === DOCUMENT SECTION === */
.doc-item {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s ease;
    height: 100%;
}

.doc-item:hover {
    border-color: var(--service-accent);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    transform: translateY(-3px);
}

.doc-icon {
    width: 36px;
    height: 36px;
    background: rgba(0, 160, 142, 0.1);
    color: var(--service-accent);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.doc-text {
    font-weight: 600;
    color: var(--service-primary);
    font-size: 1.1rem;
}

/* Document image */
.document-img {
    max-height: 380px;
    filter: drop-shadow(0 15px 25px rgba(0, 0, 0, 0.1));
}

</style>

<main class="service-page">
    <?php if (isset($error) && !empty($error) && $service === null): ?>
        <div class="container  mt-5 text-center fade-in-up">
            <h2 class="text-danger">Oops!</h2>
            <p class="text-muted"><?php echo htmlspecialchars($error); ?></p>
            <a href="index.php" class="btn btn-primary rounded-pill">Back to Home</a>
        </div>
    <?php elseif ($service): ?>

        <section class="service-hero-section fade-in-up">
            <div class="hero-orb orb-1"></div>
            <div class="hero-orb orb-2"></div>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 fade-in-left">
                        <div class="service-hero-card">
                            <span class="badge bg-light text-primary mb-3 px-3 py-2 border">Financial Services</span>
                            <h1 class="service-hero-title"><?php echo htmlspecialchars($service['title']); ?></h1>
                            <div class="mb-4 text-muted fs-5">
                                <?php if (!empty($service['short_description'])): ?>
                                    <p><?php echo htmlspecialchars($service['short_description']); ?></p>
                                <?php else: ?>
                                    <p>Apply for financial services online at low rates through Udhar Capital.</p>
                                <?php endif; ?>
                            </div>
                            <a href="apply-loan.php?slug=<?php echo $slug?>" class="service-btn-custom">Apply Now <i
                                    class="fas fa-arrow-right ms-2"></i></a>
                            <div class="service-hero-badges">
                                <span class="service-pill"><i class="fas fa-bolt"></i> Fast Approval</span>
                                <span class="service-pill"><i class="fas fa-shield-alt"></i> Secure</span>
                                <span class="service-pill"><i class="fas fa-percent"></i> Low Rates</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 text-center mt-5 mt-lg-0 fade-in-right">
                       <?php
$heroImg = !empty($service['hero_image'])
    ? htmlspecialchars($service['hero_image'])
    : 'includes/assets/service detail.png';
?>

<img src="<?= $heroImg ?>"
     alt="<?= htmlspecialchars($service['title']) ?>"
     class="img-fluid parallax-img"
     style="max-height: 500px; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1));">

                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding bg-white reveal">
            <div class="container">
                <div class="text-center mb-5 fade-in-up">
                    <h2 class="section-title">Overview</h2>
                </div>

                <div class="row justify-content-center fade-in-up delay-100">
                    <div class="col-lg-10">
                        <?php if ($overview && !empty($overview['intro'])): ?>
                            <div class="overview-text">
                                <?php echo nl2br(htmlspecialchars($overview['intro'])); ?>
                            </div>
                        <?php endif; ?>

                        <table class="overview-table">
                            <tbody>
                                <?php
                                $ov_data = ($overview && !empty($overview['data'])) ? $overview['data'] : [
                                    'Amount' => 'Up to 1 Crore',
                                    'Loan Tenure' => '3 to 5 Years',
                                    'Interest Rates' => 'Starting from 10.5% p.a.',
                                    'Processing Time' => '72 Hours'
                                ];
                                foreach ($ov_data as $key => $val) {
                                    echo "<tr><td class='overview-key'>" . htmlspecialchars($key) . "</td><td>" . htmlspecialchars($val) . "</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding bg-light reveal">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 fade-in-up">
                        <div class="p-5 rounded-4 border shadow-sm" style="background: linear-gradient(135deg, #1a1241 0%, #0f0b2c 100%);">
                            <h3 class="fw-bold mb-4" style="color: white;text-align:center;">Detailed Information</h3>
                          <div class="fs-5" style="line-height: 1.8; color: #e5e7eb;">

                                <?php
                                if (!empty($service['long_description'])) {
                                    echo nl2br(htmlspecialchars($service['long_description']));
                                } else {
                                    echo "Contact us for more detailed information about this service.";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding bg-white reveal">
            <div class="container">
                <div class="text-center mb-5 fade-in-up">
                    <h2 class="section-title">Features & Benefits</h2>
                    <p class="section-subtitle">Why this product is right for you</p>
                </div>

                <div class="row g-4">
                    <?php
                    $feat_data = (count($features) > 0) ? $features : [
                        ['title' => 'Low Interest Rates', 'description' => 'Acquire a loan at competitive rates.'],
                        ['title' => 'Fast Disbursal', 'description' => 'Funds in your account within 24 hours.'],
                        ['title' => 'Flexible Tenure', 'description' => 'Repayment options from 12 to 60 months.'],
                        ['title' => 'Minimal Documentation', 'description' => '100% paperless process.']
                    ];

                    foreach ($feat_data as $index => $feat) {
                        $delay = ($index % 4) * 100; // Staggered animation
                        ?>
                        <div class="col-md-6 fade-in-up" style="animation-delay: <?php echo $delay; ?>ms;">
                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                                <div>
                                    <h4 class="fw-bold mb-2"><?php echo htmlspecialchars($feat['title']); ?></h4>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($feat['description']); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </section>

       

        <section class="section-padding bg-white reveal">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7 fade-in-up">
                        <div class="mb-4">
                            <h2 class="section-title">Eligibility Criteria</h2>
                            <p class="section-subtitle mb-0">Check if you qualify:</p>
                        </div>
                        <table class="custom-table">
                            <tbody>
                                <?php
                                $elig_data = (count($eligibility) > 0) ? $eligibility : [
                                    ['criteria_key' => 'Age', 'criteria_value' => '21 - 65 years'],
                                    ['criteria_key' => 'Income', 'criteria_value' => 'Min ₹25k/month']
                                ];
                                foreach ($elig_data as $row) {
                                    echo "<tr><td>" . htmlspecialchars($row['criteria_key']) . "</td><td>" . htmlspecialchars($row['criteria_value']) . "</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-5 text-center mt-5 mt-lg-0 fade-in-up delay-200">
                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/business-loan-illustration-download-in-svg-png-gif-file-formats--finance-money-investment-bank-pack-people-illustrations-4609389.png?f=webp"
                            alt="Illustration" class="img-fluid delay-200"
                            style="max-height: 400px; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1));">

                    </div>
                </div>
            </div>
        </section>
 <section class="section-padding bg-light reveal">
            <div class="container">
                <div class="text-center mb-5 fade-in-up">
                    <h2 class="section-title">Why Choose Udhar Capital?</h2>
                </div>

                <div class="row g-4 justify-content-center">
                    <?php
                    $wc_data = (count($why_choose) > 0) ? $why_choose : [
                        ['image' => '', 'title' => 'Quick Approval', 'description' => 'Instant approval system.'],
                        ['image' => '', 'title' => 'Transparency', 'description' => 'No hidden charges.'],
                        ['image' => '', 'title' => 'Secure', 'description' => '256-bit encryption.']
                    ];

                    foreach ($wc_data as $index => $wc) {
                        ?>
                        <div class="col-md-4 fade-in-up delay-<?php echo ($index + 1) * 100; ?>">
                            <div class="wc-card h-100">
                                <div class="mb-3">
                                    <?php if (!empty($wc['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($wc['image']); ?>" style="width: 60px;">
                                    <?php else: ?>
                                        <i class="fas fa-award wc-icon-lg"></i>
                                    <?php endif; ?>
                                </div>
                                <h4 class="fw-bold"><?php echo htmlspecialchars($wc['title']); ?></h4>
                                <p class="text-muted"><?php echo htmlspecialchars($wc['description']); ?></p>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </section>
<section class="section-padding bg-light reveal">
    <div class="container">
        <div class="row align-items-center g-5">

            <!-- LEFT : DOCUMENT LIST -->
            <div class="col-lg-6 fade-in-up">
                <h2 class="section-title mb-4">
                    Documents Required
                </h2>

                <p class="text-muted mb-4">
                    Keep these documents ready to ensure a smooth and fast loan approval process.
                </p>

                <div class="row g-3">
                    <?php
                    $doc_list = (count($documents) > 0) ? $documents : [
                        ['doc_name' => 'Aadhaar Card'],
                        ['doc_name' => 'PAN Card'],
                        ['doc_name' => 'Photograph'],
                        ['doc_name' => 'Business Proof'],
                        ['doc_name' => 'Ownership Proof (Optional)'],
                    ];

                    foreach ($doc_list as $doc):
                    ?>
                        <div class="col-sm-6">
                            <div class="doc-item">
                                <span class="doc-icon">
                                    <i class="fas fa-file-alt"></i>
                                </span>
                                <span class="doc-text">
                                    <?= htmlspecialchars($doc['doc_name']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <small class="text-muted d-block mt-4">
                    <i class="fas fa-info-circle me-1"></i>
                    Documents must be valid and authorised.
                </small>
            </div>

            <!-- RIGHT : IMAGE -->
            <div class="col-lg-6 text-center fade-in-up delay-200">
                <img src="includes/assets/document.jpg"
                     alt="Documents Required"
                     class="img-fluid document-img">
            </div>

        </div>
    </div>
</section>

        <section class="section-padding bg-white reveal">
            <div class="container">
                <div class="text-center mb-5 fade-in-up">
                    <h2 class="section-title">Fees & Charges</h2>
                </div>
                <div class="row justify-content-center fade-in-up">
                    <div class="col-lg-8">
                        <table class="custom-table">
                            <tbody>
                                <?php
                                $fees_data = (count($fees) > 0) ? $fees : [['fee_key' => 'Processing Fee', 'fee_value' => '1% - 2%']];
                                foreach ($fees_data as $row) {
                                    echo "<tr><td>" . htmlspecialchars($row['fee_key']) . "</td><td>" . htmlspecialchars($row['fee_value']) . "</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding bg-light reveal">
            <div class="container">
                <div class="text-center mb-5 fade-in-up">
                    <h2 class="section-title">Partner Banks</h2>
                    <p class="section-subtitle">Trusted institutions powering our approvals and disbursals.</p>
                </div>
                <div class="bank-grid fade-in-up">
                    <?php
                    $bank_data = (count($banks) > 0) 
                        ? $banks 
                        : [['bank_key' => 'HDFC Bank', 'bank_value' => 'Partner', 'bank_image' => '']];

                    foreach ($bank_data as $row) {
                        ?>
                        <div class="bank-card">
                            <div class="bank-logo">
                                <?php if (!empty($row['bank_image'])): ?>
                                    <img src="<?= htmlspecialchars($row['bank_image']) ?>"
                                         alt="<?= htmlspecialchars($row['bank_key']) ?>">
                                <?php else: ?>
                                    <span class="text-muted small">Logo</span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="bank-name"><?= htmlspecialchars($row['bank_key']) ?></div>
                                <div class="bank-meta"><?= htmlspecialchars($row['bank_value']) ?></div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </section>

        <section class="section-padding bg-white reveal">
            <div class="container">
                <div class="text-center mb-5 fade-in-up">
                    <h2 class="section-title">Different Forms of Loan Repayments</h2>
                    <p class="section-subtitle">Choose the repayment style that fits your cash flow.</p>
                </div>
                <div class="repayment-grid">
                    <?php
                    $repay_data = (count($repayments) > 0) ? $repayments : [
                        ['title' => 'Standard EMI', 'description' => 'Fixed monthly payments.']
                    ];
                    foreach ($repay_data as $repay) {
                        ?>
                        <div class="repayment-card fade-in-up">
                            <div class="mb-3 text-primary fs-2"><i class="fas fa-wallet"></i></div>
                            <h4 class="fw-bold"><?php echo htmlspecialchars($repay['title']); ?></h4>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($repay['description']); ?></p>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </section>

    <?php endif; ?>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const observerOptions = { threshold: 0.1 };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = "running";
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in-up').forEach(el => {
            el.style.animationPlayState = "paused"; // Pause initially
            observer.observe(el);
        });

        document.querySelectorAll('.fade-in-left, .fade-in-right').forEach(el => {
            el.style.animationPlayState = "paused";
            observer.observe(el);
        });

        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

        const parallaxImg = document.querySelector('.parallax-img');
        if (parallaxImg) {
            window.addEventListener('scroll', () => {
                const scrollY = window.scrollY || window.pageYOffset;
                parallaxImg.style.transform = `translateY(${scrollY * 0.08}px)`;
            });
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
