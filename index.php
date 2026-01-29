<?php 
// Start session at the very top
session_start();

// Include database connection
require_once 'includes/header.php';
include 'includes/connection.php';

// Fetch loan types from services_subcategories table
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
    <!-- Your custom CSS will be added below -->
</head>
<body>

<style>
    /* Hero Section - Professional Design with Background Image */
    .hero-section {
        position: relative;
        background: 
            linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)),
            url('includes/assets/hero_section2.png') no-repeat;
        background-size: cover;
        background-position: center 0%;
        color: white;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }
    
    /* Add subtle blur effect to background */
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: inherit;
        filter: blur(3px);
        z-index: 1;
        opacity: 0.3;
    }
    
    .hero-content {
        position: relative;
        z-index: 3;
    }
    
    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    .hero-subtitle {
        font-size: 1.25rem;
        font-weight: 300;
        opacity: 0.9;
        margin-bottom: 30px;
        max-width: 600px;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }
    
    .hero-stats {
        display: flex;
        gap: 30px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }
    
    .stat-item {
        text-align: center;
        padding: 15px;
        min-width: 120px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: white;
        display: block;
        line-height: 1;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-top: 5px;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }
    
    /* Button Fixes - Force horizontal layout */
    .hero-buttons {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .hero-buttons .btn {
        flex-shrink: 0;
        white-space: nowrap;
    }
    
    /* Form Styles - Professional */
    .form-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
        z-index: 3;
    }
    
    .form-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .form-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }
    
    .form-subtitle {
        color: #666;
        font-size: 0.95rem;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
        font-size: 0.95rem;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        background: white;
    }
    
    .submit-btn {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        padding: 15px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 8px;
        width: 100%;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
    }
    
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
    }
    
    .security-note {
        text-align: center;
        font-size: 0.8rem;
        color: #888;
        margin-top: 15px;
    }
    
    /* Verified Badge */
    .verified-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.9);
        color: #1f2937;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 500;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .verified-badge .badge {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    }
    
    /* Messages */
    .alert {
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 0.95rem;
        backdrop-filter: blur(10px);
    }
    
    .alert-success {
        background: rgba(209, 250, 229, 0.9);
        color: #065f46;
        border: 1px solid rgba(167, 243, 208, 0.8);
    }
    
    .alert-danger {
        background: rgba(254, 226, 226, 0.9);
        color: #991b1b;
        border: 1px solid rgba(254, 202, 202, 0.8);
    }
    
    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border: none;
        padding: 12px 30px;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
    }
    
    .btn-outline-light {
        border: 2px solid rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }
    
    .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
    }

    /* Existing styles for other sections */
    /* How It Works specific styles */
    .how-it-works-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f1f5f9;
    }

    .how-it-works-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(37, 99, 235, 0.12) !important;
    }

    .step-number {
        font-size: 1.2rem;
        font-weight: bold;
    }

    /* Glass Card for Form */
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
        border-radius: 20px;
        position: relative;
        z-index: 1;
    }

    /* Floating Labels Styling */
    .form-floating > .form-control {
        border-color: #e2e8f0;
        background-color: rgba(255, 255, 255, 0.8);
    }
    .form-floating > .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(200, 16, 46, 0.1);
    }

    .gradient-text {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .feature-icon-box {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        margin: 0 auto 15px;
        transition: transform 0.3s ease;
    }

    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f1f5f9;
    }
    .card-hover:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(200, 16, 46, 0.08) !important;
        border-color: rgba(200, 16, 46, 0.2);
    }
    .card-hover:hover .feature-icon-box {
        transform: scale(1.1);
    }

    /* Section Divider */
    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
        margin: 2rem 0;
    }
    
    /* Badge Styles using Root Variables */
    .badge.bg-success {
        background-color: #10b981 !important;
    }

    /* Border Color using Root Variables */
    .border-primary {
        border-color: var(--primary-color) !important;
    }

    .border-success {
        border-color: #10b981 !important;
    }

    .border-warning {
        border-color: #f59e0b !important;
    }

    /* Text Color using Root Variables */
    .text-primary {
        color: var(--primary-color) !important;
    }

    /* Background Color using Root Variables */
    .bg-primary {
        background-color: var(--primary-color) !important;
    }

    .bg-primary.bg-opacity-10 {
        background-color: rgba(200, 16, 46, 0.1) !important;
    }

    /* Button Styles using Root Variables */
    .btn-outline-primary {
        color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    /* Alert Styles */
    .alert-success {
        background-color: #d1fae5;
        border-color: #10b981;
        color: #065f46;
    }

    .alert-danger {
        background-color: #fee2e2;
        border-color: #ef4444;
        color: #991b1b;
    }

    /* Animations */
    .animate-up { animation: fadeInUp 0.8s ease-out forwards; opacity: 0; }
    .animate-delay-1 { animation-delay: 0.2s; }
    .animate-delay-2 { animation-delay: 0.4s; }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Why Choose Us Section Styles */
    .why-choose-section {
        position: relative;
        background-attachment: fixed;
    }
    
    .why-choose-card {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }
    
    .why-choose-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2) !important;
        border-color: rgba(200, 16, 46, 0.2);
    }
    
    .why-choose-icon {
        transition: transform 0.3s ease;
    }
    
    .why-choose-card:hover .why-choose-icon {
        transform: scale(1.1);
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .why-choose-section {
            background-attachment: scroll;
        }
        
        .hero-section {
            padding: 60px 0;
            min-height: auto;
        }
        
        .hero-title {
            font-size: 2.8rem;
        }
        
        .hero-stats {
            gap: 20px;
        }
        
        .stat-item {
            min-width: 100px;
            padding: 12px;
        }
        
        .stat-number {
            font-size: 2rem;
        }
        
        .form-card {
            margin-top: 40px;
        }
        
        /* Mobile buttons - stack vertically */
        .hero-buttons {
            flex-direction: column !important;
            gap: 15px;
        }
        
        .hero-buttons .btn {
            width: 100%;
            justify-content: center;
        }
    }
    
    @media (max-width: 576px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-subtitle {
            font-size: 1rem;
        }
        
        .form-card {
            padding: 25px;
        }
        
        .hero-stats {
            gap: 15px;
        }
        
        .stat-item {
            min-width: 90px;
            padding: 10px;
        }
        
        .stat-number {
            font-size: 1.8rem;
        }
    }
</style>

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
                        <span class="stat-number">10 Min</span>
                        <span class="stat-label">Approval Time</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">₹25L</span>
                        <span class="stat-label">Max Amount</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">10.5%</span>
                        <span class="stat-label">Interest Rate</span>
                    </div>
                </div>
                
                <div class="hero-buttons d-flex flex-row gap-3">
                    <a href="#loanForm" class="btn btn-primary btn-lg rounded-pill px-5">
                        <i class="fas fa-file-contract me-2"></i> Apply For Loan
                    </a>
                    
                    <a href="tel:+919569408620" class="btn btn-outline-light btn-lg rounded-pill px-5">
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
                    
                    <!-- Note: Form action points to the separate backend file -->
                    <form method="POST" action="insert/enquiry_form.php" class="needs-validation" novalidate>
                        <!-- Full Name and Phone in one row -->
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
                                           placeholder="10-digit mobile number"
                                           value=""
                                           pattern="[0-9]{10}" maxlength="10" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="email">Email *</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   placeholder="Enter your email address"
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

<section class="py-5" style="background-color: #f8fafc;">
    <div class="container py-4">
        <div class="text-center mb-5 animate-up">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">Our Services</span>
            <h2 class="fw-bold mb-2">Tailored Financial Solutions</h2>
            <p class="text-muted">Choose the product that fits your life goals</p>
        </div>
        
        <div class="row g-4">
            <?php
            $loan_products = [
                ['icon' => 'fa-user', 'name' => 'Personal Loan', 'desc' => 'Up to ₹25 Lakhs', 'rate' => '10.5% p.a.', 'color' => 'primary'],
                ['icon' => 'fa-briefcase', 'name' => 'Business Loan', 'desc' => 'Up to ₹2 Crores', 'rate' => '12% p.a.', 'color' => 'success'],
                ['icon' => 'fa-user-md', 'name' => 'Professional Loan', 'desc' => 'For Doctors & CAs', 'rate' => '11% p.a.', 'color' => 'info'],
                ['icon' => 'fa-home', 'name' => 'Home Loan', 'desc' => 'Up to ₹5 Crores', 'rate' => '8.5% p.a.', 'color' => 'warning'],
                ['icon' => 'fa-credit-card', 'name' => 'Credit Card', 'desc' => 'Lifetime Free', 'rate' => '0% Joining', 'color' => 'danger'],
                ['icon' => 'fa-car', 'name' => 'Vehicle Loan', 'desc' => '100% Finance', 'rate' => '9.5% p.a.', 'color' => 'secondary']
            ];
            
            foreach ($loan_products as $index => $product):
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card bg-white border-0 shadow-sm h-100 card-hover rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-<?php echo $product['color']; ?> bg-opacity-10 rounded-3 p-3 me-3">
                                <i class="fas <?php echo $product['icon']; ?> fs-4 text-<?php echo $product['color']; ?>"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1"><?php echo $product['name']; ?></h5>
                                <p class="text-muted small mb-0"><?php echo $product['desc']; ?></p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light">
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Starting at</small>
                                <span class="text-<?php echo $product['color']; ?> fw-bold"><?php echo $product['rate']; ?></span>
                            </div>
                            <a href="services.php" class="btn btn-sm btn-outline-<?php echo $product['color']; ?> rounded-pill px-3">
                                Check Eligibility
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
 
<!-- Why Choose Us Section -->
<section class="why-choose-section position-relative py-5" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('includes/assets/why choose us.png') center center / cover no-repeat; min-height: 500px;">
    <div class="container py-5">
        <div class="row">
            <div class="col-12 mb-4">
                <h2 class="fw-bold text-white mb-4">Why Choose Udhar Capital</h2>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-lg-3 col-md-6">
                <div class="why-choose-card bg-white rounded-3 p-4 h-100 shadow-sm position-relative overflow-hidden">
                    <div class="why-choose-border position-absolute top-0 start-0 bottom-0"
                          style="width: 4px; background-color: var(--primary-color);"></div>
                    <div class="text-center mb-3">
                        <div class="why-choose-icon bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                              style="width: 60px; height: 60px;">
                        <i class="fas fa-clock fs-3" style="color: #2a0a77;"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2 text-center">Quick & Convenient</h5>
                    <p class="text-muted text-center mb-0 small">Easy application process</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-lg-3 col-md-6">
                <div class="why-choose-card bg-white rounded-3 p-4 h-100 shadow-sm position-relative overflow-hidden">
                    <div class="why-choose-border position-absolute top-0 start-0 bottom-0"
                          style="width: 4px; background-color: var(--primary-color);"></div>
                    <div class="text-center mb-3">
                        <div class="why-choose-icon bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                              style="width: 60px; height: 60px;">
                            <i class="fas fa-check-circle fs-3 "style="color: #2a0a77;"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2 text-center">Instant Online Approval</h5>
                    <p class="text-muted text-center mb-0 small">Apply online and get instant approval on your loan application</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-lg-3 col-md-6">
                <div class="why-choose-card bg-white rounded-3 p-4 h-100 shadow-sm position-relative overflow-hidden">
                    <div class="why-choose-border position-absolute top-0 start-0 bottom-0"
                          style="width: 4px; background-color: var(--primary-color);"></div>
                    <div class="text-center mb-3">
                        <div class="why-choose-icon bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                              style="width: 60px; height: 60px;">
                            <i class="fas fa-money-bill-wave fs-3"style="color: #2a0a77;"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2 text-center">Disbursal Within Hours</h5>
                    <p class="text-muted text-center mb-0 small">Get the loan amount transferred to your bank account within hours</p>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="col-lg-3 col-md-6">
                <div class="why-choose-card bg-white rounded-3 p-4 h-100 shadow-sm position-relative overflow-hidden">
                    <div class="why-choose-border position-absolute top-0 start=0 bottom-0"
                          style="width: 4px; background-color: var(--primary-color);"></div>
                    <div class="text-center mb-3">
                        <div class="why-choose-icon bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                              style="width: 60px; height: 60px;">
                            <i class="fas fa-shield-alt fs-3"style="color: #2a0a77;"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2 text-center">No Collaterals or Hidden Charges</h5>
                    <p class="text-muted text-center mb-0 small">Collateral-free loan process with no hidden charges</p>
                </div>
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
                                Aadhaar Card
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
            <a href="#loanForm" class="btn btn-primary btn-lg rounded-pill px-5">
                <i class="fas fa-play-circle me-2"></i> Start Your Journey
            </a>
        </div>
    </div>
</section>

<hr class="my-5 mx-auto" style="max-width: 1200px; border-top: 2px solid #131416;">

<!-- Simple Interest Based EMI Calculator -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                        <i class="fas fa-calculator me-2"></i> Simple EMI Calculator
                    </span>
                    <h2 class="fw-bold mb-3">Calculate Your Loan EMI</h2>
                    <p class="text-muted">Enter loan details to see your monthly payments</p>
                </div>
                
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Input Section -->
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Loan Amount (₹)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            ₹
                                        </span>
                                        <input type="number" class="form-control"
                                                id="loanAmountInput"
                                                placeholder="e.g., 500000"
                                               value="500000">
                                    </div>
                                    <small class="text-muted">Enter amount you want to borrow</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Interest Rate (% per year)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            %
                                        </span>
                                        <input type="number" class="form-control"
                                                id="interestRateInput"
                                                placeholder="e.g., 10.5"
                                               value="10.5"
                                               step="0.1">
                                    </div>
                                    <small class="text-muted">Yearly interest rate</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Loan Period (Years)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                        <input type="number" class="form-control"
                                                id="loanPeriodInput"
                                                placeholder="e.g., 5"
                                               value="5">
                                    </div>
                                    <small class="text-muted">Loan duration in years</small>
                                </div>
                                
                                <button class="btn btn-primary w-100 py-3 fw-bold" onclick="calculateEMI()">
                                    <i class="fas fa-calculator me-2"></i> Calculate Now
                                </button>
                            </div>
                            
                            <!-- Result Section -->
                            <div class="col-md-6">
                                <div class="bg-light rounded-3 p-4 h-100">
                                    <h5 class="fw-bold mb-4 text-center border-bottom pb-3">Your Loan Summary</h5>
                                    
                                    <!-- Loan Details -->
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <small class="text-muted d-block">Loan Amount</small>
                                                <h4 class="fw-bold mb-0 text-primary" id="displayLoanAmount">₹5,00,000</h4>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">For</small>
                                                <h5 class="fw-bold mb-0" id="displayLoanPeriod">5 Years</h5>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center bg-white rounded-3 p-3 mb-3">
                                            <small class="text-muted d-block mb-1">at</small>
                                            <h3 class="fw-bold text-success mb-0" id="displayInterestRate">10.5% Interest</h3>
                                        </div>
                                    </div>
                                    
                                    <!-- EMI Result -->
                                    <div class="text-center bg-primary text-white rounded-3 p-4 mb-4">
                                        <small class="opacity-90 d-block mb-1">Your Monthly EMI</small>
                                        <h1 class="fw-bold mb-2" id="displayEMI">₹10,417</h1>
                                        <small class="opacity-90">Per month for <span id="displayMonths">60</span> months</small>
                                    </div>
                                    
                                    <!-- Breakdown -->
                                    <div class="bg-white rounded-3 p-3">
                                        <h6 class="fw-bold mb-3">Payment Breakdown</h6>
                                        
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Principal Amount:</span>
                                            <span class="fw-bold" id="displayPrincipal">₹5,00,000</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total Interest:</span>
                                            <span class="fw-bold text-danger" id="displayTotalInterest">₹2,62,500</span>
                                        </div>
                                        
                                        <hr class="my-3">
                                        
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold">Total Payable:</span>
                                            <span class="fw-bold text-success" id="displayTotalPayable">₹7,62,500</span>
                                        </div>
                                    </div>
                                    
                                    <!-- CTA -->
                                    <div class="text-center mt-4">
                                        <a href="#loanForm" class="btn btn-success w-100 py-2">
                                            <i class="fas fa-paper-plane me-2"></i> Apply for This Loan
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

<script>
    // Format currency in Indian Rupees
    function formatCurrency(amount) {
        return '₹' + amount.toLocaleString('en-IN');
    }
    
    // Calculate EMI using Simple Interest
    function calculateEMI() {
        // Get input values
        const principal = parseFloat(document.getElementById('loanAmountInput').value) || 500000;
        const rate = parseFloat(document.getElementById('interestRateInput').value) || 10.5;
        const years = parseFloat(document.getElementById('loanPeriodInput').value) || 5;
        
        // Validate inputs
        if (!principal || principal <= 0) {
            alert('Please enter a valid loan amount');
            return;
        }
        
        if (!rate || rate <= 0) {
            alert('Please enter a valid interest rate');
            return;
        }
        
        if (!years || years <= 0) {
            alert('Please enter a valid loan period');
            return;
        }
        
        // Calculate Simple Interest
        const simpleInterest = (principal * rate * years) / 100;
        
        // Calculate Total Amount
        const totalAmount = principal + simpleInterest;
        
        // Calculate Monthly EMI (Total Amount divided by total months)
        const months = years * 12;
        const monthlyEMI = totalAmount / months;
        
        // Update display values
        document.getElementById('displayLoanAmount').textContent = formatCurrency(principal);
        document.getElementById('displayLoanPeriod').textContent = years + ' Year' + (years > 1 ? 's' : '');
        document.getElementById('displayInterestRate').textContent = rate + '% Interest';
        document.getElementById('displayMonths').textContent = months;
        
        document.getElementById('displayEMI').textContent = formatCurrency(Math.round(monthlyEMI));
        document.getElementById('displayPrincipal').textContent = formatCurrency(principal);
        document.getElementById('displayTotalInterest').textContent = formatCurrency(Math.round(simpleInterest));
        document.getElementById('displayTotalPayable').textContent = formatCurrency(Math.round(totalAmount));
    }
    
    // Initialize with default values
    document.addEventListener('DOMContentLoaded', function() {
        calculateEMI();
        
        // Calculate on Enter key press
        const inputs = document.querySelectorAll('#loanAmountInput, #interestRateInput, #loanPeriodInput');
        inputs.forEach(input => {
            input.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    calculateEMI();
                }
            });
        });
        
        // Auto-calculate when inputs change
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                calculateEMI();
            });
        });
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Include footer
require_once 'includes/footer.php';
?>
</body>
</html>