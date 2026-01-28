<?php
// Include header
require_once 'includes/header.php';

// Get loan type from URL
$loan_type = isset($_GET['type']) ? $_GET['type'] : 'personal';

// Define loan titles based on type
$loan_titles = [
    'salary' => 'Salary Based Loan',
    'self' => 'Self Employed Loan',
    'credit' => 'Credit Score Loan',
    'msme' => 'MSME Loan',
    'working' => 'Working Capital Loan',
    'term' => 'Term Loan',
    'professional' => 'Professional Loan',
    'home' => 'Home Loan',
    'creditcard' => 'Credit Card',
    'personal' => 'Personal Loan'
];

$current_title = isset($loan_titles[$loan_type]) ? $loan_titles[$loan_type] : 'Personal Loan';
?>

<style>
    /* Global Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        background-color: #ffffff;
    }

    /* Hero Section */
    .service-hero {
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
        padding: 80px 32px 60px;
        position: relative;
        overflow: hidden;
    }

    .service-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, transparent 100%);
        border-radius: 50%;
    }

    .hero-container {
        max-width: 1280px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .hero-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
        font-size: 14px;
        color: #64748b;
    }

    .hero-breadcrumb a {
        color: #2563eb;
        text-decoration: none;
        transition: color 0.2s;
    }

    .hero-breadcrumb a:hover {
        color: #1d4ed8;
    }

    .hero-breadcrumb i {
        font-size: 10px;
    }

    .service-category {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #dbeafe;
        color: #2563eb;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .service-title {
        font-size: 3rem;
        color: #0f172a;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 20px;
        line-height: 1.1;
    }

    .service-description {
        font-size: 1.125rem;
        color: #64748b;
        line-height: 1.7;
        max-width: 800px;
        margin-bottom: 32px;
    }

    .hero-stats {
        display: flex;
        gap: 32px;
        margin-top: 32px;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .stat-content h4 {
        color: #0f172a;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .stat-content p {
        color: #64748b;
        font-size: 14px;
    }

    /* Main Content Section */
    .service-content {
        padding: 60px 32px;
    }

    .content-container {
        max-width: 1280px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 60px;
    }

    /* Left Column Sections */
    .content-section {
        background: white;
        border-radius: 16px;
        padding: 40px;
        margin-bottom: 32px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid #f1f5f9;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }

    .section-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 18px;
    }

    .section-header h2 {
        color: #0f172a;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .section-content {
        color: #64748b;
        line-height: 1.7;
        font-size: 15px;
    }

    /* Lists */
    .requirements-list,
    .deliverables-list {
        display: grid;
        gap: 16px;
    }

    .list-item {
        display: flex;
        gap: 12px;
        padding: 16px;
        background: #f8fafc;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .list-item:hover {
        background: #eff6ff;
        transform: translateX(4px);
    }

    .list-item-icon {
        width: 24px;
        height: 24px;
        background: #2563eb;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .list-item-content h4 {
        color: #0f172a;
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .list-item-content p {
        color: #64748b;
        font-size: 14px;
    }

    /* Process Flow */
    .process-timeline {
        position: relative;
        padding-left: 32px;
    }

    .process-timeline::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 32px;
        bottom: 32px;
        width: 2px;
        background: linear-gradient(to bottom, #2563eb, #93c5fd);
    }

    .process-step {
        position: relative;
        margin-bottom: 32px;
        padding-left: 24px;
    }

    .process-step:last-child {
        margin-bottom: 0;
    }

    .process-number {
        position: absolute;
        left: -32px;
        top: 0;
        width: 24px;
        height: 24px;
        background: #2563eb;
        border: 3px solid white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 0 0 4px #eff6ff;
    }

    .process-content h4 {
        color: #0f172a;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .process-content p {
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    /* Timeline Table */
    .timeline-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }

    .timeline-table th {
        text-align: left;
        padding: 12px 16px;
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 2px solid #e2e8f0;
    }

    .timeline-table td {
        padding: 16px;
        background: #f8fafc;
        color: #64748b;
        font-size: 14px;
    }

    .timeline-table tr {
        transition: all 0.2s ease;
    }

    .timeline-table tr:hover td {
        background: #eff6ff;
    }

    .timeline-phase {
        font-weight: 600;
        color: #0f172a;
    }

    .timeline-duration {
        color: #2563eb;
        font-weight: 600;
    }

    /* Add-on Services */
    .addon-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .addon-card {
        padding: 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    .addon-card:hover {
        border-color: #2563eb;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        transform: translateY(-2px);
    }

    .addon-card h4 {
        color: #0f172a;
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .addon-card h4 i {
        color: #2563eb;
        font-size: 16px;
    }

    .addon-card p {
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    /* FAQ Section */
    .faq-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .faq-item {
        background: #f8fafc;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .faq-question {
        padding: 20px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
    }

    .faq-question:hover {
        background: #eff6ff;
    }

    .faq-question h4 {
        color: #0f172a;
        font-size: 15px;
        font-weight: 600;
        flex: 1;
    }

    .faq-toggle {
        width: 24px;
        height: 24px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 14px;
        transition: transform 0.3s ease;
    }

    .faq-item.active .faq-toggle {
        transform: rotate(180deg);
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .faq-item.active .faq-answer {
        max-height: 300px;
    }

    .faq-answer-content {
        padding: 0 20px 20px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    /* Why Choose Us */
    .why-choose-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .why-choose-item {
        padding: 24px;
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
        border-radius: 12px;
        border: 1px solid #dbeafe;
    }

    .why-choose-item h4 {
        color: #0f172a;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .why-choose-item h4 i {
        color: #2563eb;
        font-size: 18px;
    }

    .why-choose-item p {
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    /* Right Sidebar */
    .sidebar {
        position: sticky;
        top: 100px;
        height: fit-content;
    }

    .sidebar-card {
        background: white;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #f1f5f9;
        margin-bottom: 24px;
    }

    .sidebar-card h3 {
        color: #0f172a;
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .sidebar-features {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
    }

    .sidebar-feature {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #f8fafc;
        border-radius: 8px;
    }

    .sidebar-feature i {
        color: #10b981;
        font-size: 16px;
    }

    .sidebar-feature span {
        color: #334155;
        font-size: 14px;
        font-weight: 500;
    }

    .apply-button {
        width: 100%;
        background: #2563eb;
        color: white;
        padding: 16px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 16px;
    }

    .apply-button:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
    }

    .contact-button {
        width: 100%;
        background: white;
        color: #2563eb;
        padding: 16px;
        border: 2px solid #2563eb;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .contact-button:hover {
        background: #eff6ff;
    }

    .help-box {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        padding: 24px;
        border-radius: 12px;
        text-align: center;
    }

    .help-box h4 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .help-box p {
        font-size: 14px;
        margin-bottom: 16px;
        opacity: 0.9;
    }

    .help-phone {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 1.25rem;
        font-weight: 700;
        text-decoration: none;
        color: white;
    }

    .help-phone:hover {
        opacity: 0.9;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .content-container {
            grid-template-columns: 1fr;
        }

        .sidebar {
            position: static;
        }

        .why-choose-grid,
        .addon-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .service-title {
            font-size: 2rem;
        }

        .hero-stats {
            flex-direction: column;
            gap: 16px;
        }

        .content-section {
            padding: 24px;
        }
    }
</style>

<!-- Hero Section -->
<section class="service-hero">
    <div class="hero-container">
        <div class="hero-breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="services.php">Services</a>
            <i class="fas fa-chevron-right"></i>
            <span><?php echo $current_title; ?></span>
        </div>

        <div class="service-category">
            <i class="fas fa-user"></i>
            Loan Services
        </div>

        <h1 class="service-title"><?php echo $current_title; ?> Services</h1>
        
        <p class="service-description">
            Get instant approval on <?php echo strtolower($current_title); ?> with minimal documentation. Whether it's for medical emergencies, 
            wedding expenses, education, or any personal need, we've got you covered with flexible repayment options 
            and competitive interest rates.
        </p>

        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-content">
                    <h4>Up to ₹25 Lakhs</h4>
                    <p>Loan Amount</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-content">
                    <h4>10.5% p.a.</h4>
                    <p>Starting Interest</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h4>10 Minutes</h4>
                    <p>Quick Approval</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="service-content">
    <div class="content-container">
        <!-- Left Column -->
        <div class="main-content">
            <!-- Requirements Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h2>Requirements from Client</h2>
                </div>
                <div class="requirements-list">
                    <div class="list-item">
                        <div class="list-item-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="list-item-content">
                            <h4>Identity Proof</h4>
                            <p>Aadhaar Card, PAN Card, Voter ID, or Passport</p>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="list-item-content">
                            <h4>Address Proof</h4>
                            <p>Utility bills, Rental agreement, or Property documents</p>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-icon">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div class="list-item-content">
                            <h4>Income Proof</h4>
                            <p>Last 3 months salary slips or ITR for self-employed</p>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="list-item-content">
                            <h4>Bank Statements</h4>
                            <p>Last 6 months bank account statements</p>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-icon">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div class="list-item-content">
                            <h4>Recent Photographs</h4>
                            <p>2 passport size photographs</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deliverables Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h2>Deliverables for Client</h2>
                </div>
                <div class="deliverables-list">
                    <div class="list-item">
                        <div class="list-item-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="list-item-content">
                            <h4>Loan Approval Letter</h4>
                            <p>Official approval document with loan terms and conditions</p>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="list-item-content">
                            <h4>Loan Agreement Copy</h4>
                            <p>Signed loan agreement with all terms clearly mentioned</p>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="list-item-content">
                            <h4>Repayment Schedule</h4>
                            <p>Detailed EMI schedule with payment dates and amounts</p>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="list-item-content">
                            <h4>Welcome Kit</h4>
                            <p>Customer care details, online portal access, and insurance documents</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Process Flow Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h2>Process Flow of Execution</h2>
                </div>
                <div class="process-timeline">
                    <div class="process-step">
                        <div class="process-number">1</div>
                        <div class="process-content">
                            <h4>Application Submission</h4>
                            <p>Fill out the online application form with basic details and submit required documents.</p>
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="process-number">2</div>
                        <div class="process-content">
                            <h4>Document Verification</h4>
                            <p>Our team verifies all submitted documents and may request additional information if needed.</p>
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="process-number">3</div>
                        <div class="process-content">
                            <h4>Credit Assessment</h4>
                            <p>We evaluate your credit score, income, and repayment capacity to determine eligibility.</p>
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="process-number">4</div>
                        <div class="process-content">
                            <h4>Approval & Sanction</h4>
                            <p>Once approved, you'll receive a sanction letter with loan amount, interest rate, and terms.</p>
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="process-number">5</div>
                        <div class="process-content">
                            <h4>Agreement Signing</h4>
                            <p>Sign the loan agreement digitally or physically at our branch location.</p>
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="process-number">6</div>
                        <div class="process-content">
                            <h4>Loan Disbursal</h4>
                            <p>Loan amount is disbursed directly to your bank account within 24-48 hours.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h2>Timeline</h2>
                </div>
                <table class="timeline-table">
                    <thead>
                        <tr>
                            <th>Phase</th>
                            <th>Duration</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="timeline-phase">Application</td>
                            <td class="timeline-duration">15 minutes</td>
                            <td>Online form filling and document upload</td>
                        </tr>
                        <tr>
                            <td class="timeline-phase">Verification</td>
                            <td class="timeline-duration">2-4 hours</td>
                            <td>Document verification and KYC process</td>
                        </tr>
                        <tr>
                            <td class="timeline-phase">Assessment</td>
                            <td class="timeline-duration">4-6 hours</td>
                            <td>Credit score check and eligibility assessment</td>
                        </tr>
                        <tr>
                            <td class="timeline-phase">Approval</td>
                            <td class="timeline-duration">10 minutes</td>
                            <td>Final approval and sanction letter generation</td>
                        </tr>
                        <tr>
                            <td class="timeline-phase">Disbursal</td>
                            <td class="timeline-duration">24-48 hours</td>
                            <td>Amount credited to your bank account</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Add-on Services Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <h2>Add-on Services</h2>
                </div>
                <div class="addon-grid">
                    <div class="addon-card">
                        <h4><i class="fas fa-shield-alt"></i> Loan Insurance</h4>
                        <p>Protect your loan with comprehensive insurance coverage in case of unforeseen events.</p>
                    </div>
                    <div class="addon-card">
                        <h4><i class="fas fa-calendar-check"></i> EMI Holiday</h4>
                        <p>Get flexibility to skip up to 2 EMIs in the first year without penalty.</p>
                    </div>
                    <div class="addon-card">
                        <h4><i class="fas fa-redo"></i> Top-up Facility</h4>
                        <p>Get additional loan amount on your existing loan after 6 months.</p>
                    </div>
                    <div class="addon-card">
                        <h4><i class="fas fa-hand-holding-usd"></i> Balance Transfer</h4>
                        <p>Transfer your existing loan from another bank at lower interest rates.</p>
                    </div>
                    <div class="addon-card">
                        <h4><i class="fas fa-money-check-alt"></i> Part Payment</h4>
                        <p>Make part prepayments without any charges to reduce your loan tenure.</p>
                    </div>
                    <div class="addon-card">
                        <h4><i class="fas fa-mobile-alt"></i> Digital Tracking</h4>
                        <p>Track your loan status, EMI payments, and statements through our mobile app.</p>
                    </div>
                </div>
            </div>

            <!-- Service FAQ Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h2>Service FAQ</h2>
                </div>
                <div class="faq-list">
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <h4>What is the minimum eligibility for a <?php echo strtolower($current_title); ?>?</h4>
                            <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                You should be an Indian resident aged between 21-60 years with a minimum monthly income of ₹15,000 for salaried individuals or a business vintage of at least 2 years for self-employed.
                            </div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <h4>How long does the approval process take?</h4>
                            <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                Our instant approval process takes approximately 10 minutes once all documents are verified. The entire process from application to disbursal typically takes 24-48 hours.
                            </div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <h4>Can I prepay my loan before the tenure ends?</h4>
                            <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                Yes, you can make full or partial prepayments at any time without any prepayment charges. This helps you save on interest costs.
                            </div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <h4>What is the maximum loan amount I can get?</h4>
                            <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                You can get a <?php echo strtolower($current_title); ?> up to ₹25 lakhs depending on your income, credit score, and repayment capacity. The final amount is determined after assessment.
                            </div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <h4>Do I need a guarantor for this loan?</h4>
                            <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                No, <?php echo strtolower($current_title); ?>s are unsecured loans and do not require any guarantor or collateral. Your eligibility is based on your income and credit profile.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Why Choose Us Section -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h2>Why Choose Us</h2>
                </div>
                <div class="why-choose-grid">
                    <div class="why-choose-item">
                        <h4><i class="fas fa-bolt"></i> Lightning Fast Approval</h4>
                        <p>Get instant loan approval within 10 minutes with our advanced AI-powered assessment system.</p>
                    </div>
                    <div class="why-choose-item">
                        <h4><i class="fas fa-percentage"></i> Competitive Interest Rates</h4>
                        <p>Enjoy some of the lowest interest rates in the industry starting from just 10.5% p.a.</p>
                    </div>
                    <div class="why-choose-item">
                        <h4><i class="fas fa-file-alt"></i> Minimal Documentation</h4>
                        <p>Simple paperwork with digital document submission - no need for multiple branch visits.</p>
                    </div>
                    <div class="why-choose-item">
                        <h4><i class="fas fa-hand-holding-usd"></i> Flexible Repayment</h4>
                        <p>Choose your loan tenure from 1-5 years with flexible EMI options that suit your budget.</p>
                    </div>
                    <div class="why-choose-item">
                        <h4><i class="fas fa-shield-alt"></i> 100% Secure Process</h4>
                        <p>Bank-level encryption and data security to protect your personal and financial information.</p>
                    </div>
                    <div class="why-choose-item">
                        <h4><i class="fas fa-headset"></i> 24/7 Customer Support</h4>
                        <p>Round-the-clock assistance through phone, email, and chat for all your queries.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-card">
                <h3>Loan Highlights</h3>
                <div class="sidebar-features">
                    <div class="sidebar-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>No collateral required</span>
                    </div>
                    <div class="sidebar-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Instant online approval</span>
                    </div>
                    <div class="sidebar-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Minimal documentation</span>
                    </div>
                    <div class="sidebar-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Flexible repayment options</span>
                    </div>
                    <div class="sidebar-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>No prepayment charges</span>
                    </div>
                </div>
                <button class="apply-button">
                    <i class="fas fa-paper-plane"></i>
                    Apply Now
                </button>
                <button class="contact-button">
                    <i class="fas fa-phone"></i>
                    Contact Us
                </button>
            </div>

            <div class="sidebar-card help-box">
                <h4>Need Help?</h4>
                <p>Our loan experts are ready to assist you</p>
                <a href="tel:+919569408620" class="help-phone">
                    <i class="fas fa-phone-alt"></i>
                    +91 95694 08620
                </a>
            </div>
        </aside>
    </div>
</section>

<script>
    function toggleFAQ(element) {
        const faqItem = element.parentElement;
        const allFAQs = document.querySelectorAll('.faq-item');
        
        // Close all other FAQs
        allFAQs.forEach(item => {
            if (item !== faqItem) {
                item.classList.remove('active');
            }
        });
        
        // Toggle current FAQ
        faqItem.classList.toggle('active');
    }
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?>