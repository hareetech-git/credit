<?php 
// Start session at the very top
session_start();

// Include database connection
require_once 'includes/header.php';
include 'includes/connection.php';

// Fetch loan types from services_subcategories table
$loan_types = [];
if (!function_exists('limitWords')) {
    function limitWords($text, $limit = 10) {
        $text = trim((string)$text);
        if ($text === '') return $text;
        $words = preg_split('/\s+/', $text);
        if (count($words) <= $limit) {
            return $text;
        }
        return implode(' ', array_slice($words, 0, $limit)) . '...';
    }
}

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

// Fetch service cards dynamically from services table
$service_cards = [];
if (isset($conn)) {
    $service_res = mysqli_query(
        $conn,
        "SELECT id, service_name, title, slug, short_description, hero_image, card_img
         FROM services
         WHERE slug IS NOT NULL AND slug != ''
         ORDER BY id DESC
         LIMIT 6"
    );

    if ($service_res && mysqli_num_rows($service_res) > 0) {
        while ($row = mysqli_fetch_assoc($service_res)) {
            $service_cards[] = $row;
        }
    }
}

// Fetch FAQs
$faq_items = [];
if (isset($conn)) {
    $faq_res = mysqli_query($conn, "SELECT id, question, answer FROM faqs WHERE status = 1 ORDER BY id DESC LIMIT 8");
    if ($faq_res && mysqli_num_rows($faq_res) > 0) {
        while ($row = mysqli_fetch_assoc($faq_res)) {
            $faq_items[] = $row;
        }
    }
}

// Fetch latest 3 published blogs for homepage
$latest_blogs = [];
if (isset($conn)) {
    $blog_res = mysqli_query(
        $conn,
        "SELECT id, title, slug, short_description, content, featured_image, created_at
         FROM blogs
         WHERE status = 1
         ORDER BY id DESC
         LIMIT 3"
    );

    if ($blog_res && mysqli_num_rows($blog_res) > 0) {
        while ($row = mysqli_fetch_assoc($blog_res)) {
            if (trim((string) $row['short_description']) === '') {
                $plain = trim(strip_tags((string) $row['content']));
                $row['short_description'] = strlen($plain) > 180 ? substr($plain, 0, 180) . '...' : $plain;
            }
            $latest_blogs[] = $row;
        }
    }
}

// Check for session messages
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';

// Clear session messages after displaying
unset($_SESSION['errors']);
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Udhar Capital - Home</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="includes/css/index.css">
</head>
<body>



<!-- Hero Section -->
<section class="hero-section py-5">
    <div class="container py-lg-4 position-relative z-1">
        <div class="row g-5 align-items-center">
            
            <div class="col-lg-6 animate-up">
                <div class="verified-badge mb-4">
                    <span class="badge rounded-pill me-2">Verified</span>
                    <span>Trusted by 10,000+ Indians</span>
                </div>
                
                <h1 class="hero-title">
                    Financial Freedom <br>
                    <span style="color: white">Starts Today</span>
                </h1>
                
                <p class="hero-subtitle">
                    Experience India's fastest loan processing. Get approvals from ₹10k to ₹25 Lakhs in minutes with minimal documentation.
                </p>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">48 Hours</span>
                        <span class="stat-label">Approval Time</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">upto 5CR</span>
                        <span class="stat-label">Max Amount</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">9.75%</span>
                        <span class="stat-label">Interest Rate</span>
                    </div>
                </div>
                
                <div class="hero-buttons d-flex flex-row gap-3">
                    <a href="apply-loan.php" class="btn btn-primary btn-lg rounded-pill px-5">
                        <i class="fas fa-file-contract me-2"></i> Apply For Loan
                    </a>
                    
                    <a href="dsa-register.php" class="btn btn-outline-light btn-lg rounded-pill px-5">
                        <i class="fas fa-handshake me-2"></i> Become a DSA Partner
                    </a>
                </div>
            </div>
            
            <div class="col-lg-5 offset-lg-1 animate-up animate-delay-2" id="loanForm">
                <div class="form-card">
                    <div class="form-header">
                        <h3 class="form-title">Get Started Today</h3>
                        <p class="form-subtitle">We'll get back to you shortly</p>
                    </div>
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="full_name">Full Name *</label>
                                    <input type="text" name="full_name" id="full_name" class="form-control"
                                           placeholder="Enter your full name"
                                           value=""
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="phone">Phone Number *</label>
                                    <input type="tel" name="phone" id="phone" class="form-control"
                                           placeholder="Enter Your Mobile Number"
                                           value=""
                                           pattern="[0-9]{10}" maxlength="10" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="email">Email *</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   placeholder="Enter Your email"
                                   value=""
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="loan_type">Select Loan Type *</label>
                            <select name="loan_type" id="loan_type" class="form-control" required>
                                <option value="">-- Select Loan Type --</option>
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
                            <label class="form-label" for="query_message">Your Query/Message *</label>
                            <textarea name="query_message" id="query_message" class="form-control"
                                      placeholder="Please describe your loan requirements or any questions you have"
                                      rows="3" required></textarea>
                        </div>
                        
                        <button type="submit" name="submit_enquiry" class="submit-btn">
                            <i class="fas fa-paper-plane me-2"></i> Submit Enquiry
                        </button>
                        
                        <div class="security-note">
                            <i class="fas fa-lock me-1"></i> Your information is 256-bit SSL encrypted
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>
<!-- Tailored Financial Solutions Section -->
<section class="py-5" style="background-color: #f8fafc;">
    <div class="container py-4">
        <div class="text-center mb-5 animate-up">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">Our Services</span>
            <h2 class="fw-bold mb-2">Tailored Financial Solutions</h2>
               <p class="text-muted">Powered by Fundify Communication Pvt Ltd</p>
        </div>
        
        <div class="row g-4">
            <?php
            $card_colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
            $card_icons = ['fa-user', 'fa-briefcase', 'fa-user-md', 'fa-home', 'fa-credit-card', 'fa-car'];
?>
            <?php if (!empty($service_cards)): ?>
                <?php foreach ($service_cards as $index => $service_card):
                    $color = $card_colors[$index % count($card_colors)];
                    $icon = $card_icons[$index % count($card_icons)];
                    $card_title = !empty($service_card['service_name']) ? $service_card['service_name'] : $service_card['title'];
                    $card_desc = !empty($service_card['short_description']) ? limitWords($service_card['short_description'], 8) : 'Explore this service';
                    $card_image = !empty($service_card['card_img'])
                        ? $service_card['card_img']
                        : 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=600';
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card bg-white border-0 shadow-sm h-100 card-hover rounded-4 overflow-hidden">
                        <div style="height: 200px; overflow: hidden;">
                            <img src="<?= htmlspecialchars($card_image) ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?= htmlspecialchars($card_title) ?>">
                        </div>
                        
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-<?= $color ?> bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="fas <?= $icon ?> fs-4 text-<?= $color ?>"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($card_title) ?></h5>
                                    <p class="text-muted small mb-0"><?= htmlspecialchars($card_desc) ?></p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Service</small>
                                    <span class="text-<?= $color ?> fw-bold">Available</span>
                                </div>
                                <a href="services.php?slug=<?= urlencode($service_card['slug']) ?>" class="btn btn-sm btn-outline-<?= $color ?> rounded-pill px-3">
                                    Check Eligibility
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-light border text-center mb-0">No services available right now.</div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- View All Services Button -->
        <div class="text-center mt-5">
            <a href="all_services.php" class="btn btn-lg rounded-pill px-5 view-all-theme-btn">
                View All Services <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
 <!-- Why Choose Us Section -->
<section class="py-5" style="background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 text-center position-relative">
                <div class="presence-map-container">
                    <div class="map-bg-circle"></div>
                    <i class="fas fa-map-location-dot map-main-icon"></i>
                    
                    <div class="branch-badge shadow-lg animate-bounce">
                        <span class="d-block fw-bold fs-3">18+</span>
                        <span class="text-uppercase small">Physical Branches</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                    <i class="fas fa-earth-asia me-2"></i> Our Footprint
                </span>
                <h2 class="fw-bold mb-4" style="color: #1a1a1a;">Expanding Across <span class="text-primary">India</span></h2>
                <p class="text-muted mb-5">We are committed to bringing financial freedom to every doorstep. With a growing network of branches and digital support, we are now serving customers across major Indian states.</p>

                <div class="row g-3">
                    <?php 
                    $states = [
                        ['name' => 'Uttar Pradesh', 'icon' => 'fa-temple'],
                        ['name' => 'Bihar', 'icon' => 'fa-archway'],
                        ['name' => 'Delhi & NCR', 'icon' => 'fa-city'],
                        ['name' => 'West Bengal', 'icon' => 'fa-bridge'],
                        ['name' => 'Rajasthan', 'icon' => 'fa-fort-awesome'],
                        ['name' => 'Maharashtra', 'icon' => 'fa-mountain-sun']
                    ];
                    foreach ($states as $state): ?>
                    <div class="col-6 col-md-4">
                        <div class="state-card p-3 shadow-sm border-0 rounded-4 bg-white h-100 d-flex align-items-center">
                            <div class="state-icon-sm me-2 bg-primary bg-opacity-10 text-primary rounded-circle">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <span class="fw-semibold text-dark small"><?= $state['name'] ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-5 p-4 rounded-4 bg-primary text-white shadow-lg d-flex align-items-center">
                    <div class="me-3 fs-1 opacity-50"><i class="fas fa-building-circle-check"></i></div>
                    <div>
                        <h5 class="mb-1 fw-bold">18 Specialized Branches</h5>
                        <p class="mb-0 small opacity-90">Visit us for personalized financial consulting and on-the-spot document verification.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
.presence-map-container {
    position: relative;
    padding: 40px;
}

.map-main-icon {
    font-size: 180px;
    color: var(--bs-primary);
    opacity: 0.15;
    position: relative;
    z-index: 2;
}

.map-bg-circle {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(13, 110, 253, 0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.branch-badge {
    position: absolute;
    top: 20%;
    right: 15%;
    background: #fff;
    padding: 15px 25px;
    border-radius: 20px;
    border-left: 5px solid var(--bs-primary);
    z-index: 3;
}

.state-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.state-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
}

.state-icon-sm {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.animate-bounce {
    animation: bounce 4s infinite ease-in-out;
}
</style>
<section class="ud-cap-feature-section" style="background-image: url('includes/assets/why choose us.png');">
    <div class="ud-cap-overlay"></div>
    
    <div class="container ud-cap-container">
        <div class="row">
            <div class="col-lg-6">
                <div class="ud-cap-accent"></div>
                <h2 class="fw-bold mb-4" style="color: #fff; font-size: 2.5rem;">Why Choose Udhar Capital</h2>
            </div>
        </div>

        <div class="ud-cap-grid">
            <div class="ud-cap-card">
                <div class="ud-cap-icon-wrapper">
                    <i class="fas fa-clock"></i>
                </div>
                <h5 class="ud-cap-h5">Quick & Convenient</h5>
                <p class="ud-cap-p">A streamlined digital process designed to get you started in minutes without the usual bank hassles.</p>
            </div>

            <div class="ud-cap-card">
                <div class="ud-cap-icon-wrapper">
                    <i class="fas fa-check-double"></i>
                </div>
                <h5 class="ud-cap-h5">Instant Approval</h5>
                <p class="ud-cap-p">Our smart evaluation engine provides real-time feedback on your loan eligibility and approval status.</p>
            </div>

            <div class="ud-cap-card">
                <div class="ud-cap-icon-wrapper">
                    <i class="fas fa-history"></i>
                </div>
                <h5 class="ud-cap-h5">Same-Day Disbursal</h5>
                <p class="ud-cap-p">Once approved, the funds reach your account within hours, ensuring your financial needs are met instantly.</p>
            </div>

            <div class="ud-cap-card">
                <div class="ud-cap-icon-wrapper">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h5 class="ud-cap-h5">No Collateral</h5>
                <p class="ud-cap-p">Access capital based on your merit. No need to pledge assets or worry about hidden security fees.</p>
            </div>
        </div>
    </div>
</section>


<!-- How It Works Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5 animate-up">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">Simple Process</span>
            <h2 class="fw-bold mb-2">How It Works</h2>
            <p class="text-muted">Get your loan in just 3 easy steps</p>
        </div>
        
        <div class="row g-4 position-relative">
            <!-- Connecting Line for Desktop -->
            <div class="d-none d-lg-block position-absolute top-50 start-0 end-0" style="z-index: 0;">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="position-relative" style="height: 4px;">
                                <div class="position-absolute top-0 start-0 end-0"
                                      style="background: linear-gradient(90deg, #e2e8f0 0%, var(--primary-color) 50%, #e2e8f0 100%); height: 2px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 1 -->
            <div class="col-lg-4 col-md-6 animate-up" style="z-index: 1;">
                <div class="card bg-white border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body p-4 text-center">
                        <div class="position-relative mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                                  style="width: 80px; height: 80px;">
                                <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                       style="width: 30px; height: 30px; position: absolute; top: -10px; right: -10px;">1</span>
                                <i class="fas fa-mobile-alt fs-2 text-primary"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3">Quick Apply</h4>
                        <p class="text-muted mb-0">
                            Fill our simple online form in just 2 minutes. No lengthy paperwork or physical visits required.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Step 2 -->
            <div class="col-lg-4 col-md-6 animate-up animate-delay-1" style="z-index: 1;">
                <div class="card bg-white border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body p-4 text-center">
                        <div class="position-relative mb-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                                  style="width: 80px; height: 80px;">
                                <span class="badge bg-success rounded-circle d-flex align-items-center justify-content-center"
                                       style="width: 30px; height: 30px; position: absolute; top: -10px; right: -10px;">2</span>
                                <i class="fas fa-file-upload fs-2 text-success"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3">Upload Documents</h4>
                        <p class="text-muted mb-0">
                            Upload digital copies of your documents. Our AI system verifies them instantly.
                        </p>
                        <div class="mt-3">
                            <small class="text-muted d-block">
                                <i class="fas fa-check-circle text-success me-1"></i>
                                Identity Proof
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-check-circle text-success me-1"></i>
                                PAN Card
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-check-circle text-success me-1"></i>
                                Bank Statement
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 3 -->
            <div class="col-lg-4 col-md-6 animate-up animate-delay-2" style="z-index: 1;">
                <div class="card bg-white border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body p-4 text-center">
                        <div class="position-relative mb-4">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                                  style="width: 80px; height: 80px;">
                                <span class="badge bg-warning rounded-circle d-flex align-items-center justify-content-center"
                                       style="width: 30px; height: 30px; position: absolute; top: -10px; right: -10px;">3</span>
                                <i class="fas fa-rupee-sign fs-2 text-warning"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3">Get Money</h4>
                        <p class="text-muted mb-0">
                            Receive instant approval and get money directly in your bank account within 10 minutes.
                        </p>
                        <div class="mt-3">
                            <div class="d-flex justify-content-center align-items-center">
                                <i class="fas fa-bolt text-warning me-2"></i>
                                <span class="fw-bold">10 Min Disbursal</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- CTA Button -->
        <div class="text-center mt-5 pt-3">
            <a href="apply-loan.php" class="btn btn-primary btn-lg rounded-pill px-5 journey-btn">
                <i class="fas fa-play-circle me-2"></i> Start Your Journey
            </a>
        </div>
    </div>
</section>

<hr class="my-5 mx-auto" style="max-width: 1200px; border-top: 2px solid #131416;">

<!-- Modern EMI Calculator -->
<section class="py-5 bg-white emi-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                        <i class="fas fa-calculator me-2"></i> Smart EMI Calculator
                    </span>
                    <h2 class="fw-bold mb-3">Calculate Your Monthly EMI</h2>
                    <p class="text-muted">Quickly estimate your EMI using a standard reducing-balance formula.</p>
                </div>
                
                <div class="card border-0 shadow-sm rounded-4 emi-card">
                    <div class="card-body p-4 p-lg-5">
                        <div class="row g-4 align-items-stretch">
                            <!-- Input Section -->
                            <div class="col-md-6">
                                <div class="emi-input-card">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Loan Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">&#8377;</span>
                                            <input type="number" class="form-control" id="loanAmountInput"
                                                   placeholder="e.g., 500000" value="500000" min="50000" step="1000">
                                        </div>
                                        <input type="range" class="form-range mt-3" id="loanAmountRange"
                                               min="50000" max="2500000" step="10000" value="500000">
                                        <small class="text-muted">Choose between &#8377;50,000 and &#8377;25,00,000</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Interest Rate (per year)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">%</span>
                                            <input type="number" class="form-control" id="interestRateInput"
                                                   placeholder="e.g., 10.5" value="10.5" step="0.1" min="6" max="30">
                                        </div>
                                        <input type="range" class="form-range mt-3" id="interestRateRange"
                                               min="6" max="30" step="0.1" value="10.5">
                                        <small class="text-muted">Typical range: 6% to 30%</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Loan Tenure (years)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <input type="number" class="form-control" id="loanPeriodInput"
                                                   placeholder="e.g., 5" value="5" min="1" max="20" step="1">
                                        </div>
                                        <input type="range" class="form-range mt-3" id="loanPeriodRange"
                                               min="1" max="20" step="1" value="5">
                                        <small class="text-muted">Choose between 1 and 20 years</small>
                                    </div>
                                    
                                    <button class="btn w-100 py-3 fw-bold emi-action-btn" id="calculateBtn">
                                        <i class="fas fa-bolt me-2"></i> Calculate EMI
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Result Section -->
                            <div class="col-md-6">
                                <div class="emi-result-card h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <small class="text-muted d-block">Loan Amount</small>
                                            <h4 class="fw-bold mb-0 text-primary" id="displayLoanAmount">&#8377;5,00,000</h4>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block">Tenure</small>
                                            <h5 class="fw-bold mb-0" id="displayLoanPeriod">5 Years</h5>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center bg-white rounded-3 p-3 mb-3 border">
                                        <small class="text-muted d-block mb-1">Interest Rate</small>
                                        <h3 class="fw-bold text-success mb-0" id="displayInterestRate">10.5% p.a.</h3>
                                    </div>
                                    
                                    <div class="text-center emi-highlight p-4 mb-4">
                                        <small class="opacity-90 d-block mb-1">Your Monthly EMI</small>
                                        <h1 class="fw-bold mb-2" id="displayEMI">&#8377;10,750</h1>
                                        <small class="opacity-90">Per month for <span id="displayMonths">60</span> months</small>
                                    </div>

                                    <div class="bg-white rounded-3 p-3 border">
                                        <h6 class="fw-bold mb-3">Payment Breakdown</h6>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Principal</span>
                                            <span class="fw-bold" id="displayPrincipal">&#8377;5,00,000</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total Interest</span>
                                            <span class="fw-bold text-danger" id="displayTotalInterest">&#8377;1,45,000</span>
                                        </div>
                                        <hr class="my-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold">Total Payable</span>
                                            <span class="fw-bold text-success" id="displayTotalPayable">&#8377;6,45,000</span>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center mt-4">
                                        <a href="#loanForm" class="btn w-100 py-2 emi-action-btn">
                                            <i class="fas fa-paper-plane me-2"></i> Apply with These Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Partner Benefits Network Section -->
<section class="partner-benefits-section py-5">
    <div class="container py-3">
        <div class="text-center mb-4">
            <span class="badge bg-light text-dark px-3 py-2 rounded-pill mb-3">
                <i class="fas fa-star me-2"></i> Why Choose Us
            </span>
            <h2 class="fw-bold text-white mb-2">Built For Growth, Trust, and Speed</h2>
            <p class="text-white-50 mb-0">A complete ecosystem designed for better performance and better customer outcomes.</p>
        </div>

        <div class="benefit-network">
            <div class="network-line"></div>

            <div class="network-item left">
                <div class="network-node"><i class="fas fa-laptop-medical"></i></div>
                <div class="network-card">
                    <h6>Easy On-boarding</h6>
                    <p>Quick activation and smooth start for every partner.</p>
                </div>
            </div>

            <div class="network-item right">
                <div class="network-node"><i class="fas fa-cubes"></i></div>
                <div class="network-card">
                    <h6>Multiple Products</h6>
                    <p>Wide product basket to match every customer profile.</p>
                </div>
            </div>

            <div class="network-item left">
                <div class="network-node"><i class="fas fa-circle-check"></i></div>
                <div class="network-card">
                    <h6>Instant Approvals</h6>
                    <p>Faster decisioning pipeline for better conversion.</p>
                </div>
            </div>

            <div class="network-item right">
                <div class="network-node"><i class="fas fa-wallet"></i></div>
                <div class="network-card">
                    <h6>Prompt Payouts</h6>
                    <p>Reliable and transparent payout tracking system.</p>
                </div>
            </div>

            <div class="network-item left">
                <div class="network-node"><i class="fas fa-shield-halved"></i></div>
                <div class="network-card">
                    <h6>Secure Data</h6>
                    <p>Strong security and compliance-led data handling.</p>
                </div>
            </div>

            <div class="network-item right">
                <div class="network-node"><i class="fas fa-chart-line"></i></div>
                <div class="network-card">
                    <h6>Unified Dashboard</h6>
                    <p>Single view of leads, status, payouts, and insights.</p>
                </div>
            </div>

            <div class="network-item left">
                <div class="network-node"><i class="fas fa-gem"></i></div>
                <div class="network-card">
                    <h6>Rewards &amp; Loyalty</h6>
                    <p>Growth-linked incentives and lifetime partner value.</p>
                </div>
            </div>

            <div class="network-item right">
                <div class="network-node"><i class="fas fa-headset"></i></div>
                <div class="network-card">
                    <h6>Excellent Customer Support</h6>
                    <p>Dedicated assistance from onboarding to disbursal.</p>
                </div>
            </div>

            <div class="network-item left">
                <div class="network-node"><i class="fas fa-handshake-angle"></i></div>
                <div class="network-card">
                    <h6>Training Programs</h6>
                    <p>Skill-building sessions to improve quality and output.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Brands Section -->
<section class="py-5" style="background-color: #ffffff;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                <i class="fas fa-handshake me-2"></i> Trusted Partners
            </span>
            <h2 class="fw-bold mb-3">Brands Who Trust Us</h2>
            <p class="text-muted">We are amongst the top 3 most-preferred channel partners for loans for India's widest network of Banks, NBFCs, and Fintech</p>
        </div>
        
        <?php
        // Fetch all brands from database (no limit in query)
        $brands_query = "SELECT id, brand_name, brand_img FROM brands WHERE active = 1 ORDER BY id DESC";
        $brands_result = mysqli_query($conn, $brands_query);
        $all_brands = [];
        if ($brands_result && mysqli_num_rows($brands_result) > 0) {
            while ($brand = mysqli_fetch_assoc($brands_result)) {
                $all_brands[] = $brand;
            }
        }
        
        // UI LIMIT: Only show first 16 brands maximum on the frontend
        $total_brands = count($all_brands);
        $ui_limit = 16;
        
        // Get first 8 brands for display (or less if total is less)
        $display_count = min(8, $total_brands);
        $display_brands = array_slice($all_brands, 0, $display_count);
        
        // Remaining brands (up to 8 more, but not exceeding UI limit of 16)
        $remaining_brands = array_slice($all_brands, 8, 8); // Show next 8 only (total max 16)
        $has_more = count($remaining_brands) > 0 && $total_brands > 8;
        
        // Check if we've reached the UI limit
        $total_displayed = count($display_brands) + count($remaining_brands);
        $reached_limit = $total_displayed >= $ui_limit;
        ?>
        
        <!-- Brands Grid - First 8 (4 per row) -->
        <div class="row g-4 justify-content-center" id="brandsGrid">
            <?php if (!empty($display_brands)): ?>
                <?php foreach ($display_brands as $brand): ?>
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="card border-0 shadow-sm h-100 brand-card">
                            <div class="card-body p-3 d-flex align-items-center justify-content-center" style="min-height: 120px;">
                                <?php 
                                // Fix image path
                                $image_path = 'admin/' . $brand['brand_img'];
                                ?>
                                <img src="<?= htmlspecialchars($image_path) ?>" 
                                     alt="<?= htmlspecialchars($brand['brand_name']) ?>"
                                     class="img-fluid brand-logo"
                                     style="max-width: 100%; max-height: 80px; object-fit: contain;"
                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/150x80?text=<?= urlencode($brand['brand_name']) ?>';">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4 text-muted">
                    <p>No brands available</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Remaining Brands (Hidden initially) - Only show if within UI limit -->
        <?php if ($has_more && !$reached_limit): ?>
            <div class="row g-4 justify-content-center mt-2" id="moreBrands" style="display: none;">
                <?php foreach ($remaining_brands as $brand): ?>
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="card border-0 shadow-sm h-100 brand-card">
                            <div class="card-body p-3 d-flex align-items-center justify-content-center" style="min-height: 120px;">
                                <?php 
                                $image_path = 'admin/' . $brand['brand_img'];
                                ?>
                                <img src="<?= htmlspecialchars($image_path) ?>" 
                                     alt="<?= htmlspecialchars($brand['brand_name']) ?>"
                                     class="img-fluid brand-logo"
                                     style="max-width: 100%; max-height: 80px; object-fit: contain;"
                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/150x80?text=<?= urlencode($brand['brand_name']) ?>';">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if ($total_displayed >= $ui_limit): ?>
                    <div class="col-12 text-center text-muted mt-3">
                        <small><i class="fas fa-info-circle me-1"></i> Showing <?= $ui_limit ?> of <?= $total_brands ?> brands</small>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- View All Button -->
            <div class="text-center mt-4" id="viewAllBtn">
                <button class="btn btn-outline-primary rounded-pill px-4" onclick="toggleBrands()">
                    <span id="btnText">VIEW ALL</span> <i class="fas fa-chevron-down ms-2" id="btnIcon"></i>
                </button>
            </div>
        <?php elseif ($total_brands > 16): ?>
            <div class="text-center text-muted mt-4">
                <small><i class="fas fa-info-circle me-1"></i> Showing 16 of <?= $total_brands ?> brands</small>
            </div>
        <?php endif; ?>
    </div>
</section>



<script>
// Toggle brands function
function toggleBrands() {
    const moreBrands = document.getElementById('moreBrands');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');
    
    if (moreBrands) {
        if (moreBrands.style.display === 'none' || moreBrands.style.display === '') {
            moreBrands.style.display = 'flex';
            moreBrands.style.flexWrap = 'wrap';
            if (btnText) btnText.textContent = 'VIEW LESS';
            if (btnIcon) {
                btnIcon.className = 'fas fa-chevron-up ms-2';
            }
        } else {
            moreBrands.style.display = 'none';
            if (btnText) btnText.textContent = 'VIEW ALL';
            if (btnIcon) {
                btnIcon.className = 'fas fa-chevron-down ms-2';
            }
        }
    }
}

// Make sure DOM is loaded before any operations
document.addEventListener('DOMContentLoaded', function() {
    // Ensure moreBrands is hidden initially
    const moreBrands = document.getElementById('moreBrands');
    if (moreBrands) {
        moreBrands.style.display = 'none';
    }
});
</script>
<!-- Latest Blogs Section -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">
                    <i class="fas fa-newspaper me-2"></i> Latest Updates
                </span>
                <h2 class="fw-bold mb-0">From Our Blog</h2>
            </div>
            <a href="blogs.php" class="btn btn-outline-dark rounded-pill px-4 mt-2 mt-md-0">View All Blogs</a>
        </div>

        <div class="row g-4">
            <?php if (!empty($latest_blogs)): ?>
                <?php foreach ($latest_blogs as $blog): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden blog-home-card">
                            <?php if (!empty($blog['featured_image'])): ?>
                                <img src="<?= htmlspecialchars((string) $blog['featured_image']) ?>"
                                     alt="<?= htmlspecialchars((string) $blog['title']) ?>"
                                     style="width:100%; height:210px; object-fit:cover;">
                            <?php endif; ?>
                            <div class="card-body p-4">
                                <small class="text-muted d-block mb-2">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?= date('d M Y', strtotime((string) $blog['created_at'])) ?>
                                </small>
                                <h5 class="fw-bold mb-2"><?= htmlspecialchars((string) $blog['title']) ?></h5>
                                <p class="text-muted small mb-3"><?= htmlspecialchars((string) limitWords((string) $blog['short_description'], 24)) ?></p>
                                <a href="blog-details.php?slug=<?= urlencode((string) $blog['slug']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    Read More <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted">Blogs will be available soon.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/certificate-section.php'; ?>


<!-- Testimonials Section -->
<section class="py-5 partner-stories-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-2 testimonial-section-title">Highest Standards. Happiest Partners</h2>
            <p class="testimonial-section-subtitle">Our partners are our strength</p>
        </div>
        
        <?php
        // Fetch all active testimonials for slider
        $testimonial_query = "SELECT partner_name, designation, testimonial_text, partner_img FROM testimonials WHERE active = 1 ORDER BY id DESC";
        $testimonial_result = mysqli_query($conn, $testimonial_query);
        $testimonials = [];
        if ($testimonial_result && mysqli_num_rows($testimonial_result) > 0) {
            while ($row = mysqli_fetch_assoc($testimonial_result)) {
                $testimonials[] = $row;
            }
        }

        if (!function_exists('resolveFrontendTestimonialImage')) {
            function resolveFrontendTestimonialImage(string $storedPath): string
            {
                $path = trim($storedPath);
                if ($path === '') {
                    return '';
                }
                if (preg_match('/^https?:\/\//i', $path)) {
                    return $path;
                }

                $path = ltrim($path, '/');

                if (strpos($path, 'admin/') === 0) {
                    return $path;
                }
                if (strpos($path, 'assets/') === 0) {
                    return 'admin/' . $path;
                }
                if (strpos($path, 'uploads/') === 0) {
                    return $path;
                }
                return 'admin/assets/testimonials/' . $path;
            }
        }
        ?>
        
        <?php if (!empty($testimonials)): ?>
            <div class="testimonial-slider-container" id="testimonialSlider">
                <div class="testimonial-display">
                    <?php foreach ($testimonials as $index => $testimonial): ?>
                        <?php $imgSrc = resolveFrontendTestimonialImage((string) ($testimonial['partner_img'] ?? '')); ?>
                        <div class="testimonial-slide <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
                            <div class="row g-0 align-items-stretch testimonial-row">
                                <div class="col-lg-6">
                                    <div class="testimonial-image-wrapper">
                                        <?php if ($imgSrc !== ''): ?>
                                            <img src="<?= htmlspecialchars($imgSrc) ?>" 
                                                 alt="<?= htmlspecialchars($testimonial['partner_name']) ?>"
                                                 class="img-fluid testimonial-image"
                                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/400x400?text=Partner';">
                                        <?php else: ?>
                                            <div class="testimonial-placeholder-image d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user-circle fa-6x text-white opacity-75"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <div class="testimonial-content-wrapper">
                                        <div class="testimonial-text-container">
                                            <p class="testimonial-text">
                                                "<?= htmlspecialchars($testimonial['testimonial_text']) ?>"
                                            </p>
                                            
                                            <div class="testimonial-author-chip mt-4">
                                                <?php if ($imgSrc !== ''): ?>
                                                    <img src="<?= htmlspecialchars($imgSrc) ?>" 
                                                         alt="<?= htmlspecialchars($testimonial['partner_name']) ?>"
                                                         class="testimonial-author-image"
                                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/80x80?text=U';">
                                                <?php else: ?>
                                                    <span class="testimonial-author-fallback"><i class="fas fa-user"></i></span>
                                                <?php endif; ?>

                                                <div class="testimonial-author-meta">
                                                    <h5><?= htmlspecialchars($testimonial['partner_name']) ?></h5>
                                                    <p><?= htmlspecialchars($testimonial['designation'] ?: 'Partner') ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($testimonials) > 1): ?>
                    <div class="testimonial-controls">
                        <div class="testimonial-dots">
                            <?php foreach ($testimonials as $index => $testimonial): ?>
                                <button type="button" class="dot <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>" aria-label="Go to testimonial <?= $index + 1 ?>"></button>
                            <?php endforeach; ?>
                        </div>

                        <div class="testimonial-arrows">
                            <button class="arrow prev" type="button" aria-label="Previous testimonial">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="arrow next" type="button" aria-label="Next testimonial">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-star mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                <p>No testimonials available yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('testimonialSlider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.testimonial-slide');
    const dots = slider.querySelectorAll('.dot');
    const prevBtn = slider.querySelector('.arrow.prev');
    const nextBtn = slider.querySelector('.arrow.next');

    if (!slides.length) return;

    let currentIndex = 0;
    let intervalId = null;
    const AUTO_MS = 6000;

    function showSlide(index) {
        if (index >= slides.length) index = 0;
        if (index < 0) index = slides.length - 1;

        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        slides[index].classList.add('active');
        if (dots[index]) dots[index].classList.add('active');
        currentIndex = index;
    }

    function stopAutoSlide() {
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
    }

    function startAutoSlide() {
        if (slides.length <= 1) return;
        stopAutoSlide();
        intervalId = setInterval(() => {
            showSlide(currentIndex + 1);
        }, AUTO_MS);
    }

    dots.forEach(dot => {
        dot.addEventListener('click', function() {
            const idx = Number(this.getAttribute('data-index'));
            showSlide(idx);
            startAutoSlide();
        });
    });

    prevBtn?.addEventListener('click', function() {
        showSlide(currentIndex - 1);
        startAutoSlide();
    });

    nextBtn?.addEventListener('click', function() {
        showSlide(currentIndex + 1);
        startAutoSlide();
    });

    slider.addEventListener('mouseenter', stopAutoSlide);
    slider.addEventListener('mouseleave', startAutoSlide);

    document.addEventListener('visibilitychange', function() {
        if (document.hidden) stopAutoSlide();
        else startAutoSlide();
    });

    showSlide(0);
    startAutoSlide();
});
</script>
<!-- FAQ Section -->
<section class="py-5 bg-white faq-section">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-4">
                <div class="faq-panel">
                    <div class="faq-kicker mb-3">
                        <i class="fas fa-circle-question"></i> FAQs
                    </div>
                    <h2 class="fw-bold mb-2">Questions? We have answers.</h2>
                    <p class="text-muted mb-4">Clear, quick responses to common loan queries.</p>

                    <div class="faq-stats">
                        <div class="faq-stat">
                            <h5>48 hours</h5>
                            <span>Fast approvals</span>
                        </div>
                        <div class="faq-stat">
                            <h5>24x7</h5>
                            <span>Support access</span>
                        </div>
                        <div class="faq-stat">
                            <h5>₹250CR</h5>
                            <span>Max amount</span>
                        </div>
                        <div class="faq-stat">
                            <h5>9.75%</h5>
                            <span>Rates from</span>
                        </div>
                    </div>

                    <a href="contact.php" class="btn btn-outline-primary w-100 rounded-pill mt-4">
                        <i class="fas fa-headset me-2"></i> Talk to an Expert
                    </a>
                </div>
            </div>

            <div class="col-lg-8">
                <?php if (!empty($faq_items)): ?>
                    <div class="accordion faq-accordion" id="faqAccordion">
                        <?php foreach ($faq_items as $index => $faq): 
                            $collapse_id = 'faqItem' . (int)$faq['id'];
                            $heading_id = 'heading' . (int)$faq['id'];
                            $is_first = $index === 0;
                        ?>
                        <div class="accordion-item shadow-sm rounded-3 mb-3">
                            <h2 class="accordion-header" id="<?= $heading_id ?>">
                                <button class="accordion-button <?= $is_first ? '' : 'collapsed' ?>" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#<?= $collapse_id ?>"
                                        aria-expanded="<?= $is_first ? 'true' : 'false' ?>" aria-controls="<?= $collapse_id ?>">
                                    <?= htmlspecialchars($faq['question']) ?>
                                </button>
                            </h2>
                            <div id="<?= $collapse_id ?>" class="accordion-collapse collapse <?= $is_first ? 'show' : '' ?>"
                                 aria-labelledby="<?= $heading_id ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted">FAQs will be available soon.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<!-- CTA Section -->
<section class="cta-section py-5">
    <i class="fas fa-coins cta-float float-1"></i>
    <i class="fas fa-shield-alt cta-float float-2"></i>
    <div class="container py-3 position-relative">
        <div class="cta-card">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <h2 class="cta-title">Ready to unlock your next big move?</h2>
                    <p class="cta-subtitle">
                        Apply in minutes with instant eligibility checks and transparent pricing &mdash; no surprises.
                    </p>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <a href="#loanForm" class="cta-btn">
                        <i class="fas fa-bolt"></i> Get Instant Quote
                    </a>
                    <a href="contact.php" class="cta-outline">
                        <i class="fas fa-headset"></i> Talk to Expert
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Format currency in Indian Rupees
    function formatCurrency(amount) {
        return '\u20B9' + Number(amount).toLocaleString('en-IN');
    }
    
    // Calculate EMI using reducing-balance formula
    function calculateEMI() {
        const principalInput = document.getElementById('loanAmountInput');
        const rateInput = document.getElementById('interestRateInput');
        const yearsInput = document.getElementById('loanPeriodInput');

        const principal = parseFloat(principalInput.value) || 0;
        const annualRate = parseFloat(rateInput.value) || 0;
        const years = parseFloat(yearsInput.value) || 0;

        if (principal <= 0 || annualRate <= 0 || years <= 0) {
            return;
        }

        const months = Math.round(years * 12);
        const monthlyRate = (annualRate / 12) / 100;
        let emi = 0;

        if (monthlyRate === 0) {
            emi = principal / months;
        } else {
            const factor = Math.pow(1 + monthlyRate, months);
            emi = (principal * monthlyRate * factor) / (factor - 1);
        }

        const totalPayable = emi * months;
        const totalInterest = totalPayable - principal;

        document.getElementById('displayLoanAmount').textContent = formatCurrency(principal);
        document.getElementById('displayLoanPeriod').textContent = years + ' Year' + (years > 1 ? 's' : '');
        document.getElementById('displayInterestRate').textContent = annualRate + '% p.a.';
        document.getElementById('displayMonths').textContent = months;

        document.getElementById('displayEMI').textContent = formatCurrency(Math.round(emi));
        document.getElementById('displayPrincipal').textContent = formatCurrency(principal);
        document.getElementById('displayTotalInterest').textContent = formatCurrency(Math.round(totalInterest));
        document.getElementById('displayTotalPayable').textContent = formatCurrency(Math.round(totalPayable));

    }

    function syncInputs() {
        const amountInput = document.getElementById('loanAmountInput');
        const amountRange = document.getElementById('loanAmountRange');
        const rateInput = document.getElementById('interestRateInput');
        const rateRange = document.getElementById('interestRateRange');
        const yearsInput = document.getElementById('loanPeriodInput');
        const yearsRange = document.getElementById('loanPeriodRange');

        amountRange.value = amountInput.value || amountRange.value;
        rateRange.value = rateInput.value || rateRange.value;
        yearsRange.value = yearsInput.value || yearsRange.value;
    }
    
    // Initialize with default values
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('loanAmountInput');
        const amountRange = document.getElementById('loanAmountRange');
        const rateInput = document.getElementById('interestRateInput');
        const rateRange = document.getElementById('interestRateRange');
        const yearsInput = document.getElementById('loanPeriodInput');
        const yearsRange = document.getElementById('loanPeriodRange');
        const calculateBtn = document.getElementById('calculateBtn');

        const inputPairs = [
            { input: amountInput, range: amountRange },
            { input: rateInput, range: rateRange },
            { input: yearsInput, range: yearsRange }
        ];

        inputPairs.forEach(pair => {
            pair.input.addEventListener('input', () => {
                pair.range.value = pair.input.value;
                calculateEMI();
            });

            pair.range.addEventListener('input', () => {
                pair.input.value = pair.range.value;
                calculateEMI();
            });
        });

        calculateBtn?.addEventListener('click', calculateEMI);

        syncInputs();
        calculateEMI();

    });
    
    // Form validation
    (function() {
        'use strict';
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
    })();

    // Phone number formatting
    document.getElementById('phone')?.addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '').substring(0, 10);
    });

    // Smooth scroll to form
    const applyButtons = document.querySelectorAll('a[href="#loanForm"]');
    applyButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const formElement = document.getElementById('loanForm');
            if(formElement) {
                formElement.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    });
</script>



<?php
// Include footer
require_once 'includes/footer.php';
?>
</body>
</html>

