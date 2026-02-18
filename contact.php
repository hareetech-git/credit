<?php
require_once 'includes/header.php';

$loan_types = [];
if (isset($conn)) {
    $query = "SELECT id, sub_category_name 
              FROM services_subcategories 
              WHERE status = 'active' AND live = 1 
              ORDER BY sequence ASC";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $loan_types[] = $row;
        }
    }
}

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['errors'], $_SESSION['success_message']);

// Fetch FAQs
$faq_items = [];
if (isset($conn)) {
    $faq_res = mysqli_query($conn, "SELECT id, question, answer FROM faqs WHERE status = 1 ORDER BY id DESC LIMIT 6");
    if ($faq_res && mysqli_num_rows($faq_res) > 0) {
        while ($row = mysqli_fetch_assoc($faq_res)) {
            $faq_items[] = $row;
        }
    }
}
?>

<style>
    :root {
        --contact-ink: #0f172a;
        --contact-muted: #64748b;
        --contact-surface: #ffffff;
        --contact-soft: #f8fafc;
        --contact-accent: var(--primary-color);
        --contact-accent-dark: var(--primary-dark);
        --contact-accent-teal: var(--accent-teal);
        --contact-outline: rgba(15, 23, 42, 0.08);
    }

    .contact-hero {
        position: relative;
        padding: 90px 0 60px;
        background: radial-gradient(circle at 10% 20%, rgba(11, 8, 27, 0.35), transparent 52%),
                    radial-gradient(circle at 85% 15%, rgba(0, 212, 170, 0.2), transparent 45%),
                    linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 45%, #0f172a 100%);
        color: #fff;
        overflow: hidden;
    }

    .contact-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url('includes/assets/hero_section2.png');
        background-size: cover;
        background-position: center;
        opacity: 0.08;
        mix-blend-mode: screen;
        pointer-events: none;
    }

    .hero-glow {
        position: absolute;
        right: -120px;
        bottom: -120px;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.25), transparent 60%);
        filter: blur(8px);
        opacity: 0.7;
    }

    .contact-hero-content {
        position: relative;
        z-index: 2;
    }

    .contact-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 10px 18px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        margin-bottom: 22px;
    }

    .contact-hero h1 {
        font-size: 3.1rem;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 18px;
        text-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
    }

    .contact-hero p {
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.8);
        max-width: 560px;
        margin-bottom: 28px;
    }

    .contact-hero-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .contact-hero-tag {
        padding: 10px 16px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .contact-body {
        background: var(--contact-soft);
        padding: 70px 0 90px;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
        gap: 40px;
        align-items: start;
    }

    .contact-card {
        background: var(--contact-surface);
        border-radius: 20px;
        padding: 35px;
        border: 1px solid var(--contact-outline);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }

    .contact-card h2 {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--contact-ink);
    }

    .contact-card p {
        color: var(--contact-muted);
        margin-bottom: 20px;
    }

    .contact-details-list {
        display: grid;
        gap: 20px;
        margin-top: 28px;
    }

    .detail-item {
        display: flex;
        gap: 16px;
        padding: 18px;
        border-radius: 16px;
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
    }

    .detail-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--contact-accent), var(--contact-accent-teal));
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .detail-content h4 {
        margin: 0 0 6px;
        font-size: 1rem;
        font-weight: 700;
        color: var(--contact-ink);
    }

    .detail-content p {
        margin: 0;
        color: var(--contact-muted);
        font-size: 0.95rem;
    }

    .detail-content a {
        color: var(--contact-accent);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .contact-form-card {
        position: relative;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(14px);
        border-radius: 24px;
        padding: 40px;
        border: 1px solid rgba(255, 255, 255, 0.7);
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.18);
    }

    .contact-form-card::before {
        content: '';
        position: absolute;
        inset: 16px;
        border-radius: 20px;
        border: 1px dashed rgba(15, 23, 42, 0.12);
        pointer-events: none;
    }

    .form-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--contact-ink);
        margin-bottom: 8px;
    }

    .form-subtitle {
        color: var(--contact-muted);
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #1f2937;
    }

    .form-control {
        width: 100%;
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 1rem;
        background: rgba(255, 255, 255, 0.95);
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--contact-accent);
        box-shadow: 0 0 0 4px rgba(11, 8, 27, 0.12);
    }

    .submit-btn {
        width: 100%;
        border: none;
        border-radius: 12px;
        padding: 14px 18px;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, var(--contact-accent), var(--contact-accent-dark));
        box-shadow: 0 12px 25px rgba(11, 8, 27, 0.3);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 30px rgba(11, 8, 27, 0.35);
    }

    .security-note {
        text-align: center;
        margin-top: 15px;
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .support-strip {
        margin-top: 50px;
        background: #ffffff;
        border-radius: 20px;
        padding: 28px;
        border: 1px solid var(--contact-outline);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
    }

    .support-strip-item {
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .support-strip-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(11, 8, 27, 0.08);
        color: var(--contact-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .support-strip-item h4 {
        margin: 0 0 4px;
        font-size: 1rem;
        font-weight: 700;
        color: var(--contact-ink);
    }

    .support-strip-item p {
        margin: 0;
        color: var(--contact-muted);
        font-size: 0.9rem;
    }

    .contact-features {
        margin-top: 70px;
    }

    .contact-features-header {
        text-align: center;
        margin-bottom: 35px;
    }

    .contact-features-header span {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(11, 8, 27, 0.08);
        color: var(--contact-accent);
        padding: 8px 16px;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .contact-features-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
    }

    .feature-card {
        background: #fff;
        border-radius: 18px;
        padding: 24px;
        border: 1px solid var(--contact-outline);
        box-shadow: 0 12px 25px rgba(15, 23, 42, 0.06);
        height: 100%;
    }

    .feature-card .feature-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(11, 8, 27, 0.08);
        color: var(--contact-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 14px;
    }

    .feature-card h3 {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--contact-ink);
    }

    .feature-card p {
        color: var(--contact-muted);
        margin: 0;
        font-size: 0.95rem;
    }

    .faq-section {
        margin-top: 60px;
        background: #ffffff;
        border-radius: 22px;
        padding: 30px;
        border: 1px solid var(--contact-outline);
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.07);
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
        gap: 24px;
    }

    .faq-list {
        display: grid;
        gap: 12px;
    }

    .faq-item {
        border-radius: 14px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(248, 250, 252, 0.8);
        overflow: hidden;
    }

    .faq-item button {
        width: 100%;
        background: transparent;
        border: none;
        text-align: left;
        padding: 14px 16px;
        font-size: 1rem;
        font-weight: 700;
        color: var(--contact-ink);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .faq-item button::after {
        content: '+';
        font-weight: 800;
        color: var(--contact-accent);
    }

    .faq-item button[aria-expanded="true"]::after {
        content: '–';
    }

    .faq-item .faq-body {
        padding: 0 16px 14px;
        color: var(--contact-muted);
        font-size: 0.92rem;
        display: none;
    }

    .faq-item .faq-body.show {
        display: block;
    }

    .hours-card {
        background: linear-gradient(135deg, rgba(11, 8, 27, 0.08), rgba(0, 212, 170, 0.12));
        border-radius: 18px;
        padding: 22px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        height: 100%;
    }

    .hours-card h4 {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 10px;
        color: var(--contact-ink);
    }

    .hours-card ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 10px;
    }

    .hours-card li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.95rem;
        color: var(--contact-muted);
    }

    .hours-card li span {
        font-weight: 600;
        color: var(--contact-ink);
    }

    .apply-banner {
        margin-top: 60px;
        padding: 28px 30px;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .apply-banner h3 {
        margin: 0 0 6px;
        font-size: 1.4rem;
        font-weight: 700;
    }

    .apply-banner p {
        margin: 0;
        color: rgba(255, 255, 255, 0.8);
    }

    .apply-banner a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 22px;
        border-radius: 999px;
        background: #fff;
        color: var(--primary-color);
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
    }

    .contact-map {
        margin-top: 60px;
        border-radius: 22px;
        overflow: hidden;
        border: 1px solid var(--contact-outline);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }

    .contact-map iframe {
        width: 100%;
        height: 420px;
        border: 0;
        display: block;
    }

    @media (max-width: 991px) {
        .contact-hero {
            padding: 70px 0 50px;
        }

        .contact-hero h1 {
            font-size: 2.4rem;
        }

        .contact-grid {
            grid-template-columns: 1fr;
        }

        .contact-form-card {
            padding: 30px;
        }

        .support-strip {
            grid-template-columns: 1fr;
        }

        .contact-features-grid {
            grid-template-columns: 1fr;
        }

        .faq-section {
            grid-template-columns: 1fr;
        }

        .apply-banner {
            text-align: center;
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .contact-hero h1 {
            font-size: 2rem;
        }

        .contact-card,
        .contact-form-card {
            padding: 24px;
        }
    }
</style>

<section class="contact-hero">
    <div class="hero-glow"></div>
    <div class="container contact-hero-content">
        <span class="contact-eyebrow">
            <i class="fas fa-shield-alt"></i> Udhaar Capital Support
        </span>
        <h1>Lets build your next financial step</h1>
        <p>
            Speak with our loan specialists and get tailored recommendations in minutes.
            We will guide you on the right plan, the right rate, and the right timing.
        </p>
        <div class="contact-hero-tags">
            <span class="contact-hero-tag"><i class="fas fa-bolt"></i> 10 minute approvals</span>
            <span class="contact-hero-tag"><i class="fas fa-headset"></i> Dedicated support</span>
            <span class="contact-hero-tag"><i class="fas fa-lock"></i> Secure submissions</span>
        </div>
    </div>
</section>

<section class="contact-body" id="contactForm">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-card">
                <h2>Reach our team directly</h2>
                <p>We are here all week to help you compare loan options, rates, and timelines.</p>

                <div class="contact-details-list">
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="detail-content">
                            <h4>Call us anytime</h4>
<p><?php echo htmlspecialchars($webSettings['site_phone']); ?></p>
<a href="tel:<?php echo htmlspecialchars($webSettings['site_phone']); ?>">
<i class="fas fa-phone-alt me-1"></i>Call now</a>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-envelope"></i>
                        </div>

                        <div class="detail-content">
                            <h4>Email us</h4>
<p><?php echo htmlspecialchars($webSettings['site_email']); ?></p>
<a href="mailto:<?php echo htmlspecialchars($webSettings['site_email']); ?>">

                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="detail-content">
                            <h4>Visit our office</h4>
                          <p>
    <?php echo nl2br(htmlspecialchars($webSettings['site_address'])); ?>
</p>

                            <a href="https://maps.google.com/?q=Kasana+Tower+Greater+Noida" target="_blank">
                                <i class="fas fa-map me-1"></i>View on map
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-form-card">
                <h3 class="form-title">Send us your loan enquiry</h3>
                <p class="form-subtitle">Use the same quick form from our homepage to get a faster response.</p>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div class="mb-1"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="insert/enquiry_form.php" class="needs-validation" novalidate>
                    <input type="hidden" name="redirect_to" value="../contact.php#contactForm">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="full_name">Full name *</label>
                                <input type="text" name="full_name" id="full_name" class="form-control"
                                       placeholder="Enter your full name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="phone">Phone number *</label>
                                <input type="tel" name="phone" id="phone" class="form-control"
                                       placeholder="Enter Your Mobile Number" pattern="[0-9]{10}" maxlength="10" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email *</label>
                        <input type="email" name="email" id="email" class="form-control"
                               placeholder="devkratika8726@.com" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="loan_type">Select loan type *</label>
                        <select name="loan_type" id="loan_type" class="form-control" required>
                            <option value="">-- Select loan type --</option>
                            <?php if (!empty($loan_types)): ?>
                                <?php foreach ($loan_types as $loan): ?>
                                    <option value="<?php echo htmlspecialchars($loan['id']); ?>">
                                        <?php echo htmlspecialchars($loan['sub_category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No loan types available</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="query_message">Your query or message *</label>
                        <textarea name="query_message" id="query_message" class="form-control"
                                  placeholder="Please describe your requirements" rows="4" required></textarea>
                    </div>

                    <button type="submit" name="submit_enquiry" class="submit-btn">
                        <i class="fas fa-paper-plane me-2"></i> Submit enquiry
                    </button>

                    <div class="security-note">
                        <i class="fas fa-lock me-1"></i> Your information is 256-bit SSL encrypted
                    </div>
                </form>
            </div>
        </div>

        <div class="support-strip">
            <div class="support-strip-item">
                <div class="support-strip-icon"><i class="fas fa-clock"></i></div>
                <div>
                    <h4>Fast response</h4>
                    <p>Average response time under 2 hours for new enquiries.</p>
                </div>
            </div>
            <div class="support-strip-item">
                <div class="support-strip-icon"><i class="fas fa-user-check"></i></div>
                <div>
                    <h4>Verified advisors</h4>
                    <p>Every consultation is handled by trained loan specialists.</p>
                </div>
            </div>
            <div class="support-strip-item">
                <div class="support-strip-icon"><i class="fas fa-file-signature"></i></div>
                <div>
                    <h4>Paperless process</h4>
                    <p>Minimal paperwork and secure digital documentation.</p>
                </div>
            </div>
        </div>

        <div class="contact-features">
            <div class="contact-features-header">
                <span><i class="fas fa-star"></i> Why customers choose us</span>
                <h2 class="mt-3">Personal support from first call to disbursal</h2>
                <p class="text-muted">We keep every step transparent so you can move with confidence.</p>
            </div>
            <div class="contact-features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <h3>Smart rate matching</h3>
                    <p>Compare multiple lenders to find the best match for your profile.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>Secure data handling</h3>
                    <p>Your documents stay protected with industry standard security.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-hands-helping"></i></div>
                    <h3>Dedicated case manager</h3>
                    <p>One point of contact for approvals, documentation, and updates.</p>
                </div>
            </div>
        </div>

        <div class="faq-section">
            <div>
                <h3 class="mb-3">Frequently asked questions</h3>
                <div class="faq-list">
                    <?php if (!empty($faq_items)): ?>
                        <?php foreach ($faq_items as $index => $faq): 
                            $faq_id = 'contactFaq' . (int)$faq['id'];
                            $is_open = $index === 0;
                        ?>
                            <div class="faq-item">
                                <button type="button"
                                        class="faq-toggle"
                                        aria-expanded="<?= $is_open ? 'true' : 'false' ?>"
                                        aria-controls="<?= $faq_id ?>">
                                    <span><?= htmlspecialchars($faq['question']) ?></span>
                                </button>
                                <div id="<?= $faq_id ?>" class="faq-body <?= $is_open ? 'show' : '' ?>">
                                    <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted">FAQs will be available soon.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hours-card">
                <h4>Support hours</h4>
                <ul>
                    <li><span>Mon - Fri</span> 9:00 AM - 8:00 PM</li>
                    <li><span>Saturday</span> 10:00 AM - 6:00 PM</li>
                    <li><span>Sunday</span> By appointment</li>
                    <li><span>WhatsApp</span> 9:00 AM - 9:00 PM</li>
                </ul>
            </div>
        </div>

        <div class="apply-banner">
            <div>
                <h3>Ready to start your loan journey?</h3>
                <p>Apply in minutes and track updates from your dashboard.</p>
            </div>
            <a href="apply-loan.php"><i class="fas fa-file-signature"></i>Apply for loan</a>
        </div>

        <div class="contact-map">
  <iframe 
               src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d12584.172480502648!2d80.98895420485427!3d26.890442876563796!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMjbCsDUzJzMyLjYiTiA4McKwMDAnMjYuOCJF!5e1!3m2!1sen!2sin!4v1770789581193!5m2!1sen!2sin" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<script>
    (function() {
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').substring(0, 10);
            });
        }

        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

        document.querySelectorAll('.faq-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const bodyId = btn.getAttribute('aria-controls');
                const body = document.getElementById(bodyId);
                const isOpen = btn.getAttribute('aria-expanded') === 'true';

                document.querySelectorAll('.faq-toggle').forEach(otherBtn => {
                    otherBtn.setAttribute('aria-expanded', 'false');
                    const otherBody = document.getElementById(otherBtn.getAttribute('aria-controls'));
                    if (otherBody) otherBody.classList.remove('show');
                });

                btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                if (body) {
                    body.classList.toggle('show', !isOpen);
                }
            });
        });
    })();
</script>

<?php
require_once 'includes/footer.php';
?>
