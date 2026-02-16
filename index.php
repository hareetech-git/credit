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
    
    /* EMI Calculator */
    .emi-section .emi-card {
        border: 1px solid #f1f5f9;
    }

    .emi-input-card {
        background: #f8fafc;
        border-radius: 18px;
        padding: 22px;
        border: 1px solid #e2e8f0;
    }

    .emi-result-card {
        background: #f9fafb;
        border-radius: 18px;
        padding: 22px;
        border: 1px solid #e5e7eb;
    }

    .emi-highlight {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(200, 16, 46, 0.25);
    }

    .emi-section .form-range::-webkit-slider-thumb {
        background: var(--primary-color);
    }

    .emi-section .form-range::-moz-range-thumb {
        background: var(--primary-color);
        border: none;
    }

    .emi-section .form-range::-webkit-slider-runnable-track {
        height: 6px;
        background: #e5e7eb;
        border-radius: 999px;
    }

    .emi-section .form-range::-moz-range-track {
        height: 6px;
        background: #e5e7eb;
        border-radius: 999px;
    }

    .emi-section .form-range::-webkit-slider-thumb {
        margin-top: -5px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .emi-section .form-range::-moz-range-thumb {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    /* FAQ */
    .faq-section {
        position: relative;
        background: #ffffff;
        overflow: hidden;
    }

    .faq-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(360px 220px at 12% 20%, rgba(200, 16, 46, 0.08), transparent),
            radial-gradient(320px 200px at 88% 80%, rgba(200, 16, 46, 0.06), transparent),
            repeating-linear-gradient(135deg, rgba(15, 23, 42, 0.03) 0 1px, transparent 1px 10px);
        opacity: 0.6;
        pointer-events: none;
    }

    .faq-panel {
        position: relative;
        z-index: 1;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        padding: 28px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.08);
    }

    .faq-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(200, 16, 46, 0.1);
        color: var(--primary-color);
        font-weight: 700;
        font-size: 0.78rem;
    }

    .faq-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }

    .faq-stat {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px;
        text-align: left;
    }

    .faq-stat h5 {
        margin: 0 0 4px 0;
        font-weight: 800;
        color: #0f172a;
    }

    .faq-stat span {
        font-size: 0.85rem;
        color: #64748b;
    }

    .faq-section .accordion-item {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        background: #ffffff;
    }

    .faq-section .accordion-item:hover {
        border-color: rgba(200, 16, 46, 0.25);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
    }

    .faq-section .accordion-button {
        padding: 18px 22px;
        position: relative;
        background: #ffffff;
        font-weight: 600;
    }

    .faq-section .accordion-button:not(.collapsed) {
        color: var(--primary-color);
        background: rgba(200, 16, 46, 0.08);
        box-shadow: none;
    }

    .faq-section .accordion-button::after {
        background-image: none;
        content: '+';
        font-weight: 800;
        color: var(--primary-color);
        font-size: 1.2rem;
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
    }

    .faq-section .accordion-button:not(.collapsed)::after {
        content: '–';
    }

    .faq-section .accordion-body {
        background: #fafafa;
        border-top: 1px solid #eef2f7;
        padding: 18px 22px;
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

    /* CTA Section */
    .cta-section {
        position: relative;
        background: #ffffff;
        overflow: hidden;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(300px 180px at 10% 20%, rgba(200, 16, 46, 0.12), transparent),
            radial-gradient(260px 160px at 90% 80%, rgba(200, 16, 46, 0.08), transparent),
            repeating-linear-gradient(45deg, rgba(200, 16, 46, 0.04) 0 2px, transparent 2px 10px);
        opacity: 0.6;
        pointer-events: none;
    }

    .cta-card {
        position: relative;
        z-index: 1;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 24px;
        padding: 44px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
    }

    .cta-title {
        font-size: 2.1rem;
        font-weight: 700;
        margin-bottom: 10px;
        color: #111827;
    }

    .cta-subtitle {
        font-size: 1.05rem;
        color: #6b7280;
        margin-bottom: 0;
    }

    .cta-btn {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: #ffffff;
        border: none;
        padding: 12px 26px;
        font-weight: 700;
        border-radius: 999px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 12px 24px rgba(200, 16, 46, 0.25);
    }

    .cta-btn:hover {
        transform: translateY(-2px);
        color: #ffffff;
        box-shadow: 0 16px 30px rgba(200, 16, 46, 0.35);
    }

    .cta-outline {
        border: 2px solid rgba(200, 16, 46, 0.3);
        color: var(--primary-color);
        padding: 10px 22px;
        border-radius: 999px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        margin-left: 10px;
    }

    .cta-float {
        position: absolute;
        z-index: 0;
        opacity: 0.18;
        color: var(--primary-color);
        animation: floatY 6s ease-in-out infinite;
    }

    .cta-float.float-1 {
        top: 20%;
        left: 6%;
        font-size: 48px;
    }

    .cta-float.float-2 {
        bottom: 18%;
        right: 8%;
        font-size: 56px;
        animation-delay: 1.5s;
    }

    @keyframes floatY {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }


    /* why choose us section start here  */
       /* Unique Light Glassmorphism Design */
.ud-cap-feature-section {
    position: relative;
    background-attachment: fixed;
    background-size: cover;
    background-position: center;
    padding: 80px 0;
}

/* The semi-transparent light overlay */
.ud-cap-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.7) 100%);
    z-index: 1;
}

.ud-cap-container {
    position: relative;
    z-index: 2;
}

.ud-cap-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-top: 40px;
}

.ud-cap-card {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 15px;
    padding: 35px 25px;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
}

.ud-cap-card:hover {
    transform: translateY(-10px);
    background: #ffffff;
    box-shadow: 0 20px 40px rgba(200, 16, 46, 0.15);
    border-color: #c8102e;
}

.ud-cap-icon-wrapper {
    width: 70px;
    height: 70px;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #f8f9fa;
    color: #2a0a77;
    font-size: 1.8rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.ud-cap-card:hover .ud-cap-icon-wrapper {
    background: #c8102e;
    color: #ffffff;
    transform: scale(1.1);
}

.ud-cap-h5 {
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 12px;
    font-size: 1.2rem;
}

.ud-cap-p {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 0;
}

/* Aesthetic accent line */
.ud-cap-accent {
    width: 50px;
    height: 4px;
    background: #c8102e;
    margin-bottom: 20px;
    border-radius: 2px;
}
    
    /* why choose us section end here  */
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
                        <span class="stat-number">48 Min</span>
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

<section class="py-5" style="background-color: #f8fafc;">
    <div class="container py-4">
        <div class="text-center mb-5 animate-up">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">Our Services</span>
            <h2 class="fw-bold mb-2">Tailored Financial Solutions</h2>
            <p class="text-muted">Choose the product that fits your life goals</p>
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
    </div>
</section>
 <!-- Why Choose Us Section -->

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
            <a href="apply-loan.php" class="btn btn-primary btn-lg rounded-pill px-5">
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
                                    
                                    <button class="btn btn-primary w-100 py-3 fw-bold" id="calculateBtn">
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
                                        <a href="#loanForm" class="btn btn-success w-100 py-2">
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
        // Fetch brands from database
        $brands_query = "SELECT id, brand_name, brand_img FROM brands WHERE active = 1 ORDER BY id DESC";
        $brands_result = mysqli_query($conn, $brands_query);
        $all_brands = [];
        if ($brands_result && mysqli_num_rows($brands_result) > 0) {
            while ($brand = mysqli_fetch_assoc($brands_result)) {
                $all_brands[] = $brand;
            }
        }
        
        // Get first 6 brands for display
        $display_brands = array_slice($all_brands, 0, 6);
        $remaining_brands = array_slice($all_brands, 6);
        $has_more = count($all_brands) > 6;
        ?>
        
        <!-- Brands Grid - First 6 -->
        <div class="row g-4 justify-content-center" id="brandsGrid">
            <?php if (!empty($display_brands)): ?>
                <?php foreach ($display_brands as $brand): ?>
                    <div class="col-lg-2 col-md-3 col-4">
                        <div class="card border-0 shadow-sm h-100 brand-card">
                            <div class="card-body p-3 d-flex align-items-center justify-content-center" style="min-height: 120px;">
                                <?php 
                                // FIXED: Add admin/ prefix to the path
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
        
        <!-- Remaining Brands (Hidden initially) -->
        <?php if ($has_more): ?>
            <div class="row g-4 justify-content-center mt-2" id="moreBrands" style="display: none;">
                <?php foreach ($remaining_brands as $brand): ?>
                    <div class="col-lg-2 col-md-3 col-4">
                        <div class="card border-0 shadow-sm h-100 brand-card">
                            <div class="card-body p-3 d-flex align-items-center justify-content-center" style="min-height: 120px;">
                                <?php 
                                // FIXED: Add admin/ prefix to the path
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
            </div>
            
            <!-- View All Button -->
            <div class="text-center mt-4" id="viewAllBtn">
                <button class="btn btn-outline-primary rounded-pill px-4" onclick="toggleBrands()">
                    <span id="btnText">VIEW ALL</span> <i class="fas fa-chevron-down ms-2" id="btnIcon"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>
</section>
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
                            <h5>48 min</h5>
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

