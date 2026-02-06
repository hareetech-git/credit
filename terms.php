<?php
require_once 'includes/header.php';
?>

<style>
    :root {
        --legal-ink: #0f172a;
        --legal-muted: #64748b;
        --legal-surface: #ffffff;
        --legal-soft: #f8fafc;
        --legal-border: rgba(15, 23, 42, 0.08);
    }

    .legal-hero {
        padding: 80px 0 50px;
        background: linear-gradient(135deg, rgba(200, 16, 46, 0.08), rgba(15, 23, 42, 0.04));
        border-bottom: 1px solid var(--legal-border);
    }

    .legal-hero h1 {
        font-size: 2.6rem;
        font-weight: 800;
        color: var(--legal-ink);
        margin-bottom: 10px;
    }

    .legal-hero p {
        color: var(--legal-muted);
        max-width: 720px;
    }

    .legal-content {
        padding: 60px 0 80px;
        background: var(--legal-soft);
    }

    .legal-card {
        background: var(--legal-surface);
        border: 1px solid var(--legal-border);
        border-radius: 18px;
        padding: 28px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
    }

    .legal-card h2 {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--legal-ink);
        margin-top: 22px;
    }

    .legal-card p, .legal-card li {
        color: var(--legal-muted);
        line-height: 1.7;
    }

    .legal-card ul {
        padding-left: 18px;
        margin-bottom: 0;
    }
</style>

<section class="legal-hero">
    <div class="container">
        <h1>Terms & Conditions</h1>
        <p>These terms govern your use of Udhar Capital's website and services. By accessing this website, you agree to the terms below.</p>
    </div>
</section>

<section class="legal-content">
    <div class="container">
        <div class="legal-card">
            <h2>1. General Information</h2>
            <p>Udhar Capital provides information related to loan services, eligibility, and application processes. All information is for general guidance and is subject to change without notice.</p>

            <h2>2. User Responsibilities</h2>
            <ul>
                <li>You agree to provide accurate and complete information during enquiries or applications.</li>
                <li>You are responsible for safeguarding your personal information and account access.</li>
                <li>You will not misuse the website for unlawful or fraudulent purposes.</li>
            </ul>

            <h2>3. Loan Approval & Disbursal</h2>
            <p>Loan approvals, interest rates, fees, and disbursal timelines are determined by partner lenders and are subject to verification, eligibility, and internal policies.</p>

            <h2>4. Fraud Disclaimer</h2>
            <p>Udhar Capital and its developer are not responsible for any fraud, scam, or unauthorized activity conducted through third parties, misrepresentation, or misuse of this website. Users should only apply via official channels and verify all communication.</p>

            <h2>5. Limitation of Liability</h2>
            <p>We are not liable for any direct or indirect losses arising from reliance on website content, delays in approval, or lender decisions. Use of this website is at your own risk.</p>

            <h2>6. Intellectual Property</h2>
            <p>All content, design, logos, and materials on this website are the property of Udhar Capital. Unauthorized use is prohibited.</p>

            <h2>7. Changes to Terms</h2>
            <p>We may update these terms at any time. Continued use of the website means you accept the revised terms.</p>

            <h2>8. Contact</h2>
            <p>For queries regarding these terms, please contact our support team via the Contact Us page.</p>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>
