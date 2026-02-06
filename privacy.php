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
        <h1>Privacy Policy</h1>
        <p>This policy explains how Udhar Capital collects, uses, and protects your personal information when you use our website.</p>
    </div>
</section>

<section class="legal-content">
    <div class="container">
        <div class="legal-card">
            <h2>1. Information We Collect</h2>
            <ul>
                <li>Contact details such as name, phone number, and email.</li>
                <li>Loan-related details you submit in enquiry forms.</li>
                <li>Technical data like device, browser, and IP address (for security and analytics).</li>
            </ul>

            <h2>2. How We Use Your Information</h2>
            <ul>
                <li>To respond to your loan enquiry and provide support.</li>
                <li>To verify eligibility and process loan applications.</li>
                <li>To improve user experience and prevent fraud or misuse.</li>
            </ul>

            <h2>3. Data Sharing</h2>
            <p>We may share necessary details with verified partner lenders or service providers for processing your request. We do not sell your data to unauthorized third parties.</p>

            <h2>4. Data Security</h2>
            <p>We use reasonable technical and organizational measures to protect your data. However, no system is 100% secure, and you provide data at your own discretion.</p>

            <h2>5. Fraud Disclaimer</h2>
            <p>Udhar Capital and its developer are not responsible for any fraud, scam, or unauthorized activity conducted through third parties or misuse of your data. Always verify official communication channels.</p>

            <h2>6. Cookies</h2>
            <p>We may use cookies to improve site performance and user experience. You can disable cookies in your browser settings.</p>

            <h2>7. Policy Updates</h2>
            <p>This policy may be updated from time to time. Continued use of the website indicates your acceptance of the changes.</p>

            <h2>8. Contact</h2>
            <p>If you have questions about this privacy policy, please contact us via the Contact Us page.</p>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>
