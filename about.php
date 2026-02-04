<?php 
// Start session at the very top
session_start();

// Include database connection
require_once 'includes/header.php';
include 'includes/connection.php';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Udhar Capital</title>
   
</head>
<body>

<style>
    /* Hero Section */
    .about-hero {
        position: relative;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        padding: 120px 0 80px;
        overflow: hidden;
    }
    
    .about-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="none"/><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.05)"/></svg>') repeat;
        opacity: 0.3;
    }
    
    .about-hero-content {
        position: relative;
        z-index: 2;
    }
    
    .breadcrumb-custom {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 10px 20px;
        border-radius: 30px;
        display: inline-block;
    }
    
    .breadcrumb-custom a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
    }
    
    .breadcrumb-custom a:hover {
        color: white;
    }
    
    /* Story Section */
    .story-section {
        position: relative;
        padding: 80px 0;
    }
    
    .story-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        height: 100%;
    }
    
    .story-timeline {
        position: relative;
        padding-left: 40px;
    }
    
    .story-timeline::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border-radius: 2px;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
        padding-left: 30px;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -44px;
        top: 5px;
        width: 16px;
        height: 16px;
        background: var(--primary-color);
        border: 4px solid white;
        border-radius: 50%;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
    }
    
    .timeline-year {
        font-weight: 700;
        color: var(--primary-color);
        font-size: 1.2rem;
        margin-bottom: 5px;
    }
    
    /* Mission Vision Section */
    .mission-vision-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px;
        padding: 40px;
        height: 100%;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .mission-vision-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(220, 38, 38, 0.05) 0%, transparent 70%);
        transition: transform 0.3s ease;
    }
    
    .mission-vision-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .mission-vision-card:hover::before {
        transform: scale(1.2);
    }
    
    .mission-vision-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        box-shadow: 0 10px 30px rgba(220, 38, 38, 0.3);
    }
    
    /* Core Values */
    .value-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        height: 100%;
    }
    
    .value-card:hover {
        transform: translateY(-10px);
        border-color: var(--primary-color);
        box-shadow: 0 15px 40px rgba(220, 38, 38, 0.15);
    }
    
    .value-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.1) 0%, rgba(220, 38, 38, 0.05) 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        transition: transform 0.3s ease;
    }
    
    .value-card:hover .value-icon {
        transform: scale(1.1) rotate(5deg);
    }
    
    /* Stats Counter */
    .stats-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }
    
    .stats-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="none"/><path d="M0,50 Q25,25 50,50 T100,50" stroke="rgba(255,255,255,0.05)" fill="none"/></svg>') repeat;
    }
    
    .stat-box {
        text-align: center;
        padding: 30px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .stat-box:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-5px);
    }
    
    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        color: white;
        display: block;
        line-height: 1;
        margin-bottom: 10px;
    }
    
    .stat-label {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
    }
    
    /* Team Section */
    .team-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }
    
    .team-image {
        width: 100%;
        height: 280px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .team-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg"><circle cx="100" cy="100" r="80" fill="rgba(255,255,255,0.1)"/></svg>') center/cover;
    }
    
    .team-image i {
        font-size: 80px;
        color: rgba(255, 255, 255, 0.9);
        position: relative;
        z-index: 1;
    }
    
    .team-info {
        padding: 25px;
        text-align: center;
    }
    
    .team-name {
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 5px;
        color: #1f2937;
    }
    
    .team-role {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .team-social {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 15px;
    }
    
    .team-social a {
        width: 35px;
        height: 35px;
        background: rgba(220, 38, 38, 0.1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        transition: all 0.3s ease;
    }
    
    .team-social a:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-3px);
    }
    
    /* Why Different Section */
    .why-different-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        border-left: 4px solid var(--primary-color);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .why-different-card:hover {
        transform: translateX(10px);
        box-shadow: 0 10px 30px rgba(220, 38, 38, 0.15);
    }
    
    .why-different-number {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 20px;
    }
    
    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }
    
    .cta-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(220, 38, 38, 0.1) 0%, transparent 70%);
        animation: pulse 15s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1) rotate(0deg); }
        50% { transform: scale(1.2) rotate(180deg); }
    }
    
    /* Animations */
    .fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
    }
    
    .delay-1 { animation-delay: 0.2s; }
    .delay-2 { animation-delay: 0.4s; }
    .delay-3 { animation-delay: 0.6s; }
    
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
    
    /* Responsive */
    @media (max-width: 768px) {
        .about-hero {
            padding: 80px 0 60px;
        }
        
        .stat-number {
            font-size: 2.5rem;
        }
        
        .story-timeline {
            padding-left: 30px;
        }
        
        .timeline-item::before {
            left: -38px;
        }
    }
</style>

<!-- Hero Section -->
<section class="about-hero">
    <div class="about-hero-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="breadcrumb-custom mb-4">
                        <a href="index.php"><i class="fas fa-home me-2"></i>Home</a>
                        <span class="mx-2 text-white">/</span>
                        <span class="text-white">About Us</span>
                    </div>
                    
                    <h1 class="text-white fw-bold mb-3 fade-in-up" style="font-size: 3.5rem;">
                        Empowering Dreams Through
                        <span style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                            Smart Lending
                        </span>
                    </h1>
                    
                    <p class="text-white-50 mb-4 fade-in-up delay-1" style="font-size: 1.2rem; max-width: 700px; margin: 0 auto;">
                        We're not just a lending company – we're your financial partners committed to making your aspirations a reality with transparent, affordable, and accessible loan solutions.
                    </p>
                    
                    <div class="fade-in-up delay-2">
                        <a href="#our-story" class="btn btn-light btn-lg rounded-pill px-5 me-3 mb-3">
                            <i class="fas fa-book-open me-2"></i> Our Story
                        </a>
                        <a href="#contact" class="btn btn-outline-light btn-lg rounded-pill px-5 mb-3">
                            <i class="fas fa-phone-alt me-2"></i> Get in Touch
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="story-section bg-light" id="our-story">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 fade-in-up">
                <div class="story-card">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                        <i class="fas fa-history me-2"></i> Our Journey
                    </span>
                    
                    <h2 class="fw-bold mb-4">From a Vision to Reality</h2>
                    
                    <p class="text-muted mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                        Udhar Capital was born from a simple observation: millions of Indians struggle to access fair and transparent financial services. Traditional lending was complicated, time-consuming, and often inaccessible to those who needed it most.
                    </p>
                    
                    <p class="text-muted mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                        We set out to change that. By leveraging technology and maintaining unwavering commitment to customer satisfaction, we've created a lending platform that puts people first – offering quick approvals, minimal documentation, and interest rates that don't burden your dreams.
                    </p>
                    
                    <div class="d-flex align-items-center p-4 bg-white rounded-3 shadow-sm">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-quote-right fs-3 text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <p class="mb-0 fst-italic text-muted">
                                "Making financial freedom accessible to every Indian, one loan at a time."
                            </p>
                            <small class="text-primary fw-bold">- Our Mission</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 fade-in-up delay-1">
                <div class="story-card bg-white">
                    <h3 class="fw-bold mb-4">Our Timeline</h3>
                    
                    <div class="story-timeline">
                        <div class="timeline-item">
                            <div class="timeline-year">2020</div>
                            <h5 class="fw-bold mb-2">The Foundation</h5>
                            <p class="text-muted mb-0">Started with a vision to democratize lending in India with our first office in Mumbai.</p>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-year">2021</div>
                            <h5 class="fw-bold mb-2">Digital Transformation</h5>
                            <p class="text-muted mb-0">Launched our digital platform, reducing approval time from days to just 10 minutes.</p>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-year">2022</div>
                            <h5 class="fw-bold mb-2">Rapid Growth</h5>
                            <p class="text-muted mb-0">Crossed 5,000 satisfied customers and disbursed over ₹50 crores in loans.</p>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-year">2023</div>
                            <h5 class="fw-bold mb-2">Pan-India Presence</h5>
                            <p class="text-muted mb-0">Expanded to 15+ cities and partnered with leading financial institutions.</p>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-year">2024</div>
                            <h5 class="fw-bold mb-2">Innovation Leader</h5>
                            <p class="text-muted mb-0">Introduced AI-powered loan processing and reached 10,000+ happy customers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5 fade-in-up">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">
                <i class="fas fa-bullseye me-2"></i> What Drives Us
            </span>
            <h2 class="fw-bold mb-2">Mission & Vision</h2>
            <p class="text-muted">The principles that guide everything we do</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-6 fade-in-up">
                <div class="mission-vision-card">
                    <div class="mission-vision-icon">
                        <i class="fas fa-rocket fs-1 text-white"></i>
                    </div>
                    
                    <h3 class="fw-bold mb-3">Our Mission</h3>
                    
                    <p class="text-muted mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                        To provide fast, transparent, and affordable financial solutions that empower individuals and businesses across India to achieve their goals without the stress of traditional lending barriers.
                    </p>
                    
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Accessibility:</strong> Making loans available to everyone
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Transparency:</strong> No hidden charges, ever
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Speed:</strong> Quick approvals in under 10 minutes
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Affordability:</strong> Competitive interest rates
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-6 fade-in-up delay-1">
                <div class="mission-vision-card">
                    <div class="mission-vision-icon">
                        <i class="fas fa-eye fs-1 text-white"></i>
                    </div>
                    
                    <h3 class="fw-bold mb-3">Our Vision</h3>
                    
                    <p class="text-muted mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                        To become India's most trusted and customer-centric financial services provider, revolutionizing the lending industry through innovation, integrity, and a commitment to financial inclusion.
                    </p>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 text-center">
                                <i class="fas fa-users fs-3 text-primary mb-2"></i>
                                <h5 class="fw-bold mb-1">Customer First</h5>
                                <p class="text-muted small mb-0">Your satisfaction is our priority</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 text-center">
                                <i class="fas fa-lightbulb fs-3 text-warning mb-2"></i>
                                <h5 class="fw-bold mb-1">Innovation</h5>
                                <p class="text-muted small mb-0">Continuously improving our services</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 text-center">
                                <i class="fas fa-shield-alt fs-3 text-success mb-2"></i>
                                <h5 class="fw-bold mb-1">Trust & Security</h5>
                                <p class="text-muted small mb-0">Your data is safe with us</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 text-center">
                                <i class="fas fa-globe fs-3 text-info mb-2"></i>
                                <h5 class="fw-bold mb-1">Nationwide Reach</h5>
                                <p class="text-muted small mb-0">Serving customers across India</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Values Section -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5 fade-in-up">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">
                <i class="fas fa-heart me-2"></i> Our Core Values
            </span>
            <h2 class="fw-bold mb-2">What We Stand For</h2>
            <p class="text-muted">The values that define our culture and commitment</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 fade-in-up">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-handshake fs-2 text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Integrity</h5>
                    <p class="text-muted mb-0">
                        We believe in honest, ethical, and transparent business practices in every interaction.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 fade-in-up delay-1">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-star fs-2 text-warning"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Excellence</h5>
                    <p class="text-muted mb-0">
                        We strive for excellence in everything we do, continuously improving our services.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 fade-in-up delay-2">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-users-cog fs-2 text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Customer Focus</h5>
                    <p class="text-muted mb-0">
                        Your needs and satisfaction drive every decision we make and action we take.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 fade-in-up delay-3">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-sync-alt fs-2 text-info"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Innovation</h5>
                    <p class="text-muted mb-0">
                        We embrace technology and innovation to provide faster, better financial solutions.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section">
    <div class="container position-relative">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-box">
                    <span class="stat-number" data-count="10000">0</span>
                    <span class="stat-label">Happy Customers</span>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-box">
                    <span class="stat-number">₹<span data-count="100">0</span>Cr+</span>
                    <span class="stat-label">Loans Disbursed</span>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-box">
                    <span class="stat-number"><span data-count="15">0</span>+</span>
                    <span class="stat-label">Cities Covered</span>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-box">
                    <span class="stat-number"><span data-count="98">0</span>%</span>
                    <span class="stat-label">Customer Satisfaction</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why We're Different Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5 fade-in-up">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">
                <i class="fas fa-gem me-2"></i> What Makes Us Unique
            </span>
            <h2 class="fw-bold mb-2">Why Choose Udhar Capital?</h2>
            <p class="text-muted">We're not just another lending company – here's what sets us apart</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-6 fade-in-up">
                <div class="why-different-card">
                    <div class="why-different-number">01</div>
                    <h4 class="fw-bold mb-3">Industry-Low Interest Rates</h4>
                    <p class="text-muted mb-0">
                        We offer some of the most competitive interest rates in the market, starting from just 10.5% p.a. Your financial wellbeing matters to us, and we believe affordable credit shouldn't be a luxury.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-6 fade-in-up delay-1">
                <div class="why-different-card">
                    <div class="why-different-number">02</div>
                    <h4 class="fw-bold mb-3">Lightning-Fast Approvals</h4>
                    <p class="text-muted mb-0">
                        Time is money, and we respect both. Our AI-powered system processes applications in under 10 minutes, with money in your account within hours – not days or weeks.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-6 fade-in-up">
                <div class="why-different-card">
                    <div class="why-different-number">03</div>
                    <h4 class="fw-bold mb-3">Minimal Documentation</h4>
                    <p class="text-muted mb-0">
                        Forget endless paperwork. We've streamlined our process to require only essential documents – Aadhaar, PAN, and bank statement. That's it!
                    </p>
                </div>
            </div>
            
            <div class="col-lg-6 fade-in-up delay-1">
                <div class="why-different-card">
                    <div class="why-different-number">04</div>
                    <h4 class="fw-bold mb-3">100% Transparent</h4>
                    <p class="text-muted mb-0">
                        No hidden charges. No fine print. No surprises. What you see is what you get – complete transparency in all our dealings is our promise to you.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-6 fade-in-up">
                <div class="why-different-card">
                    <div class="why-different-number">05</div>
                    <h4 class="fw-bold mb-3">Flexible Repayment Options</h4>
                    <p class="text-muted mb-0">
                        Life is unpredictable, and we understand that. Choose from flexible tenure options and customize your EMI to match your cash flow and convenience.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-6 fade-in-up delay-1">
                <div class="why-different-card">
                    <div class="why-different-number">06</div>
                    <h4 class="fw-bold mb-3">Dedicated Support Team</h4>
                    <p class="text-muted mb-0">
                        Our customer support team is always here to help. Whether you have questions or need assistance, we're just a call or message away – ready to serve you.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5 fade-in-up">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">
                <i class="fas fa-users me-2"></i> Meet Our Team
            </span>
            <h2 class="fw-bold mb-2">The People Behind Udhar Capital</h2>
            <p class="text-muted">Dedicated professionals committed to your financial success</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 fade-in-up">
                <div class="team-card">
                    <div class="team-image">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="team-info">
                        <h5 class="team-name">Rajesh Kumar</h5>
                        <p class="team-role">Founder & CEO</p>
                        <p class="text-muted small mb-0">15+ years in financial services</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 fade-in-up delay-1">
                <div class="team-card">
                    <div class="team-image" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="team-info">
                        <h5 class="team-name">Priya Sharma</h5>
                        <p class="team-role">Chief Operations Officer</p>
                        <p class="text-muted small mb-0">Expert in operations management</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 fade-in-up delay-2">
                <div class="team-card">
                    <div class="team-image" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="team-info">
                        <h5 class="team-name">Amit Patel</h5>
                        <p class="team-role">Head of Technology</p>
                        <p class="text-muted small mb-0">Fintech innovation specialist</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 fade-in-up delay-3">
                <div class="team-card">
                    <div class="team-image" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="team-info">
                        <h5 class="team-name">Sneha Reddy</h5>
                        <p class="team-role">Customer Success Lead</p>
                        <p class="text-muted small mb-0">Customer satisfaction expert</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- Counter Animation Script -->
<script>
    // Counter Animation
    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60 FPS
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }
    
    // Intersection Observer for counter animation
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counters = entry.target.querySelectorAll('[data-count]');
                counters.forEach(counter => animateCounter(counter));
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    // Observe stats section
    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
        counterObserver.observe(statsSection);
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
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
